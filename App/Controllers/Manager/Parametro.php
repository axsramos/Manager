<?php

namespace App\Controllers\Manager;

use App\Core\Controller;
use App\Shared\MessageDictionary;
use App\Models\CAS\CasParModel;
use App\Class\Pattern\FormDesign;
use App\Class\Pattern\FormData;
use App\Class\Pattern\FormatData;
use App\Core\AuthSession;

class Parametro extends Controller
{
    private $messages = array();
    private $formDesign;
    private $obCasParModel;
    private $dataEntry;

    public function __construct()
    {
        $this->validateAccess('Parametro');
        // $this->setProgramParameters('Parametro', '');

        $this->message = new MessageDictionary();

        $this->obCasParModel = new CasParModel();

        $this->formDesign = FormDesign::withTabs('Portal SITI', 'Parametro', 'Settings > Parâmetro', $this->getUserMenu(), $this->getSideMenu());
    }

    public function index()
    {
        $records = array();

        $fields = ['CasParCod', 'CasParDsc', 'CasParBlq', 'CasRpsCod', 'CasRpsDsc']; // CasParModel::FIELDS;
        $hidden = ['CasRpsCod'];

        $this->obCasParModel->setSelectedFields($fields);
        $this->obCasParModel->CasRpsCod = AuthSession::get()['RPS_ID'];

        if ($this->obCasParModel->readAllLinesJoin(['CasRps'])) {
            $records = $this->obCasParModel->getRecords();
        }

        /**
         * Format Data
         */
        foreach ($records as $key => $value) {
            if (isset($records[$key]['CasParBlq'])) {
                $records[$key]['CasParBlq'] = FormatData::transformSelectionSN($value['CasParBlq']);
            }
            if (isset($records[$key]['CasParBlqDtt'])) {
                $records[$key]['CasParBlqDtt'] = FormatData::transformData('OnlyDate', $value['CasParBlqDtt']);
            }
            if (isset($records[$key]['CasParAudIns'])) {
                $records[$key]['CasParAudIns'] = FormatData::transformData('OnlyDate', $value['CasParAudIns']);
            }
            if (isset($records[$key]['CasParAudUpd'])) {
                $records[$key]['CasParAudUpd'] = FormatData::transformData('OnlyDate', $value['CasParAudUpd']);
            }
            if (isset($records[$key]['CasParAudDlt'])) {
                $records[$key]['CasParAudDlt'] = FormatData::transformData('OnlyDate', $value['CasParAudDlt']);
            }
            // Add link to form //
            $linkCasPar = '';
            foreach (CasParModel::FIELDS_PK as $pkvalue) {
                // [CasRpsCod] gets from session //
                if ($pkvalue == 'CasRpsCod') {
                    continue;
                }
                $linkCasPar .= '/' . $records[$key][$pkvalue];
            }
            $records[$key]['Action']['Show'] = $linkCasPar;
        }

        /**
         * Form Design
         */
        $fields_md = array();
        foreach ($fields as $key => $field) {
            if (in_array($field, CasParModel::FIELDS)) {
                $fields_md[$field] = CasParModel::FIELDS_MD[$field];
            } else {
                // Foreign Tables //
                foreach (CasParModel::FIELDS_FOREIGN as $tableForeign => $valueFieldsForeign) {
                    if (in_array($field, $valueFieldsForeign['FIELDS'])) {
                        $fields_md[$field] = CasParModel::FIELDS_MD_FOREIGN[$tableForeign]['FIELDS_MD'][$field];
                    }
                }
            }
        }

        $this->formDesignOnForm('dsp', 0, 'ParametroViewList.php', []);
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

        $this->view('SBAdmin/Manager/ParametroView', $data);
    }

