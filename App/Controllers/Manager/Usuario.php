<?php

namespace App\Controllers\Manager;

use App\Core\Controller;
use App\Core\Config;
use App\Shared\MessageDictionary;
use App\Models\CAS\CasUsrModel;
use App\Class\Pattern\FormDesign;
use App\Class\Pattern\FormData;
use App\Class\Pattern\FormatData;
use App\Models\CAS\CasRpsModel;
use App\Models\CAS\CasTusModel;
use App\Models\CAS\CasRpuModel;
use App\Core\AuthSession;

class Usuario extends Controller
{
    private $messages = array();
    private $formDesign;
    private $obCasUsrModel;
    private $dataEntry;

    public function __construct()
    {
        $this->validateAccess('Usuario');
        // $this->setProgramParameters('Usuario', '');

        // validação para contas administrativas portal siti - ['Aplicativo','Repositorio','Token','TipoDeUsuario','Usuario'] - [start] //
        if (! in_array(strtoupper(AuthSession::get()['PROFILE']), array_merge(Config::getTypeUsersAdminPortal(), Config::getTypeUsersSupportPortal()))) {
            header('Location: /Home/Denied'); // access denied //
            exit();
        }
        // validação para contas administrativas portal siti - [end] //

        $this->message = new MessageDictionary();

        $this->obCasUsrModel = new CasUsrModel();

        $this->formDesign = FormDesign::withTabs('Portal SITI', 'Usuário', 'Manager > Usuário', $this->getUserMenu(), $this->getSideMenu());

        // // Usuários //
        $this->formDesign['Tabs']['Items'][2] = FormDesign::tabsModel('Single', '/Manager/Usuario/Repositorio')[0];
        $this->formDesign['Tabs']['Items'][2]['Name'] = 'Repositório';
    }

    public function index()
    {
        $records = array();

        $fields = ['CasUsrCod', 'CasUsrDsc', 'CasUsrNck', 'CasUsrNme', 'CasUsrSnm', 'CasUsrLgn', 'CasUsrDmn', 'CasUsrBlq']; // CasUsrModel::FIELDS;
        $hidden = [];

        if ($this->obCasUsrModel->readAllLines()) {
            $records = $this->obCasUsrModel->getRecords();
        }

        /**
         * Format Data
         */
        foreach ($records as $key => $value) {
            if (isset($records[$key]['CasUsrBlq'])) {
                $records[$key]['CasUsrBlq'] = FormatData::transformSelectionSN($value['CasUsrBlq']);
            }
            if (isset($records[$key]['CasUsrBlqDtt'])) {
                $records[$key]['CasUsrBlqDtt'] = FormatData::transformData('OnlyDate', $value['CasUsrBlqDtt']);
            }
            if (isset($records[$key]['CasUsrAudIns'])) {
                $records[$key]['CasUsrAudIns'] = FormatData::transformData('OnlyDate', $value['CasUsrAudIns']);
            }
            if (isset($records[$key]['CasUsrAudUpd'])) {
                $records[$key]['CasUsrAudUpd'] = FormatData::transformData('OnlyDate', $value['CasUsrAudUpd']);
            }
            if (isset($records[$key]['CasUsrAudDlt'])) {
                $records[$key]['CasUsrAudDlt'] = FormatData::transformData('OnlyDate', $value['CasUsrAudDlt']);
            }
        }

        /**
         * Form Design
         */
        $fields_md = array();
        foreach ($fields as $key => $field) {
            $fields_md[$field] = CasUsrModel::FIELDS_MD[$field];
        }

        $this->formDesignOnForm('dsp', 0, 'UsuarioViewList.php', '');
        $this->formDesign['Fields'] = $fields_md;
        $this->formDesign['Hidden'] = $hidden;

        /**
         * Show Screen
         */
        $data = array(
            'FormDesign' => $this->formDesign,
            'FormData' => $records
        );

        $this->view('SBAdmin/Manager/UsuarioView', $data);
    }

    public function show($id = '')
    {
        /**
         * Modos atendidos (TransMode): Insert e Update
         */
        $fields = array();

        if (empty($id)) {
            $fields = CasUsrModel::FIELDS;
        } else {
            foreach (CasUsrModel::FIELDS as $key => $fieldItem) {
                // nao atualizar senha no update //
                if ($fieldItem != 'CasUsrPwd') {
                    $fields[$key] = $fieldItem;
                }
            }
        }

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
                $this->obCasUsrModel->$field = $this->dataEntry[$field];
            }

