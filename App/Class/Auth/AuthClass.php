<?php

namespace App\Class\Auth;

use App\Shared\MessageDictionary;
use App\Core\AuthSession;
use App\Core\Config;
use App\Models\CAS\CasAfuModel;
use App\Models\CAS\CasFunModel;
use App\Models\CAS\CasFprModel;
use App\Models\CAS\CasSwnModel;
use App\Models\CAS\CasSwpModel;
use App\Models\CAS\CasPrgModel;
use App\Models\CAS\CasMprModel;
use App\Models\CAS\CasMdlModel;
use App\Models\CAS\CasPfuModel;
use App\Models\CAS\CasPfiModel;
use App\Models\CAS\CasApfModel;

class AuthClass
{
    protected $message;

    use \App\Traits\LogToFile;

    public function __construct()
    {
        $this->message = new MessageDictionary();
    }

    public function logout(): void
    {
        AuthSession::logout();
    }

    protected function setSessionAuth(object $user_account): void
    {
        foreach ($user_account as $key => $value) {
            AuthSession::set($key, $value);
        }
    }

    protected function newTokenPassword(): string
    {
        $token = strval(random_int(100000, 999999));

        return trim($token);
    }

    public function validatePasswordRule(string $password): array
    {
        $rulesPassword = Config::getRulesPassword();

        $library_alfa_upper = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $library_alfa_lower = 'abcdefghijklmnopqrstuvwxyz';
        $library_numbers = '0123456789';
        $library_specialchars = '@!#$%&*()-_=+[]{}\|;:<>?/,.^~`';
        $quant_chars = strlen(trim($password));

        /**
         * Check Rules Password
         */
        $next = false;

        if ($quant_chars >= $rulesPassword['MinimumLength']) {
            $next = true;
        }

        if ($next) {
            if ($rulesPassword['RequireUppercase']) {
                $next = false;
                for ($i = 0; $i < $quant_chars; $i++) {
                    if (str_contains($library_alfa_upper, $password[$i])) {
                        $next = true;
                        break;
                    }
                }
            }
        }

        if ($next) {
            if ($rulesPassword['RequireLowercase']) {
                $next = false;
                for ($i = 0; $i < $quant_chars; $i++) {
                    if (str_contains($library_alfa_lower, $password[$i])) {
                        $next = true;
                        break;
                    }
                }
            }
        }

        if ($next) {
            if ($rulesPassword['RequireNumbers']) {
                $next = false;
                for ($i = 0; $i < $quant_chars; $i++) {
                    if (str_contains($library_numbers, $password[$i])) {
                        $next = true;
                        break;
                    }
                }
            }
        }

        if ($next) {
            if ($rulesPassword['RequireSimbols']) {
                $next = false;
                for ($i = 0; $i < $quant_chars; $i++) {
                    if (str_contains($library_specialchars, $password[$i])) {
                        $next = true;
                        break;
                    }
                }
            }
        }

        if ($next) {
            $result = $this->message->getMessage(0);
        } else {
            $msg = "Senha: Mímino {$rulesPassword['MinimumLength']} dígitos. Verifique as regras: ";
            if ($rulesPassword['RequireUppercase']) {
                $msg .= " letras maiúsculas; ";
            }
            if ($rulesPassword['RequireLowercase']) {
                $msg .= " letras minúsculas; ";
            }
            if ($rulesPassword['RequireNumbers']) {
                $msg .= " números; ";
            }
            if ($rulesPassword['RequireSimbols']) {
                $msg .= " caracteres especiais;";
            }
            $result = $this->message->getMessage(1, 'Message', $msg);

            /**
             * Log Error
             */
            $logData = array('code' => $result['Code'], 'description' => $result['Description']);
            self::setLog(json_encode($logData), 'register_error', 'Auth');
        }

        return $result;
    }

    public function permissions(string $prg_id, bool $cached = true): array
    {
        $dataPermissions = [];
        $dataFuncionalities = [];

        if (AuthSession::get()['EXPIRES'] < time()) {
            AuthSession::logout();
            return $dataPermissions; // session expired //
        }

        // debug start //
        // return ['AUTHORIZED'];
        // debug end //
        
        // todo: obter permissöes do cache //
        if ($cached) {
            if (isset(AuthSession::get()['AUTHORIZATION']['PROGRAMS'][$prg_id]['FUNCIONALITIES'])) {
                $dataPermissions = AuthSession::get()['AUTHORIZATION']['PROGRAMS'][$prg_id]['FUNCIONALITIES'];
            }
        }

        if (empty($dataPermissions)) {
            $dataPermissions = $this->funcionalities($prg_id);
            $authorization = AuthSession::get()['AUTHORIZATION'];
            $authorization['PROGRAMS'][$prg_id]['FUNCIONALITIES'] = $dataPermissions;
            AuthSession::set('AUTHORIZATION', $authorization);
        }
        
        return $dataPermissions;
    }

