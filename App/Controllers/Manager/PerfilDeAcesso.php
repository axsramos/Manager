<?php

namespace App\Controllers\Manager;

use App\Core\Controller;
use App\Shared\MessageDictionary;
use App\Models\CAS\CasPfiModel;
use App\Class\Pattern\FormDesign;
use App\Class\Pattern\FormData;
use App\Class\Pattern\FormatData;
use App\Models\CAS\CasRpsModel;
use App\Models\CAS\CasPfuModel;
use App\Models\CAS\CasRpuModel;
use App\Models\CAS\CasAfuModel;
use App\Models\CAS\CasApfModel;
use App\Core\AuthSession;

class PerfilDeAcesso extends Controller
{
    private $messages = array();
    private $formDesign;
    private $obCasPfiModel;
    private $dataEntry;

    public function __construct()
    {
        $this->validateAccess('PerfilDeAcesso');
        // $this->setProgramParameters('PerfilDeAcesso', '');

        $this->message = new MessageDictionary();

        $this->obCasPfiModel = new CasPfiModel();

        $this->formDesign = FormDesign::withTabs('Controle de Acesso', 'Perfil de Acesso', 'Perfil > De Acesso', $this->getUserMenu(), $this->getSideMenu());

        // Usuários //
        $this->formDesign['Tabs']['Items'][2] = FormDesign::tabsModel('Single', '/Manager/PerfilDeAcesso/Usuario')[0];
        $this->formDesign['Tabs']['Items'][2]['Name'] = 'Usuário';
    }

    public function index()
    {
        $records = array();

        $fields = ['CasPfiDsc', 'CasPfiBlq', 'CasRpsCod', 'CasPfiCod']; // CasPfiModel::FIELDS;
        $hidden = ['CasRpsCod', 'CasPfiCod'];

        $this->obCasPfiModel->setSelectedFields($fields);
        $this->obCasPfiModel->CasRpsCod = AuthSession::get()['RPS_ID'];

        if ($this->obCasPfiModel->readAllLines(['CasRpsCod'])) {
            $records = $this->obCasPfiModel->getRecords();
        }

        /**
         * Format Data
         */
        foreach ($records as $key => $value) {
            if (isset($records[$key]['CasPfiBlq'])) {
                $records[$key]['CasPfiBlq'] = FormatData::transformSelectionSN($value['CasPfiBlq']);
            }
            if (isset($records[$key]['CasPfiBlqDtt'])) {
                $records[$key]['CasPfiBlqDtt'] = FormatData::transformData('OnlyDate', $value['CasPfiBlqDtt']);
            }
            if (isset($records[$key]['CasPfiAudIns'])) {
                $records[$key]['CasPfiAudIns'] = FormatData::transformData('OnlyDate', $value['CasPfiAudIns']);
            }
            if (isset($records[$key]['CasPfiAudUpd'])) {
                $records[$key]['CasPfiAudUpd'] = FormatData::transformData('OnlyDate', $value['CasPfiAudUpd']);
            }
            if (isset($records[$key]['CasPfiAudDlt'])) {
                $records[$key]['CasPfiAudDlt'] = FormatData::transformData('OnlyDate', $value['CasPfiAudDlt']);
            }
            // Add link to form //
            $linkCasPfi = '';
            foreach (CasPfiModel::FIELDS_PK as $pkvalue) {
                // [CasRpsCod] gets from session //
                if ($pkvalue == 'CasRpsCod') {
                    continue;
                }
                $linkCasPfi .= '/' . $records[$key][$pkvalue];
            }
            $records[$key]['Action']['Show'] = $linkCasPfi;
        }

        /**
         * Form Design
         */
        $fields_md = array();
        foreach ($fields as $key => $field) {
            if (in_array($field, CasPfiModel::FIELDS)) {
                $fields_md[$field] = CasPfiModel::FIELDS_MD[$field];
            } else {
                // Foreign Tables //
                foreach (CasPfiModel::FIELDS_FOREIGN as $tableForeign => $valueFieldsForeign) {
                    if (in_array($field, $valueFieldsForeign['FIELDS'])) {
                        $fields_md[$field] = CasPfiModel::FIELDS_MD_FOREIGN[$tableForeign]['FIELDS_MD'][$field];
                    }
                }
            }
        }

        $this->formDesignOnForm('dsp', 0, 'PerfilDeAcessoViewList.php', []);
        $this->formDesign['Styles']['CSSFiles'] = ['dataTables'];
        $this->formDesign['Scripts']['Body'] = ['dataTables'];
        $this->formDesign['Fields'] = $fields_md;
        $this->formDesign['Hidden'] = $hidden;
        $this->formDesign['Tabs']['Items'][2]['IsDisabled'] = true; // disabled when index page //

        /**
         * Show Screen
         */
        $data = array(
            'FormDesign' => $this->formDesign,
            'FormData' => $records
        );

        $this->view('SBAdmin/Manager/PerfilDeAcessoView', $data);
    }

