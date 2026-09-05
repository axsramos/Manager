<?php

namespace App\Controllers\Manager;

use App\Core\Controller;
use App\Core\Config;
use App\Shared\MessageDictionary;
use App\Models\CAS\CasRpsModel;
use App\Models\CAS\CasRpaModel;
use App\Models\CAS\CasRpuModel;
use App\Models\CAS\CasAppModel;
use App\Models\CAS\CasUsrModel;
use App\Models\CAS\CasTusModel;
use App\Class\Pattern\FormDesign;
use App\Class\Pattern\FormData;
use App\Class\Pattern\FormatData;
use App\Metadata\CAS\CasUsrMD;
use App\Class\Manager\CreateUserSupportClass;
use App\Class\Manager\ApplyApplicationSettings;
use App\Class\Manager\UserTypeDomain;
use App\Core\AuthSession;
use RuntimeException;

class Repositorio extends Controller
{
    private $messages = array();
    private $formDesign;
    private $obCasRpsModel;
    private $obCasAppModel;
    private $obCasUsrModel;
    private $obCasTusModel;
    private $dataEntry;

    public function __construct()
    {
        $this->validateAccess('Repositorio');
        // $this->setProgramParameters('Repositorio', '');

        // validação para contas administrativas portal siti - ['Aplicativo','Repositorio','Token','TipoDeUsuario','Usuario'] - [start] //
        if (! in_array(strtoupper(AuthSession::get()['PROFILE']), array_merge(Config::getTypeUsersAdminPortal(), Config::getTypeUsersSupportPortal()))) {
            header('Location: /Home/Denied'); // access denied //
            exit();
        }
        // validação para contas administrativas portal siti - [end] //

        $this->message = new MessageDictionary();

        $this->obCasRpsModel = new CasRpsModel();
        $this->obCasAppModel = new CasAppModel();
        $this->obCasUsrModel = new CasUsrModel();

        $this->formDesign = FormDesign::withTabs('Portal SITI', 'Repositório', 'Manager > Repositório', $this->getUserMenu(), $this->getSideMenu());

        // Aplicativo //
        $this->formDesign['Tabs']['Items'][2] = FormDesign::tabsModel('Single', '/Manager/Repositorio/Aplicativo')[0];
        $this->formDesign['Tabs']['Items'][2]['Name'] = 'Aplicativos';

        // Usuario //
        $this->formDesign['Tabs']['Items'][3] = FormDesign::tabsModel('Single', '/Manager/Repositorio/Usuario')[0];
        $this->formDesign['Tabs']['Items'][3]['Name'] = 'Usuários';
    }

    public function index()
    {
        $records = array();

        $fields = ['CasRpsCod', 'CasRpsDsc', 'CasRpsBlq']; // CasRpsModel::FIELDS; // 
        $hidden = [];

        $this->obCasRpsModel->setSelectedFields($fields);
        $this->obCasRpsModel->CasRpsCod = AuthSession::get()['RPS_ID'];

        if ($this->obCasRpsModel->readAllLinesJoin(['CasRps', 'CasRpsCod'])) {
            $records = $this->obCasRpsModel->getRecords();
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
            if (isset($records[$key]['CasRpsAudIns'])) {
                $records[$key]['CasRpsAudIns'] = FormatData::transformData('OnlyDate', $value['CasRpsAudIns']);
            }
            if (isset($records[$key]['CasRpsAudUpd'])) {
                $records[$key]['CasRpsAudUpd'] = FormatData::transformData('OnlyDate', $value['CasRpsAudUpd']);
            }
            if (isset($records[$key]['CasRpsAudDlt'])) {
                $records[$key]['CasRpsAudDlt'] = FormatData::transformData('OnlyDate', $value['CasRpsAudDlt']);
            }
            // Add link to form //
            $linkCasRps = '';
            foreach (CasRpsModel::FIELDS_PK as $pkvalue) {
                $linkCasRps .= '/' . $records[$key][$pkvalue];
            }
            $records[$key]['Action']['Show'] = $linkCasRps;
        }

        /**
         * Form Design
         */
        $fields_md = array();
        foreach ($fields as $key => $field) {
            $fields_md[$field] = CasRpsModel::FIELDS_MD[$field];
        }

        $this->formDesignOnForm('dsp', 0, 'RepositorioViewList.php', []);
        $this->formDesign['Styles']['CSSFiles'] = ['dataTables'];
        $this->formDesign['Scripts']['Body'] = ['dataTables'];
        $this->formDesign['Fields'] = $fields_md;
        $this->formDesign['Hidden'] = $hidden;
        $this->formDesign['Tabs']['Items'][2]['IsDisabled'] = true; // disabled when index page //
        $this->formDesign['Tabs']['Items'][3]['IsDisabled'] = true; // disabled when index page //
        

        /**
         * Show Screen
         */
        $data = array(
            'FormDesign' => $this->formDesign,
            'FormData' => $records
        );

        $this->view('SBAdmin/Manager/RepositorioView', $data);
    }

