<?php

namespace App\Controllers\Manager;

use App\Core\Controller;
use App\Shared\MessageDictionary;
use App\Models\CAS\CasMdlModel;
use App\Class\Pattern\FormDesign;
use App\Class\Pattern\FormData;
use App\Class\Pattern\FormatData;
use App\Core\AuthSession;
use App\Models\CAS\CasPrgModel;
use App\Models\CAS\CasMprModel;

class Modulo extends Controller
{
    private $messages = array();
    private $formDesign;
    private $obCasMdlModel;
    private $dataEntry;

    public function __construct()
    {
        $this->validateAccess('Modulo');
        // $this->setProgramParameters('Modulo', '');

        $this->message = new MessageDictionary();

        $this->obCasMdlModel = new CasMdlModel();

        $this->formDesign = FormDesign::withTabs('Portal SITI', 'Módulo', 'Settings > Módulo', $this->getUserMenu(), $this->getSideMenu());

        // Programas //
        $this->formDesign['Tabs']['Items'][2] = FormDesign::tabsModel('Single', '/Manager/Modulo/Programas')[0];
        $this->formDesign['Tabs']['Items'][2]['Name'] = 'Programas';
    }

    public function index()
    {
        $records = array();

        $fields = ['CasMdlCod', 'CasMdlDsc', 'CasMdlBlq', 'CasRpsCod', 'CasRpsDsc']; // $fields = CasMdlModel::FIELDS;
        $hidden = ['CasRpsCod'];

        $this->obCasMdlModel->setSelectedFields($fields);
        $this->obCasMdlModel->CasRpsCod = AuthSession::get()['RPS_ID'];

        if ($this->obCasMdlModel->readAllLinesJoin(['CasRps'])) {
            $records = $this->obCasMdlModel->getRecords();
        }

        /**
         * Format Data
         */
        foreach ($records as $key => $value) {
            if (isset($records[$key]['CasMdlBlq'])) {
                $records[$key]['CasMdlBlq'] = FormatData::transformSelectionSN($value['CasMdlBlq']);
            }
            if (isset($records[$key]['CasMdlBlqDtt'])) {
                $records[$key]['CasMdlBlqDtt'] = FormatData::transformData('OnlyDate', $value['CasMdlBlqDtt']);
            }
            if (isset($records[$key]['CasMdlAudIns'])) {
                $records[$key]['CasMdlAudIns'] = FormatData::transformData('OnlyDate', $value['CasMdlAudIns']);
            }
            if (isset($records[$key]['CasMdlAudUpd'])) {
                $records[$key]['CasMdlAudUpd'] = FormatData::transformData('OnlyDate', $value['CasMdlAudUpd']);
            }
            if (isset($records[$key]['CasMdlAudDlt'])) {
                $records[$key]['CasMdlAudDlt'] = FormatData::transformData('OnlyDate', $value['CasMdlAudDlt']);
            }
            // Add link to form //
            $linkCasMdl = '';
            foreach (CasMdlModel::FIELDS_PK as $pkvalue) {
                // [CasRpsCod] gets from session //
                if ($pkvalue == 'CasRpsCod') {
                    continue;
                }
                $linkCasMdl .= '/' . $records[$key][$pkvalue];
            }
            $records[$key]['Action']['Show'] = $linkCasMdl;
        }

        /**
         * Form Design
         */
        $fields_md = array();
        foreach ($fields as $key => $field) {
            if (in_array($field, CasMdlModel::FIELDS)) {
                $fields_md[$field] = CasMdlModel::FIELDS_MD[$field];
            } else {
                // Foreign Tables //
                foreach (CasMdlModel::FIELDS_FOREIGN as $tableForeign => $valueFieldsForeign) {
                    if (in_array($field, $valueFieldsForeign['FIELDS'])) {
                        $fields_md[$field] = CasMdlModel::FIELDS_MD_FOREIGN[$tableForeign]['FIELDS_MD'][$field];
                    }
                }
            }
        }

        $this->formDesignOnForm('dsp', 0, 'ModuloViewList.php', []);
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

        $this->view('SBAdmin/Manager/ModuloView', $data);
    }