    public function show($pfi = '')
    {
        $rps = AuthSession::get()['RPS_ID'];
        $rps_dsc = AuthSession::get()['RPS_DSC'];

        /**
         * Modos atendidos (TransMode): Insert e Update
         */
        $fields = CasPfiModel::FIELDS;
        array_push($fields, 'CasRpsDsc');

        $this->dataEntry = $this->getDataInput($fields);
        $this->dataEntry = FormData::secFields($fields, $this->dataEntry);

        if (empty($pfi)) {
            // insert //
            $this->dataEntry[CasPfiModel::FIELDS_PK[0]] = $rps;
            $this->dataEntry['CasRpsDsc'] = $rps_dsc;
            $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[1];
        } else {
            // update //
            $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[2];
        }

        if (isset($_POST['btnConfirmInsert']) || isset($_POST['btnConfirmUpdate'])) {
            foreach ($fields as $field) {
                $this->dataEntry[$field];
                $this->obCasPfiModel->$field = $this->dataEntry[$field];
            }

            // insert //
            if (isset($_POST['btnConfirmInsert'])) {
                if ($this->obCasPfiModel->createRegister()) {
                    array_push($this->messages, $this->message->getMessage(0, 'Message', 'Registro inserido com sucesso!'));
                    $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[0];
                    $this->formDesign['FormDisable'] = true;
                    $pfi = $this->obCasPfiModel->CasPfiCod;
                } else {
                    array_push($this->messages, $this->message->getMessage(1, 'Message', 'Erro ao inserir o registro!'));
                }
            }

            // update //
            if (isset($_POST['btnConfirmUpdate'])) {
                $this->obCasPfiModel->CasRpsCod = $this->dataEntry['CasRpsCod'];
                $this->obCasPfiModel->CasPfiCod = $this->dataEntry['CasPfiCod'];
                if ($this->obCasPfiModel->updateRegister()) {
                    array_push($this->messages, $this->message->getMessage(0, 'Message', 'Registro atualizado com sucesso!'));
                    $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[0];
                    $this->formDesign['FormDisable'] = true;
                    $pfi = $this->obCasPfiModel->CasPfiCod;
                } else {
                    array_push($this->messages, $this->message->getMessage(1, 'Message', 'Erro ao atualizar o registro!'));
                }
            }
        }

        if (!empty($pfi)) {
            $this->obCasPfiModel->CasRpsCod = $rps;
            $this->obCasPfiModel->CasPfiCod = $pfi;

            $this->obCasPfiModel->setSelectedFields($fields);
            
            if ($this->obCasPfiModel->readRegisterJoin(['CasRps','CasPfiCod'])) {
                $this->dataEntry = $this->obCasPfiModel->getRecords()[0];
            }
        }

        /**
         * Format Data
         */
        $this->formatDataOnForm();

        /**
         * Form Design
         */
        $transMode = (empty($pfi) ? 'ins' : 'upd');
        $this->formDesignOnForm($transMode, 1, 'PerfilDeAcessoViewForm.php', [$pfi]);
        if ($transMode == 'ins') {
            $this->formDesign['Tabs']['Items'][2]['IsDisabled'] = true; // disabled when insert //
        }

        /**
         * Show Screen
         */
        $data = array(
            'FormDesign' => $this->formDesign,
            'FormData' => FormData::secFields($fields, $this->dataEntry),
        );

        $this->view('SBAdmin/Manager/PerfilDeAcessoView', $data);
    }

