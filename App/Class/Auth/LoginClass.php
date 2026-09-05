<?php

namespace App\Class\Auth;

use App\Core\Config;
use App\Class\Auth\AuthClass;
use App\Core\AuthSession;
use App\Models\CAS\CasUsrModel;
use App\Models\CAS\CasRpsModel;
use App\Models\CAS\CasRpuModel;
use App\Models\CAS\CasSwbModel;
use App\Models\CAS\CasTusModel;
use App\Models\CAS\CasWksModel;
use App\Helpers\ObtainBrowserName;
use App\Helpers\ObtainClientInfo;

class LoginClass extends AuthClass
{
    public function login(string $account_email, string $account_password, string $swap = ''): array
    {
        // validate form data //
        if (empty(trim($account_email)) || empty(trim($account_password))) {
            return $this->message->getMessage(600);
        }

        $result = (bool) filter_var($account_email, FILTER_VALIDATE_EMAIL);
        if (!$result) {
            return $this->message->getMessage(614);
        }

        // swap -> recebe o id do repositório a ser trocado //
        // troca por swap ocorre com o usuario já autenticado //
        // user_auth quando account_email é o repositório principal do usuario //
        // user_auth quando swap é o repositório a ser trocado //
        $user_auth = (empty($swap) ? md5(strtolower($account_email)) : $swap);
        $user_pass = (empty($swap) ? md5($account_password) : $account_password);
        
        $result = $this->message->getMessage(602); // standard error message //

        $parts = explode('@', $account_email);

        $obCasUsrModel = new CasUsrModel();
        $obCasUsrModel->CasUsrDmn = '@' . $parts[1];
        $obCasUsrModel->CasUsrLgn = $parts[0];
        $obCasUsrModel->CasUsrPwd = $user_pass;

            /**
             * Validate Login
             */
            if ($obCasUsrModel->checkLogin()) {
                if ($obCasUsrModel->CasUsrBlq == 'S') {
                    $result = $this->message->getMessage(615);
                } elseif (AccountActivationService::isAccessExpired(
                    $obCasUsrModel->CasUsrActDtt,
                    $obCasUsrModel->CasUsrAudIns
                )) {
                    $result = $this->message->getMessage(625);
                } else {
                    $obCasRpsModel = new CasRpsModel();
                    $obCasRpsModel->setSelectedFields(['CasRpsCod', 'CasRpsDsc', 'CasRpsBlq']);
                    $obCasRpsModel->CasRpsCod = $user_auth;

                    if ($obCasRpsModel->readRegister()) {
                        if ($obCasRpsModel->CasRpsBlq == 'S') {
                            $result = $this->message->getMessage(616);
                        } else {
                            $obCasRpuModel = new CasRpuModel();
                            $obCasRpuModel->CasRpsCod = $user_auth;
                            $obCasRpuModel->CasUsrCod = $obCasUsrModel->CasUsrCod;
                            $obCasRpuModel->setSelectedFields(['CasRpsCod', 'CasUsrCod', 'CasTusCod', 'CasRpuBlq']);

                            if ($obCasRpuModel->readRegister()) {
                                //  when not to return erros //
                                $result = $this->message->getMessage(0);

                                if ($obCasRpuModel->CasRpuBlq == 'S') {
                                    $result = $this->message->getMessage(617);
                                }

                                $obCasTusModel = new CasTusModel();
                                $obCasTusModel->CasRpsCod = $user_auth;
                                $obCasTusModel->CasTusCod = $obCasRpuModel->CasTusCod;
                                $obCasRpuModel->setSelectedFields(['CasTusCod', 'CasTusDsc', 'CasTusBlq', 'CasTusLnk']);
                                if ($obCasTusModel->readRegister()) {
                                    if ($obCasTusModel->CasTusBlq == 'S') {
                                        $result = $this->message->getMessage(618);
                                    }
                                } else {
                                    $result = $this->message->getMessage(619);
                                }
                            } else {
                                $result = $this->message->getMessage(619);
                            }
                        }
                    } else {
                        $result = $this->message->getMessage(619);
                    }
                }

                /**
                 * Create Session
                 */
                if ($result['Code'] == 0 && $result['Type'] == 'SUCCESS') {
                    $repositories = array();
                    
                    // save new terminal //
                    $obCasWksModel = new CasWksModel();
                    $obCasWksModel->setSelectedFields();
                    $obCasWksModel->CasRpsCod = $user_auth;
                    $obCasWksModel->CasWksCod = $user_auth;
                    $obCasWksModel->CasWksDsc = 'WORKSTATION_DEFAULT';
                    $obCasWksModel->CasWksGrp = $user_auth;
                    if ($obCasWksModel->readRegister()) {
                        if ($obCasWksModel->CasWksBlq == 'S') {
                            $result = $this->message->getMessage(621);
                        }
                    } else {
                        $obCasWksModel->CasWksDsc = ObtainClientInfo::getClientHostname();
                        $obCasWksModel->CasWksEip = ObtainClientInfo::getClientIp();
                        $obCasWksModel->CasWksObs = ObtainBrowserName::getLongBrowserName();
                        $r = $obCasWksModel->createRegister();
                    }

                    // get repositories //
                    if ($obCasRpuModel->getAllRepositories()) {
                        $repositories = $obCasRpuModel->getRecords();
                    }

                    // create session on table CasSwb //
                    $obCasSwbModel = new CasSwbModel();
                    $obCasSwbModel->CasRpsCod = $user_auth;
                    $obCasSwbModel->CasSwbCod = AuthSession::get()['SSW_ID'];

                    // when exists session, create new session //
                    if ($obCasSwbModel->readRegister()) {
                        AuthSession::logout();
                        $obCasSwbModel = new CasSwbModel();
                        $obCasSwbModel->CasRpsCod = $user_auth;
                        $obCasSwbModel->CasSwbCod = AuthSession::get()['SSW_ID'];
                    }
                    $obCasSwbModel->CasSwbWks = ObtainClientInfo::getClientHostname(); // Ou getClientIp() se preferir
                    $obCasSwbModel->CasSwbUsu = ObtainClientInfo::getRemoteUser(); // Geralmente vazio, mas tenta
                    $obCasSwbModel->CasSwbBrw = ObtainBrowserName::getBrowserName();
                    if (!$obCasSwbModel->createRegister()) {
                        $result = $this->message->getMessage(620);
                    }

                    if (!$this->validateTerminalLicense()) {
                        $result = $this->message->getMessage(622);
                    }
                }

                if ($result['Code'] == 0 && $result['Type'] == 'SUCCESS') {
                    // Formatar data de integração do usuário
                    $integratedDateStr = "Data não disponível";
                    $rawDateValue = $obCasUsrModel->CasUsrAudIns;
                    // Validação alternativa: verifica se a variável não é nula
                    // e se, após remover espaços em branco das extremidades, não é uma string vazia.
                    if ($rawDateValue !== null && trim((string)$rawDateValue) !== '') {
                        try {
                            $dateObject = new \DateTime($rawDateValue);
                            $year = $dateObject->format('Y');
                            $monthNumber = (int)$dateObject->format('n'); // Mês como número (1-12)
                            $monthNamesPt = [
                                1 => 'janeiro',
                                2 => 'fevereiro',
                                3 => 'março',
                                4 => 'abril',
                                5 => 'maio',
                                6 => 'junho',
                                7 => 'julho',
                                8 => 'agosto',
                                9 => 'setembro',
                                10 => 'outubro',
                                11 => 'novembro',
                                12 => 'dezembro'
                            ];
                            $monthName = $monthNamesPt[$monthNumber] ?? ''; // Garante que $monthName seja uma string

                            $integratedDateStr = "Membro desde " . $monthName . " de " . $year . ".";
                        } catch (\Exception $e) {
                            // A data não pôde ser parseada. $integratedDateStr permanece "Data não disponível".
                            // Para depuração, você pode logar o erro e o valor recebido:
                            // error_log("LoginClass: Falha ao formatar CasUsrAudIns. Erro: " . $e->getMessage() . ". Valor: '" . $rawDateValue . "'");
                        }
                    }

                    $presentation = array(
                        'AVATAR' => '/SBAdmin/images/emblem-small.jpg',
                        'USER' => $obCasUsrModel->CasUsrDsc,
                        'INTEGRATED' => $integratedDateStr,
                        'ACCOUNT' => $obCasUsrModel->CasUsrLgn . $obCasUsrModel->CasUsrDmn,
                        'ACCESS_TYPE' => $obCasTusModel->CasTusDsc
                    );

                    $item = array();
                    $item['RPS_ID'] = $obCasRpsModel->CasRpsCod;
                    $item['RPS_DSC'] = $obCasRpsModel->CasRpsDsc;
                    $item['USR_ID'] = $obCasUsrModel->CasUsrCod;
                    $item['USR_LOGGED'] = $obCasUsrModel->CasUsrDsc;
                    $item['PROFILE'] = $obCasTusModel->CasTusDsc;
                    $item['PRESENTATION'] = $presentation;
                    $item['LANGUAGE'] = AuthSession::get()['LANGUAGE'];
                    $item['SSW_ID'] = $obCasSwbModel->CasSwbCod;
                    $item['EXPIRES'] = (time() + (int) (Config::$AUTHENTICATION_SESSION_LIMIT));
                    $item['HOME_PAGE'] = $obCasTusModel->CasTusLnk;
                    $item['REPOSITORIES'] = $repositories;
                    $item['AUTHORIZATION'] = []; // Preenchido por: AuthClass->permissions() //

                    $this->setSessionAuth((object) $item);
                }
            }

        /**
         * Log Error
         */
        if ($result['Code'] > 0) {
            $logData = array('code' => $result['Code'], 'description' => $result['Description'], 'input' => array('account' => $account_email));
            self::setLog(json_encode($logData), 'login_error', 'Auth');
        }

        return $result;
    }

    private function validateTerminalLicense(): bool
    {
        return true;
    }
}
