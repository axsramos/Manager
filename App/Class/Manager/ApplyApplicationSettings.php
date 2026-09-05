<?php

namespace App\Class\Manager;

use DateTime;
use App\Models\CAS\CasRpsModel;
use App\Models\CAS\CasAppModel;
use App\Models\CAS\CasFunModel;
use App\Models\CAS\CasMdlModel;
use App\Models\CAS\CasPrgModel;
use App\Models\CAS\CasParModel;
use App\Models\CAS\CasFprModel;
use App\Models\CAS\CasMprModel;
use App\Models\CAS\CasPfiModel;
use App\Models\CAS\CasPfuModel;
use App\Models\CAS\CasRpuModel;
use App\Models\CAS\CasApfModel;
use App\Models\CAS\CasAfuModel;
use App\Models\CAS\CasUsrModel;
use App\Models\CAS\CasMnuModel;
use App\Models\CAS\CasMnaModel;
use App\Models\CAS\CasWksModel;


class ApplyApplicationSettings
{
    use \App\Traits\DataPackage;

    private $recordsCasFun_all = array();
    private $recordsCasPrg_all = array();
    private $recordsCasMdl_all = array();
    private $recordsCasPfi_all = array();
    private $recordsCasUsr_all = array();

    public function run(string $rps_id, string $app_id): array
    {
        $data = $this->getDataPackage($app_id);

        if ($data === []) {
            return [
                'status' => 'warning',
                'message' => 'Pacote de configurações não localizado ou incompatível com o aplicativo.',
            ];
        }

        $baseTableData = array();

        if (! is_null($data)) {
            foreach ($data as $key => $value) {
                if ($key == 'BaseTableData') {
                    $baseTableData = $value;
                    break;
                }
            }

            $obCasRpsModel = new CasRpsModel();
            $obCasRpsModel->CasRpsCod = $rps_id;
            $obCasRpsModel->setSelectedFields(['CasRpsCod']);
            if (! $obCasRpsModel->readRegister()) {
                return [
                    'status' => 'warning',
                    'message' => 'Repositório inválido para aplicação das configurações.',
                ];
            }

            foreach ($baseTableData as $dataTable) {
                foreach ($dataTable as $table => $value) {
                    switch ($table) {
                        case 'CasFun':
                            $this->processCasFun($rps_id, $app_id, $value);
                            break;
                        case 'CasMdl':
                            $this->processCasMdl($rps_id, $app_id, $value);
                            break;
                        case 'CasPrg':
                            $this->processCasPrg($rps_id, $app_id, $value);
                            break;
                        case 'CasPar':
                            $this->processCasPar($rps_id, $app_id, $value);
                            break;
                        case 'CasFpr':
                            $this->processCasFpr($rps_id, $app_id, $value);
                            break;
                        case 'CasMpr':
                            $this->processCasMpr($rps_id, $app_id, $value);
                            break;
                        case 'CasMnu':
                            $this->processCasMnu($rps_id, $app_id, $value);
                            break;
                        case 'CasMna':
                            $this->processCasMna($rps_id, $app_id, $value);
                            break;
                        case 'CasPfi':
                            $this->processCasPfi($rps_id, $app_id, $value);
                            break;
                        case 'CasPfu':
                            $this->processCasPfu($rps_id, $app_id, $value);
                            break;
                        case 'CasApf':
                            $this->processCasApf($rps_id, $app_id, $value);
                            break;
                        case 'CasAfu':
                            $this->processCasAfu($rps_id, $app_id, $value);
                            break;
                        case 'CasWks':
                            $this->processCasWks($rps_id, $app_id, $value);
                            break;
                    }
                }
            }

            $this->updateStatusProgress($rps_id, $app_id, 'UPDATED');
        }

        return [
            'status' => 'applied',
            'message' => 'Configurações do aplicativo aplicadas.',
        ];
    }

