<?php

namespace App\Controllers\Manager;

use App\Core\Controller;
use App\Core\Config;
use App\Shared\MessageDictionary;
use App\Models\CAS\CasTknModel;
use App\Class\Pattern\FormDesign;
use App\Class\Pattern\FormData;
use App\Class\Pattern\FormatData;
use App\Core\AuthSession;

class Token extends Controller
{
    private $messages = array();
    private $formDesign;
    private $obCasTknModel;
    private $dataEntry;

    public function __construct()
    {
        $this->validateAccess('Token');
        // $this->setProgramParameters('Token', '');

        // validação para contas administrativas portal siti - ['Aplicativo','Repositorio','Token','TipoDeUsuario','Usuario'] - [start] //
        if (! in_array(strtoupper(AuthSession::get()['PROFILE']), array_merge(Config::getTypeUsersAdminPortal(), Config::getTypeUsersSupportPortal()))) {
            header('Location: /Home/Denied'); // access denied //
            exit();
        }
        // validação para contas administrativas portal siti - [end] //

        $this->message = new MessageDictionary();

        $this->obCasTknModel = new CasTknModel();

        $this->formDesign = FormDesign::withTabs('Portal SITI', 'Token', 'Manager > Token', $this->getUserMenu(), $this->getSideMenu());
    }

    public function index()
    {
        $records = array();

        $fields = ['CasTknCod', 'CasTknDsc', 'CasTknBlq']; // CasTknModel::FIELDS; // 
        $hidden = [];

        if ($this->obCasTknModel->readAllLines()) {
            $records = $this->obCasTknModel->getRecords();
        }

        /**
         * Format Data
         */
        foreach ($records as $key => $value) {
            if (isset($records[$key]['CasTknBlq'])) {
                $records[$key]['CasTknBlq'] = FormatData::transformSelectionSN($value['CasTknBlq']);
            }
            if (isset($records[$key]['CasTknBlqDtt'])) {
                $records[$key]['CasTknBlqDtt'] = FormatData::transformData('OnlyDate', $value['CasTknBlqDtt']);
            }
            if (isset($records[$key]['CasTknKeyExp'])) {
                $records[$key]['CasTknKeyExp'] = FormatData::transformData('OnlyDate', $value['CasTknKeyExp']);
            }
            if (isset($records[$key]['CasTknAudIns'])) {
                $records[$key]['CasTknAudIns'] = FormatData::transformData('OnlyDate', $value['CasTknAudIns']);
            }
            if (isset($records[$key]['CasTknAudUpd'])) {
                $records[$key]['CasTknAudUpd'] = FormatData::transformData('OnlyDate', $value['CasTknAudUpd']);
            }
            if (isset($records[$key]['CasTknAudDlt'])) {
                $records[$key]['CasTknAudDlt'] = FormatData::transformData('OnlyDate', $value['CasTknAudDlt']);
            }
        }

        /**
         * Form Design
         */
        $fields_md = array();
        foreach ($fields as $key => $field) {
            $fields_md[$field] = CasTknModel::FIELDS_MD[$field];
        }

        $this->formDesignOnForm(0, 'TokenViewList.php', '');
        $this->formDesign['Styles']['CSSFiles'] = ['dataTables'];
        $this->formDesign['Scripts']['Body'] = ['dataTables'];
        $this->formDesign['Fields'] = $fields_md;
        $this->formDesign['Hidden'] = $hidden;

        /**
         * Show Screen
         */
        $data = array(
            'FormDesign' => $this->formDesign,
            'FormData' => $records
        );

        $this->view('SBAdmin/Manager/TokenView', $data);
    }

