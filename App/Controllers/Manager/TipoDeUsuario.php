<?php

namespace App\Controllers\Manager;

use App\Core\Controller;
use App\Core\Config;
use App\Shared\MessageDictionary;
use App\Models\CAS\CasTusModel;
use App\Class\Pattern\FormDesign;
use App\Class\Pattern\FormData;
use App\Class\Pattern\FormatData;
use App\Core\AuthSession;

class TipoDeUsuario extends Controller
{
    private $messages = array();
    private $formDesign;
    private $obCasTusModel;
    private $dataEntry;

    public function __construct()
    {
        $this->validateAccess('TipoDeUsuario');
        // $this->setProgramParameters('TipoDeUsuario', '');

        // validação para contas administrativas portal siti - ['Aplicativo','Repositorio','Token','TipoDeUsuario','Usuario'] - [start] //
        if (! in_array(strtoupper(AuthSession::get()['PROFILE']), array_merge(Config::getTypeUsersAdminPortal(), Config::getTypeUsersSupportPortal()))) {
            header('Location: /Home/Denied'); // access denied //
            exit();
        }
        // validação para contas administrativas portal siti - [end] //

        $this->message = new MessageDictionary();

        $this->obCasTusModel = new CasTusModel();

        $this->formDesign = FormDesign::withTabs('Portal SITI', 'Tipo de Usuário', 'Manager > Tipo de Usuário', $this->getUserMenu(), $this->getSideMenu());
    }

    public function index()
    {
        $records = array();

        // $fields = CasTusModel::FIELDS;
        $fields = ['CasTusCod', 'CasTusDsc', 'CasTusBlq', 'CasRpsCod'];
        $hidden = [];

        $this->obCasTusModel->setSelectedFields($fields);
        $this->obCasTusModel->CasRpsCod = AuthSession::get()['RPS_ID'];

        if ($this->obCasTusModel->readAllLinesJoin(['CasRps'])) {
            $records = $this->obCasTusModel->getRecords();
        }

        /**
         * Format Data
         */
        foreach ($records as $key => $value) {
            if (isset($records[$key]['CasTusBlq'])) {
                $records[$key]['CasTusBlq'] = FormatData::transformSelectionSN($value['CasTusBlq']);
            }
            if (isset($records[$key]['CasTusBlqDtt'])) {
                $records[$key]['CasTusBlqDtt'] = FormatData::transformData('OnlyDate', $value['CasTusBlqDtt']);
            }
            if (isset($records[$key]['CasTusAudIns'])) {
                $records[$key]['CasTusAudIns'] = FormatData::transformData('OnlyDate', $value['CasTusAudIns']);
            }
            if (isset($records[$key]['CasTusAudUpd'])) {
                $records[$key]['CasTusAudUpd'] = FormatData::transformData('OnlyDate', $value['CasTusAudUpd']);
            }
            if (isset($records[$key]['CasTusAudDlt'])) {
                $records[$key]['CasTusAudDlt'] = FormatData::transformData('OnlyDate', $value['CasTusAudDlt']);
            }
            // Add link to form //
            $linkCasTus = '';
            foreach (CasTusModel::FIELDS_PK as $pkvalue) {
                // [CasRpsCod] gets from session //
                if ($pkvalue == 'CasRpsCod') {
                    continue;
                }
                $linkCasTus .= '/' . $records[$key][$pkvalue];
            }
            $records[$key]['Action']['Show'] = $linkCasTus;
        }

        /**
         * Form Design
         */
        $fields_md = array();
        foreach ($fields as $key => $field) {
            if (in_array($field, CasTusModel::FIELDS)) {
                $fields_md[$field] = CasTusModel::FIELDS_MD[$field];
            } else {
                // Foreign Tables //
                foreach (CasTusModel::FIELDS_FOREIGN as $tableForeign => $valueFieldsForeign) {
                    if (in_array($field, $valueFieldsForeign['FIELDS'])) {
                        $fields_md[$field] = CasTusModel::FIELDS_MD_FOREIGN[$tableForeign]['FIELDS_MD'][$field];
                    }
                }
            }
        }

        $this->formDesignOnForm('dsp', 0, 'TipoDeUsuarioViewList.php', []);
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

        $this->view('SBAdmin/Manager/TipoDeUsuarioView', $data);
    }