    private function processCasFun(string $rps_id, string $app_id, array $data): void
    {
        $inserted = 0;
        $erros = 0;

        foreach ($data as $value) {
            $isValid = true;

            foreach (CasFunModel::FIELDS_REQUIRED as $fieldRequired) {
                if (in_array($fieldRequired, ['CasFunBlq'])) {
                    continue; // not required, will apply default value //
                }
                if (!isset($value[$fieldRequired])) {
                    $isValid = false;
                    break;
                }
            }
            if ($isValid) {
                $obCasFunModel = new CasFunModel();
                $obCasFunModel->CasRpsCod = $rps_id;
                $obCasFunModel->CasFunCod = ''; // randomic when empty //
                foreach (CasFunModel::FIELDS as $field) {
                    if (in_array($field, ['CasRpsCod'])) {
                        continue;
                    }
                    if (isset($value[$field])) {
                        $obCasFunModel->$field = $value[$field];
                    }
                }
                if (! $obCasFunModel->readRegister()) {
                    $result = $obCasFunModel->createRegister();
                    if ($result) {
                        $inserted ++;
                    } else {
                        $erros ++;
                    }
                }
            }
        }

        $this->updateStatusProgress($rps_id, $app_id, 'TASK-0001', array('Inserted' => $inserted, 'Table' => 'CasFun', 'Errors' => $erros));
    }

    private function processCasMdl(string $rps_id, string $app_id, array $data): void
    {
        $inserted = 0;
        $erros = 0;

        foreach ($data as $value) {
            $isValid = true;
            foreach (CasMdlModel::FIELDS_REQUIRED as $fieldRequired) {
                if (in_array($fieldRequired, ['CasMdlBlq'])) {
                    continue; // not required, will apply default value //
                }
                if (!isset($value[$fieldRequired])) {
                    $isValid = false;
                    break;
                }
            }
            if ($isValid) {
                $obCasMdlModel = new CasMdlModel();
                $obCasMdlModel->CasRpsCod = $rps_id;
                $obCasMdlModel->CasMdlCod = ''; // randomic when empty //
                foreach (CasMdlModel::FIELDS as $field) {
                    if (in_array($field, ['CasRpsCod'])) {
                        continue;
                    }
                    if (isset($value[$field])) {
                        $obCasMdlModel->$field = $value[$field];
                    }
                }
                if (! $obCasMdlModel->readRegister()) {
                    $result = $obCasMdlModel->createRegister();
                    if ($result) {
                        $inserted ++;
                    } else {
                        $erros ++;
                    }
                }
            }
        }

        $this->updateStatusProgress($rps_id, $app_id, 'TASK-0002', array('Inserted' => $inserted, 'Table' => 'CasMdl', 'Errors' => $erros));
    }

    private function processCasPrg(string $rps_id, string $app_id, array $data): void
    {
        $inserted = 0;
        $erros = 0;

        foreach ($data as $value) {
            $isValid = true;
            foreach (CasPrgModel::FIELDS_REQUIRED as $fieldRequired) {
                if (in_array($fieldRequired, ['CasPrgBlq', 'CasPrgTst'])) {
                    continue; // not required, will apply default value //
                }
                if (!isset($value[$fieldRequired])) {
                    $isValid = false;
                    break;
                }
            }
            if ($isValid) {
                $obCasPrgModel = new CasPrgModel();
                $obCasPrgModel->CasRpsCod = $rps_id;
                $obCasPrgModel->CasPrgCod = ''; // randomic when empty //
                foreach (CasPrgModel::FIELDS as $field) {
                    if (in_array($field, ['CasRpsCod'])) {
                        continue;
                    }
                    if (isset($value[$field])) {
                        $obCasPrgModel->$field = $value[$field];
                    }
                }
                if (! $obCasPrgModel->readRegister()) {
                    $result = $obCasPrgModel->createRegister();
                    if ($result) {
                        $inserted ++;
                    } else {
                        $erros ++;
                    }
                }
            }
        }

        $this->updateStatusProgress($rps_id, $app_id, 'TASK-0003', array('Inserted' => $inserted, 'Table' => 'CasPrg', 'Errors' => $erros));
    }

    private function processCasPar(string $rps_id, string $app_id, array $data): void 
    {
        $inserted = 0;
        $erros = 0;

        foreach ($data as $value) {
            $isValid = true;
            foreach (CasParModel::FIELDS_REQUIRED as $fieldRequired) {
                if (in_array($fieldRequired, ['CasParBlq'])) {
                    continue; // not required, will apply default value //
                }
                if (!isset($value[$fieldRequired])) {
                    $isValid = false;
                    break;
                }
            }
            if ($isValid) {
                $obCasParModel = new CasParModel();
                $obCasParModel->CasRpsCod = $rps_id;
                $obCasParModel->CasParCod = ''; // randomic when empty //
                foreach (CasParModel::FIELDS as $field) {
                    if (in_array($field, ['CasRpsCod'])) {
                        continue;
                    }
                    if (isset($value[$field])) {
                        $obCasParModel->$field = $value[$field];
                    }
                }
                if (! $obCasParModel->readRegister()) {
                    $result = $obCasParModel->createRegister();
                    if ($result) {
                        $inserted ++;
                    } else {
                        $erros ++;
                    }
                }
            }
        }

        $this->updateStatusProgress($rps_id, $app_id, 'TASK-0004', array('Inserted' => $inserted, 'Table' => 'CasPar', 'Errors' => $erros));
    }

