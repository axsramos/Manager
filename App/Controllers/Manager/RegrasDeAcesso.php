<?php

namespace App\Controllers\Manager;

use App\Core\Controller;
use App\Shared\MessageDictionary;
use App\Models\CAS\CasApfModel;
use App\Models\CAS\CasPfiModel;
use App\Class\Pattern\FormDesign;
use App\Class\Pattern\FormData;
use App\Class\Pattern\FormatData;
use App\Models\CAS\CasRpsModel;
use App\Models\CAS\CasFprModel;
use App\Models\CAS\CasPrgModel;
use App\Core\AuthSession;
use App\Core\Config;
use App\Models\CAS\CasAfuModel;
use App\Models\CAS\CasPfuModel;

class RegrasDeAcesso extends Controller
{
    use \App\Traits\LogToFile;

    private $messages = array();
    private $formDesign;
    private $obCasApfModel;
    private $obCasPfiModel;
    private $dataEntry;

    public function __construct()
    {
        $this->validateAccess('RegrasDeAcesso');
        // $this->setProgramParameters('RegrasDeAcesso', '');

        $this->message = new MessageDictionary();

        $this->obCasApfModel = new CasApfModel();
        $this->obCasPfiModel = new CasPfiModel();

        $this->formDesign = FormDesign::withTabs('Controle de Acesso', 'Regras de Acesso', 'Regras > De Acesso', $this->getUserMenu(), $this->getSideMenu());
    }