    public function usuario($pfi = '')
    {
        $rps = AuthSession::get()['RPS_ID'];

        /**
         * Modos atendidos (TransMode): Insert e Update
         */
        $fields = array_intersect(CasPfiModel::FIELDS, ['CasPfiCod', 'CasPfiDsc']);

        $this->dataEntry = array();

        if (!empty($pfi)) {
            $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[2];

            $this->obCasPfiModel->setSelectedFields($fields);
            $this->obCasPfiModel->CasRpsCod = $rps;
            $this->obCasPfiModel->CasPfiCod = $pfi;

            if ($this->obCasPfiModel->readRegister()) {
                $this->dataEntry = $this->obCasPfiModel->getRecords()[0];

                // load all users //
                $this->loadUsers($rps, $pfi);
            } else {
                $pfi = '';
            }
        }

        if (empty($pfi)) {
            $this->pageNotFound();
            exit;
        }

        if (isset($_POST['inputGroupSelectCasUsrCod']) && $_POST['inputGroupSelectCasUsrCod'] == 'Selecione uma opção') {
            array_push($this->messages, $this->message->getMessage(2, 'Message', 'Selecione uma opção válida.'));
        } else {
            if (isset($_POST['btnActionAdd']) || isset($_POST['btnActionRemove']) || isset($_POST['btnActionBlolock'])) {
                // insert //
                if (isset($_POST['btnActionAdd'])) {
                    $obCasPfuModel = new CasPfuModel();
                    $obCasPfuModel->setSelectedFields(['CasRpsCod', 'CasPfiCod', 'CasUsrCod']);
                    $obCasPfuModel->CasRpsCod = $rps;
                    $obCasPfuModel->CasPfiCod = $pfi;
                    $obCasPfuModel->CasUsrCod = $_POST['inputGroupSelectCasUsrCod'];
                    if ($obCasPfuModel->readRegister()) {
                        array_push($this->messages, $this->message->getMessage(3, 'Message', 'O item selecionado já está cadastrado.'));
                    } else {
                        if ($obCasPfuModel->createRegister()) {
                            array_push($this->messages, $this->message->getMessage(0, 'Message', 'Registro inserido com sucesso!'));
                        } else {
                            array_push($this->messages, $this->message->getMessage(1, 'Message', 'Erro ao inserir o registro!'));
                        }
                    }
                }

                // update //
                if (isset($_POST['btnActionBlolock'])) {
                    $obCasRpuModel = new CasRpuModel();
                    $obCasRpuModel->setSelectedFields(['CasRpsCod', 'CasUsrCod', 'CasRpuBlq']);
                    $obCasRpuModel->CasRpsCod = $rps;
                    $obCasRpuModel->CasUsrCod = $_POST['inputGroupSelectCasUsrCod'];
                    if ($obCasRpuModel->readRegister()) {
                        $obCasRpuModel->CasRpuBlq = (substr($obCasRpuModel->CasRpuBlq, 0, 1) == 'S' ? 'N' : 'S');
                        if ($obCasRpuModel->updateRegister()) {
                            array_push($this->messages, $this->message->getMessage(0, 'Message', 'Registro atualizado com sucesso!'));
                        } else {
                            array_push($this->messages, $this->message->getMessage(1, 'Message', 'Erro ao atualizar o registro!'));
                        }
                    } else {
                        array_push($this->messages, $this->message->getMessage(3, 'Message', 'O item selecionado não está cadastrado.'));
                    }
                }

                // delete //
                if (isset($_POST['btnActionRemove'])) {
                    $obCasPfuModel = new CasPfuModel();
                    $obCasPfuModel->setSelectedFields(['CasRpsCod', 'CasUsrCod', 'CasPfiCod']);
                    $obCasPfuModel->CasRpsCod = $rps;
                    $obCasPfuModel->CasUsrCod = $_POST['inputGroupSelectCasUsrCod'];
                    $obCasPfuModel->CasPfiCod = $pfi;
                    if ($obCasPfuModel->readRegister()) {
                        // remove CasAfu //
                        $obCasAfuModel = new CasAfuModel();
                        $obCasAfuModel->CasRpsCod = $rps;
                        $obCasAfuModel->CasPfiCod = $pfi;
                        $obCasAfuModel->CasUsrCod = $_POST['inputGroupSelectCasUsrCod'];
                        $obCasAfuModel->CasPrgCod = '';
                        $obCasAfuModel->CasFunCod = '';
                        $obCasAfuModel->deleteAllUserAuthorized();

                        // remove CasApf //
                        $obCasApfModel = new CasApfModel();
                        $obCasApfModel->CasRpsCod = $rps;
                        $obCasApfModel->CasPfiCod = $pfi;
                        $obCasApfModel->CasUsrCod = $_POST['inputGroupSelectCasUsrCod'];
                        $obCasApfModel->CasPrgCod = '';
                        $obCasApfModel->deleteAllUserAuthorized();

                        if ($obCasPfuModel->deleteRegister()) {
                            array_push($this->messages, $this->message->getMessage(0, 'Message', 'Registro removido com sucesso!'));
                        } else {
                            array_push($this->messages, $this->message->getMessage(1, 'Message', 'Erro ao remover o registro!'));
                        }
                    } else {
                        array_push($this->messages, $this->message->getMessage(3, 'Message', 'O item selecionado não está cadastrado.'));
                    }
                }
            }
        }


        if (!empty($pfi)) {
            $this->loadUsers($rps, $pfi);
        }

        /**
         * Format Data
         */
        $this->formatDataOnForm();

        /**
         * Form Design
         */
        $this->formDesignOnForm('upd', 2, 'PerfilDeAcessoViewUsers.php', [$pfi]);

        /**
         * Show Screen
         */
        $data = array(
            'FormDesign' => $this->formDesign,
            'FormData' => $this->dataEntry,
        );

        $this->view('SBAdmin/Manager/PerfilDeAcessoView', $data);
    }

