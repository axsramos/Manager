<?php

namespace App\Controllers\Manager;

use App\Core\Controller;
use App\Shared\MessageDictionary;
use App\Models\CAS\CasMnuModel;
use App\Models\CAS\CasMnaModel;
use App\Models\CAS\CasPrgModel;
use App\Class\Pattern\FormDesign;
use App\Class\Pattern\FormData;
use App\Class\Pattern\FormatData;
use App\Core\AuthSession;

class Menu extends Controller
{
    private $messages = array();
    private $formDesign;
    private $obCasMnuModel;
    private $obCasMnaModel;
    private $obCasPrgModel;
    private $dataEntry;

    public function __construct()
    {
        $this->validateAccess('Menu');
        // $this->setProgramParameters('Menu', '');

        $this->message = new MessageDictionary();

        $this->obCasMnuModel = new CasMnuModel();
        $this->obCasMnaModel = new CasMnaModel();
        $this->obCasPrgModel = new CasPrgModel();

        $this->formDesign = FormDesign::withTabs('Portal SITI', 'Menu', 'Settings > Menu', $this->getUserMenu(), $this->getSideMenu());

        // Árvore //
        $this->formDesign['Tabs']['Items'][2] = FormDesign::tabsModel('Single', '/Manager/Menu/Arvore/{mnu}/{mna}')[0];
        $this->formDesign['Tabs']['Items'][2]['Name'] = 'Árvore';
    }

    public function index()
    {
        $records = array();

        $fields = ['CasMnuCod', 'CasMnuDsc', 'CasMnuBlq', 'CasRpsCod', 'CasRpsDsc']; // $fields = CasMnuModel::FIELDS;
        $hidden = ['CasRpsCod'];

        $this->obCasMnuModel->setSelectedFields($fields);
        $this->obCasMnuModel->CasRpsCod = AuthSession::get()['RPS_ID'];

        if ($this->obCasMnuModel->readAllLinesJoin(['CasRps'])) {
            $records = $this->obCasMnuModel->getRecords();
        }

        /**
         * Format Data
         */
        foreach ($records as $key => $value) {
            if (isset($records[$key]['CasMnuBlq'])) {
                $records[$key]['CasMnuBlq'] = FormatData::transformSelectionSN($value['CasMnuBlq']);
            }
            if (isset($records[$key]['CasMnuBlqDtt'])) {
                $records[$key]['CasMnuBlqDtt'] = FormatData::transformData('OnlyDate', $value['CasMnuBlqDtt']);
            }
            if (isset($records[$key]['CasMnuAudIns'])) {
                $records[$key]['CasMnuAudIns'] = FormatData::transformData('OnlyDate', $value['CasMnuAudIns']);
            }
            if (isset($records[$key]['CasMnuAudUpd'])) {
                $records[$key]['CasMnuAudUpd'] = FormatData::transformData('OnlyDate', $value['CasMnuAudUpd']);
            }
            if (isset($records[$key]['CasMnuAudDlt'])) {
                $records[$key]['CasMnuAudDlt'] = FormatData::transformData('OnlyDate', $value['CasMnuAudDlt']);
            }
            // Add link to form //
            $linkCasMnu = '';
            foreach (CasMnuModel::FIELDS_PK as $pkvalue) {
                // [CasRpsCod] gets from session //
                if ($pkvalue == 'CasRpsCod') {
                    continue;
                }
                $linkCasMnu .= '/' . $records[$key][$pkvalue];
            }
            $records[$key]['Action']['Show'] = $linkCasMnu;
        }

        /**
         * Form Design
         */
        $fields_md = array();
        foreach ($fields as $key => $field) {
            if (in_array($field, CasMnuModel::FIELDS)) {
                $fields_md[$field] = CasMnuModel::FIELDS_MD[$field];
            } else {
                // Foreign Tables //
                foreach (CasMnuModel::FIELDS_FOREIGN as $tableForeign => $valueFieldsForeign) {
                    if (in_array($field, $valueFieldsForeign['FIELDS'])) {
                        $fields_md[$field] = CasMnuModel::FIELDS_MD_FOREIGN[$tableForeign]['FIELDS_MD'][$field];
                    }
                }
            }
        }

        $this->formDesignOnForm('dsp', 0, 'MenuViewList.php', []);
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

        $this->view('SBAdmin/Manager/MenuView', $data);
    }