    public function authorizations(string $prg_id): array
    {
        $dataPermissions = [];
        $dataPerfils = [];
        $authorizedProgram = false;

        $obCasPfuModel = new CasPfuModel();
        $obCasPfuModel->setSelectedFields(['CasRpsCod', 'CasPfiCod', 'CasUsrCod', 'CasPfiBlq']);
        $obCasPfuModel->CasRpsCod = AuthSession::get()['RPS_ID'];
        $obCasPfuModel->CasUsrCod = AuthSession::get()['USR_ID'];
        if ($obCasPfuModel->readRegisterJoin(['CasRpu'])) {
            foreach ($obCasPfuModel->getRecords() as $perfil) {
                if ($perfil['CasPfiBlq'] == 'N'){
                    array_push($dataPerfils, $perfil['CasPfiCod']);
                }
            }
        }
        
        // check program access authorization //
        $perfilsDoPrograma = [];
        if ($dataPerfils) {
            $obCasApfModel = new CasApfModel();
            $obCasApfModel->setSelectedFields(['CasRpsCod', 'CasPfiCod', 'CasUsrCod', 'CasPrgCod']);
            $obCasApfModel->CasRpsCod = AuthSession::get()['RPS_ID'];
            $obCasApfModel->CasUsrCod = AuthSession::get()['USR_ID'];
            $obCasApfModel->CasPrgCod = $prg_id;
            if($obCasApfModel->readRegisterJoin(['CasRpu', 'CasPrg'])) {
                foreach ($obCasApfModel->getRecords() as $perfilPrograma) {
                    array_push($perfilsDoPrograma, $perfilPrograma['CasPfiCod']);
                    foreach ($dataPerfils as $perfilUsuario) {
                        if ($perfilUsuario == $perfilPrograma['CasPfiCod']) {
                            $authorizedProgram = true;
                            break;
                        }
                    }
                    if ($authorizedProgram) {
                        break;
                    }
                }
            }

            // check program functionality authorization //
            if ($authorizedProgram && count($perfilsDoPrograma) > 0) {
                foreach ($perfilsDoPrograma as $perfil) {
                    $obCasAfuModel = new CasAfuModel();
                    $obCasAfuModel->setSelectedFields(['CasRpsCod', 'CasPfiCod', 'CasUsrCod', 'CasPrgCod', 'CasFunCod']);
                    $obCasAfuModel->CasRpsCod = AuthSession::get()['RPS_ID'];
                    $obCasAfuModel->CasPfiCod = $perfil;
                    $obCasAfuModel->CasUsrCod = AuthSession::get()['USR_ID'];
                    $obCasAfuModel->CasPrgCod = $prg_id;
                    if ($obCasAfuModel->readRegisterJoin(['CasApf', 'CasRpsCod', 'CasPrgCod'])) {
                        foreach ($obCasAfuModel->getRecords() as $funcionality) {
                            array_push($dataPermissions, $funcionality['CasFunCod']);
                        }
                    }
                }
            }
        }

        return $dataPermissions;
    }

