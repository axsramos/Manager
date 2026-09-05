<?php

namespace App\Controllers\Manager;

use App\Core\Controller;
use App\Shared\MessageDictionary;
use App\Models\CAS\CasFunModel;
use App\Class\Pattern\FormDesign;
use App\Class\Pattern\FormData;
use App\Class\Pattern\FormatData;
use app\Core\AuthSession;

class Funcionalidade extends Controller
{
    private $messages = array();
    private $formDesign;
    private $obCasFunModel;
    private $dataEntry;

    public function __construct()
    {
        $this->validateAccess('Funcionalidade');
        // $this->setProgramParameters('Funcionalidade', '');

        $this->message = new MessageDictionary();

        $this->obCasFunModel = new CasFunModel();

        $this->formDesign = FormDesign::withTabs('Portal SITI', 'Funcionalidade', 'Settings > Funcionalidade', $this->getUserMenu(), $this->getSideMenu());
    }

    public function index()
    {
        $records = array();

        $fields = ['CasFunCod', 'CasFunDsc', 'CasFunBlq', 'CasRpsCod', 'CasRpsDsc']; // $fields = CasFunModel::FIELDS;
        $hidden = ['CasRpsCod'];

        $this->obCasFunModel->setSelectedFields($fields);
        $this->obCasFunModel->CasRpsCod = AuthSession::get()['RPS_ID'];

        if ($this->obCasFunModel->readAllLinesJoin(['CasRps'])) {
            $records = $this->obCasFunModel->getRecords();
        }

        /**
         * Format Data
         */
        foreach ($records as $key => $value) {
            if (isset($records[$key]['CasFunBlq'])) {
                $records[$key]['CasFunBlq'] = FormatData::transformSelectionSN($value['CasFunBlq']);
            }
            if (isset($records[$key]['CasFunBlqDtt'])) {
                $records[$key]['CasFunBlqDtt'] = FormatData::transformData('OnlyDate', $value['CasFunBlqDtt']);
            }
            if (isset($records[$key]['CasFunAudIns'])) {
                $records[$key]['CasFunAudIns'] = FormatData::transformData('OnlyDate', $value['CasFunAudIns']);
            }
            if (isset($records[$key]['CasFunAudUpd'])) {
                $records[$key]['CasFunAudUpd'] = FormatData::transformData('OnlyDate', $value['CasFunAudUpd']);
            }
            if (isset($records[$key]['CasFunAudDlt'])) {
                $records[$key]['CasFunAudDlt'] = FormatData::transformData('OnlyDate', $value['CasFunAudDlt']);
            }
            // Add link to form //
            $linkCasFun = '';
            foreach (CasFunModel::FIELDS_PK as $pkvalue) {
                // [CasRpsCod] gets from session //
                if ($pkvalue == 'CasRpsCod') {
                    continue;
                }
                $linkCasFun .= '/' . $records[$key][$pkvalue];
            }
            $records[$key]['Action']['Show'] = $linkCasFun;
        }

        /**
         * Form Design
         */
        $fields_md = array();
        foreach ($fields as $key => $field) {
            if (in_array($field, CasFunModel::FIELDS)) {
                $fields_md[$field] = CasFunModel::FIELDS_MD[$field];
            } else {
                // Foreign Tables //
                foreach (CasFunModel::FIELDS_FOREIGN as $tableForeign => $valueFieldsForeign) {
                    if (in_array($field, $valueFieldsForeign['FIELDS'])) {
                        $fields_md[$field] = CasFunModel::FIELDS_MD_FOREIGN[$tableForeign]['FIELDS_MD'][$field];
                    }
                }
            }
        }

        $this->formDesignOnForm('dsp', 0, 'FuncionalidadeViewList.php', []);
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

        $this->view('SBAdmin/Manager/FuncionalidadeView', $data);
    }

