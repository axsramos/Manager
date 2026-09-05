<?php

namespace App\Class\Support;

use App\Core\AuthSession;
use App\Class\Manager\ApplyApplicationSettings;

class Install
{
    public function run(string $app_id, string $runScript): array
    {
        if (AuthSession::get()['USR_LOGGED'] !== 'anonymous') {
            switch ($runScript) {
                case 'ApplyApplicationSettings':
                    return $this->applyApplicationSettings($app_id);

                default:
                    return ['status' => 'ignored', 'message' => 'Rotina não reconhecida.'];
            }
        }

        return ['status' => 'ignored', 'message' => 'É necessário autenticar-se para executar a instalação interativa.'];
    }

    private function applyApplicationSettings(string $app_id): array
    {
        $obApplyApplicationSettings = new ApplyApplicationSettings();
        return $obApplyApplicationSettings->run(AuthSession::get()['RPS_ID'], $app_id);
    }
}
