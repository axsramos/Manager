<?php

namespace App\Controllers\Manager;

use App\Core\Controller;
use App\Shared\MessageDictionary;
use App\Models\CAS\CasUsrModel;
use App\Class\Pattern\FormDesign;
use App\Class\Pattern\FormData;
use App\Core\AuthSession;
use App\Core\Config;

class MeuPerfil extends Controller
{
    private $messages = array();
    private $formDesign;
    private $obCasUsrModel;
    private $dataEntry;

    public function __construct()
    {
        $this->validateAccess('MeuPerfil');
        // $this->setProgramParameters('MeuPerfil', '');

        $this->message = new MessageDictionary();

        $this->obCasUsrModel = new CasUsrModel();

        $this->formDesign = FormDesign::withTabs('Controle de Acesso', 'Meu Perfil', 'Perfil > Meu Perfil', $this->getUserMenu(), $this->getSideMenu());

        // Informações Gerais //
        $this->formDesign['Tabs']['Items'][0] = FormDesign::tabsModel('Single', '/Manager/MeuPerfil/General')[0];
        $this->formDesign['Tabs']['Items'][0]['Name'] = 'Informações Gerais';

        // Cadastro //
        $this->formDesign['Tabs']['Items'][1] = FormDesign::tabsModel('Single', '/Manager/MeuPerfil/Register')[0];
        $this->formDesign['Tabs']['Items'][1]['Name'] = 'Cadastro';

        // // Suporte //
        $this->formDesign['Tabs']['Items'][2] = FormDesign::tabsModel('Single', '/Manager/MeuPerfil/Support')[0];
        $this->formDesign['Tabs']['Items'][2]['Name'] = 'Suporte';
    }

    public function index()
    {
        $fields = array_intersect(CasUsrModel::FIELDS, ['CasUsrCod', 'CasUsrDsc', 'CasUsrNck', 'CasUsrDmn', 'CasUsrLgn']);
        array_push($fields, 'CasRpsCod');
        array_push($fields, 'CasUsrMai');

        // read //
        $this->obCasUsrModel->CasUsrCod = AuthSession::get()['USR_ID'];
        $this->obCasUsrModel->setSelectedFields($fields);

        if ($this->obCasUsrModel->readRegister()) {
            $this->dataEntry = $this->obCasUsrModel->getRecords()[0];
            $this->dataEntry['CasUsrMai'] = $this->obCasUsrModel->CasUsrLgn . $this->obCasUsrModel->CasUsrDmn;
            $this->dataEntry['CasRpsCod'] = AuthSession::get()['RPS_ID'];;
        }

        /**
         * Format Data
         */
        $this->formatDataOnForm();

        /**
         * Form Design
         */
        $this->formDesignOnForm('dsp', 0, 'MeuPerfilViewGeneral.php', []);

        /**
         * Show Screen
         */
        $formData = FormData::secFields($fields, $this->dataEntry);
        $formData['PRESENTATION'] = AuthSession::get()['PRESENTATION'];

        $data = array(
            'FormDesign' => $this->formDesign,
            'FormData' => $formData
        );

        $this->view('SBAdmin/Manager/MeuPerfilView', $data);
    }

    public function register()
    {
        $fields = array_intersect(CasUsrModel::FIELDS, ['CasUsrCod', 'CasUsrDsc', 'CasUsrNck', 'CasUsrDmn', 'CasUsrLgn']);
        array_push($fields, 'CasRpsCod');
        array_push($fields, 'CasUsrMai');

        $this->dataEntry = $this->getDataInput($fields);
        $this->dataEntry = FormData::secFields($fields, $this->dataEntry);

        // update //
        $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[2];

        if (isset($_POST['btnConfirmUpdate'])) {
            // prepare data for update, read before //
            $this->obCasUsrModel->CasUsrCod = AuthSession::get()['USR_ID'];
            $this->obCasUsrModel->setSelectedFields();
            $readDataUser = $this->obCasUsrModel->readRegister();

            $authorizedFields = ['CasUsrDsc', 'CasUsrNck'];
            foreach ($fields as $field) {
                $this->dataEntry[$field];
                if (in_array($field, $authorizedFields)) {
                    $this->obCasUsrModel->$field = $this->dataEntry[$field];
                }
            }

            $this->obCasUsrModel->CasUsrCod = AuthSession::get()['USR_ID'];
            if ($this->obCasUsrModel->updateRegister()) {
                array_push($this->messages, $this->message->getMessage(0, 'Message', 'Registro atualizado com sucesso!'));
            } else {
                array_push($this->messages, $this->message->getMessage(1, 'Message', 'Erro ao atualizar o registro!'));
            }
        }

        // read //
        $this->obCasUsrModel->CasUsrCod = AuthSession::get()['USR_ID'];
        $this->obCasUsrModel->setSelectedFields($fields);

        if ($this->obCasUsrModel->readRegister()) {
            $this->dataEntry = $this->obCasUsrModel->getRecords()[0];
            $this->dataEntry['CasUsrMai'] = $this->obCasUsrModel->CasUsrLgn . $this->obCasUsrModel->CasUsrDmn;
            $this->dataEntry['CasRpsCod'] = AuthSession::get()['RPS_ID'];;
        }

        /**
         * Format Data
         */
        $this->formatDataOnForm();

        /**
         * Form Design
         */
        $this->formDesignOnForm('dsp', 1, 'MeuPerfilViewRegister.php', []);

        /**
         * Show Screen
         */
        $formData = FormData::secFields($fields, $this->dataEntry);
        $formData['PRESENTATION'] = AuthSession::get()['PRESENTATION'];

        $data = array(
            'FormDesign' => $this->formDesign,
            'FormData' => $formData,
        );

        $this->view('SBAdmin/Manager/MeuPerfilView', $data);
    }

    public function support()
    {
        $formData = array(
            'PRESENTATION' => AuthSession::get()['PRESENTATION'],
            'MAIL_SUPPORT' => Config::getContact('support'),
        );

        /**
         * Form Design
         */
        $this->formDesignOnForm('dsp', 2, 'MeuPerfilViewSupport.php', []);

        /**
         * Show Screen
         */
        $data = array(
            'FormDesign' => $this->formDesign,
            'FormData' => $formData
        );
        
        $this->view('SBAdmin/Manager/MeuPerfilView', $data);
    }

    private function formatDataOnForm() {}

    private function formDesignOnForm($transMode, $current, $file, $keys)
    {
        $urlKeys = implode('/', $keys);

        $this->formDesign['Tabs']['Current'] = $current;
        $this->formDesign['Tabs']['LoadFile'] = "App/Views/SBAdmin/Manager/" . $file;
        $this->formDesign['Tabs']['Items'][0]['Link'] = '/Manager/MeuPerfil';

        $this->formDesign['Fields'] = CasUsrModel::FIELDS_MD;

        switch ($transMode) {
            case 'upd':
                $this->formDesign['Tabs']['Items'][1]['Link'] = "/Manager/MeuPerfil/Register";
                break;

            default:
                $this->formDesign['Tabs']['Items'][0]['Link'] = "/Manager/MeuPerfil";
                break;
        }

        if ($this->messages) {
            $this->formDesign['Message'] = $this->messages[0];
        }
    }
}