            // insert //
            if (isset($_POST['btnConfirmInsert'])) {
                // gerar senha inicial //
                if (empty($this->obCasUsrModel->CasUsrPwd)) {
                    $usr_id = ($this->dataEntry['CasUsrLgn'] . $this->dataEntry['CasUsrDmn']);
                    $this->obCasUsrModel->CasUsrPwd = md5($usr_id);
                }
                if ($this->obCasUsrModel->createRegister()) {
                    array_push($this->messages, $this->message->getMessage(0, 'Message', 'Registro inserido com sucesso!'));
                    $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[0];
                    $this->formDesign['FormDisable'] = true;
                    $id = $this->obCasUsrModel->CasUsrCod;
                } else {
                    array_push($this->messages, $this->message->getMessage(1, 'Message', 'Erro ao inserir o registro!'));
                }
            }

            // update //
            if (isset($_POST['btnConfirmUpdate'])) {
                $this->obCasUsrModel->setSelectedFields($fields);
                if ($this->obCasUsrModel->updateRegister()) {
                    array_push($this->messages, $this->message->getMessage(0, 'Message', 'Registro atualizado com sucesso!'));
                    $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[0];
                    $this->formDesign['FormDisable'] = true;
                    $id = $this->obCasUsrModel->CasUsrCod;
                } else {
                    array_push($this->messages, $this->message->getMessage(1, 'Message', 'Erro ao atualizar o registro!'));
                }
            }
        }

        if (!empty($id)) {
            $this->obCasUsrModel->CasUsrCod = $id;
            if ($this->obCasUsrModel->readRegister()) {
                $this->dataEntry = $this->obCasUsrModel->getRecords()[0];
            }
        }

        /**
         * Format Data
         */
        $this->formatDataOnForm();

        /**
         * Form Design
         */
        $transMode = (empty($id) ? 'ins' : 'upd');
        $this->formDesignOnForm($transMode, 1, 'UsuarioViewForm.php', $id);

        /**
         * Show Screen
         */
        $data = array(
            'FormDesign' => $this->formDesign,
            'FormData' => FormData::secFields($fields, $this->dataEntry),
        );

        $this->view('SBAdmin/Manager/UsuarioView', $data);
    }

    public function repository($usr = '')
    {
        $records = array();

        $fields = ['CasRpsDsc', 'CasRpsGrp', 'CasRpsBlq', 'CasRpsBlqDtt', 'CasRpuBlq', 'CasRpuBlqDtt', 'CasTusDsc', 'CasTusLnk', 'CasRpsCod', 'CasUsrCod', 'CasTusCod'];
        $hidden = ['CasRpsCod', 'CasUsrCod', 'CasTusCod'];

        $obCasRpuModel = new CasRpuModel();
        $obCasRpuModel->setSelectedFields($fields);
        $obCasRpuModel->CasUsrCod = $usr;
        if ($obCasRpuModel->getAllRepositories()) {
            $records = $obCasRpuModel->getRecords();
        }

        /**
         * Format Data
         */
        foreach ($records as $key => $value) {
            if (isset($records[$key]['CasRpsBlq'])) {
                $records[$key]['CasRpsBlq'] = FormatData::transformSelectionSN($value['CasRpsBlq']);
            }
            if (isset($records[$key]['CasRpsBlqDtt'])) {
                $records[$key]['CasRpsBlqDtt'] = FormatData::transformData('OnlyDate', $value['CasRpsBlqDtt']);
            }
            if (isset($records[$key]['CasRpuBlq'])) {
                $records[$key]['CasRpuBlq'] = FormatData::transformSelectionSN($value['CasRpuBlq']);
            }
            if (isset($records[$key]['CasRpuBlqDtt'])) {
                $records[$key]['CasRpuBlqDtt'] = FormatData::transformData('OnlyDate', $value['CasRpuBlqDtt']);
            }
            if (isset($records[$key]['CasRpuAudIns'])) {
                $records[$key]['CasRpuAudIns'] = FormatData::transformData('OnlyDate', $value['CasRpuAudIns']);
            }
            if (isset($records[$key]['CasRpuAudUpd'])) {
                $records[$key]['CasRpuAudUpd'] = FormatData::transformData('OnlyDate', $value['CasRpuAudUpd']);
            }
            if (isset($records[$key]['CasRpuAudDlt'])) {
                $records[$key]['CasRpuAudDlt'] = FormatData::transformData('OnlyDate', $value['CasRpuAudDlt']);
            }
        }

        /**
         * Form Design
         */
        $fields_md = array();
        foreach ($fields as $key => $field) {
            if (array_intersect([$field], CasRpsModel::FIELDS)) {
                $fields_md[$field] = CasRpsModel::FIELDS_MD[$field];
            } elseif (array_intersect([$field], CasRpuModel::FIELDS)) {
                $fields_md[$field] = CasRpuModel::FIELDS_MD[$field];
            } elseif (array_intersect([$field], CasUsrModel::FIELDS)) {
                $fields_md[$field] = CasUsrModel::FIELDS_MD[$field];
            } elseif (array_intersect([$field], CasTusModel::FIELDS)) {
                $fields_md[$field] = CasTusModel::FIELDS_MD[$field];
            }
        }

        $this->formDesignOnForm('dsp', 2, 'UsuarioViewListRepository.php', $usr);
        $this->formDesign['Fields'] = $fields_md;
        $this->formDesign['Hidden'] = $hidden;

        /**
         * Show Screen
         */
        $data = array(
            'FormDesign' => $this->formDesign,
            'FormData' => $records,
        );

        $this->view('SBAdmin/Manager/UsuarioView', $data);
    }

    public function remove($id = '')
    {
        $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[3];
        $this->formDesign['FormDisable'] = true;

        $fields = array_merge(CasUsrModel::FIELDS, CasUsrModel::FIELDS_AUDIT);

        if (!empty($id)) {
            $this->obCasUsrModel->CasUsrCod = $id;
            if (! $this->obCasUsrModel->isEmptyAllReferencedTables()) {
                array_push($this->messages, $this->message->getMessage(2, 'Message', 'Existem dados relacionados a este registro!'));
            }
            if (isset($_POST['btnConfirmDelete'])) {
                if ($this->obCasUsrModel->deleteRegister()) {
                    array_push($this->messages, $this->message->getMessage(0, 'Message', 'Registro excluído com sucesso!'));
                    header('Location: /Manager/Usuario');
                    exit();
                } else {
                    array_push($this->messages, $this->message->getMessage(1, 'Message', 'Erro ao excluir o registro!'));
                    if ($this->obCasUsrModel->readRegister()) {
                        $this->dataEntry = $this->obCasUsrModel->getRecords()[0];
                    }
                }
                // if ($this->obCasUsrModel->checkReferencialKey()) {
                // } else {
                //     array_push($this->messages, $this->message->getMessage(1, 'Message', 'Erro! Outros dados se relacionam com este registro.'));
                // }
            } else {
                if ($this->obCasUsrModel->readRegister()) {
                    $this->dataEntry = $this->obCasUsrModel->getRecords()[0];
                }

                // $checkResult = $this->obCasUsrModel->checkReferencialKey();
                // if (!$checkResult) {
                //     array_push($this->messages, $this->message->getMessage(1, 'Message', 'Atenção! Outros dados se relacionam com este registro.'));
                // }
            }
        }

        /**
         * Format Data
         */
        $this->formatDataOnForm();

        /**
         * Form Design
         */
        $this->formDesignOnForm('dlt', 1, 'UsuarioViewForm.php', $id);
        $this->formDesign['Tabs']['Items'][1]['Link'] = str_replace('/Show/', '/Remove/', $this->formDesign['Tabs']['Items'][1]['Link']);

        /**
         * Show Screen
         */
        $data = array(
            'FormDesign' => $this->formDesign,
            'FormData' => FormData::secFields($fields, $this->dataEntry),
        );

        $this->view('SBAdmin/Manager/UsuarioView', $data);
    }

    private function formatDataOnForm()
    {
        $this->dataEntry['CasUsrBlq'] = FormatData::transformSelectionSN($this->dataEntry['CasUsrBlq'], false);
    }

    private function formDesignOnForm($transMode, $current, $file, $id)
    {
        $this->formDesign['Tabs']['Current'] = $current;
        $this->formDesign['Tabs']['LoadFile'] = "App/Views/SBAdmin/Manager/" . $file;
        $this->formDesign['Tabs']['Items'][0]['Link'] = '/Manager/Usuario';
        $this->formDesign['Tabs']['Items'][1]['Link'] = "/Manager/Usuario/Show/{$id}";
        $this->formDesign['Tabs']['Items'][2]['Link'] = "/Manager/Usuario/Repository/{$id}";
        $this->formDesign['Fields'] = CasUsrModel::FIELDS_MD;
        $this->formDesign['TransLinkRemove'] = str_replace('{id}', $id, '/Manager/Usuario/Remove/{id}');

        if ($current == 2 || ($current == 1 && $transMode == 'upd')) {
            $this->formDesign['FormDisable'] = false; // enable form on insert mode //
        } else {
            $this->formDesign['Tabs']['Items'][2]['IsDisabled'] = ($current == 2 ? false : true); // enable repository tab only on show mode //
        }

        if ($current == 0 || $current == 2) {
            $this->formDesign['Styles']['CSSFiles'] = ['dataTables'];
            $this->formDesign['Scripts']['Body'] = ['dataTables'];
        }

        if ($this->messages) {
            $this->formDesign['Message'] = $this->messages[0];
        }
    }
}
