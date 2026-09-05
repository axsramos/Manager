<?php

namespace App\Controllers\Manager;

use App\Class\Auth\AuthClass;
use App\Core\Controller;
use App\Core\Config;
use App\Shared\MessageDictionary;
use App\Models\CAS\CasAppModel;
use App\Class\Pattern\FormDesign;
use App\Class\Pattern\FormData;
use App\Class\Pattern\FormatData;
use App\Core\AuthSession;

class Aplicativo extends Controller
{
    private $messages = array();
    private $formDesign;
    private $obCasAppModel;
    private $dataEntry;

    public function __construct()
    {
        $this->validateAccess('Aplicativo');
        // $this->setProgramParameters('Aplicativo', '');

        // validação para contas administrativas portal siti - ['Aplicativo','Repositorio','Token','TipoDeUsuario','Usuario'] - [start] //
        if (! in_array(strtoupper(AuthSession::get()['PROFILE']), array_merge(Config::getTypeUsersAdminPortal(), Config::getTypeUsersSupportPortal()))) {
            header('Location: /Home/Denied'); // access denied //
            exit();
        }
        // validação para contas administrativas portal siti - [end] //

        $this->message = new MessageDictionary();

        $this->obCasAppModel = new CasAppModel();

        $this->formDesign = FormDesign::withTabs('Portal SITI', 'Aplicativo', 'Manager > Aplicativo', $this->getUserMenu(), $this->getSideMenu());
    }

    public function index()
    {
        $records = array();

        $fields = ['CasAppCod', 'CasAppDsc', 'CasAppBlq']; // CasAppModel::FIELDS; // 
        $hidden = [];


        if ($this->obCasAppModel->readAllLines()) {
            $records = $this->obCasAppModel->getRecords();
        }

        /**
         * Format Data
         */
        foreach ($records as $key => $value) {
            if (isset($records[$key]['CasAppBlq'])) {
                $records[$key]['CasAppBlq'] = FormatData::transformSelectionSN($value['CasAppBlq']);
            }
            if (isset($records[$key]['CasAppBlqDtt'])) {
                $records[$key]['CasAppBlqDtt'] = FormatData::transformData('OnlyDate', $value['CasAppBlqDtt']);
            }
            if (isset($records[$key]['CasAppTst'])) {
                $records[$key]['CasAppTst'] = FormatData::transformSelectionSN($value['CasAppTst']);
            }
            if (isset($records[$key]['CasAppTstDtt'])) {
                $records[$key]['CasAppTstDtt'] = FormatData::transformData('OnlyDate', $value['CasAppTstDtt']);
            }
            if (isset($records[$key]['CasAppVerDtt'])) {
                $records[$key]['CasAppVerDtt'] = FormatData::transformData('OnlyDate', $value['CasAppVerDtt']);
            }
            if (isset($records[$key]['CasAppKeyExp'])) {
                $records[$key]['CasAppKeyExp'] = FormatData::transformData('OnlyDate', $value['CasAppKeyExp']);
            }
            if (isset($records[$key]['CasAppAudIns'])) {
                $records[$key]['CasAppAudIns'] = FormatData::transformData('OnlyDate', $value['CasAppAudIns']);
            }
            if (isset($records[$key]['CasAppAudUpd'])) {
                $records[$key]['CasAppAudUpd'] = FormatData::transformData('OnlyDate', $value['CasAppAudUpd']);
            }
            if (isset($records[$key]['CasAppAudDlt'])) {
                $records[$key]['CasAppAudDlt'] = FormatData::transformData('OnlyDate', $value['CasAppAudDlt']);
            }
        }

        /**
         * Form Design
         */
        $fields_md = array();
        foreach ($fields as $key => $field) {
            $fields_md[$field] = CasAppModel::FIELDS_MD[$field];
        }

        $this->formDesignOnForm(0, 'AplicativoViewList.php', '');
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

        $this->view('SBAdmin/Manager/AplicativoView', $data);
    }

    public function show($id = '')
    {
        /**
         * Modos atendidos (TransMode): Insert e Update
         */
        $fields = CasAppModel::FIELDS;

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
                $this->obCasAppModel->$field = $this->dataEntry[$field];
            }

