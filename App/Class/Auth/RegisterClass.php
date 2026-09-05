<?php

namespace App\Class\Auth;

use App\Class\Auth\AuthClass;
use App\Core\Config;
use App\Models\CAS\CasRpaModel;
use App\Class\Manager\CreateUserRepositoryAccount;
use App\Class\Manager\ApplyApplicationSettings;
use App\Class\Manager\UserTypeDomain;
use RuntimeException;
use Throwable;

class RegisterClass extends AuthClass
{
    public function createAccount(array $data_account): array
    {
        // validate form data //
        if (empty(trim($data_account['FirstName']))) {
            return $this->message->getMessage(604);
        }
        if (empty(trim($data_account['LastName']))) {
            return $this->message->getMessage(604);
        }
        if (empty(trim($data_account['Account']))) {
            return $this->message->getMessage(604);
        }
        if (empty(trim($data_account['Password']))) {
            return $this->message->getMessage(604);
        }
        if (empty(trim($data_account['PasswordConfirm']))) {
            return $this->message->getMessage(604);
        }
        if ($data_account['Password'] != $data_account['PasswordConfirm']) {
            return $this->message->getMessage(605);
        }

        try {
            $user_auth = md5(strtolower($data_account['Account']));
            $new_data_user = $this->generateDataUser($user_auth, $data_account);

        // configure repository and users account //
        $obCreateUserRepositoryAccount = new CreateUserRepositoryAccount();
        $types = UserTypeDomain::definitions($user_auth);
        $obCreateUserRepositoryAccount->run(
            $new_data_user,
            $user_auth,
            $user_auth,
            $types,
            $types[UserTypeDomain::MANAGER]['CasTusCod'],
            $this->getSupportContext($types[UserTypeDomain::SUPPORT]['CasTusCod'])
        );

        // add application //
        $obCasRpaModel = new CasRpaModel();
        $obCasRpaModel->setSelectedFields(['CasRpsCod', 'CasAppCod', 'CasRpaDsc', 'CasRpaBlq', 'CasRpaGrp']);
        $obCasRpaModel->CasRpsCod = $user_auth;
        $obCasRpaModel->CasAppCod = Config::$APP_KEY;
        if (!$obCasRpaModel->readRegister()) {
            $obCasRpaModel->CasRpaDsc = Config::$APP_NAME;
            $obCasRpaModel->CasRpaBlq = 'N';
            $result = $obCasRpaModel->createRegister();
        }

        // configure program settings //
        $obApplyApplicationSettings = new ApplyApplicationSettings();
        $obApplyApplicationSettings->run($user_auth, Config::$APP_KEY);

        // login user //
        $obLoginClass = new LoginClass();
        $result = $obLoginClass->login($data_account['Account'], $data_account['Password'], '');

        /**
         * Log Error
         */
        if ($result['Code'] > 0) {
            // remove data password for logs //
            unset($data_account['Password']);
            unset($data_account['PasswordConfirm']);

            $logData = array(
                'code' => $result['Code'],
                'description' => $result['Description'],
                'account_hash' => hash('sha256', strtolower(trim((string) $data_account['Account']))),
            );
            self::setLog(json_encode($logData), 'register_error', 'Auth');
        }

        return $result;
        } catch (Throwable $exception) {
            try {
                self::setLog(json_encode([
                    'event' => 'register_exception',
                    'exception' => get_class($exception),
                    'message' => $exception->getMessage(),
                    'account_hash' => hash('sha256', strtolower(trim((string) ($data_account['Account'] ?? '')))),
                ]), 'register_error', 'Auth');
            } catch (Throwable) {
            }

            return $this->message->getMessage(1, 'Cadastro', 'Nao foi possivel criar a conta.');
        }
    }

    private function getSupportContext(string $supportTypeId): array
    {
        $path = Config::getPathRules('Manager') . 'SupportUser.json';
        $content = is_file($path) ? file_get_contents($path) : false;
        $records = $content === false ? null : json_decode($content, true);
        $supportUserId = is_array($records) ? ($records[0]['CasUsrCod'] ?? '') : '';
        if (! is_string($supportUserId) || $supportUserId === '') {
            throw new RuntimeException('SupportUser.json não contém uma conta de suporte válida.');
        }

        return ['user_id' => $supportUserId, 'type_id' => $supportTypeId];
    }

    private function generateDataUser(string $user_auth, array $data_account): array
    {
        $new_data_user = array();

        foreach ($data_account as $key => $value) {
            // Do not include fields //
            if ($key == 'Password' || $key == 'PasswordConfirm') {
                continue;
            }
            $new_data_user[$key] = $value;
        }

        // apply others fields //
        $new_data_user['Password'] = md5($data_account['Password']);
        $new_data_user['Repository'] = $user_auth;

        // apply account identifiers //
        $new_data_user['USR_ID'] = $user_auth;
        $new_data_user['USR_LOGGED'] = $data_account['FirstName'] . ' ' . $data_account['LastName'];

        return $new_data_user;
    }
}
