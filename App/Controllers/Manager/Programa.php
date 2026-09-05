<?php

namespace App\Controllers\Manager;

use App\Core\Controller;
use App\Shared\MessageDictionary;
use App\Models\CAS\CasPrgModel;
use App\Models\CAS\CasFprModel;
use App\Models\CAS\CasFunModel;
use App\Class\Pattern\FormDesign;
use App\Class\Pattern\FormData;
use App\Class\Pattern\FormatData;
use App\Core\AuthSession;
use App\Models\CAS\CasApfModel;
use App\Models\CAS\CasAfuModel;

class Programa extends Controller
{
    private $messages = array();
    private $formDesign;
    private $obCasPrgModel;
    private $dataEntry;

    public function __construct()
    {
        $this->validateAccess('Programa');
        // $this->setProgramParameters('Programa', '');

        $this->message = new MessageDictionary();

        $this->obCasPrgModel = new CasPrgModel();

        $this->formDesign = FormDesign::withTabs('Portal SITI', 'Programa', 'Settings > Programa', $this->getUserMenu(), $this->getSideMenu());

        // // Funcionalidades //
        $this->formDesign['Tabs']['Items'][2] = FormDesign::tabsModel('Single', '/Manager/MeuPerfil/Funcionalidade')[0];
        $this->formDesign['Tabs']['Items'][2]['Name'] = 'Funcionalidade';
    }

    public function index()
    {
        $records = array();


        $fields = ['CasPrgCod', 'CasPrgDsc', 'CasPrgBlq', 'CasPrgTst', 'CasRpsCod', 'CasRpsDsc']; // $fields = CasPrgModel::FIELDS;
        $hidden = ['CasRpsCod'];

        $this->obCasPrgModel->setSelectedFields($fields);
        $this->obCasPrgModel->CasRpsCod = AuthSession::get()['RPS_ID'];

        if ($this->obCasPrgModel->readAllLinesJoin(['CasRps'])) {
            $records = $this->obCasPrgModel->getRecords();
        }

        /**
         * Format Data
         */
        foreach ($records as $key => $value) {
            if (isset($records[$key]['CasPrgBlq'])) {
                $records[$key]['CasPrgBlq'] = FormatData::transformSelectionSN($value['CasPrgBlq']);
            }
            if (isset($records[$key]['CasPrgBlqDtt'])) {
                $records[$key]['CasPrgBlqDtt'] = FormatData::transformData('OnlyDate', $value['CasPrgBlqDtt']);
            }
            if (isset($records[$key]['CasPrgTst'])) {
                $records[$key]['CasPrgTst'] = FormatData::transformSelectionSN($value['CasPrgTst']);
            }
            if (isset($records[$key]['CasPrgTstDtt'])) {
                $records[$key]['CasPrgTstDtt'] = FormatData::transformData('OnlyDate', $value['CasPrgTstDtt']);
            }
            if (isset($records[$key]['CasPrgAudIns'])) {
                $records[$key]['CasPrgAudIns'] = FormatData::transformData('OnlyDate', $value['CasPrgAudIns']);
            }
            if (isset($records[$key]['CasPrgAudUpd'])) {
                $records[$key]['CasPrgAudUpd'] = FormatData::transformData('OnlyDate', $value['CasPrgAudUpd']);
            }
            if (isset($records[$key]['CasPrgAudDlt'])) {
                $records[$key]['CasPrgAudDlt'] = FormatData::transformData('OnlyDate', $value['CasPrgAudDlt']);
            }
            // Add link to form //
            $linkCasPrg = '';
            foreach (CasPrgModel::FIELDS_PK as $pkvalue) {
                // [CasRpsCod] gets from session //
                if ($pkvalue == 'CasRpsCod') {
                    continue;
                }
                $linkCasPrg .= '/' . $records[$key][$pkvalue];
            }
            $records[$key]['Action']['Show'] = $linkCasPrg;
        }

        /**
         * Form Design
         */
        $fields_md = array();
        foreach ($fields as $key => $field) {
            if (in_array($field, CasPrgModel::FIELDS)) {
                $fields_md[$field] = CasPrgModel::FIELDS_MD[$field];
            } else {
                // Foreign Tables //
                foreach (CasPrgModel::FIELDS_FOREIGN as $tableForeign => $valueFieldsForeign) {
                    if (in_array($field, $valueFieldsForeign['FIELDS'])) {
                        $fields_md[$field] = CasPrgModel::FIELDS_MD_FOREIGN[$tableForeign]['FIELDS_MD'][$field];
                    }
                }
            }
        }

        $this->formDesignOnForm('dsp', 0, 'ProgramaViewList.php', []);
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

        $this->view('SBAdmin/Manager/ProgramaView', $data);
    }