            // insert //
            if (isset($_POST['btnConfirmInsert'])) {
                if ($this->obCasAppModel->createRegister()) {
                    array_push($this->messages, $this->message->getMessage(0, 'Message', 'Registro inserido com sucesso!'));
                    $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[0];
                    $this->formDesign['FormDisable'] = true;
                    $id = $this->obCasAppModel->CasAppCod;
                } else {
                    array_push($this->messages, $this->message->getMessage(1, 'Message', 'Erro ao inserir o registro!'));
                }
            }

            // update //
            if (isset($_POST['btnConfirmUpdate'])) {
                if ($this->obCasAppModel->updateRegister()) {
                    array_push($this->messages, $this->message->getMessage(0, 'Message', 'Registro atualizado com sucesso!'));
                    $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[0];
                    $this->formDesign['FormDisable'] = true;
                    $id = $this->obCasAppModel->CasAppCod;
                } else {
                    array_push($this->messages, $this->message->getMessage(1, 'Message', 'Erro ao atualizar o registro!'));
                }
            }
        }

        if (!empty($id)) {
            $this->obCasAppModel->setSelectedFields($fields);
            $this->obCasAppModel->CasAppCod = $id;
            if ($this->obCasAppModel->readRegister()) {
                $this->dataEntry = $this->obCasAppModel->getRecords()[0];
            }
        }

        /**
         * Format Data
         */
        $this->formatDataOnForm();

        /**
         * Form Design
         */
        $this->formDesignOnForm(1, 'AplicativoViewForm.php', $id);

        /**
         * Show Screen
         */
        $data = array(
            'FormDesign' => $this->formDesign,
            'FormData' => FormData::secFields($fields, $this->dataEntry),
        );

        $this->view('SBAdmin/Manager/AplicativoView', $data);
    }

    public function remove($id = '')
    {
        $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[3];
        $this->formDesign['FormDisable'] = true;

        $fields = array_merge(CasAppModel::FIELDS, CasAppModel::FIELDS_AUDIT);

        if (!empty($id)) {
            if (isset($_POST['btnConfirmDelete'])) {
                $this->obCasAppModel->CasAppCod = $id;
                if (! $this->obCasAppModel->isEmptyAllReferencedTables()) {
                    array_push($this->messages, $this->message->getMessage(2, 'Message', 'Existem dados relacionados a este registro!'));
                }
                if ($this->obCasAppModel->deleteRegister()) {
                    array_push($this->messages, $this->message->getMessage(0, 'Message', 'Registro excluído com sucesso!'));
                    header('Location: /Manager/Aplicativo');
                    exit();
                } else {
                    array_push($this->messages, $this->message->getMessage(1, 'Message', 'Erro ao excluir o registro!'));
                }
            } else {
                $this->obCasAppModel->CasAppCod = $id;
                if ($this->obCasAppModel->readRegister()) {
                    $this->dataEntry = $this->obCasAppModel->getRecords()[0];
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
        $this->formDesignOnForm(1, 'AplicativoViewForm.php', $id);
        $this->formDesign['Tabs']['Items'][1]['Link'] = str_replace('/Show/', '/Remove/', $this->formDesign['Tabs']['Items'][1]['Link']);

        /**
         * Show Screen
         */
        $data = array(
            'FormDesign' => $this->formDesign,
            'FormData' => FormData::secFields($fields, $this->dataEntry),
        );

        $this->view('SBAdmin/Manager/AplicativoView', $data);
    }

    private function formatDataOnForm()
    {
        $this->dataEntry['CasAppBlq'] = FormatData::transformSelectionSN($this->dataEntry['CasAppBlq'], false);
        $this->dataEntry['CasAppTst'] = FormatData::transformSelectionSN($this->dataEntry['CasAppTst'], false);
    }

    private function formDesignOnForm($current, $file, $id)
    {
        $this->formDesign['Tabs']['Current'] = $current;
        $this->formDesign['Tabs']['LoadFile'] = "App/Views/SBAdmin/Manager/" . $file;
        $this->formDesign['Tabs']['Items'][0]['Link'] = '/Manager/Aplicativo';
        $this->formDesign['Tabs']['Items'][1]['Link'] = str_replace('{id}', $id, '/Manager/Aplicativo/Show/{id}');
        $this->formDesign['Fields'] = CasAppModel::FIELDS_MD;
        $this->formDesign['TransLinkRemove'] = str_replace('{id}', $id, '/Manager/Aplicativo/Remove/{id}');

        if ($this->messages) {
            $this->formDesign['Message'] = $this->messages[0];
        }
    }
}