    public function show($rps = '', $par = '')
    {
        $rps = AuthSession::get()['RPS_ID'];

        /**
         * Modos atendidos (TransMode): Insert e Update
         */
        $fields = CasParModel::FIELDS;

        $this->dataEntry = $this->getDataInput($fields);
        $this->dataEntry = FormData::secFields($fields, $this->dataEntry);

        if (empty($par)) {
            // insert //
            $this->dataEntry[CasParModel::FIELDS_PK[0]] = $rps;
            $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[1];
        } else {
            // update //
            $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[2];
        }

        if (isset($_POST['btnConfirmInsert']) || isset($_POST['btnConfirmUpdate'])) {
            foreach ($fields as $field) {
                $this->dataEntry[$field];
                $this->obCasParModel->$field = $this->dataEntry[$field];
            }

            // insert //
            if (isset($_POST['btnConfirmInsert'])) {
                if ($this->obCasParModel->createRegister()) {
                    array_push($this->messages, $this->message->getMessage(0, 'Message', 'Registro inserido com sucesso!'));
                    $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[0];
                    $this->formDesign['FormDisable'] = true;
                    $par = $this->obCasParModel->CasParCod;
                } else {
                    array_push($this->messages, $this->message->getMessage(1, 'Message', 'Erro ao inserir o registro!'));
                }
            }

            // update //
            if (isset($_POST['btnConfirmUpdate'])) {
                $this->obCasParModel->CasRpsCod = $this->dataEntry['CasRpsCod'];
                $this->obCasParModel->CasParCod = $this->dataEntry['CasParCod'];
                if ($this->obCasParModel->updateRegister()) {
                    array_push($this->messages, $this->message->getMessage(0, 'Message', 'Registro atualizado com sucesso!'));
                    $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[0];
                    $this->formDesign['FormDisable'] = true;
                    $par = $this->obCasParModel->CasParCod;
                } else {
                    array_push($this->messages, $this->message->getMessage(1, 'Message', 'Erro ao atualizar o registro!'));
                }
            }
        }

        if (!empty($par)) {
            $this->obCasParModel->CasRpsCod = $rps;
            $this->obCasParModel->CasParCod = $par;
            if ($this->obCasParModel->readRegister()) {
                $this->dataEntry = $this->obCasParModel->getRecords()[0];
            }
        }

        /**
         * Format Data
         */
        $this->formatDataOnForm();

        /**
         * Form Design
         */
        $transMode = (empty($par) ? 'ins' : 'upd');
        $this->formDesignOnForm($transMode, 1, 'ParametroViewForm.php', [$par]);

        /**
         * Show Screen
         */
        $data = array(
            'FormDesign' => $this->formDesign,
            'FormData' => FormData::secFields($fields, $this->dataEntry),
        );

        $this->view('SBAdmin/Manager/ParametroView', $data);
    }

    public function remove($par = '')
    {
        $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[3];
        $this->formDesign['FormDisable'] = true;

        $fields = array_merge(CasParModel::FIELDS, CasParModel::FIELDS_AUDIT);

        if (!empty($par)) {
            $this->obCasParModel->CasRpsCod = AuthSession::get()['RPS_ID'];
            $this->obCasParModel->CasParCod = $par;
            if (! $this->obCasParModel->isEmptyAllReferencedTables()) {
                array_push($this->messages, $this->message->getMessage(2, 'Message', 'Existem dados relacionados a este registro!'));
            }
            if (isset($_POST['btnConfirmDelete'])) {
                if ($this->obCasParModel->deleteRegister()) {
                    array_push($this->messages, $this->message->getMessage(0, 'Message', 'Registro excluído com sucesso!'));
                    header('Location: /Manager/Parametro');
                    exit();
                } else {
                    array_push($this->messages, $this->message->getMessage(1, 'Message', 'Erro ao excluir o registro!'));
                }
            } else {
                if ($this->obCasParModel->readRegister()) {
                    $this->dataEntry = $this->obCasParModel->getRecords()[0];
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
        $this->formDesignOnForm('dlt', 1, 'ParametroViewForm.php', [$par]);

        /**
         * Show Screen
         */
        $data = array(
            'FormDesign' => $this->formDesign,
            'FormData' => FormData::secFields($fields, $this->dataEntry),
        );

        $this->view('SBAdmin/Manager/ParametroView', $data);
    }

    private function formatDataOnForm()
    {
        $this->dataEntry['CasParBlq'] = FormatData::transformSelectionSN($this->dataEntry['CasParBlq'], false);
    }

    private function formDesignOnForm($transMode, $current, $file, $keys)
    {
        $urlKeys = implode('/', $keys);

        $this->formDesign['Tabs']['Current'] = $current;
        $this->formDesign['Tabs']['LoadFile'] = "App/Views/SBAdmin/Manager/" . $file;
        $this->formDesign['Tabs']['Items'][0]['Link'] = '/Manager/Parametro';

        $this->formDesign['Fields'] = CasParModel::FIELDS_MD;
        $this->formDesign['TransLinkRemove'] = "/Manager/Parametro/Remove/{$urlKeys}";

        switch ($transMode) {
            case 'upd':
                $this->formDesign['Tabs']['Items'][1]['Link'] = "/Manager/Parametro/Show/{$urlKeys}";
                break;

            case 'dlt':
                $this->formDesign['Tabs']['Items'][1]['Link'] = $this->formDesign['TransLinkRemove'];
                break;

            default:
                $this->formDesign['Tabs']['Items'][1]['Link'] = "/Manager/Parametro/Show";
                break;
        }

        if ($this->messages) {
            $this->formDesign['Message'] = $this->messages[0];
        }
    }
}