    private function processCasFpr(string $rps_id, string $app_id, array $data): void
    {
        $inserted = 0;
        $erros = 0;

        // get all CasFun, CasPrg records //
        $this->getAllCasFun($rps_id);
        $this->getAllCasPrg($rps_id);

        if (empty($this->recordsCasFun_all) && empty($this->recordsCasPrg_all)) {
            return; // not data for process //
        }

        foreach ($data as $value) {
            $isValid = true;
            $recordsCasFun = array();
            $recordsCasPrg = array();

            foreach (CasFprModel::FIELDS_REQUIRED as $fieldRequired) {
                // if (in_array($fieldRequired, [])) {
                //     continue; // not required, will apply default value //
                // }
                if (!isset($value[$fieldRequired])) {
                    $isValid = false;
                    break;
                }
            }
            if ($isValid) {
                if (!isset($value['CasFunCod']) || !isset($value['CasPrgCod'])) {
                    $isValid = false;
                }
            }
            if ($isValid) {
                // get CasFun records //
                $recordsCasFun = (empty($value['CasFunCod']) ? $this->recordsCasFun_all : [$value['CasFunCod']]);
                if (! in_array($recordsCasFun[0], $this->recordsCasFun_all)) {
                    continue; // value in file is invalid, not data for process //
                }
                
                // get CasPrg records //
                $recordsCasPrg = (empty($value['CasPrgCod']) ? $this->recordsCasPrg_all : [$value['CasPrgCod']]);
                if (! in_array($recordsCasPrg[0], $this->recordsCasPrg_all)) {
                    continue; // value in file is invalid, not data for process //
                }

                // create CasFpr records //
                foreach ($recordsCasFun as $itemCasFun) {
                    foreach ($recordsCasPrg as $itemCasPrg) {
                        $obCasFprModel = new CasFprModel();
                        $obCasFprModel->CasRpsCod = $rps_id;
                        $obCasFprModel->CasFunCod = $itemCasFun;
                        $obCasFprModel->CasPrgCod = $itemCasPrg;
                        foreach (CasFprModel::FIELDS as $field) {
                            if (in_array($field, ['CasRpsCod', 'CasFunCod', 'CasPrgCod'])) {
                                continue;
                            }
                            if (isset($value[$field])) {
                                $obCasFprModel->$field = $value[$field];
                            }
                        }
                        if (! $obCasFprModel->readRegister()) {
                            $result = $obCasFprModel->createRegister();
                            if ($result) {
                                $inserted ++;
                            } else {
                                $erros ++;
                            }
                        }
                    }
                }
            }
        }

        $this->updateStatusProgress($rps_id, $app_id, 'TASK-0005', array('Inserted' => $inserted, 'Table' => 'CasFpr', 'Errors' => $erros));
    }
    
