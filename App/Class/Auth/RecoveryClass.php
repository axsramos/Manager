<?php

namespace App\Class\Auth;

use App\Class\Auth\AuthClass;
use App\Models\CAS\CasUsrModel;
use App\Models\CAS\CasTknModel;

class RecoveryClass extends AuthClass
{
    public $name;
    public $token;

    public function generateTokenToRecoverPassword(string $account_account_email): array
    {
        $result = $this->message->getMessage(0);

        // validate form data //
        if (empty(trim($account_account_email))) {
            return $this->message->getMessage(607);
        }
        if (filter_var($account_account_email, FILTER_VALIDATE_EMAIL) === false) {
            return $this->message->getMessage(614);
        }

        $result = $this->message->getMessage(602); // standard error message //
        $parts = explode('@', $account_account_email);
        $user_auth_description = 'RECOVERYPASS ' . strtolower($account_account_email);
        $user_auth = md5($user_auth_description);
        $this->token = $this->newTokenPassword();

        $obCasUsrModel = new CasUsrModel();
        $obCasUsrModel->CasUsrDmn = '@' . $parts[1];
        $obCasUsrModel->CasUsrLgn = $parts[0];

        if ($obCasUsrModel->existsMailAccount()) {
            if ($obCasUsrModel->readRegister()) {
                $this->name = $obCasUsrModel->CasUsrNme . ' ' . $obCasUsrModel->CasUsrSnm;
                if ($obCasUsrModel->CasUsrBlq == 'S') {
                    $result = $this->message->getMessage(615);
                } elseif (empty($obCasUsrModel->CasUsrActDtt)) {
                    $result = $this->message->getMessage(626);
                } else {
                    // gerar token //
                    $obCasTknModel = new CasTknModel();
                    $obCasTknModel->CasTknCod = $user_auth;
                    $obCasTknModel->CasTknDsc = $user_auth_description;
                    $obCasTknModel->CasTknBlq = 'N';
                    $obCasTknModel->CasTknKey = $this->token;
                    if ($obCasTknModel->readRegister()) {
                        $obCasTknModel->CasTknBlq = 'N';
                        $obCasTknModel->CasTknKey = $this->token;
                        if ($obCasTknModel->updateRegister()) {
                            $result = $this->message->getMessage(0);
                        }
                    } else {
                        if ($obCasTknModel->createRegister()) {
                            $result = $this->message->getMessage(0);
                        }
                    }
                }
            }
        } else {
            $result = $this->message->getMessage(608);
        }

        /**
         * Log Error
         */
        if ($result['Code'] > 0) {
            $logData = array('code' => $result['Code'], 'description' => $result['Description'], 'input' => array('account' => $account_account_email));
            self::setLog(json_encode($logData), 'recovery_error', 'Auth');
        }

        return $result;
    }

    public function updatePassword(string $token, string $account_email, string $account_password, string $account_passwordConfirm): array
    {
        $result = $this->message->getMessage(0);

        if (empty(trim($account_email)) || empty(trim($account_password))) {
            $result = $this->message->getMessage(604);
        } elseif (filter_var($account_email, FILTER_VALIDATE_EMAIL) === false) {
            $result = $this->message->getMessage(614);
        } else {
            if ($account_password != $account_passwordConfirm) {
                $result = $this->message->getMessage(605);
            } else {
                $result = $this->message->getMessage(602); // standard error message //
                $parts = explode('@', $account_email);
                $user_auth_description = 'RECOVERYPASS ' . strtolower($account_email);
                $user_auth = md5($user_auth_description);

                $obCasUsrModel = new CasUsrModel();
                $obCasUsrModel->CasUsrDmn = '@' . $parts[1];
                $obCasUsrModel->CasUsrLgn = $parts[0];

                if ($obCasUsrModel->existsMailAccount()) {
                    if ($obCasUsrModel->readRegister()) {
                        if ($obCasUsrModel->CasUsrBlq == 'S') {
                            $result = $this->message->getMessage(615);
                        } elseif (empty($obCasUsrModel->CasUsrActDtt)) {
                            $result = $this->message->getMessage(626);
                        } else {
                            // validar token //
                            $obCasTknModel = new CasTknModel();
                            $obCasTknModel->CasTknCod = $user_auth;
                            if ($obCasTknModel->readRegister()) {
                                if ($obCasTknModel->CasTknBlq == 'S') {
                                    $result = $this->message->getMessage(624);
                                } else {
                                    if ($token == $obCasTknModel->CasTknKey) {
                                        $obCasUsrModel->CasUsrPwd = md5($account_password);
                                        if ($obCasUsrModel->updateRegister()) {
                                            $result = $this->message->getMessage(0);
                                            // bloquear token utilizado //
                                            $obCasTknModel->CasTknBlq = 'S';
                                            $obCasTknModel->updateRegister();
                                        }
                                    } else {
                                        $result = $this->message->getMessage(610);
                                    }
                                }
                            }
                        }
                    }
                } else {
                    $result = $this->message->getMessage(608);
                }
            }
        }

        return $result;
    }
}