    private function loadUsers($rps, $pfi)
    {
        // UsersInProfile //
        $obCasRpuModel = new CasRpuModel();
        $obCasRpuModel->setSelectedFields(['CasRpsCod', 'CasPfiCod', 'CasUsrCod', 'CasRpuDsc', 'CasUsrDsc', 'CasUsrBlq', 'CasTusCod', 'CasTusDsc', 'CasRpuBlq']);
        $obCasRpuModel->CasRpsCod = trim($rps);

        if ($obCasRpuModel->readAllLinesJoin(['CasRps'])) {
            $this->dataEntry['UsersInProfile'] = array();
            foreach ($obCasRpuModel->getRecords() as $usuario) {
                $obCasPfuModel = new CasPfuModel();
                $obCasPfuModel->setSelectedFields(['CasRpsCod', 'CasPfiCod', 'CasUsrCod']);
                $obCasPfuModel->CasRpsCod = $rps;
                $obCasPfuModel->CasPfiCod = $pfi;
                $obCasPfuModel->CasUsrCod = $usuario['CasUsrCod'];
                if ($obCasPfuModel->readRegister()) {
                    array_push($this->dataEntry['UsersInProfile'], $usuario);
                }
            }
        }

        // AllUsers //
        $obCasRpuModel = new CasRpuModel();
        $obCasRpuModel->setSelectedFields(['CasRpsCod', 'CasPfiCod', 'CasUsrCod', 'CasRpuDsc', 'CasUsrDsc', 'CasUsrBlq', 'CasRpuBlq']);
        $obCasRpuModel->CasRpsCod = $rps;
        $obCasRpuModel->CasPfiCod = $pfi;
        if ($obCasRpuModel->readAllLinesJoin(['CasRps', 'CasPfi'])) {
            $this->dataEntry['AllUsers'] = $obCasRpuModel->getRecords();
        }
    }