    private function processCasMpr(string $rps_id, string $app_id, array $data): void
    {
        $inserted = 0;
        $erros = 0;

        // get all CasFun, CasPrg records //
        $this->getAllCasMdl($rps_id);
        $this->getAllCasPrg($rps_id);

        if (empty($this->recordsCasMdl_all) && empty($this->recordsCasPrg_all)) {
            return; // not data for process //
        }

        foreach ($data as $value) {
            $isValid = true;
            $recordsCasMdl = array();
            $recordsCasPrg = array();

            foreach (CasMprModel::FIELDS_REQUIRED as $fieldRequired) {
                // if (in_array($fieldRequired, [])) {
                //     continue; // not required, will apply default value //
                // }
                if (!isset($value[$fieldRequired])) {
                    $isValid = false;
                    break;
                }
            }
            if ($isValid) {
                if (!isset($value['CasMdlCod']) || !isset($value['CasPrgCod'])) {
                    $isValid = false;
                }
            }
            if ($isValid) {
                // get CasMdl records //
                $recordsCasMdl = (empty($value['CasMdlCod']) ? $this->recordsCasMdl_all : [$value['CasMdlCod']]);
                if (! in_array($recordsCasMdl[0], $this->recordsCasMdl_all)) {
                    continue; // value in file is invalid, not data for process //
                }

                // get CasPrg records //
                $recordsCasPrg = (empty($value['CasPrgCod']) ? $this->recordsCasPrg_all : [$value['CasPrgCod']]);
                if (! in_array($recordsCasPrg[0], $this->recordsCasPrg_all)) {
                    continue; // value in file is invalid, not data for process //
                }

                // create CasMpr records //
                foreach ($recordsCasMdl as $itemCasMdl) {
                    foreach ($recordsCasPrg as $itemCasPrg) {
                        $obCasMprModel = new CasMprModel();
                        $obCasMprModel->CasRpsCod = $rps_id;
                        $obCasMprModel->CasMdlCod = $itemCasMdl;
                        $obCasMprModel->CasPrgCod = $itemCasPrg;
                        foreach (CasMprModel::FIELDS as $field) {
                            if (in_array($field, ['CasRpsCod', 'CasMdlCod', 'CasPrgCod'])) {
                                continue;
                            }
                            if (isset($value[$field])) {
                                $obCasMprModel->$field = $value[$field];
                            }
                        }
                        if (! $obCasMprModel->readRegister()) {
                            $result = $obCasMprModel->createRegister();
                            if ($result) {
                                $inserted ++;
                            } else {
                                $erros ++;
                            }
                        }
                    }
                }
            }
        }

        $this->updateStatusProgress($rps_id, $app_id, 'TASK-0006', array('Inserted' => $inserted, 'Table' => 'CasMpr', 'Errors' => $erros));
    }

    private function processCasMnu(string $rps_id, string $app_id, array $data): void
    {
        $inserted = 0;
        $erros = 0;

        foreach ($data as $value) {
            $isValid = true;
            foreach (CasMnuModel::FIELDS_REQUIRED as $fieldRequired) {
                if (in_array($fieldRequired, ['CasMnuBlq'])) {
                    continue; // not required, will apply default value //
                }
                if (!isset($value[$fieldRequired])) {
                    $isValid = false;
                    break;
                }
            }
            if ($isValid) {
                $obCasMnuModel = new CasMnuModel();
                $obCasMnuModel->CasRpsCod = $rps_id;
                $obCasMnuModel->CasMnuCod = ''; // randomic when empty //
                foreach (CasMnuModel::FIELDS as $field) {
                    if (in_array($field, ['CasRpsCod'])) {
                        continue;
                    }
                    if (isset($value[$field])) {
                        $obCasMnuModel->$field = $value[$field];
                    }
                }
                if (! $obCasMnuModel->readRegister()) {
                    $result = $obCasMnuModel->createRegister();
                    if ($result) {
                        $inserted ++;
                    } else {
                        $erros ++;
                    }
                }
            }
        }

        $this->updateStatusProgress($rps_id, $app_id, 'TASK-0007', array('Inserted' => $inserted, 'Table' => 'CasMnu', 'Errors' => $erros));
    }
    
    private function processCasMna(string $rps_id, string $app_id, array $data): void 
    {
        $inserted = 0;
        $erros = 0;

        foreach ($data as $value) {
            $isValid = true;
            foreach (CasMnaModel::FIELDS_REQUIRED as $fieldRequired) {
                if (in_array($fieldRequired, ['CasMnaBlq'])) {
                    continue; // not required, will apply default value //
                }
                if (!isset($value[$fieldRequired])) {
                    $isValid = false;
                    break;
                }
            }
            if ($isValid) {
                $obCasMnaModel = new CasMnaModel();
                $obCasMnaModel->CasRpsCod = $rps_id;
                $obCasMnaModel->CasMnaCod = ''; // randomic when empty //
                foreach (CasMnaModel::FIELDS as $field) {
                    if (in_array($field, ['CasRpsCod'])) {
                        continue;
                    }
                    if (isset($value[$field])) {
                        $obCasMnaModel->$field = $value[$field];
                    }
                }
                if (! $obCasMnaModel->readRegister()) {
                    $result = $obCasMnaModel->createRegister();
                    if ($result) {
                        $inserted ++;
                    } else {
                        $erros ++;
                    }
                }
            }
        }

        $this->updateStatusProgress($rps_id, $app_id, 'TASK-0008', array('Inserted' => $inserted, 'Table' => 'CasMna', 'Errors' => $erros));
    }
    