    public function funcionalities(string $prg_id): array
    {
        $dataFuncionalities = [];

        $obCasPrgModel = new CasPrgModel();
        $obCasPrgModel->setSelectedFields(['CasRpsCod', 'CasPrgCod', 'CasPrgBlq']);
        $obCasPrgModel->CasRpsCod = AuthSession::get()['RPS_ID'];
        $obCasPrgModel->CasPrgCod = $prg_id;
        $vCasPrgBlq = 'N';
        $vCasPrgTst = 'S';
        $vCasMdlBlq = 'N';

        // check program test or blocked //
        if ($obCasPrgModel->readRegister()) {
            $vCasPrgTst = $obCasPrgModel->CasPrgTst;
            $vCasPrgBlq = $obCasPrgModel->CasPrgBlq;

            if ($vCasPrgTst == 'S') {
                if (AuthSession::get()['PROFILE'] != 'USER QUALITY TEST') {
                    return $dataFuncionalities; // access denied //
                }
            }

            // check program blocked //
            if ($vCasPrgBlq == 'S') {
                return $dataFuncionalities; // access denied //
            } else {
                // check module blocked //
                $obCasMprModel = new CasMprModel();
                $obCasMprModel->setSelectedFields(['CasRpsCod', 'CasPrgCod', 'CasMdlCod']);
                $obCasMprModel->CasRpsCod = AuthSession::get()['RPS_ID'];
                $obCasMprModel->CasPrgCod = $prg_id;
                if ($obCasMprModel->readRegisterJoin(['CasRps', 'CasPrg'])) {
                    foreach ($obCasMprModel->getRecords() as $modules) {
                        foreach ($modules as $key => $value) {
                            if ($key == 'CasMdlCod') {
                                $obCasMdlModel = new CasMdlModel();
                                $obCasMdlModel->setSelectedFields(['CasRpsCod', 'CasMdlCod', 'CasMdlBlq']);
                                $obCasMdlModel->CasRpsCod = AuthSession::get()['RPS_ID'];
                                $obCasMdlModel->CasMdlCod = $value;
                                if ($obCasMdlModel->readRegister()) {
                                    $vCasMdlBlq = $obCasMdlModel->CasMdlBlq;
                                }
                            }
                        }
                        if ($vCasMdlBlq == 'S') {
                            break;
                        }
                    }
                }
            }

            // check funcionalities blocked //
            if ($vCasMdlBlq == 'N') {
                $obCasFprModel = new CasFprModel();
                $obCasFprModel->setSelectedFields(['CasRpsCod', 'CasPrgCod', 'CasFunCod', 'CasFprCod']);
                $obCasFprModel->CasRpsCod = AuthSession::get()['RPS_ID'];
                $obCasFprModel->CasPrgCod = $prg_id;
                if ($obCasFprModel->readRegisterJoin(['CasRps', 'CasPrg'])) {
                    foreach ($obCasFprModel->getRecords()  as $funcionalities) {
                        foreach ($funcionalities as $key => $value) {
                            if ($key == 'CasFunCod') {
                                $obCasFunModel = new CasFunModel();
                                $obCasFunModel->setSelectedFields(['CasRpsCod', 'CasPrgCod', 'CasFunCod', 'CasFunDsc', 'CasFunBlq']);
                                $obCasFunModel->CasRpsCod = AuthSession::get()['RPS_ID'];
                                $obCasFunModel->CasPrgCod = $prg_id;
                                $obCasFunModel->CasFunCod = $value;
                                if ($obCasFunModel->readRegister()) {
                                    foreach ($obCasFunModel->getRecords() as $funcionalityItem) {
                                        if ($funcionalityItem['CasFunBlq'] == 'N') {
                                            array_push($dataFuncionalities, $funcionalityItem['CasFunCod']);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $dataFuncionalities;
    }

    public function setProgramHistory(string $prg_id, string $details = ''): void
    {
        $dataSession = AuthSession::get();

        if ($dataSession['RPS_ID'] !== 'anonymous') {
            $description = array('Program' => $prg_id, 'Details' => $details);

            $obCasSwnModel = new CasSwnModel();
            $obCasSwnModel->CasRpsCod = $dataSession['RPS_ID'];
            $obCasSwnModel->CasSwbCod = $dataSession['SSW_ID'];
            $newId = $obCasSwnModel->getLastId();

            $obCasSwnModel->CasSwnCod = $newId;
            $obCasSwnModel->CasSwnDsc = json_encode($description);
            $result = $obCasSwnModel->createRegister();
        }
    }

    public function setProgramParameters(string $prg_id, string $parameters): void
    {
        $dataSession = AuthSession::get();

        if ($dataSession['RPS_ID'] !== 'anonymous') {
            $obCasPrgModel = new CasPrgModel();
            $obCasPrgModel->setSelectedFields(['CasRpsCod', 'CasPrgCod']);
            $obCasPrgModel->CasRpsCod = $dataSession['RPS_ID'];
            $obCasPrgModel->CasPrgCod = $prg_id;
            if ($obCasPrgModel->readRegister()) {
                $obCasSwpModel = new CasSwpModel();
                $obCasSwpModel->setSelectedFields(['CasRpsCod', 'CasSwbCod', 'CasPrgCod', 'CasSwpTxt']);
                $obCasSwpModel->CasRpsCod = $dataSession['RPS_ID'];
                $obCasSwpModel->CasSwbCod = $dataSession['SSW_ID'];
                $obCasSwpModel->CasPrgCod = $prg_id;
                $obCasSwpModel->CasSwpTxt = $parameters;
                if ($obCasSwpModel->readRegister()) {
                    $obCasSwpModel->CasSwpTxt = $parameters;
                    $result = $obCasSwpModel->updateRegister();
                } else {
                    $result = $obCasSwpModel->createRegister();
                }
            }
        }
    }
}