    public function show($prg = '')
    {
        $rps = AuthSession::get()['RPS_ID'];

        /**
         * Modos atendidos (TransMode): Insert e Update
         */
        $fields = CasPrgModel::FIELDS;

        $this->dataEntry = $this->getDataInput($fields);
        $this->dataEntry = FormData::secFields($fields, $this->dataEntry);

        if (empty($prg)) {
            // insert //
            $this->dataEntry[CasPrgModel::FIELDS_PK[0]] = $rps;
            $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[1];
        } else {
            // update //
            $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[2];
        }

        if (isset($_POST['btnConfirmInsert']) || isset($_POST['btnConfirmUpdate'])) {
            foreach ($fields as $field) {
                $this->dataEntry[$field];
                $this->obCasPrgModel->$field = $this->dataEntry[$field];
            }

            // insert //
            if (isset($_POST['btnConfirmInsert'])) {
                if ($this->obCasPrgModel->createRegister()) {
                    array_push($this->messages, $this->message->getMessage(0, 'Message', 'Registro inserido com sucesso!'));
                    $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[0];
                    $this->formDesign['FormDisable'] = true;
                    $prg = $this->obCasPrgModel->CasPrgCod;
                } else {
                    array_push($this->messages, $this->message->getMessage(1, 'Message', 'Erro ao inserir o registro!'));
                }
            }

            // update //
            if (isset($_POST['btnConfirmUpdate'])) {
                $this->obCasPrgModel->CasRpsCod = $this->dataEntry['CasRpsCod'];
                $this->obCasPrgModel->CasPrgCod = $this->dataEntry['CasPrgCod'];
                if ($this->obCasPrgModel->updateRegister()) {
                    array_push($this->messages, $this->message->getMessage(0, 'Message', 'Registro atualizado com sucesso!'));
                    $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[0];
                    $this->formDesign['FormDisable'] = true;
                    $prg = $this->obCasPrgModel->CasPrgCod;
                } else {
                    array_push($this->messages, $this->message->getMessage(1, 'Message', 'Erro ao atualizar o registro!'));
                }
            }
        }

        if (!empty($prg)) {
            $this->obCasPrgModel->CasRpsCod = $rps;
            $this->obCasPrgModel->CasPrgCod = $prg;
            if ($this->obCasPrgModel->readRegister()) {
                $this->dataEntry = $this->obCasPrgModel->getRecords()[0];
            }
        }

        /**
         * Format Data
         */
        $this->formatDataOnForm();

        /**
         * Form Design
         */
        $transMode = (empty($prg) ? 'ins' : 'upd');
        $this->formDesignOnForm($transMode, 1, 'ProgramaViewForm.php', [$prg]);
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

        $this->view('SBAdmin/Manager/ProgramaView', $data);
    }