    public function show($id = '')
    {
        /**
         * Modos atendidos (TransMode): Insert e Update
         */
        $fields = CasTknModel::FIELDS;

        $this->dataEntry = $this->getDataInput($fields);
        $this->dataEntry = FormData::secFields($fields, $this->dataEntry);

        if (empty($id)) {
            // insert //
            $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[1];
        } else {
            // update //
            $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[2];
        }

        if (isset($_POST['btnConfirmInsert']) || isset($_POST['btnConfirmUpdate'])) {
            foreach ($fields as $field) {
                $this->obCasTknModel->$field = $this->dataEntry[$field];
            }

            // insert //
            if (isset($_POST['btnConfirmInsert'])) {
                if ($this->obCasTknModel->createRegister()) {
                    array_push($this->messages, $this->message->getMessage(0, 'Message', 'Registro inserido com sucesso!'));
                    $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[0];
                    $this->formDesign['FormDisable'] = true;
                    $id = $this->obCasTknModel->CasTknCod;
                } else {
                    array_push($this->messages, $this->message->getMessage(1, 'Message', 'Erro ao inserir o registro!'));
                }
            }

            // update //
            if (isset($_POST['btnConfirmUpdate'])) {
                if ($this->obCasTknModel->updateRegister()) {
                    array_push($this->messages, $this->message->getMessage(0, 'Message', 'Registro atualizado com sucesso!'));
                    $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[0];
                    $this->formDesign['FormDisable'] = true;
                    $id = $this->obCasTknModel->CasTknCod;
                } else {
                    array_push($this->messages, $this->message->getMessage(1, 'Message', 'Erro ao atualizar o registro!'));
                }
            }
        }

        if (!empty($id)) {
            $this->obCasTknModel->CasTknCod = $id;
            if ($this->obCasTknModel->readRegister()) {
                $this->dataEntry = $this->obCasTknModel->getRecords()[0];
            }
        }

        /**
         * Format Data
         */
        $this->formatDataOnForm();

        /**
         * Form Design
         */
        $this->formDesignOnForm(1, 'TokenViewForm.php', $id);

        /**
         * Show Screen
         */
        $data = array(
            'FormDesign' => $this->formDesign,
            'FormData' => FormData::secFields($fields, $this->dataEntry),
        );

        $this->view('SBAdmin/Manager/TokenView', $data);
    }

    public function remove($id = '')
    {
        $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[3];
        $this->formDesign['FormDisable'] = true;

        $fields = array_merge(CasTknModel::FIELDS, CasTknModel::FIELDS_AUDIT);

        if (!empty($id)) {
            $this->obCasTknModel->CasTknCod = $id;
            if (! $this->obCasTknModel->isEmptyAllReferencedTables()) {
                array_push($this->messages, $this->message->getMessage(2, 'Message', 'Existem dados relacionados a este registro!'));
            }
            if (isset($_POST['btnConfirmDelete'])) {
                if ($this->obCasTknModel->deleteRegister()) {
                    array_push($this->messages, $this->message->getMessage(0, 'Message', 'Registro excluído com sucesso!'));
                    header('Location: /Manager/Token');
                    exit();
                } else {
                    array_push($this->messages, $this->message->getMessage(1, 'Message', 'Erro ao excluir o registro!'));
                }
            } else {
                if ($this->obCasTknModel->readRegister()) {
                    $this->dataEntry = $this->obCasTknModel->getRecords()[0];
                }
            }
        }

        /**
         * Format Data
         */
        $this->formatDataOnForm();

        /**
         * Form Design
         */
        $this->formDesignOnForm(1, 'TokenViewForm.php', $id);
        $this->formDesign['Tabs']['Items'][1]['Link'] = str_replace('/Show/', '/Remove/', $this->formDesign['Tabs']['Items'][1]['Link']);

        /**
         * Show Screen
         */
        $data = array(
            'FormDesign' => $this->formDesign,
            'FormData' => FormData::secFields($fields, $this->dataEntry),
        );

        $this->view('SBAdmin/Manager/TokenView', $data);
    }

    private function formatDataOnForm()
    {
        $this->dataEntry['CasTknBlq'] = FormatData::transformSelectionSN($this->dataEntry['CasTknBlq'], false);
    }

    private function formDesignOnForm($current, $file, $id)
    {
        $this->formDesign['Tabs']['Current'] = $current;
        $this->formDesign['Tabs']['LoadFile'] = "App/Views/SBAdmin/Manager/" . $file;
        $this->formDesign['Tabs']['Items'][0]['Link'] = '/Manager/Token';
        $this->formDesign['Tabs']['Items'][1]['Link'] = str_replace('{id}', $id, '/Manager/Token/Show/{id}');
        $this->formDesign['Fields'] = CasTknModel::FIELDS_MD;
        $this->formDesign['TransLinkRemove'] = str_replace('{id}', $id, '/Manager/Token/Remove/{id}');

        if ($this->messages) {
            $this->formDesign['Message'] = $this->messages[0];
        }
    }
}
