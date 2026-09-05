<?php

namespace App\Core;

use App\Helpers\RequiredFields;
use App\Helpers\ObtainDataInput;
use App\Controls\Menu\TopMenuControls;
use App\Controls\Menu\SideMenuControls;
use App\Class\Auth\LoginClass;
use App\Class\Auth\AccountActivationService;
use App\Models\CAS\CasUsrModel;

class Controller
{
    public $message;

    public function model(string $model): object
    {
        require 'App/Models/' . $model . '.php';
        $classe = 'App\\Models\\' . $model;

        return new $classe();
    }

    public function view(string $view, array $data = []): void
    {
        require 'App/Views/' . $view . '.php';
    }

    public function pageNotFound(bool $api = false): void
    {
        if ($api) {
            http_response_code(404);
            $this->view('jsonView');
        } else {
            $this->view('ErrorView');
        }
    }

    protected function getUserMenu(): string
    {
        return TopMenuControls::getUserMenu();
    }

    protected function getSideMenu(): string
    {
        return SideMenuControls::getSideMenu();
    }

    protected function validateAccess(string $prg_id = ''): void
    {
        // is anonymous or empty user then redirect to login //
        $redirect_login = true;

        if (AuthSession::get()['USR_LOGGED'] !== 'anonymous') {
            $currentUser = new CasUsrModel();
            $currentUser->setSelectedFields(['CasUsrCod', 'CasUsrActDtt', 'CasUsrAudIns']);
            $currentUser->CasUsrCod = AuthSession::get()['USR_ID'];
            if ($currentUser->readRegister()
                && AccountActivationService::isAccessExpired($currentUser->CasUsrActDtt, $currentUser->CasUsrAudIns)) {
                AuthSession::logout();
                header('Location: /Auth/Login?activation=required');
                exit;
            }

            $obLoginClass = new LoginClass();
            $dataPermissions = $obLoginClass->permissions($prg_id);
            if (in_array('AUTHORIZED', $dataPermissions)) {
                $redirect_login = false;
                // set program history //
                $obLoginClass->setProgramHistory($prg_id);
            }
        }

        if ($redirect_login) {
            header('Location: /Home/Denied');
        }
    }

    protected function setProgramParameters(string $prg_id, string $parameters): void
    {
        $obLoginClass = new LoginClass();
        $result = $obLoginClass->setProgramParameters($prg_id, $parameters);
    }

    protected function checkToken(): bool
    {
        $token = RequestToken::run();
        $restul = false;

        if (!empty($token)) {
            if (strstr(get_class($this), 'TokenJWT') == 'TokenJWT') {
                $restul = true;
            }
            if (strstr(get_class($this), 'CasTkn') == 'CasTkn') {
                if ($token == Config::$APP_TOKEN) {
                    $restul = true;
                }
            }
            if ($restul === false && (strstr(get_class($this), 'CasTkn') != 'CasTkn')) {
                $CasTkn = $this->model('CasTknModel');
                $CasTkn->setCasTknKey($token);
                $restul = $CasTkn->validateToken(true);
            }
        }

        return $restul;
    }

    protected function checkMethods(string $currentMethod, array $methods, object $message): string
    {
        $result = '';
        $statusCode = 200;

        if ($this->checkToken()) {

            if ($currentMethod == 'OPTIONS') {
                $result = $message->getMessage(3, "Message", json_encode($methods));
            } else {
                if (in_array($_SERVER['REQUEST_METHOD'], $methods)) {
                    $result = $message->getMessage(0);
                } else {
                    $statusCode = 405;
                    $result = $message->getMessage($statusCode);
                }
            }
        } else {
            $statusCode = '401';
            $result = $message->getMessage(1, 'TOKEN', 'Invalid token.');
        }

        http_response_code($statusCode);

        return $result;
    }

    protected function getDataInput(array $fields = []): array
    {
        return ObtainDataInput::run($fields);
    }

    protected function checkRequiredFields(array $requiredFields, array $dataEntry): bool
    {
        return RequiredFields::run($requiredFields, $dataEntry);
    }
}