    public function show($fun = '')
    {
        $rps = AuthSession::get()['RPS_ID'];

        /**
         * Modos atendidos (TransMode): Insert e Update
         */
        $fields = CasFunModel::FIELDS;

        $this->dataEntry = $this->getDataInput($fields);
        $this->dataEntry = FormData::secFields($fields, $this->dataEntry);

        if (empty($fun)) {
            // insert //
            $this->dataEntry[CasFunModel::FIELDS_PK[0]] = $rps;
            $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[1];
        } else {
            // update //
            $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[2];
        }

        if (isset($_POST['btnConfirmInsert']) || isset($_POST['btnConfirmUpdate'])) {
            foreach ($fields as $field) {
                $this->dataEntry[$field];
                $this->obCasFunModel->$field = $this->dataEntry[$field];
            }

            // insert //
            if (isset($_POST['btnConfirmInsert'])) {
                if ($this->obCasFunModel->createRegister()) {
                    array_push($this->messages, $this->message->getMessage(0, 'Message', 'Registro inserido com sucesso!'));
                    $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[0];
                    $this->formDesign['FormDisable'] = true;
                    $fun = $this->obCasFunModel->CasFunCod;
                } else {
                    array_push($this->messages, $this->message->getMessage(1, 'Message', 'Erro ao inserir o registro!'));
                }
            }

            // update //
            if (isset($_POST['btnConfirmUpdate'])) {
                $this->obCasFunModel->CasRpsCod = $this->dataEntry['CasRpsCod'];
                $this->obCasFunModel->CasFunCod = $this->dataEntry['CasFunCod'];
                if ($this->obCasFunModel->updateRegister()) {
                    array_push($this->messages, $this->message->getMessage(0, 'Message', 'Registro atualizado com sucesso!'));
                    $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[0];
                    $this->formDesign['FormDisable'] = true;
                    $fun = $this->obCasFunModel->CasFunCod;
                } else {
                    array_push($this->messages, $this->message->getMessage(1, 'Message', 'Erro ao atualizar o registro!'));
                }
            }
        }

        if (!empty($fun)) {
            $this->obCasFunModel->CasRpsCod = $rps;
            $this->obCasFunModel->CasFunCod = $fun;
            if ($this->obCasFunModel->readRegister()) {
                $this->dataEntry = $this->obCasFunModel->getRecords()[0];
            }
        }

        /**
         * Format Data
         */
        $this->formatDataOnForm();

        /**
         * Form Design
         */
        $transMode = (empty($fun) ? 'ins' : 'upd');
        $this->formDesignOnForm($transMode, 1, 'FuncionalidadeViewForm.php', [$fun]);

        /**
         * Show Screen
         */
        $data = array(
            'FormDesign' => $this->formDesign,
            'FormData' => FormData::secFields($fields, $this->dataEntry),
        );

        $this->view('SBAdmin/Manager/FuncionalidadeView', $data);
    }

    public function remove($id = '')
    {
        $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[3];
        $this->formDesign['FormDisable'] = true;

        $fields = array_merge(CasFunModel::FIELDS, CasFunModel::FIELDS_AUDIT);

        if (!empty($id)) {
            $this->obCasFunModel->CasRpsCod = AuthSession::get()['RPS_ID'];
            $this->obCasFunModel->CasFunCod = $id;
            if (! $this->obCasFunModel->isEmptyAllReferencedTables()) {
                array_push($this->messages, $this->message->getMessage(2, 'Message', 'Existem dados relacionados a este registro!'));
            }
            if (isset($_POST['btnConfirmDelete'])) {
                if ($this->obCasFunModel->deleteRegister()) {
                    array_push($this->messages, $this->message->getMessage(0, 'Message', 'Registro excluído com sucesso!'));
                    header('Location: /Manager/Funcionalidade');
                    exit();
                } else {
                    array_push($this->messages, $this->message->getMessage(1, 'Message', 'Erro ao excluir o registro!'));
                }
            } else {
                if ($this->obCasFunModel->readRegister()) {
                    $this->dataEntry = $this->obCasFunModel->getRecords()[0];
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
        $this->formDesignOnForm('dlt', 1, 'FuncionalidadeViewForm.php', [$id]);

        /**
         * Show Screen
         */
        $data = array(
            'FormDesign' => $this->formDesign,
            'FormData' => FormData::secFields($fields, $this->dataEntry),
        );

        $this->view('SBAdmin/Manager/FuncionalidadeView', $data);
    }

    private function formatDataOnForm()
    {
        $this->dataEntry['CasFunBlq'] = FormatData::transformSelectionSN($this->dataEntry['CasFunBlq'], false);
    }

    private function formDesignOnForm($transMode, $current, $file, $keys)
    {
        $urlKeys = implode('/', $keys);

        $this->formDesign['Tabs']['Current'] = $current;
        $this->formDesign['Tabs']['LoadFile'] = "App/Views/SBAdmin/Manager/" . $file;
        $this->formDesign['Tabs']['Items'][0]['Link'] = '/Manager/Funcionalidade';

        $this->formDesign['Fields'] = CasFunModel::FIELDS_MD;
        $this->formDesign['TransLinkRemove'] = "/Manager/Funcionalidade/Remove/{$urlKeys}";

        switch ($transMode) {
            case 'upd':
                $this->formDesign['Tabs']['Items'][1]['Link'] = "/Manager/Funcionalidade/Show/{$urlKeys}";
                break;

            case 'dlt':
                $this->formDesign['Tabs']['Items'][1]['Link'] = $this->formDesign['TransLinkRemove'];
                break;

            default:
                $this->formDesign['Tabs']['Items'][1]['Link'] = "/Manager/Funcionalidade/Show";
                break;
        }

        if ($this->messages) {
            $this->formDesign['Message'] = $this->messages[0];
        }
    }
}