    public function index()
    {
        $records = array();

        $fields = ['CasPfiDsc', 'CasPfiBlq', 'CasRpsCod', 'CasPfiCod'];
        $hidden = ['CasRpsCod', 'CasPfiCod'];

        $this->obCasApfModel->setSelectedFields($fields);
        $this->obCasApfModel->CasRpsCod = AuthSession::get()['RPS_ID'];

        $records = $this->obCasApfModel->listAccessRulesQuantity();

        /**
         * Format Data
         */
        foreach ($records as $key => $value) {
            if (isset($records[$key]['CasPfiBlq'])) {
                $records[$key]['CasPfiBlq'] = FormatData::transformSelectionSN($value['CasPfiBlq']);
            }
            // Add link to form //
            $linkCasApf = '';
            $linkCasApf .= '/' . $records[$key]['CasPfiCod'];
            $records[$key]['Action']['Show'] = $linkCasApf;
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

        // Add metadata quantity //
        $fields_md['QtdMdl'] = array(
            'Type' => 'int',
            'Length' => 20,
            'Required' => false,
            'Default' => '0',
            'LongLabel' => 'Quantidade de Módulos',
            'ShortLabel' => 'Qtd.Módulos',
            'TextPlaceholder' => '',
            'TextHelp' => ''
        );
        $fields_md['QtdPrg'] = array(
            'Type' => 'int',
            'Length' => 20,
            'Required' => false,
            'Default' => '0',
            'LongLabel' => 'Quantidade de Programas',
            'ShortLabel' => 'Qtd.Programas',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        );
        $fields_md['QtdUsr'] = array(
            'Type' => 'int',
            'Length' => 20,
            'Required' => false,
            'Default' => '0',
            'LongLabel' => 'Quantidade de Usuários',
            'ShortLabel' => 'Qtd.Usuários',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        );

        $this->formDesignOnForm('dsp', 0, 'RegrasDeAcessoViewList.php', []);
        $this->formDesign['Styles']['CSSFiles'] = ['dataTables'];
        $this->formDesign['Scripts']['Body'] = ['dataTables'];
        $this->formDesign['Fields'] = $fields_md;
        $this->formDesign['Hidden'] = $hidden;
        $this->formDesign['Tabs']['Items'][1]['IsDisabled'] = true; // disabled when index page //

        /**
         * Show Screen
         */
        $data = array(
            'FormDesign' => $this->formDesign,
            'FormData' => $records
        );

        $this->view('SBAdmin/Manager/RegrasDeAcessoView', $data);
    }

    public function show($pfi = '')
    {
        $rps = AuthSession::get()['RPS_ID'];
        $rps_dsc = AuthSession::get()['RPS_DSC'];

        $fields = array_intersect(CasPfiModel::FIELDS, ['CasRpsCod', 'CasPfiCod', 'CasPfiDsc', 'CasPfiBlq']);
        array_push($fields, 'CasRpsDsc');

        // display //
        $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[0];
        $this->formDesign['FormDisable'] = true;

        $this->dataEntry = array();

        if (!empty($pfi)) {
            $this->obCasPfiModel->CasRpsCod = $rps;
            $this->obCasPfiModel->CasPfiCod = $pfi;

            $this->obCasPfiModel->setSelectedFields($fields);

            if ($this->obCasPfiModel->readRegisterJoin(['CasRps', 'CasPfiCod'])) {
                $this->dataEntry = $this->obCasPfiModel->getRecords()[0];

                // ['FormDesign']['Message']['Description']
                // $this->formDesign['Message'] = 
                if ($this->dataEntry['CasPfiBlq'] == 'S') {
                    array_push($this->messages, $this->message->getMessage(2, 'Message', 'Atenção: Perfil selecionado está bloqueado.'));
                }
            } else {
                $pfi = '';
            }
        }

        if (empty($pfi)) {
            $this->pageNotFound();
            exit;
        }

        // list group modules and quantity programs //
        $this->obCasApfModel->setSelectedFields(['CasRpsCod', 'CasPfiCod', 'CasMdlCod', 'CasMdlDsc', 'CasMdlBlq']);
        $this->obCasApfModel->CasRpsCod = AuthSession::get()['RPS_ID'];
        $this->obCasApfModel->CasPfiCod = $pfi;

        $recordsMQP = $this->obCasApfModel->listModulesQuantityPrograms();

        if ($recordsMQP) {
            $this->obCasApfModel->setSelectedFields();
            foreach ($recordsMQP as $key => $item) {
                $this->obCasApfModel->CasRpsCod = $item['CasRpsCod'];
                $recordsPMA  = $this->obCasApfModel->listProgramsInModulesAutorized($item['CasPfiCod'], $item['CasMdlCod']);
                $recordsMQP[$key]['Programs'] = $recordsPMA;
            }
        }

        $obCasPfuModel = new CasPfuModel();
        $obCasPfuModel->setSelectedFields(['CasRpsCod', 'CasPfiCod']);
        $obCasPfuModel->CasRpsCod = $rps;
        $obCasPfuModel->CasPfiCod = $pfi;
        if ($obCasPfuModel->usersWithProfile() == 0) {
            array_push($this->messages, $this->message->getMessage(2, 'Message', 'Atenção: Não há usuários com o perfil para aplicação das regras.'));
        }

        /**
         * Format Data
         */
        $this->formatDataOnForm();

        /**
         * Form Design
         */
        $rps = AuthSession::get()['RPS_ID'];
        $this->formDesignOnForm('upd', 1, 'RegrasDeAcessoViewForm.php', [$pfi]);
        $this->formDesign['Fields'] = array_merge(CasPfiModel::FIELDS_MD, array('CasRpsDsc' => CasRpsModel::FIELDS_MD['CasRpsDsc']));
        $this->formDesign['Scripts']['Body'] = ['tooltip'];
        $this->formDesign['Modal']['Title'] = 'Autorização das Funcionalidades';
        $this->formDesign['Modal']['LoadFile'] = 'App/Views/SBAdmin/Manager/RegrasDeAcessoModal.php';
        $this->formDesign['Modal']['BaseUrlSearch'] = Config::$APP_URL . "/Manager/RegrasDeAcesso/getDataRegrasDeAcessoModal/{$rps}/{$pfi}/";

        /**
         * Show Screen
         */
        $data = array(
            'FormDesign' => $this->formDesign,
            'FormData' => FormData::secFields($fields, $this->dataEntry),
            'Authorizations' => $recordsMQP,
        );

        $this->view('SBAdmin/Manager/RegrasDeAcessoView', $data);
    }

    private function formatDataOnForm()
    {
        $this->dataEntry['CasPfiBlq'] = FormatData::transformSelectionSN($this->dataEntry['CasPfiBlq'], false);
    }

    private function formDesignOnForm($transMode, $current, $file, $keys)
    {
        $urlKeys = implode('/', $keys);

        $this->formDesign['Tabs']['Current'] = $current;
        $this->formDesign['Tabs']['LoadFile'] = "App/Views/SBAdmin/Manager/" . $file;
        $this->formDesign['Tabs']['Items'][0]['Link'] = '/Manager/RegrasDeAcesso';

        $this->formDesign['Fields'] = array_merge(CasApfModel::FIELDS_MD, array('CasRpsDsc' => CasRpsModel::FIELDS_MD['CasRpsDsc']));

        switch ($transMode) {
            case 'upd':
                $this->formDesign['Tabs']['Items'][1]['Link'] = "/Manager/RegrasDeAcesso/Show/{$urlKeys}";
                break;

            case 'dlt':
                $this->formDesign['Tabs']['Items'][1]['Link'] = $this->formDesign['TransLinkRemove'];
                break;

            default:
                $this->formDesign['Tabs']['Items'][1]['Link'] = "/Manager/RegrasDeAcesso/Show";
                break;
        }

        if ($this->messages) {
            $this->formDesign['Message'] = $this->messages[0];
        }
    }

    public function getDataRegrasDeAcessoModal(string $rps_id, string $pfi_id, string $prg_id): void
    {
        if (empty($rps_id) || empty($pfi_id) || empty($prg_id)) {
            http_response_code(404);
            $this->view('jsonView');
            return;
        }

        // functionalities //
        $obCasFprModel = new CasFprModel();
        $obCasFprModel->setSelectedFields(['CasRpsCod', 'CasPrgCod', 'CasFunCod', 'CasFunDsc', 'CasFunBlq']);
        $obCasFprModel->CasRpsCod = $rps_id;
        $obCasFprModel->CasPrgCod = $prg_id;

        if ($obCasFprModel->readAllLinesJoin(['CasRps', 'CasPrg'])) {
            foreach ($obCasFprModel->getRecords() as $key => $value) {
                if ($value['CasFunBlq'] == 'N') {
                    $data['functionalities'][$key] = $value;
                    $obCasAfuModel = new CasAfuModel();
                    $obCasAfuModel->CasRpsCod = $rps_id;
                    $obCasAfuModel->CasPfiCod = $pfi_id;
                    $obCasAfuModel->CasPrgCod = $prg_id;
                    $obCasAfuModel->CasFunCod = $value['CasFunCod'];
                    $obCasAfuModel->setSelectedFields(['CasRpsCod', 'CasPfiCod', 'CasPrgCod', 'CasFunCod']);
                    $data['functionalities'][$key]['CasFunBlq'] = $obCasAfuModel->existsProfileAutorized();
                }
            }
        }

        // authorized //
        $obCasApfModel = new CasApfModel();
        $obCasApfModel->setSelectedFields(['CasRpsCod', 'CasPfiCod', 'CasPrgCod']);
        $obCasApfModel->CasRpsCod = $rps_id;
        $obCasApfModel->CasPfiCod = $pfi_id;
        $obCasApfModel->CasPrgCod = $prg_id;
        $data['Authorized'] = $obCasApfModel->existsProfileAutorized();

        http_response_code(200);

        $this->view('jsonView', $data);
    }

    public function setDataRegrasDeAcessoModal(string $rps_id, string $pfi_id, string $prg_id)
    {
        /**
         * Get Parameters
         */
        $parms = [
            'CasRpsCod' => $rps_id,
            'CasPfiCod' => $pfi_id,
            'CasPrgCod' => $prg_id,
        ];

        if (empty($rps_id) || empty($pfi_id) || empty($prg_id)) {
            http_response_code(400);
            $msg = $this->message->getMessage(400);
            $msg['Description'] .= '. Os parâmetros são inválidos. (Repositório, Perfil e Programa) são obrigatórios.';
            $this->setLog(json_encode(array('parms' => $parms, 'message' => $msg)), 'error', 'Authorization');
            $this->view('jsonView', $msg);
            return;
        }

        $currentMethod = strtoupper($_SERVER['REQUEST_METHOD']);

        if ($currentMethod != 'POST') {
            http_response_code(405);
            $msg = $this->message->getMessage(405);
            $this->setLog(json_encode(array('parms' => $parms, 'method' => $currentMethod, 'message' => $msg)), 'error', 'Authorization');
            $this->view('jsonView', $msg);
            return;
        }

        /**
         * Get Data Content 
         */
        $data = json_decode(file_get_contents('php://input'));
        $this->setLog(json_encode(array('parms' => $parms, 'method' => $currentMethod, 'data' => $data)), 'monitoring', 'Authorization');

        /**
         * Check Data Content 
         */
        // required //
        if (!isset($data->AuthorizedProgram)) {
            http_response_code(400);
            $msg = $this->message->getMessage(400);
            $msg['Description'] .= '. (AuthorizedProgram) é um campo obrigatório.';
            $this->setLog(json_encode(array('parms' => $parms, 'method' => $currentMethod, 'message' => $msg)), 'error', 'Authorization');
            $this->view('jsonView', $msg);
            return;
        } else {
            $acceptvalues = ['NEGADO', 'AUTORIZADO'];
            if ($data->AuthorizedProgram) {
                if (!in_array($data->AuthorizedProgram, $acceptvalues)) {
                    http_response_code(400);
                    $msg = $this->message->getMessage(400);
                    $msg['Description'] .= '. O valor informado não é aceito. Considere utilizar: (NEGADO | AUTORIZADO).';
                    $this->setLog(json_encode(array('parms' => $parms, 'method' => $currentMethod, 'message' => $msg)), 'error', 'Authorization');
                    $this->view('jsonView', $msg);
                    return;
                }
            }
        }

        // check repository //
        $obCasRpsModel = new CasRpsModel();
        $obCasRpsModel->CasRpsCod = $rps_id;
        $obCasRpsModel->setSelectedFields(['CasRpsCod']);
        if (! $obCasRpsModel->readRegister()) {
            http_response_code(400);
            $msg = $this->message->getMessage(400);
            $msg['Description'] .= '. (Repositório) não existe.';
            $this->setLog(json_encode(array('parms' => $parms, 'method' => $currentMethod, 'message' => $msg)), 'error', 'Authorization');
            $this->view('jsonView', $msg);
            return;
        }

        // check profile //
        $obCasPfiModel = new CasPfiModel();
        $obCasPfiModel->setSelectedFields(['CasRpsCod', 'CasPfiCod']);
        $obCasPfiModel->CasRpsCod = $rps_id;
        $obCasPfiModel->CasPfiCod = $pfi_id;
        if (! $obCasPfiModel->readRegister()) {
            http_response_code(400);
            $msg = $this->message->getMessage(400);
            $msg['Description'] .= '. (Perfil) não existe.';
            $this->setLog(json_encode(array('parms' => $parms, 'method' => $currentMethod, 'message' => $msg)), 'error', 'Authorization');
            $this->view('jsonView', $msg);
            return;
        }
        
        // check program //
        $obCasPrgModel = new CasPrgModel();
        $obCasPrgModel->CasRpsCod = $rps_id;
        $obCasPrgModel->CasPrgCod = $prg_id;
        $obCasPrgModel->setSelectedFields(['CasRpsCod', 'CasPrgCod']);
        if (! $obCasPrgModel->readRegister()) {
            http_response_code(400);
            $msg = $this->message->getMessage(400);
            $msg['Description'] .= '. (Programa) não existe.';
            $this->setLog(json_encode(array('parms' => $parms, 'method' => $currentMethod, 'message' => $msg)), 'error', 'Authorization');
            $this->view('jsonView', $msg);
            return;
        }

        /**
         * Process Data Content 
         */
        if ($data->AuthorizedProgram == 'NEGADO') {
            $obCasAfuModel = new CasAfuModel();
            $obCasAfuModel->setSelectedFields(['CasRpsCod', 'CasPfiCod', 'CasUsrCod', 'CasPrgCod', 'CasFunCod']);
            $obCasAfuModel->CasRpsCod = $rps_id;
            $obCasAfuModel->CasPfiCod = $pfi_id;
            $obCasAfuModel->CasUsrCod = '';
            $obCasAfuModel->CasPrgCod = $prg_id;
            $obCasAfuModel->CasFunCod = '';
            $obCasAfuModel->deleteAllUserAuthorized();

            $obCasApfModel = new CasApfModel();
            $obCasApfModel->setSelectedFields(['CasRpsCod', 'CasPfiCod', 'CasUsrCod', 'CasPrgCod']);
            $obCasApfModel->CasRpsCod = $rps_id;
            $obCasApfModel->CasPfiCod = $pfi_id;
            $obCasApfModel->CasUsrCod = '';
            $obCasApfModel->CasPrgCod = $prg_id;
            $obCasApfModel->deleteAllUserAuthorized();
        }

        if ($data->AuthorizedProgram == 'AUTORIZADO') {
            $obCasApfModel = new CasApfModel();
            $obCasApfModel->setSelectedFields(['CasRpsCod', 'CasPfiCod', 'CasUsrCod', 'CasPrgCod']);
            $obCasApfModel->CasRpsCod = $rps_id;
            $obCasApfModel->CasPfiCod = $pfi_id;
            $obCasApfModel->CasUsrCod = '';
            $obCasApfModel->CasPrgCod = $prg_id;
            $obCasApfModel->insertAllUserAuthorized();
            foreach ($data->Functionalities as $key => $value) {
                $obCasAfuModel = new CasAfuModel();
                $obCasAfuModel->setSelectedFields(['CasRpsCod', 'CasPfiCod', 'CasUsrCod', 'CasPrgCod', 'CasFunCod']);
                $obCasAfuModel->CasRpsCod = $rps_id;
                $obCasAfuModel->CasPfiCod = $pfi_id;
                $obCasAfuModel->CasUsrCod = '';
                $obCasAfuModel->CasPrgCod = $prg_id;
                $obCasAfuModel->CasFunCod = $value->CasFunCod;
                // inverted logic. Question: Is it enabled //
                if ($value->CasFunBlq == 'S') {
                    $obCasAfuModel->insertAllUserAuthorized();
                }
                // inverted logic. Question: Is it enabled //
                if ($value->CasFunBlq == 'N') {
                    $obCasAfuModel->deleteAllUserAuthorized();
                }
            }
        }

        $authSession = AuthSession::get();
        $this->setLog(
            json_encode([
                'actor' => [
                    'repository_id' => $authSession['RPS_ID'] ?? null,
                    'user_id' => $authSession['USR_ID'] ?? null,
                ],
                'action' => 'UPDATE_AUTHORIZATION',
                'target' => [
                    'repository_id' => $rps_id,
                    'profile_id' => $pfi_id,
                    'program_id' => $prg_id,
                ],
                'authorized_program' => $data->AuthorizedProgram,
                'functionalities' => count((array) ($data->Functionalities ?? [])),
            ]),
            'authorization',
            'Audit'
        );

        http_response_code(200);

        $this->view('jsonView', $this->message->getMessage(200));
        return;
    }
}
