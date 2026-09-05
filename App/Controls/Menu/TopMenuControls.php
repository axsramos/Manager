<?php

namespace App\Controls\Menu;

use App\Core\AuthSession;
use App\Core\Config;

class TopMenuControls
{
    public static function getUserMenu(): string
    {
        /**
         * Static Menu
         */
        $dataContent = '';
        $path = Config::$DIR_BASE . '/App/Static/Menu/UserMenu.json';

        if (file_exists($path)) {
            $dataContent = file_get_contents($path);
        }

        $result = self::buttonSigin($dataContent);

        return $result;
    }

    private static function buttonSigin(string $dataContent): string
    {
        if (AuthSession::get()['USR_ID'] === 'anonymous') {
            return $dataContent;
        }

        /**
         * Modify button text
         */
        $description = 'Sair';

        switch (AuthSession::get()['LANGUAGE']) {
            case 'en':
                $description = 'Sign out';
                break;
        }

        $menu = json_decode($dataContent);

        if (isset($menu->UserMenu)) {
            foreach ($menu->UserMenu as $key => $value) {
                if ($value->Link == '/Auth/Login') {
                    $value->Description = $description;
                    $value->Icon = '<i class="fas fa-sign-out-alt mr-2"></i>';
                }
            }
        }

        return json_encode($menu);
    }
}
