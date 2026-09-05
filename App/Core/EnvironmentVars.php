<?php

namespace App\Core;

class EnvironmentVars
{
    public const ENVIRONMENTS = ['local', 'staging', 'production'];
    public const DATABASE_CONNECTION = 'mysql';

    public static function getSupportedEnvironments(): array
    {
        return self::ENVIRONMENTS;
    }

    public static function setEnvironmentVariables(string $env = 'local'): array
    {
        $env = strtolower(trim($env));
        $envs = array();

        switch ($env) {
            case 'local':
                $envs = self::setEnvLocal();
                break;
            case 'staging':
                $envs = self::setEnvStaging();
                break;
            case 'production':
                $envs = self::setProduction();
                break;
            default:
                throw new \InvalidArgumentException("Ambiente de aplicação inválido: {$env}");
        }

        return $envs;
    }

    private static function getEnvDefault(): array
    {
        $env = array(
            // location end region //
            'APP_LOCALE' => 'pt_BR.utf-8',
            'APP_TIMEZONE' => 'America/Sao_Paulo',

            // application //
            'APP_NAME' => '',
            'APP_VERSION' => '',
            'APP_KEY' => '',
            'APP_TOKEN' => '',

            // environment support //
            'APP_ENV' => '',
            'APP_DEBUG' => false,
            'MONITORING_QUERY' => false,

            // environment application //
            'APP_URL' => 'http://localhost:8080',
            'AUTHENTICATION_SESSION_LIMIT' => 3600,
            'ACCOUNT_ACTIVATION_HOURS' => 168,

            // contact //
            'CONTACT_EMAIL' => '',
            'CONTACT_SUPPORT' => '',
            'CONTACT_SALES' => '',
            'CONTACT_GENERAL' => '',

            // api //
            'API_MANAGER_TOKEN'=> '',
            'API_MANAGER_URL'=> 'http://localhost:8080/auth',

            // database //
            'DB_CONNECTION' => self::DATABASE_CONNECTION,
            'DB_HOST' => '127.0.0.1',
            'DB_PORT' => '3306',
            'DB_DATABASE' => 'mysql',
            'DB_USERNAME' => 'root',
            'DB_PASSWORD' => '',
            'DB_CHARSET' => 'utf8mb4',
            'DB_COLLATION' => 'utf8mb4_unicode_ci',
            'DB_PREFIX' => 'tb_',

            // mail //
            'MAIL_SERVICE' => false,
            'MAIL_MAILER' => 'log',
            'MAIL_AUTH' => true,
            'MAIL_HOST' => 'smtp.gmail.com',
            'MAIL_PORT' => 587,
            'MAIL_USERNAME' => 'mail.account@gmail.com',
            'MAIL_PASSWORD' => 'password',
            'MAIL_ENCRYPTION' => 'tls',
            'MAIL_FROM_ADDRESS' => 'mail.account@gmail.com',
            'MAIL_FROM_NAME' => 'Mail Account',

            // authentication providers //
            'GOOGLE_AUTH_SERVICE' => false,
        );

        return $env;
    }

    private static function setEnvLocal(): array
    {
        // reset //
        $envs = self::getEnvDefault();

        // application //
        $envs['APP_NAME'] = 'Manager'; // PROJECT MANAGER SBADMIN LOCAL //
        $envs['APP_VERSION'] = '1.0.0';
        $envs['APP_KEY'] = '';
        $envs['APP_TOKEN'] = '';

        // environment support //
        $envs['APP_ENV'] = 'local';
        $envs['APP_DEBUG'] = true;
        $envs['MONITORING_QUERY'] = true;

            // environment application //
            $envs['APP_URL'] = 'http://localhost:8080';
            $envs['AUTHENTICATION_SESSION_LIMIT'] = 86400;
            $envs['ACCOUNT_ACTIVATION_HOURS'] = 168;

        // database //
        $envs['DB_DATABASE'] = 'manager_local';
        $envs['DB_USERNAME'] = 'root';
        $envs['DB_PASSWORD'] = '';
        $envs['DB_PREFIX'] = 'mng_';

        return $envs;
    }

    private static function setEnvStaging(): array
    {
        // reset //
        $envs = self::getEnvDefault();

        // application //
        $envs['APP_NAME'] = 'QA-MANAGER'; // PROJECT MANAGER SBADMIN STAGING //
        $envs['APP_VERSION'] = '1.0.0';
        $envs['APP_KEY'] = '';
        $envs['APP_TOKEN'] = '';

        // environment support //
        $envs['APP_ENV'] = 'staging';
        $envs['APP_DEBUG'] = false;
        $envs['MONITORING_QUERY'] = false;

        // environment application //
        $envs['APP_URL'] = '';
        $envs['AUTHENTICATION_SESSION_LIMIT'] = 3600;

        // database //
        $envs['DB_DATABASE'] = 'manager_staging';
        $envs['DB_USERNAME'] = 'root';
        $envs['DB_PASSWORD'] = '';
        $envs['DB_PREFIX'] = 'mng_';

        return $envs;
    }

    private static function setProduction(): array
    {
        // reset //
        $envs = self::getEnvDefault();

        // application //
        $envs['APP_NAME'] = 'MANAGER'; // PROJECT MANAGER SBADMIN PRODUCTION //
        $envs['APP_VERSION'] = '1.0.0';
        $envs['APP_KEY'] = '';
        $envs['APP_TOKEN'] = '';

        // environment support //
        $envs['APP_ENV'] = 'production';
        $envs['APP_DEBUG'] = false;
        $envs['MONITORING_QUERY'] = false;

        // environment application //
        $envs['APP_URL'] = '';
        $envs['AUTHENTICATION_SESSION_LIMIT'] = 3600;

        // database //
        $envs['DB_DATABASE'] = 'db_manager';
        $envs['DB_USERNAME'] = 'root';
        $envs['DB_PASSWORD'] = '';
        $envs['DB_PREFIX'] = 'mng_';

        return $envs;
    }
}