    public function show($id = '')
    {
        $rps = AuthSession::get()['RPS_ID'];

        /**
         * Modos atendidos (TransMode): Insert e Update
         */
        $fields = CasMdlModel::FIELDS;

        $this->dataEntry = $this->getDataInput($fields);
        $this->dataEntry = FormData::secFields($fields, $this->dataEntry);

        if (empty($id)) {
            // insert //
            $this->dataEntry[CasMdlModel::FIELDS_PK[0]] = $rps;
            $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[1];
        } else {
            // update //
            $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[2];
        }

        if (isset($_POST['btnConfirmInsert']) || isset($_POST['btnConfirmUpdate'])) {
            foreach ($fields as $field) {
                $this->dataEntry[$field];
                $this->obCasMdlModel->$field = $this->dataEntry[$field];
            }

            // insert //
            if (isset($_POST['btnConfirmInsert'])) {
                if ($this->obCasMdlModel->createRegister()) {
                    array_push($this->messages, $this->message->getMessage(0, 'Message', 'Registro inserido com sucesso!'));
                    $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[0];
                    $this->formDesign['FormDisable'] = true;
                    $id = $this->obCasMdlModel->CasMdlCod;
                } else {
                    array_push($this->messages, $this->message->getMessage(1, 'Message', 'Erro ao inserir o registro!'));
                }
            }

            // update //
            if (isset($_POST['btnConfirmUpdate'])) {
                $this->obCasMdlModel->CasRpsCod = $this->dataEntry['CasRpsCod'];
                $this->obCasMdlModel->CasMdlCod = $this->dataEntry['CasMdlCod'];
                if ($this->obCasMdlModel->updateRegister()) {
                    array_push($this->messages, $this->message->getMessage(0, 'Message', 'Registro atualizado com sucesso!'));
                    $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[0];
                    $this->formDesign['FormDisable'] = true;
                    $id = $this->obCasMdlModel->CasMdlCod;
                } else {
                    array_push($this->messages, $this->message->getMessage(1, 'Message', 'Erro ao atualizar o registro!'));
                }
            }
        }

        if (!empty($id)) {
            $this->obCasMdlModel->CasRpsCod = $rps;
            $this->obCasMdlModel->CasMdlCod = $id;
            if ($this->obCasMdlModel->readRegister()) {
                $this->dataEntry = $this->obCasMdlModel->getRecords()[0];
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
        $this->formDesignOnForm($transMode, 1, 'ModuloViewForm.php', [$id]);
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

        $this->view('SBAdmin/Manager/ModuloView', $data);
    }

    public function programa($mdl = '')
    {
        $rps = AuthSession::get()['RPS_ID'];

        /**
         * Modos atendidos (TransMode): Insert e Update
         */
        $fields = array_intersect(CasMdlModel::FIELDS, ['CasMdlCod', 'CasMdlDsc']);

        $this->dataEntry = array();

        if (!empty($mdl)) {
            $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[2];

            $this->obCasMdlModel->setSelectedFields($fields);
            $this->obCasMdlModel->CasRpsCod = $rps;
            $this->obCasMdlModel->CasMdlCod = $mdl;

            if ($this->obCasMdlModel->readRegister()) {
                $this->dataEntry = $this->obCasMdlModel->getRecords()[0];

                // load all programs //
                $this->loadProgramas($rps, $mdl);
            } else {
                $mdl = '';
            }
        }

        if (empty($mdl)) {
            $this->pageNotFound();
            exit;
        }

        if (isset($_POST['inputGroupSelectCasPrgCod']) && $_POST['inputGroupSelectCasPrgCod'] == 'Selecione uma opção') {
            array_push($this->messages, $this->message->getMessage(2, 'Message', 'Selecione uma opção válida.'));
        } else {
            if (isset($_POST['btnActionAdd']) || isset($_POST['btnActionRemove'])) {
                // insert //
                if (isset($_POST['btnActionAdd'])) {
                    $obCasMprModel = new CasMprModel();
                    $obCasMprModel->setSelectedFields(['CasRpsCod', 'CasMdlCod', 'CasPrgCod']);
                    $obCasMprModel->CasRpsCod = $rps;
                    $obCasMprModel->CasMdlCod = $mdl;
                    $obCasMprModel->CasPrgCod = $_POST['inputGroupSelectCasPrgCod'];
                    if ($obCasMprModel->readRegister()) {
                        array_push($this->messages, $this->message->getMessage(3, 'Message', 'O item selecionado já está cadastrado.'));
                    } else {
                        if ($obCasMprModel->createRegister()) {
                            array_push($this->messages, $this->message->getMessage(0, 'Message', 'Registro inserido com sucesso!'));
                        } else {
                            array_push($this->messages, $this->message->getMessage(1, 'Message', 'Erro ao inserir o registro!'));
                        }
                    }
                }

                // delete //
                if (isset($_POST['btnActionRemove'])) {
                    $obCasMprModel = new CasMprModel();
                    $obCasMprModel->setSelectedFields(['CasRpsCod', 'CasPrgCod', 'CasMdlCod']);
                    $obCasMprModel->CasRpsCod = $rps;
                    $obCasMprModel->CasPrgCod = $_POST['inputGroupSelectCasPrgCod'];
                    $obCasMprModel->CasMdlCod = $mdl;
                    if ($obCasMprModel->readRegister()) {
                        if ($obCasMprModel->deleteRegister()) {
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


        if (!empty($mdl)) {
            $this->loadProgramas($rps, $mdl);
        }

        /**
         * Format Data
         */
        $this->formatDataOnForm();

        /**
         * Form Design
         */
        $this->formDesignOnForm('upd', 2, 'ModuloViewProgram.php', [$mdl]);

        /**
         * Show Screen
         */
        $data = array(
            'FormDesign' => $this->formDesign,
            'FormData' => $this->dataEntry,
        );

        $this->view('SBAdmin/Manager/ProgramaView', $data);
    }

    private function loadProgramas($rps, $mdl)
    {
        // ProgramsInModule //
        $obCasMprModel = new CasMprModel();
        $obCasMprModel->setSelectedFields(['CasRpsCod', 'CasMdlCod', 'CasPrgCod', 'CasPrgDsc', 'CasPrgBlq']);
        $obCasMprModel->CasRpsCod = $rps;
        $obCasMprModel->CasMdlCod = $mdl;

        if ($obCasMprModel->readAllLinesJoin(['CasRps', 'CasMdl'])) {
            $this->dataEntry['ProgramsInModule'] = $obCasMprModel->getRecords();
        }

        // AllPrograms //
        $obCasPrgModel = new CasPrgModel();
        $obCasPrgModel->setSelectedFields(['CasRpsCod', 'CasMdlCod', 'CasPrgCod', 'CasPrgDsc', 'CasPrgBlq']);
        $obCasPrgModel->CasRpsCod = $rps;
        $obCasPrgModel->CasMdlCod = $mdl;
        if ($obCasPrgModel->readAllLinesJoin(['CasRps', 'CasMdl'])) {
            $allPrograms = $obCasPrgModel->getRecords();
            usort($allPrograms, function ($a, $b) {
                return strcmp($a['CasPrgDsc'], $b['CasPrgDsc']);
            });
            $this->dataEntry['AllPrograms'] = $allPrograms;
        }
    }

    public function remove($mdl = '')
    {
        $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[3];
        $this->formDesign['FormDisable'] = true;

        $fields = array_merge(CasMdlModel::FIELDS, CasMdlModel::FIELDS_AUDIT);

        if (!empty($mdl)) {
            $this->obCasMdlModel->CasRpsCod = AuthSession::get()['RPS_ID'];
            $this->obCasMdlModel->CasMdlCod = $mdl;
            if (! $this->obCasMdlModel->isEmptyAllReferencedTables()) {
                array_push($this->messages, $this->message->getMessage(2, 'Message', 'Existem dados relacionados a este registro!'));
            }
            if (isset($_POST['btnConfirmDelete'])) {
                if ($this->obCasMdlModel->deleteRegister()) {
                    array_push($this->messages, $this->message->getMessage(0, 'Message', 'Registro excluído com sucesso!'));
                    header('Location: /Manager/Modulo');
                    exit();
                } else {
                    array_push($this->messages, $this->message->getMessage(1, 'Message', 'Erro ao excluir o registro!'));
                }
            } else {
                if ($this->obCasMdlModel->readRegister()) {
                    $this->dataEntry = $this->obCasMdlModel->getRecords()[0];
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
        $this->formDesignOnForm('dlt', 1, 'ModuloViewForm.php', [$mdl]);

        /**
         * Show Screen
         */
        $data = array(
            'FormDesign' => $this->formDesign,
            'FormData' => FormData::secFields($fields, $this->dataEntry),
        );

        $this->view('SBAdmin/Manager/ModuloView', $data);
    }

    private function formatDataOnForm()
    {
        if (isset($this->dataEntry['ProgramsInModule'])) {
            foreach ($this->dataEntry['ProgramsInModule'] as $key => $value) {
                $this->dataEntry['ProgramsInModule'][$key]['CasPrgBlq'] = FormatData::transformSelectionSN($value['CasPrgBlq'], true);
            }
        }
    }

    private function formDesignOnForm($transMode, $current, $file, $keys)
    {
        $urlKeys = implode('/', $keys);

        $this->formDesign['Tabs']['Current'] = $current;
        $this->formDesign['Tabs']['LoadFile'] = "App/Views/SBAdmin/Manager/" . $file;
        $this->formDesign['Tabs']['Items'][0]['Link'] = '/Manager/Modulo';
        
        $this->formDesign['Fields'] = array_merge(CasMdlModel::FIELDS_MD, CasPrgModel::FIELDS_MD);
        $this->formDesign['TransLinkRemove'] = "/Manager/Modulo/Remove/{$urlKeys}";

        switch ($transMode) {
            case 'upd':
                $this->formDesign['Tabs']['Items'][1]['Link'] = "/Manager/Modulo/Show/{$urlKeys}";
                $this->formDesign['Tabs']['Items'][2]['Link'] = "/Manager/Modulo/Programa/{$urlKeys}";
                break;

            case 'upd':
                $this->formDesign['Tabs']['Items'][2]['Link'] = "/Manager/Modulo/Programa/{$urlKeys}";
                break;

            case 'dlt':
                $this->formDesign['Tabs']['Items'][1]['Link'] = $this->formDesign['TransLinkRemove'];
                break;
            
            default:
                $this->formDesign['Tabs']['Items'][1]['Link'] = "/Manager/Modulo/Show";
                break;
        }

        if ($this->messages) {
            $this->formDesign['Message'] = $this->messages[0];
        }
    }
}