    public function show($mnu = '')
    {
        $rps = AuthSession::get()['RPS_ID'];

        /**
         * Modos atendidos (TransMode): Insert e Update
         */
        $fields = CasMnuModel::FIELDS;

        $this->dataEntry = $this->getDataInput($fields);
        $this->dataEntry = FormData::secFields($fields, $this->dataEntry);

        if (empty($mnu)) {
            // insert //
            $this->dataEntry[CasMnuModel::FIELDS_PK[0]] = $rps;
            $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[1];
        } else {
            // update //
            $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[2];
        }

        if (isset($_POST['btnMnaShow'])) {
            if (isset($this->dataEntry['CasRpsCod']) && isset($this->dataEntry['CasMnuCod'])) {
                header("Location: /Manager/Menu/Arvore/{$this->dataEntry['CasMnuCod']}");
            }
        }

        if (isset($_POST['btnConfirmInsert']) || isset($_POST['btnConfirmUpdate'])) {
            foreach ($fields as $field) {
                $this->dataEntry[$field];
                $this->obCasMnuModel->$field = $this->dataEntry[$field];
            }

            // insert //
            if (isset($_POST['btnConfirmInsert'])) {
                if ($this->obCasMnuModel->createRegister()) {
                    array_push($this->messages, $this->message->getMessage(0, 'Message', 'Registro inserido com sucesso!'));
                    $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[0];
                    $this->formDesign['FormDisable'] = true;
                    $mnu = $this->obCasMnuModel->CasMnuCod;
                } else {
                    array_push($this->messages, $this->message->getMessage(1, 'Message', 'Erro ao inserir o registro!'));
                }
            }

            // update //
            if (isset($_POST['btnConfirmUpdate'])) {
                $this->obCasMnuModel->CasRpsCod = $this->dataEntry['CasRpsCod'];
                $this->obCasMnuModel->CasMnuCod = $this->dataEntry['CasMnuCod'];
                if ($this->obCasMnuModel->updateRegister()) {
                    array_push($this->messages, $this->message->getMessage(0, 'Message', 'Registro atualizado com sucesso!'));
                    $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[0];
                    $this->formDesign['FormDisable'] = true;
                    $mnu = $this->obCasMnuModel->CasMnuCod;
                } else {
                    array_push($this->messages, $this->message->getMessage(1, 'Message', 'Erro ao atualizar o registro!'));
                }
            }
        }

        if (!empty($mnu)) {
            $this->obCasMnuModel->CasRpsCod = $rps;
            $this->obCasMnuModel->CasMnuCod = $mnu;
            if ($this->obCasMnuModel->readRegister()) {
                $this->dataEntry = $this->obCasMnuModel->getRecords()[0];
            }
        }

        /**
         * Format Data
         */
        $this->formatDataOnForm();

        /**
         * Form Design
         */
        $transMode = (empty($mnu) ? 'ins' : 'upd');
        $this->formDesignOnForm($transMode, 1, 'MenuViewForm.php', [$mnu]);

        /**
         * Adicional Buttons
         */
        // $this->formDesign['Buttons'] = array();
        // if ($transMode == 'upd') {
        //     $this->formDesign['Buttons'] = array(
        //         array('Type' => 'submit', 'Name' => 'btnMnaShow', 'Class' => 'btn btn-info', 'Link' => '#', 'Label' => 'Árvore do Menu'),
        //     );
        // }

        /**
         * Show Screen
         */
        $data = array(
            'FormDesign' => $this->formDesign,
            'FormData' => FormData::secFields($fields, $this->dataEntry),
        );

        $this->view('SBAdmin/Manager/MenuView', $data);
    }