    public function remove($id = '')
    {
        $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[3];
        $this->formDesign['FormDisable'] = true;

        $fields = array_merge(CasPfiModel::FIELDS, CasPfiModel::FIELDS_AUDIT);

        if (!empty($id)) {
            $this->obCasPfiModel->CasRpsCod = AuthSession::get()['RPS_ID'];
            $this->obCasPfiModel->CasPfiCod = $id;
            if (! $this->obCasPfiModel->isEmptyAllReferencedTables()) {
                array_push($this->messages, $this->message->getMessage(2, 'Message', 'Existem dados relacionados a este registro!'));
            }
            if (isset($_POST['btnConfirmDelete'])) {
                if ($this->obCasPfiModel->deleteRegister()) {
                    array_push($this->messages, $this->message->getMessage(0, 'Message', 'Registro excluído com sucesso!'));
                    header('Location: /Manager/PerfilDeAcesso');
                    exit();
                } else {
                    array_push($this->messages, $this->message->getMessage(1, 'Message', 'Erro ao excluir o registro!'));
                }
            } else {
                if ($this->obCasPfiModel->readRegisterJoin(['CasRps','CasPfiCod'])) {
                    $this->dataEntry = $this->obCasPfiModel->getRecords()[0];
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
        $this->formDesignOnForm('dlt', 1, 'PerfilDeAcessoViewForm.php', [$id]);

        /**
         * Show Screen
         */
        $data = array(
            'FormDesign' => $this->formDesign,
            'FormData' => FormData::secFields($fields, $this->dataEntry),
        );

        $this->view('SBAdmin/Manager/PerfilDeAcessoView', $data);
    }

    private function formatDataOnForm()
    {
        if (isset($this->dataEntry['UsersInProfile'])) {
            foreach ($this->dataEntry['UsersInProfile'] as $key => $value) {
                $this->dataEntry['UsersInProfile'][$key]['CasRpuBlq'] = FormatData::transformSelectionSN($value['CasRpuBlq'], true);
            }
        }
    }

    private function formDesignOnForm($transMode, $current, $file, $keys)
    {
        $urlKeys = implode('/', $keys);

        $this->formDesign['Tabs']['Current'] = $current;
        $this->formDesign['Tabs']['LoadFile'] = "App/Views/SBAdmin/Manager/" . $file;
        $this->formDesign['Tabs']['Items'][0]['Link'] = '/Manager/PerfilDeAcesso';

        $this->formDesign['Fields'] = array_merge(CasPfiModel::FIELDS_MD, array('CasRpsDsc' => CasRpsModel::FIELDS_MD['CasRpsDsc']));
        $this->formDesign['TransLinkRemove'] = "/Manager/PerfilDeAcesso/Remove/{$urlKeys}";

        switch ($transMode) {
            case 'upd':
                $this->formDesign['Tabs']['Items'][1]['Link'] = "/Manager/PerfilDeAcesso/Show/{$urlKeys}";
                $this->formDesign['Tabs']['Items'][2]['Link'] = "/Manager/PerfilDeAcesso/Usuario/{$urlKeys}";
                break;

            case 'upd':
                $this->formDesign['Tabs']['Items'][2]['Link'] = "/Manager/PerfilDeAcesso/Usuario/{$urlKeys}";
                break;

            case 'dlt':
                $this->formDesign['Tabs']['Items'][1]['Link'] = $this->formDesign['TransLinkRemove'];
                break;

            default:
                $this->formDesign['Tabs']['Items'][1]['Link'] = "/Manager/PerfilDeAcesso/Show";
                break;
        }

        if ($this->messages) {
            $this->formDesign['Message'] = $this->messages[0];
        }
    }
}