    public function show($tus = '')
    {
        $rps = AuthSession::get()['RPS_ID'];
        $rps_dsc = AuthSession::get()['RPS_DSC'];

        /**
         * Modos atendidos (TransMode): Insert e Update
         */
        $fields = CasTusModel::FIELDS;

        $this->dataEntry = $this->getDataInput($fields);
        $this->dataEntry = FormData::secFields($fields, $this->dataEntry);

        if (empty($tus)) {
            // insert //
            $this->dataEntry[CasTusModel::FIELDS_PK[0]] = $rps;
            $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[1];
        } else {
            // update //
            $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[2];
        }

        if (isset($_POST['btnConfirmInsert']) || isset($_POST['btnConfirmUpdate'])) {
            foreach ($fields as $field) {
                $this->dataEntry[$field];
                $this->obCasTusModel->$field = $this->dataEntry[$field];
            }

            // insert //
            if (isset($_POST['btnConfirmInsert'])) {
                if ($this->obCasTusModel->createRegister()) {
                    array_push($this->messages, $this->message->getMessage(0, 'Message', 'Registro inserido com sucesso!'));
                    $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[0];
                    $this->formDesign['FormDisable'] = true;
                    $tus = $this->obCasTusModel->CasTusCod;
                } else {
                    array_push($this->messages, $this->message->getMessage(1, 'Message', 'Erro ao inserir o registro!'));
                }
            }

            // update //
            if (isset($_POST['btnConfirmUpdate'])) {
                $this->obCasTusModel->CasRpsCod = $this->dataEntry['CasRpsCod'];
                $this->obCasTusModel->CasTusCod = $this->dataEntry['CasTusCod'];
                if ($this->obCasTusModel->updateRegister()) {
                    array_push($this->messages, $this->message->getMessage(0, 'Message', 'Registro atualizado com sucesso!'));
                    $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[0];
                    $this->formDesign['FormDisable'] = true;
                    $tus = $this->obCasTusModel->CasTusCod;
                } else {
                    array_push($this->messages, $this->message->getMessage(1, 'Message', 'Erro ao atualizar o registro!'));
                }
            }
        }


        if (!empty($tus)) {
            $this->obCasTusModel->CasRpsCod = $rps;
            $this->obCasTusModel->CasTusCod = $tus;
            if ($this->obCasTusModel->readRegister()) {
                $this->dataEntry = $this->obCasTusModel->getRecords()[0];
            }
        }
        // Add Description of Repository //
        array_push($fields, 'CasRpsDsc');
        $this->dataEntry['CasRpsDsc'] = $rps_dsc;

        /**
         * Format Data
         */
        $this->formatDataOnForm();

        /**
         * Form Design
         */
        $transMode = (empty($tus) ? 'ins' : 'upd');
        $this->formDesignOnForm($transMode, 1, 'TipoDeUsuarioViewForm.php', [$tus]);

        /**
         * Show Screen
         */
        $data = array(
            'FormDesign' => $this->formDesign,
            'FormData' => FormData::secFields($fields, $this->dataEntry),
        );

        $this->view('SBAdmin/Manager/TipoDeUsuarioView', $data);
    }

    public function remove($tus = '')
    {
        $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[3];
        $this->formDesign['FormDisable'] = true;

        $fields = array_merge(CasTusModel::FIELDS, CasTusModel::FIELDS_AUDIT);

        if (!empty($tus)) {
            $this->obCasTusModel->CasRpsCod = AuthSession::get()['RPS_ID'];
            $this->obCasTusModel->CasTusCod = $tus;
            if (! $this->obCasTusModel->isEmptyAllReferencedTables()) {
                array_push($this->messages, $this->message->getMessage(2, 'Message', 'Existem dados relacionados a este registro!'));
            }
            if (isset($_POST['btnConfirmDelete'])) {
                if ($this->obCasTusModel->deleteRegister()) {
                    array_push($this->messages, $this->message->getMessage(0, 'Message', 'Registro excluído com sucesso!'));
                    header('Location: /Manager/TipoDeUsuario');
                    exit();
                } else {
                    array_push($this->messages, $this->message->getMessage(1, 'Message', 'Erro ao excluir o registro!'));
                }
            } else {
                if ($this->obCasTusModel->readRegister()) {
                    $this->dataEntry = $this->obCasTusModel->getRecords()[0];
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
        $this->formDesignOnForm('dlt', 1, 'TipoDeUsuarioViewForm.php', [$tus]);

        /**
         * Show Screen
         */
        $data = array(
            'FormDesign' => $this->formDesign,
            'FormData' => FormData::secFields($fields, $this->dataEntry),
        );

        $this->view('SBAdmin/Manager/TipoDeUsuarioView', $data);
    }

    private function formatDataOnForm()
    {
        $this->dataEntry['CasTusBlq'] = FormatData::transformSelectionSN($this->dataEntry['CasTusBlq'], false);
    }

    private function formDesignOnForm($transMode, $current, $file, $keys)
    {
        $urlKeys = implode('/', $keys);

        $this->formDesign['Tabs']['Current'] = $current;
        $this->formDesign['Tabs']['LoadFile'] = "App/Views/SBAdmin/Manager/" . $file;
        $this->formDesign['Tabs']['Items'][0]['Link'] = '/Manager/TipoDeUsuario';

        $this->formDesign['Fields'] = CasTusModel::FIELDS_MD;
        foreach (CasTusModel::FIELDS_MD_FOREIGN['CasRps']['FIELDS_MD'] as $key => $value) {
            if ($key == 'CasRpsDsc') {
                $this->formDesign['Fields']['CasRpsDsc'] = $value;
            }
        }
        $this->formDesign['TransLinkRemove'] = "/Manager/TipoDeUsuario/Remove/{$urlKeys}";

        switch ($transMode) {
            case 'upd':
                $this->formDesign['Tabs']['Items'][1]['Link'] = "/Manager/TipoDeUsuario/Show/{$urlKeys}";
                break;

            case 'dlt':
                $this->formDesign['Tabs']['Items'][1]['Link'] = $this->formDesign['TransLinkRemove'];
                break;

            default:
                $this->formDesign['Tabs']['Items'][1]['Link'] = "/Manager/TipoDeUsuario/Show";
                break;
        }

        if ($this->messages) {
            $this->formDesign['Message'] = $this->messages[0];
        }
    }
}