    public function funcionalidade($prg = '')
    {
        $rps = AuthSession::get()['RPS_ID'];

        /**
         * Modos atendidos (TransMode): Insert e Update
         */
        $fields = array_intersect(CasPrgModel::FIELDS, ['CasPrgCod', 'CasPrgDsc']);

        $this->dataEntry = array();

        if (!empty($prg)) {
            $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[2];

            $this->obCasPrgModel->setSelectedFields($fields);
            $this->obCasPrgModel->CasRpsCod = $rps;
            $this->obCasPrgModel->CasPrgCod = $prg;

            if ($this->obCasPrgModel->readRegister()) {
                $this->dataEntry = $this->obCasPrgModel->getRecords()[0];

                // load all functionalities //
                $this->loadFuncionalidades($rps, $prg);
            } else {
                $prg = '';
            }
        }

        if (empty($prg)) {
            $this->pageNotFound();
            exit;
        }

        if (isset($_POST['inputGroupSelectCasFunCod']) && $_POST['inputGroupSelectCasFunCod'] == 'Selecione uma opção') {
            array_push($this->messages, $this->message->getMessage(2, 'Message', 'Selecione uma opção válida.'));
        } else {
            if (isset($_POST['btnActionAdd']) || isset($_POST['btnActionRemove'])) {
                // insert //
                if (isset($_POST['btnActionAdd'])) {
                    $obCasFprModel = new CasFprModel();
                    $obCasFprModel->setSelectedFields(['CasRpsCod', 'CasPrgCod', 'CasFunCod']);
                    $obCasFprModel->CasRpsCod = $rps;
                    $obCasFprModel->CasPrgCod = $prg;
                    $obCasFprModel->CasFunCod = $_POST['inputGroupSelectCasFunCod'];
                    if ($obCasFprModel->readRegister()) {
                        array_push($this->messages, $this->message->getMessage(3, 'Message', 'O item selecionado já está cadastrado.'));
                    } else {
                        if ($obCasFprModel->createRegister()) {
                            array_push($this->messages, $this->message->getMessage(0, 'Message', 'Registro inserido com sucesso!'));
                        } else {
                            array_push($this->messages, $this->message->getMessage(1, 'Message', 'Erro ao inserir o registro!'));
                        }
                    }
                }

                // delete //
                if (isset($_POST['btnActionRemove'])) {
                    $obCasFprModel = new CasFprModel();
                    $obCasFprModel->setSelectedFields(['CasRpsCod', 'CasFunCod', 'CasPrgCod']);
                    $obCasFprModel->CasRpsCod = $rps;
                    $obCasFprModel->CasFunCod = $_POST['inputGroupSelectCasFunCod'];
                    $obCasFprModel->CasPrgCod = $prg;
                    if ($obCasFprModel->readRegister()) {
                        // remove CasAfu //
                        $obCasAfuModel = new CasAfuModel();
                        $obCasAfuModel->CasRpsCod = $rps;
                        $obCasAfuModel->CasPfiCod = '';
                        $obCasAfuModel->CasPrgCod = $prg;
                        $obCasAfuModel->CasFunCod = $_POST['inputGroupSelectCasFunCod'];
                        $obCasAfuModel->deleteAllUserAuthorized();

                        // remove CasApf //
                        $obCasApfModel = new CasApfModel();
                        $obCasApfModel->CasRpsCod = $rps;
                        $obCasApfModel->CasPfiCod = '';
                        $obCasApfModel->CasPrgCod = $prg;
                        $obCasApfModel->deleteAllUserAuthorized();

                        if ($obCasFprModel->deleteRegister()) {
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


        if (!empty($prg)) {
            $this->loadFuncionalidades($rps, $prg);
        }

        /**
         * Format Data
         */
        $this->formatDataOnForm();

        /**
         * Form Design
         */
        $this->formDesignOnForm('upd', 2, 'ProgramaViewFunctionality.php', [$prg]);

        /**
         * Show Screen
         */
        $data = array(
            'FormDesign' => $this->formDesign,
            'FormData' => $this->dataEntry,
        );

        $this->view('SBAdmin/Manager/ProgramaView', $data);
    }

    private function loadFuncionalidades($rps, $prg)
    {
        // FunctionalitiesInProgram //
        $obCasFprModel = new CasFprModel();
        $obCasFprModel->setSelectedFields(['CasRpsCod', 'CasPrgCod', 'CasFunCod', 'CasFunDsc', 'CasFunBlq']);
        $obCasFprModel->CasRpsCod = $rps;
        $obCasFprModel->CasPrgCod = $prg;

        if ($obCasFprModel->readAllLinesJoin(['CasRps', 'CasPrg'])) {
            $this->dataEntry['FunctionalitiesInProgram'] = $obCasFprModel->getRecords();
        }

        // AllFunctionalities //
        $obCasFunModel = new CasFunModel();
        $obCasFunModel->setSelectedFields(['CasRpsCod', 'CasPrgCod', 'CasFunCod', 'CasFunDsc', 'CasFunBlq']);
        $obCasFunModel->CasRpsCod = $rps;
        $obCasFunModel->CasPrgCod = $prg;
        if ($obCasFunModel->readAllLinesJoin(['CasRps', 'CasPrg'])) {
            $this->dataEntry['AllFunctionalities'] = $obCasFunModel->getRecords();
        }
    }

    public function remove($id = '')
    {
        $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[3];
        $this->formDesign['FormDisable'] = true;

        $fields = array_merge(CasPrgModel::FIELDS, CasPrgModel::FIELDS_AUDIT);

        if (!empty($id)) {
            $this->obCasPrgModel->CasRpsCod = AuthSession::get()['RPS_ID'];
            $this->obCasPrgModel->CasPrgCod = $id;
            if (! $this->obCasPrgModel->isEmptyAllReferencedTables()) {
                array_push($this->messages, $this->message->getMessage(2, 'Message', 'Existem dados relacionados a este registro!'));
            }
            if (isset($_POST['btnConfirmDelete'])) {
                if ($this->obCasPrgModel->deleteRegister()) {
                    array_push($this->messages, $this->message->getMessage(0, 'Message', 'Registro excluído com sucesso!'));
                    header('Location: /Manager/Programa');
                    exit();
                } else {
                    array_push($this->messages, $this->message->getMessage(1, 'Message', 'Erro ao excluir o registro!'));
                }
            } else {
                if ($this->obCasPrgModel->readRegister()) {
                    $this->dataEntry = $this->obCasPrgModel->getRecords()[0];
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
        $this->formDesignOnForm('dlt', 1, 'ProgramaViewForm.php', [$id]);

        /**
         * Show Screen
         */
        $data = array(
            'FormDesign' => $this->formDesign,
            'FormData' => FormData::secFields($fields, $this->dataEntry),
        );

        $this->view('SBAdmin/Manager/ProgramaView', $data);
    }

    private function formatDataOnForm()
    {
        if (isset($this->dataEntry['FunctionalitiesInProgram'])) {
            foreach ($this->dataEntry['FunctionalitiesInProgram'] as $key => $value) {
                $this->dataEntry['FunctionalitiesInProgram'][$key]['CasFunBlq'] = FormatData::transformSelectionSN($value['CasFunBlq'], true);
            }
        }
    }

    private function formDesignOnForm($transMode, $current, $file, $keys)
    {
        $urlKeys = implode('/', $keys);

        $this->formDesign['Tabs']['Current'] = $current;
        $this->formDesign['Tabs']['LoadFile'] = "App/Views/SBAdmin/Manager/" . $file;
        $this->formDesign['Tabs']['Items'][0]['Link'] = '/Manager/Programa';

        $this->formDesign['Fields'] = array_merge(CasPrgModel::FIELDS_MD, CasFunModel::FIELDS_MD);
        $this->formDesign['TransLinkRemove'] = "/Manager/Programa/Remove/{$urlKeys}";

        switch ($transMode) {
            case 'upd':
                $this->formDesign['Tabs']['Items'][1]['Link'] = "/Manager/Programa/Show/{$urlKeys}";
                $this->formDesign['Tabs']['Items'][2]['Link'] = "/Manager/Programa/Funcionalidade/{$urlKeys}";
                break;

            case 'upd':
                $this->formDesign['Tabs']['Items'][2]['Link'] = "/Manager/Programa/Funcionalidade/{$urlKeys}";
                break;

            case 'dlt':
                $this->formDesign['Tabs']['Items'][1]['Link'] = $this->formDesign['TransLinkRemove'];
                break;

            default:
                $this->formDesign['Tabs']['Items'][1]['Link'] = "/Manager/Programa/Show";
                break;
        }

        if ($this->messages) {
            $this->formDesign['Message'] = $this->messages[0];
        }
    }
}