    private function processCasPfi(string $rps_id, string $app_id, array $data): void
    {
        $inserted = 0;
        $erros = 0;

        foreach ($data as $value) {
            $isValid = true;
            foreach (CasPfiModel::FIELDS_REQUIRED as $fieldRequired) {
                if (in_array($fieldRequired, ['CasPfiBlq'])) {
                    continue; // not required, will apply default value //
                }
                if (!isset($value[$fieldRequired])) {
                    $isValid = false;
                    break;
                }
            }
            if ($isValid) {
                $obCasPfiModel = new CasPfiModel();
                $obCasPfiModel->CasRpsCod = $rps_id;
                $obCasPfiModel->CasPfiCod = ''; // randomic when empty //
                foreach (CasPfiModel::FIELDS as $field) {
                    if (in_array($field, ['CasRpsCod'])) {
                        continue;
                    }
                    if (isset($value[$field])) {
                        $obCasPfiModel->$field = $value[$field];
                    }
                }
                if (! $obCasPfiModel->readRegister()) {
                    $result = $obCasPfiModel->createRegister();
                    if ($result) {
                        $inserted ++;
                    } else {
                        $erros ++;
                    }
                }
            }
        }

        $this->updateStatusProgress($rps_id, $app_id, 'TASK-0009', array('Inserted' => $inserted, 'Table' => 'CasPfi', 'Errors' => $erros));
    }

    private function processCasPfu(string $rps_id, string $app_id, array $data): void
    {
        $inserted = 0;
        $erros = 0;

        // get all CasPfi, CasUsr records //
        $this->getAllCasPfi($rps_id);
        $this->getAllCasUsr($rps_id);

        if (empty($this->recordsCasPfi_all) && empty($this->recordsCasUsr_all)) {
            return; // not data for process //
        }

        foreach ($data as $value) {
            $isValid = true;
            $recordsCasPfi = array();
            $recordsCasUsr = array();

            foreach (CasPfuModel::FIELDS_REQUIRED as $fieldRequired) {
                // if (in_array($fieldRequired, [])) {
                //     continue; // not required, will apply default value //
                // }
                if (!isset($value[$fieldRequired])) {
                    $isValid = false;
                    break;
                }
            }
            if ($isValid) {
                if (!isset($value['CasPfiCod']) || !isset($value['CasUsrCod'])) {
                    $isValid = false;
                }
            }
            if ($isValid) {
                // get CasPfi records //
                $recordsCasPfi = (empty($value['CasPfiCod']) ? $this->recordsCasPfi_all : [$value['CasPfiCod']]);
                if (! in_array($recordsCasPfi[0], $this->recordsCasPfi_all)) {
                    continue; // value in file is invalid, not data for process //
                }

                // get CasUsr records //
                $recordsCasUsr = (empty($value['CasUsrCod']) ? $this->recordsCasUsr_all : [$value['CasUsrCod']]);
                if (! in_array($recordsCasUsr[0], $this->recordsCasUsr_all)) {
                    continue; // value in file is invalid, not data for process //
                }

                // create CasPfu records //
                foreach ($recordsCasUsr as $itemCasUsr) {
                    foreach ($recordsCasPfi as $itemCasPfi) {
                        $obCasPfuModel = new CasPfuModel();
                        $obCasPfuModel->CasRpsCod = $rps_id;
                        $obCasPfuModel->CasPfiCod = $itemCasPfi;
                        $obCasPfuModel->CasUsrCod = $itemCasUsr;
                        foreach (CasPfuModel::FIELDS as $field) {
                            if (in_array($field, ['CasRpsCod', 'CasPfiCod', 'CasUsrCod'])) {
                                continue;
                            }
                            if (isset($value[$field])) {
                                $obCasPfuModel->$field = $value[$field];
                            }
                        }
                        if (! $obCasPfuModel->readRegister()) {
                            $result = $obCasPfuModel->createRegister();
                            if ($result) {
                                $inserted ++;
                            } else {
                                $erros ++;
                            }
                        }
                    }
                }
            }
        }

        $this->updateStatusProgress($rps_id, $app_id, 'TASK-0010', array('Inserted' => $inserted, 'Table' => 'CasPfu', 'Errors' => $erros));
    }