    public function arvore($mnu = '', $mna = '')
    {
        $rps = AuthSession::get()['RPS_ID'];
        $prg = '_blank'; // default //

        /**
         * Modos atendidos (TransMode): Insert e Update
         */
        $fields = CasMnaModel::FIELDS;

        $this->dataEntry = $this->getDataInput($fields);
        $this->dataEntry = FormData::secFields($fields, $this->dataEntry);

        if (empty($mna)) {
            // insert //
            $this->dataEntry[CasMnaModel::FIELDS_PK[0]] = $rps;
            $this->dataEntry[CasMnaModel::FIELDS_PK[1]] = $mnu;
            $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[1];
        } else {
            // update //
            $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[2];
        }

        if (isset($_POST['btnConfirmInsert']) || isset($_POST['btnConfirmUpdate'])) {
            foreach ($fields as $field) {
                $this->dataEntry[$field];
                $this->obCasMnaModel->$field = $this->dataEntry[$field];
            }

            // insert //
            if (isset($_POST['btnConfirmInsert'])) {
                if ($this->obCasMnaModel->createRegister()) {
                    array_push($this->messages, $this->message->getMessage(0, 'Message', 'Registro inserido com sucesso!'));
                    $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[0];
                    $this->formDesign['FormDisable'] = true;
                    $mnu = $this->obCasMnaModel->CasMnuCod;
                    $mna = $this->obCasMnaModel->CasMnaCod;
                } else {
                    array_push($this->messages, $this->message->getMessage(1, 'Message', 'Erro ao inserir o registro!'));
                }
            }

            // update //
            if (isset($_POST['btnConfirmUpdate'])) {
                $this->obCasMnaModel->CasRpsCod = $this->dataEntry['CasRpsCod'];
                $this->obCasMnaModel->CasMnaCod = $this->dataEntry['CasMnaCod'];
                if ($this->obCasMnaModel->updateRegister()) {
                    array_push($this->messages, $this->message->getMessage(0, 'Message', 'Registro atualizado com sucesso!'));
                    $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[0];
                    $this->formDesign['FormDisable'] = true;
                    $mnu = $this->obCasMnaModel->CasMnuCod;
                    $mna = $this->obCasMnaModel->CasMnaCod;
                } else {
                    array_push($this->messages, $this->message->getMessage(1, 'Message', 'Erro ao atualizar o registro!'));
                }
            }
        }

        if (!empty($mna)) {
            $this->obCasMnaModel->CasRpsCod = $rps;
            $this->obCasMnaModel->CasMnuCod = $mnu;
            $this->obCasMnaModel->CasMnaCod = $mna;
            if ($this->obCasMnaModel->readRegister()) {
                $this->dataEntry = $this->obCasMnaModel->getRecords()[0];
            }
        }

        // Menu //
        $dataCasMnuSearch = array();

        $this->obCasMnuModel->setSelectedFields(['CasRpsCod', 'CasMnuCod', 'CasPrgCod', 'CasMnuDsc', 'CasMnuBlq']);
        $this->obCasMnuModel->CasRpsCod = $rps;

        if ($this->obCasMnuModel->readAllLinesJoin(['CasRps'])) {
            $records = $this->obCasMnuModel->getRecords();
            foreach ($records as $record) {
                $record['Selected'] = ($record['CasMnuCod'] == $mnu ? 'selected' : '');
                if ($record['CasMnuBlq'] == 'N') {
                    array_push($dataCasMnuSearch, $record);
                }
            }
        }

        // Programas //
        $dataCasPrgSearch = array();

        $this->obCasPrgModel->setSelectedFields(['CasRpsCod', 'CasPrgCod', 'CasPrgDsc', 'CasPrgBlq', 'CasPrgTst']);
        $this->obCasPrgModel->CasRpsCod = $rps;

        if (! empty($this->dataEntry['CasPrgCod'])) {
            $prg = $this->dataEntry['CasPrgCod'];
        }

        if ($this->obCasPrgModel->readAllLinesJoin(['CasRps'])) {
            $records = $this->obCasPrgModel->getRecords();
            foreach ($records as $record) {
                $record['Selected'] = ($record['CasPrgCod'] == $prg ? 'selected' : '');
                if ($record['CasPrgBlq'] == 'N') {
                    array_push($dataCasPrgSearch, $record);
                }
            }
        }

        // Rows 
        $records_mna = array();
        $this->obCasMnaModel = new CasMnaModel();

        // $fields = CasMnuModel::FIELDS;
        $this->obCasMnaModel->setSelectedFields(['CasMnaCod','CasMnuDsc','CasMnaDsc','CasRpsCod','CasMnuCod']);
        $fields[] = 'CasMnuDsc'; // adicionado para compor o grid //
        $hidden = ['CasRpsCod','CasMnuCod'];
        $this->obCasMnaModel->CasRpsCod = $rps;
        $this->obCasMnaModel->CasMnuCod = $mnu;
        
        if ($this->obCasMnaModel->readAllLinesJoin(['CasRps','CasMnu'])) {
            $records_mna = $this->obCasMnaModel->getRecords();
        }

        /**
         * Format Data
         */
        $this->formatDataOnForm();

        /**
         * Form Design
         */
        $fields_md = array();
        foreach ($fields as $key => $field) {
            if (isset(CasMnaModel::FIELDS_MD[$field])) {
                $fields_md[$field] = CasMnaModel::FIELDS_MD[$field];
            }
            if (isset(CasMnuModel::FIELDS_MD[$field])) {
                $fields_md[$field] = CasMnuModel::FIELDS_MD[$field];
            }
        }

        $transMode = (empty($mna) ? 'ins' : 'upd');
        $this->formDesignOnForm($transMode, 2, 'MenuViewListArvore.php', [$mnu, $mna]);
        $this->formDesign['Styles']['CSSFiles'] = ['dataTables'];
        $this->formDesign['Scripts']['Body'] = ['dataTables'];
        $this->formDesign['Fields'] = $fields_md;
        $this->formDesign['Hidden'] = $hidden;
        // $this->formDesign['FieldsMnuSearch'] = CasMnuModel::FIELDS_MD;
        $this->formDesign['CasMnuSearch'] = $dataCasMnuSearch;
        $this->formDesign['CasPrgSearch'] = $dataCasPrgSearch;
        if (empty($mnu)) {
            $this->formDesign['Tabs']['Items'][2]['Link'] = "/Manager/Menu/Arvore/{$mnu}";
        } else {
            $this->formDesign['Tabs']['Items'][2]['Link'] = str_replace('{mnu}', $mnu, $this->formDesign['Tabs']['Items'][2]['Link']);
            $this->formDesign['Tabs']['Items'][2]['Link'] = str_replace('{mna}', $mna, $this->formDesign['Tabs']['Items'][2]['Link']);
        }

        /**
         * Adicional Buttons
         */
        $this->formDesign['Buttons'] = array();
        if ($this->formDesign['FormDisable']) {
            $this->formDesign['Buttons'] = array(
                array('Type' => 'button', 'Name' => 'btnMnaNew', 'Class' => 'btn btn-secondary', 'Link' => '/Manager/Menu/Arvore/' . $mnu, 'Label' => 'Novo Registro'),
            );
        }

        /**
         * Show Screen
         */
        $data = array(
            'FormDesign' => $this->formDesign,
            'FormData' => FormData::secFields($fields, $this->dataEntry),
            'FormDataRows' => $records_mna,
        );

        $this->view('SBAdmin/Manager/MenuView', $data);
    }