    public function show($id = '')
    {
        /**
         * Modos atendidos (TransMode): Insert e Update
         */
        $fields = CasRpsModel::FIELDS;

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
                $this->obCasRpsModel->$field = $this->dataEntry[$field];
            }

            // insert //
            if (isset($_POST['btnConfirmInsert'])) {
                if ($this->obCasRpsModel->createRegister()) {
                    array_push($this->messages, $this->message->getMessage(0, 'Message', 'Registro inserido com sucesso!'));
                    $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[0];
                    $this->formDesign['FormDisable'] = true;
                    $id = $this->obCasRpsModel->CasRpsCod;
                    $this->runRulesInsertUserSupport($id);
                } else {
                    array_push($this->messages, $this->message->getMessage(1, 'Message', 'Erro ao inserir o registro!'));
                }
            }

            // update //
            if (isset($_POST['btnConfirmUpdate'])) {
                if ($this->obCasRpsModel->updateRegister()) {
                    array_push($this->messages, $this->message->getMessage(0, 'Message', 'Registro atualizado com sucesso!'));
                    $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[0];
                    $this->formDesign['FormDisable'] = true;
                    $id = $this->obCasRpsModel->CasRpsCod;
                } else {
                    array_push($this->messages, $this->message->getMessage(1, 'Message', 'Erro ao atualizar o registro!'));
                }
            }
        }

        if (!empty($id)) {
            $this->obCasRpsModel->CasRpsCod = $id;
            if ($this->obCasRpsModel->readRegister()) {
                $this->dataEntry = $this->obCasRpsModel->getRecords()[0];
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
        $this->formDesignOnForm($transMode, 1, 'RepositorioViewForm.php', [$id]);
        if ($transMode == 'ins') {
            $this->formDesign['Tabs']['Items'][2]['IsDisabled'] = true; // disabled when index page //
            $this->formDesign['Tabs']['Items'][3]['IsDisabled'] = true; // disabled when index page //
        }

        /**
         * Adicional Buttons
         */
        // $this->formDesign['Buttons'] = array();
        // if ($transMode == 'upd') {
        //     $this->formDesign['Buttons'] = array(
        //         array('Type' => 'Button', 'Name' => 'btnRpaShow', 'Class' => 'btn btn-info', 'Link' => '/Manager/Repositorio/Aplicativo/' . $id, 'Label' => 'Aplicativos'),
        //         array('Type' => 'Button', 'Name' => 'btnRpuShow', 'Class' => 'btn btn-info', 'Link' => '/Manager/Repositorio/Usuario/' . $id, 'Label' => 'Usuários'),
        //     );
        // }

        /**
         * Show Screen
         */
        $data = array(
            'FormDesign' => $this->formDesign,
            'FormData' => FormData::secFields($fields, $this->dataEntry),
        );

        $this->view('SBAdmin/Manager/RepositorioView', $data);
    }

    public function aplicativo($rps = '', $app = '')
    {
        // Section Search [start] //
        $fieldsSearch = ['CasRpsCodSearch', 'CasAppCodSearch'];

        $this->dataEntry = $this->getDataInput($fieldsSearch);
        $this->dataEntry = FormData::secFields($fieldsSearch, $this->dataEntry);

        /**
         * Atualizar cadastro
         */
        if (isset($_POST['btnAppAdd']) || isset($_POST['btnAppRemove'])) {
            $obCasRpaModel = new CasRpaModel();
            $obCasRpaModel->setSelectedFields(CasRpaModel::FIELDS);
            $obCasRpaModel->CasRpsCod = $this->dataEntry['CasRpsCodSearch'];
            $obCasRpaModel->CasAppCod = $this->dataEntry['CasAppCodSearch'];

            // insert //
            if ($this->existsRps($obCasRpaModel->CasRpsCod) && $this->existsApp($obCasRpaModel->CasAppCod)) {
                if (isset($_POST['btnAppAdd'])) {
                    $obCasRpaModel->CasRpaDsc = '/' . $obCasRpaModel->CasRpsCod . '/' . $obCasRpaModel->CasAppCod;
                    $obCasRpaModel->CasRpaGrp = $obCasRpaModel->CasAppCod;
                    $obCasRpaModel->CasRpaBlq = 'N';
                    $obCasRpaModel->CasRpaBlqDtt = null;

                    if ($obCasRpaModel->createRegister()) {
                        // run Application Settings //
                        $this->runRulesInsertApplicationSettings($obCasRpaModel->CasRpsCod, $obCasRpaModel->CasAppCod);
                        // end //
                        array_push($this->messages, $this->message->getMessage(0, 'Message', 'Registro inserido com sucesso!'));
                    } else {
                        array_push($this->messages, $this->message->getMessage(1, 'Message', 'Erro ao inserir o registro!'));
                    }
                }
            } else {
                array_push($this->messages, $this->message->getMessage(1, 'Message', 'Informe Repositório e Aplicativo válido!'));
            }

            // delete //
            if (isset($_POST['btnAppRemove'])) {
                if ($obCasRpaModel->deleteRegister()) {
                    array_push($this->messages, $this->message->getMessage(0, 'Message', 'Registro excluído com sucesso!'));
                } else {
                    array_push($this->messages, $this->message->getMessage(1, 'Message', 'Erro ao excluir o registro!'));
                }
            }
        } else {
            if ($this->existsRps($rps)) {
                $this->dataEntry['CasRpsCodSearch'] = $rps;
            } else {
                $rps = '';
            }
            if ($this->existsApp($app)) {
                $this->dataEntry['CasAppCodSearch'] = $app;
            } else {
                $app = '';
            }
        }

        /**
         * Section Search CasRpa
         */
        $records = array();

        $obCasRpaModel = new CasRpaModel();

        $fields = [
            'CasRpaDsc',
            'CasAppDsc',
            'CasRpsDsc',
            'CasRpaBlq',
            'CasAppTst',
            'CasRpaGrp',
            'CasRpsCod',
            'CasAppCod',
        ]; // CasRpaModel::FIELDS;
        $hidden = ['CasRpsCod', 'CasAppCod', 'CasRpaDsc'];

        $obCasRpaModel->CasRpsCod = $this->dataEntry['CasRpsCodSearch'];

        $obCasRpaModel->setSelectedFields($fields);

        if ($obCasRpaModel->readAllLinesJoin(['CasRps'])) {
            $records = $obCasRpaModel->getRecords();
        }

        /**
         * Section Search Repository
         */
        $this->dataEntry['Repository']['CasRpsCod'] = '';
        $this->dataEntry['Repository']['CasRpsDsc'] = '';
        $this->dataEntry['Repository']['CasRpsBlq'] = '';
        $this->dataEntry['Repository']['CasRpsGrp'] = '';
        if (!empty($this->dataEntry['CasRpsCodSearch'])) {
            $this->obCasRpsModel->CasRpsCod = $this->dataEntry['CasRpsCodSearch'];
            if ($this->obCasRpsModel->readRegister()) {
                $this->dataEntry['Repository']['CasRpsCod'] = $this->obCasRpsModel->getRecords()[0]['CasRpsCod'];
                $this->dataEntry['Repository']['CasRpsDsc'] = $this->obCasRpsModel->getRecords()[0]['CasRpsDsc'];
                $this->dataEntry['Repository']['CasRpsBlq'] = $this->obCasRpsModel->getRecords()[0]['CasRpsBlq'];
                $this->dataEntry['Repository']['CasRpsGrp'] = $this->obCasRpsModel->getRecords()[0]['CasRpsGrp'];
            }
        }

        /**
         * Section Search Application
         */
        $this->dataEntry['Application']['CasAppCod'] = '';
        $this->dataEntry['Application']['CasAppDsc'] = '';
        $this->dataEntry['Application']['CasAppBlq'] = '';
        $this->dataEntry['Application']['CasAppGrp'] = '';
        $this->dataEntry['Application']['CasAppTst'] = '';
        $this->dataEntry['Application']['CasAppVer'] = '';
        if (!empty($this->dataEntry['CasAppCodSearch'])) {
            $this->obCasAppModel->CasAppCod = $this->dataEntry['CasAppCodSearch'];
            if ($this->obCasAppModel->readRegister()) {
                $this->dataEntry['Application']['CasAppCod'] = $this->obCasAppModel->getRecords()[0]['CasAppCod'];
                $this->dataEntry['Application']['CasAppDsc'] = $this->obCasAppModel->getRecords()[0]['CasAppDsc'];
                $this->dataEntry['Application']['CasAppBlq'] = $this->obCasAppModel->getRecords()[0]['CasAppBlq'];
                $this->dataEntry['Application']['CasAppGrp'] = $this->obCasAppModel->getRecords()[0]['CasAppGrp'];
                $this->dataEntry['Application']['CasAppTst'] = $this->obCasAppModel->getRecords()[0]['CasAppTst'];
                $this->dataEntry['Application']['CasAppVer'] = $this->obCasAppModel->getRecords()[0]['CasAppVer'];
            }
        }
        // Section Search [end] //

        /**
         * Format Data
         */
        foreach ($records as $key => $value) {
            if (isset($records[$key]['CasRpaBlq'])) {
                $records[$key]['CasRpaBlq'] = FormatData::transformSelectionSN($value['CasRpaBlq']);
            }
            if (isset($records[$key]['CasRpaBlqDtt'])) {
                $records[$key]['CasRpaBlqDtt'] = FormatData::transformData('OnlyDate', $value['CasRpaBlqDtt']);
            }
            if (isset($records[$key]['CasAppTst'])) {
                $records[$key]['CasAppTst'] = FormatData::transformSelectionSN($value['CasAppTst']);
            }
            if (isset($records[$key]['CasAppTstDtt'])) {
                $records[$key]['CasAppTstDtt'] = FormatData::transformData('OnlyDate', $value['CasAppTstDtt']);
            }
            if (isset($records[$key]['CasRpaAudIns'])) {
                $records[$key]['CasRpaAudIns'] = FormatData::transformData('OnlyDate', $value['CasRpaAudIns']);
            }
            if (isset($records[$key]['CasRpaAudUpd'])) {
                $records[$key]['CasRpaAudUpd'] = FormatData::transformData('OnlyDate', $value['CasRpaAudUpd']);
            }
            if (isset($records[$key]['CasRpaAudDlt'])) {
                $records[$key]['CasRpaAudDlt'] = FormatData::transformData('OnlyDate', $value['CasRpaAudDlt']);
            }
        }
        // Repository
        if (isset($this->dataEntry['Repository']['CasRpsBlq'])) {
            $this->dataEntry['Repository']['CasRpsBlq'] = FormatData::transformSelectionSN($this->dataEntry['Repository']['CasRpsBlq'], true);
        }
        // Application
        if (isset($this->dataEntry['Application']['CasAppBlq'])) {
            $this->dataEntry['Application']['CasAppBlq'] = FormatData::transformSelectionSN($this->dataEntry['Application']['CasAppBlq'], true);
        }

        /**
         * Form Design
         */
        $fields_md = array();
        foreach ($fields as $key => $field) {
            if (isset(CasRpaModel::FIELDS_MD[$field])) {
                $fields_md[$field] = CasRpaModel::FIELDS_MD[$field];
            }
            if (isset(CasRpsModel::FIELDS_MD[$field])) {
                $fields_md[$field] = CasRpsModel::FIELDS_MD[$field];
            }
            if (isset(CasAppModel::FIELDS_MD[$field])) {
                $fields_md[$field] = CasAppModel::FIELDS_MD[$field];
            }
        }

        $transMode = (empty($app) ? 'ins' : 'upd');
        $this->formDesignOnForm($transMode, 2, 'RepositorioViewListAplicativo.php', [$rps, $app]);
        $this->formDesign['Styles']['CSSFiles'] = ['dataTables'];
        $this->formDesign['Scripts']['Body'] = ['dataTables'];
        $this->formDesign['Fields'] = $fields_md;
        $this->formDesign['Hidden'] = $hidden;
        $this->formDesign['FieldsRepositorySearch'] = CasRpsModel::FIELDS_MD;
        $this->formDesign['FieldsApplicationSearch'] = CasAppModel::FIELDS_MD;
        if (empty($rps)) {
            $this->formDesign['Tabs']['Items'][2]['Link'] = '/Manager/Repositorio/Aplicativo';
        } else {
            $this->formDesign['Tabs']['Items'][2]['Link'] = '/Manager/Repositorio/Aplicativo/' . $rps . '/';
        }

        /**
         * Show Screen
         */
        $data = array(
            'FormDesign' => $this->formDesign,
            'FormData' => $records,
            'FormDataSearch' => $this->dataEntry,
        );

        $this->view('SBAdmin/Manager/RepositorioView', $data);
    }

    public function usuario($rps = '', $usr = '')
    {
        // Section Search [start] //
        $fieldsSearch = ['CasRpsCodSearch', 'CasUsrCodSearch', 'CasTusCodSearch'];

        $this->dataEntry = $this->getDataInput($fieldsSearch);
        $this->dataEntry = FormData::secFields($fieldsSearch, $this->dataEntry);

        /**
         * Atualizar cadastro
         */
        if (isset($_POST['btnUsrAdd']) || isset($_POST['btnUsrRemove'])) {
            $obCasRpuModel = new CasRpuModel();
            $obCasRpuModel->setSelectedFields(CasRpuModel::FIELDS);
            $obCasRpuModel->CasRpsCod = $this->dataEntry['CasRpsCodSearch'];
            $obCasRpuModel->CasUsrCod = $this->dataEntry['CasUsrCodSearch'];
            $obCasRpuModel->CasTusCod = $this->dataEntry['CasTusCodSearch'];

            // insert //
            if ($this->existsRps($obCasRpuModel->CasRpsCod) && $this->existsUsr($obCasRpuModel->CasUsrCod)) {
                if (isset($_POST['btnUsrAdd'])) {
                    $obCasRpuModel->CasRpuDsc = '/' . $obCasRpuModel->CasRpsCod . '/' . $obCasRpuModel->CasUsrCod;
                    $obCasRpuModel->CasRpuBlq = 'N';
                    $obCasRpuModel->CasRpuBlqDtt = null;

                    if ($obCasRpuModel->createRegister()) {
                        array_push($this->messages, $this->message->getMessage(0, 'Message', 'Registro inserido com sucesso!'));
                    } else {
                        array_push($this->messages, $this->message->getMessage(1, 'Message', 'Erro ao inserir o registro!'));
                    }
                }
            } else {
                array_push($this->messages, $this->message->getMessage(1, 'Message', 'Informe Repositório e Usuário válido!'));
            }

            // delete //
            if (isset($_POST['btnUsrRemove'])) {
                if ($obCasRpuModel->deleteRegister()) {
                    array_push($this->messages, $this->message->getMessage(0, 'Message', 'Registro excluído com sucesso!'));
                } else {
                    array_push($this->messages, $this->message->getMessage(1, 'Message', 'Erro ao excluir o registro!'));
                }
            }
        } else {
            if ($this->existsRps($rps)) {
                $this->dataEntry['CasRpsCodSearch'] = $rps;
            } else {
                $rps = '';
            }
            if ($this->existsUsr($usr)) {
                $this->dataEntry['CasUsrCodSearch'] = $usr;
            } else {
                $usr = '';
            }
        }

        /**
         * Section Search CasRpu
         */
        $records = array();

        $obCasRpuModel = new CasRpuModel();

        $fields = [
            'CasRpuDsc',
            'CasRpsCod',
            'CasRpsDsc',
            'CasUsrCod',
            'CasUsrDsc',
            'CasTusCod',
            'CasTusDsc',
            'CasRpuBlq',
        ];
        $hidden = ['CasRpsCod', 'CasUsrCod', 'CasTusCod'];

        $obCasRpuModel->CasRpsCod = $this->dataEntry['CasRpsCodSearch'];

        $obCasRpuModel->setSelectedFields($fields);

        if ($obCasRpuModel->readAllLinesJoin(['CasRps'])) {
            $records = $obCasRpuModel->getRecords();
        }

        /**
         * Section Search Repository
         */
        $this->dataEntry['Repository']['CasRpsCod'] = '';
        $this->dataEntry['Repository']['CasRpsDsc'] = '';
        $this->dataEntry['Repository']['CasRpsBlq'] = '';
        $this->dataEntry['Repository']['CasRpsGrp'] = '';
        if (!empty($this->dataEntry['CasRpsCodSearch'])) {
            $this->obCasRpsModel->CasRpsCod = $this->dataEntry['CasRpsCodSearch'];
            if ($this->obCasRpsModel->readRegister()) {
                $this->dataEntry['Repository']['CasRpsCod'] = $this->obCasRpsModel->getRecords()[0]['CasRpsCod'];
                $this->dataEntry['Repository']['CasRpsDsc'] = $this->obCasRpsModel->getRecords()[0]['CasRpsDsc'];
                $this->dataEntry['Repository']['CasRpsBlq'] = $this->obCasRpsModel->getRecords()[0]['CasRpsBlq'];
                $this->dataEntry['Repository']['CasRpsGrp'] = $this->obCasRpsModel->getRecords()[0]['CasRpsGrp'];
            }
        }

        /**
         * Section Search Usuario
         */
        $this->dataEntry['Usuario']['CasUsrCod'] = '';
        $this->dataEntry['Usuario']['CasUsrDsc'] = '';
        $this->dataEntry['Usuario']['CasUsrBlq'] = '';
        $this->dataEntry['Usuario']['CasTusCod'] = '';
        if (!empty($this->dataEntry['CasUsrCodSearch'])) {
            $this->obCasUsrModel->CasUsrCod = $this->dataEntry['CasUsrCodSearch'];
            if ($this->obCasUsrModel->readRegister()) {
                $this->dataEntry['Usuario']['CasUsrCod'] = $this->obCasUsrModel->getRecords()[0]['CasUsrCod'];
                $this->dataEntry['Usuario']['CasUsrDsc'] = $this->obCasUsrModel->getRecords()[0]['CasUsrDsc'];
                $this->dataEntry['Usuario']['CasUsrBlq'] = $this->obCasUsrModel->getRecords()[0]['CasUsrBlq'];
                $this->dataEntry['Usuario']['CasTusCod'] = ''; // $this->obCasUsrModel->getRecords()[0]['CasTusCod'];
            }
        }

        /**
         * List CasTus
         */
        $this->obCasTusModel = new CasTusModel();
        $this->obCasTusModel->setSelectedFields(['CasTusCod', 'CasTusDsc']);
        $this->obCasTusModel->CasRpsCod = $this->dataEntry['CasRpsCodSearch'];
        if ($this->obCasTusModel->readAllLines(['CasRpsCod'])) {
            $this->dataEntry['FilCasTus'] = $this->obCasTusModel->getRecords();
        }
        
        // remover da lista 'CasTusDsc' o item 'ADMINISTRATOR ACCOUNT'
        // exceto quando o usuário logado for do tipo 'ADMINISTRATOR ACCOUNT'
        if (!in_array(strtoupper(AuthSession::get()['PROFILE']), Config::getTypeUsersAdminPortal())) {
            if (isset($this->dataEntry['FilCasTus'])) {
                $this->dataEntry['FilCasTus'] = array_filter($this->dataEntry['FilCasTus'], function ($item) {
                    return strtoupper($item['CasTusDsc']) !== Config::getTypeUsersAdminPortal()[0];
                });
            }
        }

        // Section Search [end] //

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
        // Repository
        if (isset($this->dataEntry['Repository']['CasRpsBlq'])) {
            $this->dataEntry['Repository']['CasRpsBlq'] = FormatData::transformSelectionSN($this->dataEntry['Repository']['CasRpsBlq'], true);
        }
        // Usuario
        if (isset($this->dataEntry['Usuario']['CasUsrBlq'])) {
            $this->dataEntry['Usuario']['CasUsrBlq'] = FormatData::transformSelectionSN($this->dataEntry['Usuario']['CasUsrBlq'], true);
        }

        /**
         * Form Design
         */
        $fields_md = array();
        foreach ($fields as $key => $field) {
            if (isset(CasRpuModel::FIELDS_MD[$field])) {
                $fields_md[$field] = CasRpuModel::FIELDS_MD[$field];
            }
            if (isset(CasRpsModel::FIELDS_MD[$field])) {
                $fields_md[$field] = CasRpsModel::FIELDS_MD[$field];
            }
            if (isset(CasUsrModel::FIELDS_MD[$field])) {
                $fields_md[$field] = CasUsrModel::FIELDS_MD[$field];
            }
            if (isset(CasTusModel::FIELDS_MD[$field])) {
                $fields_md[$field] = CasTusModel::FIELDS_MD[$field];
            }
        }

        $transMode = (empty($usr) ? 'ins' : 'upd');
        $this->formDesignOnForm($transMode, 3, 'RepositorioViewListUsuario.php', [$rps, $usr]);
        $this->formDesign['Styles']['CSSFiles'] = ['dataTables'];
        $this->formDesign['Scripts']['Body'] = ['dataTables'];
        $this->formDesign['Fields'] = $fields_md;
        $this->formDesign['Hidden'] = $hidden;
        $this->formDesign['FieldsRepositorySearch'] = CasRpsModel::FIELDS_MD;
        $this->formDesign['FieldsUsuarioSearch'] = array_merge(CasUsrMD::FIELDS_MD, CasRpuModel::FIELDS_MD);
        if (empty($rps)) {
            $this->formDesign['Tabs']['Items'][3]['Link'] = '/Manager/Repositorio/Usuario';
        } else {
            $this->formDesign['Tabs']['Items'][3]['Link'] = '/Manager/Repositorio/Usuario/' . $rps . '/';
        }

        /**
         * Show Screen
         */
        $data = array(
            'FormDesign' => $this->formDesign,
            'FormData' => $records,
            'FormDataSearch' => $this->dataEntry,
        );

        $this->view('SBAdmin/Manager/RepositorioView', $data);
    }

    public function remove($id = '')
    {
        $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[3];
        $this->formDesign['FormDisable'] = true;

        $fields = array_merge(CasRpsModel::FIELDS, CasRpsModel::FIELDS_AUDIT);

        if (!empty($id)) {
            $this->obCasRpsModel->CasRpsCod = $id;
            if (! $this->obCasRpsModel->isEmptyAllReferencedTables()) {
                array_push($this->messages, $this->message->getMessage(2, 'Message', 'Existem dados relacionados a este registro!'));
            }
            if (isset($_POST['btnConfirmDelete'])) {
                if ($this->obCasRpsModel->deleteRegister()) {
                    array_push($this->messages, $this->message->getMessage(0, 'Message', 'Registro excluído com sucesso!'));
                    header('Location: /Manager/Repositorio');
                    exit();
                } else {
                    array_push($this->messages, $this->message->getMessage(1, 'Message', 'Erro ao excluir o registro!'));
                }
            } else {
                $this->obCasRpsModel->CasRpsCod = $id;
                if ($this->obCasRpsModel->readRegister()) {
                    $this->dataEntry = $this->obCasRpsModel->getRecords()[0];
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
        $this->formDesignOnForm('dlt', 1, 'RepositorioViewForm.php', [$id]);
        $this->formDesign['Tabs']['Items'][1]['Link'] = str_replace('/Show/', '/Remove/', $this->formDesign['Tabs']['Items'][1]['Link']);

        /**
         * Show Screen
         */
        $data = array(
            'FormDesign' => $this->formDesign,
            'FormData' => FormData::secFields($fields, $this->dataEntry),
        );

        $this->view('SBAdmin/Manager/RepositorioView', $data);
    }

    private function formatDataOnForm()
    {
        $this->dataEntry['CasRpsBlq'] = FormatData::transformSelectionSN($this->dataEntry['CasRpsBlq'], false);
    }

    private function formDesignOnForm($transMode, $current, $file, $keys)
    {
        $urlKeys = implode('/', $keys);

        $this->formDesign['Tabs']['Current'] = $current;
        $this->formDesign['Tabs']['LoadFile'] = 'App/Views/SBAdmin/Manager/' . $file;
        $this->formDesign['Tabs']['Items'][0]['Link'] = '/Manager/Repositorio';

        $this->formDesign['Fields'] = CasRpsModel::FIELDS_MD;
        $this->formDesign['TransLinkRemove'] = "/Manager/Repositorio/Remove/{$urlKeys}";

        switch ($transMode) {
            case 'upd':
                $this->formDesign['Tabs']['Items'][1]['Link'] = "/Manager/Repositorio/Show/{$urlKeys}";
                $this->formDesign['Tabs']['Items'][2]['Link'] = "/Manager/Repositorio/Aplicativo/{$urlKeys}";
                $this->formDesign['Tabs']['Items'][3]['Link'] = "/Manager/Repositorio/Usuario/{$urlKeys}";
                break;

            case 'dlt':
                $this->formDesign['Tabs']['Items'][1]['Link'] = $this->formDesign['TransLinkRemove'];
                break;

            default:
                $this->formDesign['Tabs']['Items'][1]['Link'] = "/Manager/Repositorio/Show";
                $this->formDesign['Tabs']['Items'][2]['Link'] = "/Manager/Repositorio/Aplicativo/{$urlKeys}";
                $this->formDesign['Tabs']['Items'][3]['Link'] = "/Manager/Repositorio/Usuario/{$urlKeys}";
                break;
        }

        if ($this->messages) {
            $this->formDesign['Message'] = $this->messages[0];
        }
    }

    private function existsRps($id)
    {
        $obCasRpsModel = new CasRpsModel();
        $obCasRpsModel->setSelectedFields(['CasRpsCod']);
        $obCasRpsModel->CasRpsCod = $id;
        if ($obCasRpsModel->readRegister()) {
            return true;
        } else {
            return false;
        }
    }

    private function existsApp($id)
    {
        $obCasAppModel = new CasAppModel();
        $obCasAppModel->setSelectedFields(['CasAppCod']);
        $obCasAppModel->CasAppCod = $id;
        if ($obCasAppModel->readRegister()) {
            return true;
        } else {
            return false;
        }
    }

    private function existsUsr($id)
    {
        $obCasUsrModel = new CasUsrModel();
        $obCasUsrModel->setSelectedFields(['CasUsrCod']);
        $obCasUsrModel->CasUsrCod = $id;
        if ($obCasUsrModel->readRegister()) {
            return true;
        } else {
            return false;
        }
    }

    private function runRulesInsertUserSupport($id)
    {
        $path = Config::getPathRules('Manager') . 'SupportUser.json';
        $content = is_file($path) ? file_get_contents($path) : false;
        $records = $content === false ? null : json_decode($content, true);
        $supportUserId = is_array($records) ? ($records[0]['CasUsrCod'] ?? '') : '';
        if (! is_string($supportUserId) || $supportUserId === '') {
            throw new RuntimeException('SupportUser.json não contém uma conta de suporte válida.');
        }

        $types = UserTypeDomain::definitions((string) $id);
        $obCreateUserSupportClass = new CreateUserSupportClass();
        $obCreateUserSupportClass->run(
            (string) $id,
            $supportUserId,
            $types[UserTypeDomain::SUPPORT]['CasTusCod']
        );
    }
    private function runRulesInsertApplicationSettings($rps_id, $app_id)
    {
        $obApplyApplicationSettings = new ApplyApplicationSettings();
        $obApplyApplicationSettings->run($rps_id, $app_id);
    }
}