    private function processCasApf(string $rps_id, string $app_id, array $data): void
    {
        $inserted = 0;
        $erros = 0;

        // get all CasPfi, CasUsr, CasPrg records //
        $this->getAllCasPfi($rps_id);
        $this->getAllCasUsr($rps_id);
        $this->getAllCasPrg($rps_id);

        if (empty($this->recordsCasPfi_all) && empty($this->recordsCasUsr_all) && empty($this->recordsCasPrg_all)) {
            return; // not data for process //
        }

        foreach ($data as $value) {
            $isValid = true;
            $recordsCasPfi = array();
            $recordsCasUsr = array();
            $recordsCasPrg = array();

            foreach (CasApfModel::FIELDS_REQUIRED as $fieldRequired) {
                // if (in_array($fieldRequired, [])) {
                //     continue; // not required, will apply default value //
                // }
                if (!isset($value[$fieldRequired])) {
                    $isValid = false;
                    break;
                }
            }
            if ($isValid) {
                if (!isset($value['CasPfiCod']) || !isset($value['CasUsrCod']) || !isset($value['CasPrgCod'])) {
                    $isValid = false;
                }
            }
            if ($isValid) {
                // get CasPfi records //
                $recordsCasPfi = (empty($value['CasPfiCod']) ? $this->recordsCasPfi_all : [$value['CasPfiCod']]);
                if (! in_array($recordsCasPfi[0], $this->recordsCasPfi_all)) {
                    continue; // value in file is invalid, not data for process //
                }
                
                // get CasUsr records //
                $recordsCasUsr = (empty($value['CasUsrCod']) ? $this->recordsCasUsr_all : [$value['CasUsrCod']]);
                if (! in_array($recordsCasUsr[0], $this->recordsCasUsr_all)) {
                    continue; // value in file is invalid, not data for process //
                }

                // get CasPrg records //
                $recordsCasPrg = (empty($value['CasPrgCod']) ? $this->recordsCasPrg_all : [$value['CasPrgCod']]);
                if (! in_array($recordsCasPrg[0], $this->recordsCasPrg_all)) {
                    continue; // value in file is invalid, not data for process //
                }

                // create CasApf records //
                foreach ($recordsCasUsr as $itemCasUsr) {
                    foreach ($recordsCasPfi as $itemCasPfi) {
                        foreach ($recordsCasPrg as $itemCasPrg) {
                            // check exists in CasPfu //
                            $obCasPfuModel = new CasPfuModel();
                            $obCasPfuModel->setSelectedFields(['CasRpsCod', 'CasPfiCod', 'CasUsrCod']);
                            $obCasPfuModel->CasRpsCod = $rps_id;
                            $obCasPfuModel->CasPfiCod = $itemCasPfi;
                            $obCasPfuModel->CasUsrCod = $itemCasUsr;
                            if ($obCasPfuModel->readRegister()) {
                                // insert CasApf //
                                $obCasApfModel = new CasApfModel();
                                $obCasApfModel->CasRpsCod = $rps_id;
                                $obCasApfModel->CasPfiCod = $itemCasPfi;
                                $obCasApfModel->CasUsrCod = $itemCasUsr;
                                $obCasApfModel->CasPrgCod = $itemCasPrg;
                                foreach (CasApfModel::FIELDS as $field) {
                                    if (in_array($field, ['CasRpsCod', 'CasPfiCod', 'CasUsrCod', 'CasPrgCod'])) {
                                        continue;
                                    }
                                    if (isset($value[$field])) {
                                        $obCasApfModel->$field = $value[$field];
                                    }
                                }
                                if (! $obCasApfModel->readRegister()) {
                                    $result = $obCasApfModel->createRegister();
                                    if ($result) {
                                        $inserted ++;
                                    } else {
                                        $erros ++;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $this->updateStatusProgress($rps_id, $app_id, 'TASK-0011', array('Inserted' => $inserted, 'Table' => 'CasApf', 'Errors' => $erros));
    }

    private function processCasAfu(string $rps_id, string $app_id, array $data): void
    {
        $inserted = 0;
        $erros = 0;

        // get all CasPfi, CasUsr, CasPrg, CasFun records //
        $this->getAllCasPfi($rps_id);
        $this->getAllCasUsr($rps_id);
        $this->getAllCasPrg($rps_id);
        $this->getAllCasFun($rps_id);

        // process CasAfu records //
        foreach ($data as $value) {
            $isValid = true;
            $recordsCasPfi = array();
            $recordsCasUsr = array();
            $recordsCasPrg = array();
            $recordsCasFun = array();

            foreach (CasApfModel::FIELDS_REQUIRED as $fieldRequired) {
                // if (in_array($fieldRequired, [])) {
                //     continue; // not required, will apply default value //
                // }
                if (!isset($value[$fieldRequired])) {
                    $isValid = false;
                    break;
                }
            }
            if ($isValid) {
                if (!isset($value['CasPfiCod']) || !isset($value['CasUsrCod']) || !isset($value['CasPrgCod']) || !isset($value['CasFunCod'])) {
                    $isValid = false;
                }
            }
            if ($isValid) {
                $recordsCasPfi = (empty($value['CasPfiCod']) ? $this->recordsCasPfi_all : [$value['CasPfiCod']]);
                $recordsCasPrg = (empty($value['CasPrgCod']) ? $this->recordsCasPrg_all : [$value['CasPrgCod']]);
                $recordsCasFun = (empty($value['CasFunCod']) ? $this->recordsCasFun_all : [$value['CasFunCod']]);
                $recordsCasUsr = (empty($value['CasUsrCod']) ? $this->recordsCasUsr_all : [$value['CasUsrCod']]);

                // create CasAfu records //
                foreach ($recordsCasFun as $itemCasFun) {
                    foreach ($recordsCasPfi as $itemCasPfi) {
                        foreach ($recordsCasPrg as $itemCasPrg) {
                            foreach ($recordsCasUsr as $key => $itemCasUsr) {
                                // check exists in CasApf //
                                $obCasApfModel = new CasApfModel();
                                $obCasApfModel->setSelectedFields(['CasRpsCod', 'CasPfiCod', 'CasUsrCod', 'CasPrgCod']);
                                $obCasApfModel->CasRpsCod = $rps_id;
                                $obCasApfModel->CasPfiCod = $itemCasPfi;
                                $obCasApfModel->CasUsrCod = $itemCasUsr;
                                $obCasApfModel->CasPrgCod = $itemCasPrg;
                                if ($obCasApfModel->readRegister()) {
                                    // insert CasAfu //
                                    $obCasAfuModel = new CasAfuModel();
                                    $obCasAfuModel->CasRpsCod = $rps_id;
                                    $obCasAfuModel->CasPfiCod = $itemCasPfi;
                                    $obCasAfuModel->CasUsrCod = $itemCasUsr;
                                    $obCasAfuModel->CasPrgCod = $itemCasPrg;
                                    $obCasAfuModel->CasFunCod = $itemCasFun;
                                    foreach (CasAfuModel::FIELDS as $field) {
                                        if (in_array($field, ['CasRpsCod', 'CasPfiCod', 'CasUsrCod', 'CasPrgCod', 'CasFunCod'])) {
                                            continue;
                                        }
                                        if (isset($value[$field])) {
                                            $obCasAfuModel->$field = $value[$field];
                                        }
                                    }
                                    if (! $obCasAfuModel->readRegister()) {
                                        $result = $obCasAfuModel->createRegister();
                                        if ($result) {
                                            $inserted ++;
                                        } else {
                                            $erros ++;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $this->updateStatusProgress($rps_id, $app_id, 'TASK-0012', array('Inserted' => $inserted, 'Table' => 'CasAfu', 'Errors' => $erros));
    }

    private function processCasWks(string $rps_id, string $app_id, array $data): void 
    {
        $inserted = 0;
        $erros = 0;

        foreach ($data as $value) {
            $isValid = true;
            foreach (CasWksModel::FIELDS_REQUIRED as $fieldRequired) {
                if (in_array($fieldRequired, ['CasWksBlq'])) {
                    continue; // not required, will apply default value //
                }
                if (!isset($value[$fieldRequired])) {
                    $isValid = false;
                    break;
                }
            }
            if ($isValid) {
                $obCasWksModel = new CasWksModel();
                $obCasWksModel->CasRpsCod = $rps_id;
                $obCasWksModel->CasWksCod = ''; // randomic when empty //
                foreach (CasWksModel::FIELDS as $field) {
                    if (in_array($field, ['CasRpsCod'])) {
                        continue;
                    }
                    if (isset($value[$field])) {
                        $obCasWksModel->$field = $value[$field];
                    }
                }
                if (! $obCasWksModel->readRegister()) {
                    $result = $obCasWksModel->createRegister();
                    if ($result) {
                        $inserted ++;
                    } else {
                        $erros ++;
                    }
                }
            }
        }

        $this->updateStatusProgress($rps_id, $app_id, 'TASK-0013', array('Inserted' => $inserted, 'Table' => 'CasWks', 'Errors' => $erros));
    }

    private function getAllCasFun(string $rps_id): void
    {
        if (! empty($this->recordsCasFun_all)) {
            return;
        }

        $obCasFunModel = new CasFunModel();
        $obCasFunModel->setSelectedFields(['CasRpsCod', 'CasFunCod']);
        $obCasFunModel->CasRpsCod = $rps_id;
        if ($obCasFunModel->readAllLinesJoin(['CasRps'])) {
            foreach ($obCasFunModel->getRecords() as $item) {
                $this->recordsCasFun_all[] = $item['CasFunCod'];
            }
        }
    }

    private function getAllCasPrg(string $rps_id): void
    {
        if (! empty($this->recordsCasPrg_all)) {
            return;
        }

        $obCasPrgModel = new CasPrgModel();
        $obCasPrgModel->setSelectedFields(['CasRpsCod', 'CasPrgCod']);
        $obCasPrgModel->CasRpsCod = $rps_id;
        if ($obCasPrgModel->readAllLinesJoin(['CasRps'])) {
            foreach ($obCasPrgModel->getRecords() as $item) {
                $this->recordsCasPrg_all[] = $item['CasPrgCod'];
            }
        }
    }

    private function getAllCasMdl(string $rps_id): void
    {
        if (! empty($this->recordsCasMdl_all)) {
            return;
        }

        $obCasMdlModel = new CasMdlModel();
        $obCasMdlModel->setSelectedFields(['CasRpsCod', 'CasMdlCod']);
        $obCasMdlModel->CasRpsCod = $rps_id;
        if ($obCasMdlModel->readAllLinesJoin(['CasRps'])) {
            foreach ($obCasMdlModel->getRecords() as $item) {
                $this->recordsCasMdl_all[] = $item['CasMdlCod'];
            }
        }
    }

    private function getAllCasPfi(string $rps_id): void
    {
        if (! empty($this->recordsCasPfi_all)) {
            return;
        }

        $obCasPfiModel = new CasPfiModel();
        $obCasPfiModel->setSelectedFields(['CasRpsCod', 'CasPfiCod']);
        $obCasPfiModel->CasRpsCod = $rps_id;
        if ($obCasPfiModel->readAllLinesJoin(['CasRps'])) {
            foreach ($obCasPfiModel->getRecords() as $item) {
                $this->recordsCasPfi_all[] = $item['CasPfiCod'];
            }
        }
    }

    private function getAllCasUsr(string $rps_id): void
    {
        if (! empty($this->recordsCasUsr_all)) {
            return;
        }

        $obCasRpuModel = new CasRpuModel();
        $obCasRpuModel->setSelectedFields(['CasRpsCod', 'CasUsrCod']);
        $obCasRpuModel->CasRpsCod = $rps_id;
        if ($obCasRpuModel->readAllLinesJoin(['CasRps'])) {
            foreach ($obCasRpuModel->getRecords() as $item) {
                $this->recordsCasUsr_all[] = $item['CasUsrCod'];
            }
        }
    }

    private function updateStatusProgress(string $rps_id, string $app_id, string $step, array $dataContent = []): void
    {
        $dtnow = new DateTime('now');
        $dtnow_str = $dtnow->format('Y-m-d H:i:s');
        
        $dataContent['LastUpdated'] = $dtnow_str;

        if ($step !== 'UPDATED') {
            $dataContent['Step'] = $step;
        }

        $obCasParModel = new CasParModel();
        $obCasParModel->CasRpsCod = $rps_id;
        $obCasParModel->CasParCod = substr($app_id, 0, 36) . '-' . $step;
        $obCasParModel->CasParTbl = '_blank';
        if ($obCasParModel->readRegister()) {
            // replace //
            $obCasParModel->CasParTxt = json_encode($dataContent);
            $obCasParModel->updateRegister();
        } else {
            // created //
            $obCasParModel->CasParDsc = $step;
            $obCasParModel->CasParGrp = $app_id;
            $obCasParModel->CasParTxt = json_encode($dataContent);
            $obCasParModel->createRegister();
        }
    }
}