    public function remove($mnu = '', $mna= '')
    {
        $rps = AuthSession::get()['RPS_ID'];
        $prg = '_blank'; // default //

        $this->formDesign['TransMode'] = FormDesign::TRANSACTION_MODE[3];
        $this->formDesign['FormDisable'] = true;

        if (! empty($mnu)) {
            if (! empty($mna)) {
                $fields = CasMnaModel::FIELDS;

                $this->dataEntry = $this->getDataInput($fields);
                $this->dataEntry = FormData::secFields($fields, $this->dataEntry);

                // delete casmna //
                $this->obCasMnaModel->CasRpsCod = $rps;
                $this->obCasMnaModel->CasMnuCod = $mnu;
                $this->obCasMnaModel->CasMnaCod = $mna;
                if (! $this->obCasMnaModel->isEmptyAllReferencedTables()) {
                    array_push($this->messages, $this->message->getMessage(2, 'Message', 'Existem dados relacionados a este registro!'));
                }
                if (isset($_POST['btnConfirmDelete'])) {
                    if ($this->obCasMnaModel->deleteRegister()) {
                        array_push($this->messages, $this->message->getMessage(0, 'Message', 'Registro excluído com sucesso!'));
                        header('Location: /Manager/Menu');
                        exit();
                    } else {
                        array_push($this->messages, $this->message->getMessage(1, 'Message', 'Erro ao excluir o registro!'));
                    }
                } else {
                    $this->obCasMnaModel->CasRpsCod = $rps;
                    $this->obCasMnaModel->CasMnuCod = $mnu;
                    $this->obCasMnaModel->CasMnaCod = $mna;
                    if ($this->obCasMnaModel->readRegister()) {
                        $this->dataEntry = $this->obCasMnaModel->getRecords()[0];
                    }
                }

                // Menu //
                $dataCasMnuSearch = array();

                $this->obCasMnuModel->setSelectedFields(['CasRpsCod', 'CasMnuCod', 'CasPrgCod', 'CasMnuDsc', 'CasMnuBlq']);
                $this->obCasMnuModel->CasRpsCod = $rps;

                if ($this->obCasMnuModel->readAllLinesJoin(['CasRps'])) {
                    $records = $this->obCasMnuModel->getRecords();
                    foreach ($records as $record) {
                        $record['Selected'] = ($record['CasMnuCod'] == $mnu ? 'selected' : '');
                        if ($record['CasMnuBlq'] == 'N') {
                            array_push($dataCasMnuSearch, $record);
                        }
                    }
                }

                // Programas //
                $dataCasPrgSearch = array();

                $this->obCasPrgModel->setSelectedFields(['CasRpsCod', 'CasPrgCod', 'CasPrgDsc', 'CasPrgBlq', 'CasPrgTst']);
                $this->obCasPrgModel->CasRpsCod = $rps;

                if (! empty($this->dataEntry['CasPrgCod'])) {
                    $prg = $this->dataEntry['CasPrgCod'];
                }

                if ($this->obCasPrgModel->readAllLinesJoin(['CasRps'])) {
                    $records = $this->obCasPrgModel->getRecords();
                    foreach ($records as $record) {
                        $record['Selected'] = ($record['CasPrgCod'] == $prg ? 'selected' : '');
                        if ($record['CasPrgBlq'] == 'N') {
                            array_push($dataCasPrgSearch, $record);
                        }
                    }
                }

                // Rows 
                $records_mna = array();
                $this->obCasMnaModel = new CasMnaModel();

                // $fields = CasMnuModel::FIELDS;
                $this->obCasMnaModel->setSelectedFields(['CasMnaCod','CasMnuDsc','CasMnaDsc','CasRpsCod','CasMnuCod']);
                $fields[] = 'CasMnuDsc'; // adicionado para compor o grid //
                $hidden = ['CasRpsCod','CasMnuCod'];
                $this->obCasMnaModel->CasRpsCod = $rps;
                $this->obCasMnaModel->CasMnuCod = $mnu;
                
                if ($this->obCasMnaModel->readAllLinesJoin(['CasRps','CasMnu'])) {
                    $records_mna = $this->obCasMnaModel->getRecords();
                }
            } else {
                $fields = array_merge(CasMnuModel::FIELDS, CasMnuModel::FIELDS_AUDIT);

                // delete casmnu //
                $this->obCasMnuModel->CasRpsCod = $rps;
                $this->obCasMnuModel->CasMnuCod = $mnu;
                if (! $this->obCasMnuModel->isEmptyAllReferencedTables()) {
                    array_push($this->messages, $this->message->getMessage(2, 'Message', 'Existem dados relacionados a este registro!'));
                }
                if (isset($_POST['btnConfirmDelete'])) {
                    if ($this->obCasMnuModel->deleteRegister()) {
                        array_push($this->messages, $this->message->getMessage(0, 'Message', 'Registro excluído com sucesso!'));
                        header('Location: /Manager/Menu');
                        exit();
                    } else {
                        array_push($this->messages, $this->message->getMessage(1, 'Message', 'Erro ao excluir o registro!'));
                    }
                } else {
                    if ($this->obCasMnuModel->readRegister()) {
                        $this->dataEntry = $this->obCasMnuModel->getRecords()[0];
                    }
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
        if (empty($mna)) {
            $this->formDesignOnForm('dlt', 1, 'MenuViewForm.php', [$mnu]);
            $records_mna = array();
        } else {
            $fields_md = array();
            foreach ($fields as $key => $field) {
                if (isset(CasMnaModel::FIELDS_MD[$field])) {
                    $fields_md[$field] = CasMnaModel::FIELDS_MD[$field];
                }
                if (isset(CasMnuModel::FIELDS_MD[$field])) {
                    $fields_md[$field] = CasMnuModel::FIELDS_MD[$field];
                }
            }
            $this->formDesignOnForm('dlt', 2, 'MenuViewListArvore.php', [$mnu, $mna]);
            $this->formDesign['Styles']['CSSFiles'] = ['dataTables'];
            $this->formDesign['Scripts']['Body'] = ['dataTables'];
            $this->formDesign['Fields'] = $fields_md;
            $this->formDesign['Hidden'] = $hidden;
            $this->formDesign['CasMnuSearch'] = $dataCasMnuSearch;
            $this->formDesign['CasPrgSearch'] = $dataCasPrgSearch;
            if (empty($mnu)) {
                $this->formDesign['Tabs']['Items'][2]['Link'] = "/Manager/Menu/Arvore/{$mnu}";
            } else {
                $this->formDesign['Tabs']['Items'][2]['Link'] = str_replace('{mnu}', $mnu, $this->formDesign['Tabs']['Items'][2]['Link']);
                $this->formDesign['Tabs']['Items'][2]['Link'] = str_replace('{mna}', $mna, $this->formDesign['Tabs']['Items'][2]['Link']);
            }
        }

        /**
         * Show Screen
         */
        $data = array(
            'FormDesign' => $this->formDesign,
            'FormData' => FormData::secFields($fields, $this->dataEntry),
            'FormDataRows' => $records_mna,
        );

        $this->view('SBAdmin/Manager/MenuView', $data);
    }

    private function formatDataOnForm()
    {
        if (isset($this->dataEntry['CasMnuBlq'])) {
            $this->dataEntry['CasMnuBlq'] = FormatData::transformSelectionSN($this->dataEntry['CasMnuBlq'], false);
        }
        if (isset($this->dataEntry['CasMnaBlq'])) {
            $this->dataEntry['CasMnaBlq'] = FormatData::transformSelectionSN($this->dataEntry['CasMnaBlq'], false);
        }
    }

    private function formDesignOnForm($transMode, $current, $file, $keys)
    {
        $urlKeys = implode('/', $keys);

        $this->formDesign['Tabs']['Current'] = $current;
        $this->formDesign['Tabs']['LoadFile'] = "App/Views/SBAdmin/Manager/" . $file;
        $this->formDesign['Tabs']['Items'][0]['Link'] = '/Manager/Menu';

        $this->formDesign['Fields'] = CasMnuModel::FIELDS_MD;
        $this->formDesign['TransLinkRemove'] = "/Manager/Menu/Remove/{$urlKeys}";

        switch ($transMode) {
            case 'upd':
                $this->formDesign['Tabs']['Items'][1]['Link'] = "/Manager/Menu/Show/{$urlKeys}";
                $this->formDesign['Tabs']['Items'][2]['Link'] = "/Manager/Menu/Arvore/{$urlKeys}";
                break;

            case 'dlt':
                $this->formDesign['Tabs']['Items'][1]['Link'] = $this->formDesign['TransLinkRemove'];
                $this->formDesign['Tabs']['Items'][2]['Link'] = $this->formDesign['TransLinkRemove'];
                break;

            default:
                $this->formDesign['Tabs']['Items'][1]['Link'] = "/Manager/Menu/Show";
                $this->formDesign['Tabs']['Items'][2]['Link'] = "/Manager/Menu/Arvore/{$urlKeys}";
                break;
        }

        if ($this->messages) {
            $this->formDesign['Message'] = $this->messages[0];
        }
    }
}
