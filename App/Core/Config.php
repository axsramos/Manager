<?php

namespace App\Core;

class Config
{
    public const LOG_DIRECTORY = 'Temp/Logs';

    public static ?string $DIR_BASE = null;
    public static ?string $HOMEPAGE = null;
    public static ?string $HOMEPAGE_METHOD = null;
    public static ?string $PAGE_NOT_FOUND = null;
    public static ?string $PAGE_NOT_FOUND_METHOD = null;
    public static ?bool $ISPRODUCTION = null;
    public static ?string $APP_NAME = null;
    public static ?string $APP_VERSION = null;
    public static ?string $APP_KEY = null;
    public static ?string $APP_TOKEN = null;
    public static ?bool $APP_DEBUG = null;
    public static ?string $APP_ENV = null;
    public static ?string $APP_URL = null;
    public static ?bool $MAIL_SERVICE = null;
    public static ?bool $GOOGLE_AUTH_SERVICE = null;
    public static ?array $MAIL = null;
    public static ?string $API_MANAGER_TOKEN = null;
    public static ?string $API_MANAGER_URL = null;
    public static ?int $AUTHENTICATION_SESSION_LIMIT = null;
    public static ?int $ACCOUNT_ACTIVATION_HOURS = null;
    public static ?array $DB_STORAGE = null;
    public static ?bool $MONITORING_QUERY = null;
    private static ?self $instance = null;
    private static ?array $envs = null;

    private function __construct()
    {
        self::$DIR_BASE = dirname(__DIR__, 2);
        self::$HOMEPAGE = 'Home';
        self::$HOMEPAGE_METHOD = 'index';
        self::$PAGE_NOT_FOUND = 'PageNotFound';
        self::$PAGE_NOT_FOUND_METHOD = 'pageNotFound';

        self::readConfig();

        $environment = self::resolveEnvironment();
        $defaults = EnvironmentVars::setEnvironmentVariables($environment);

        self::$envs = array_merge($defaults, self::$envs);
        self::applyProcessEnvironment();
        unset(self::$envs['DIR_FILE_LOG']);
        self::$envs['APP_ENV'] = $environment;

        self::validateRequiredConfig($environment);

        /**
         * Define locale and timezone
         */
        setlocale(LC_ALL, self::$envs['APP_LOCALE']);
        date_default_timezone_set(self::$envs['APP_TIMEZONE']);

        /**
         * Define APP_NAME, APP_VERSION and APP_KEY
         * APP_KEY // chave de segurança para o sistema
         * APP_TOKEN // token genérico para obter acesso ao sistema (área publica)
         */
        self::$APP_NAME = self::$envs['APP_NAME']; // PROJECT MANAGER SBADMIN LOCAL //
        self::$APP_VERSION = self::$envs['APP_VERSION'];
        self::$APP_KEY = self::$envs['APP_KEY'];
        self::$APP_TOKEN = self::$envs['APP_TOKEN'];

        /**
         * Define API Manager
         */
        self::$API_MANAGER_TOKEN = self::$envs['API_MANAGER_TOKEN'];
        self::$API_MANAGER_URL = self::$envs['API_MANAGER_URL'];

        /**
         */

        /**
         * Define Environment
         */
        self::$APP_ENV = self::$envs['APP_ENV'];
        self::$APP_DEBUG = self::toBool(self::$envs['APP_DEBUG'], 'APP_DEBUG');
        self::$MONITORING_QUERY = self::toBool(self::$envs['MONITORING_QUERY'], 'MONITORING_QUERY');
        self::$ISPRODUCTION = self::$APP_ENV === 'production';

        ini_set('display_errors', self::$APP_DEBUG ? '1' : '0');

        self::$APP_URL = self::$envs['APP_URL'];
        self::$AUTHENTICATION_SESSION_LIMIT = self::toPositiveInt(
            self::$envs['AUTHENTICATION_SESSION_LIMIT'],
            'AUTHENTICATION_SESSION_LIMIT'
        );
        self::$ACCOUNT_ACTIVATION_HOURS = self::toPositiveInt(
            self::$envs['ACCOUNT_ACTIVATION_HOURS'],
            'ACCOUNT_ACTIVATION_HOURS'
        );

        self::$DB_STORAGE = self::buildDatabaseStorage();

        /**
         * Define Service Mail
         * [MAIL_MAILER] => log // para logar os emails
         * [MAIL_MAILER] => smtp // para enviar os emails
         */
        self::$MAIL_SERVICE = self::toBool(self::$envs['MAIL_SERVICE'], 'MAIL_SERVICE');
        self::$GOOGLE_AUTH_SERVICE = self::toBool(self::$envs['GOOGLE_AUTH_SERVICE'], 'GOOGLE_AUTH_SERVICE');

        // Mail Default //
        self::$MAIL = array(
            'MAIL_MAILER' => self::$envs['MAIL_MAILER'],
            'MAIL_AUTH' => self::toBool(self::$envs['MAIL_AUTH'], 'MAIL_AUTH'),
            'MAIL_HOST' => self::$envs['MAIL_HOST'],
            'MAIL_PORT' => self::toPositiveInt(self::$envs['MAIL_PORT'], 'MAIL_PORT'),
            'MAIL_USERNAME' => self::$envs['MAIL_USERNAME'],
            'MAIL_PASSWORD' => self::$envs['MAIL_PASSWORD'],
            'MAIL_ENCRYPTION' => self::$envs['MAIL_ENCRYPTION'],
            'MAIL_FROM_ADDRESS' => self::$envs['MAIL_FROM_ADDRESS'],
            'MAIL_FROM_NAME' => self::$envs['MAIL_FROM_NAME'],
        );

    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new Config();
        }
        return self::$instance;
    }

    public static function getDbStorage(string $storage = 'Default'): array
    {
        self::getInstance();

        $storage = trim($storage);
        if ($storage === '' || ! isset(self::$DB_STORAGE[$storage])) {
            throw new \RuntimeException("Storage de banco de dados inválido: {$storage}");
        }

        self::validateDbStorage($storage, self::$DB_STORAGE[$storage], true);

        return self::$DB_STORAGE[$storage];
    }

    public static function getDbStorageDatabase(string $storage = 'Default'): string
    {
        return self::getDbStorage($storage)['DB_DATABASE'];
    }

    public static function getPageGroup(): array
    {
        return array(
            'auth',
            'api',
            'manager',
            'support'
        );
    }

    public static function getPathPrivacy(): string
    {
        return self::$DIR_BASE . '/App/Static/Template/privacy.html';
    }

    public static function getPathTerms(): string
    {
        return self::$DIR_BASE . '/App/Static/Template/terms.html';
    }

    public static function getPathMailRegister(): string
    {
        return self::$DIR_BASE . '/App/Static/Template/email_register.html';
    }

    public static function getPathMailRecovery(): string
    {
        return self::$DIR_BASE . '/App/Static/Template/email_recovery.html';
    }

    public static function getPathMailActivation(): string
    {
        return self::$DIR_BASE . '/App/Static/Template/email_activation.html';
    }

    public static function getPathMailRegisterVerification(): string
    {
        return self::$DIR_BASE . '/App/Static/Template/email_register_verification.html';
    }

    public static function getPathRules(string $directory): string
    {
        return self::$DIR_BASE . '/App/Static/Rules/' . $directory . '/';
    }

    public static function getPathRepositories(): string
    {
        return self::$DIR_BASE . '/Repositories/';
    }

    public static function getPathLogs(): string
    {
        return dirname(__DIR__, 2)
            . DIRECTORY_SEPARATOR
            . str_replace('/', DIRECTORY_SEPARATOR, self::LOG_DIRECTORY);
    }

    public static function getPathAuthLogs(): string
    {
        return self::getPathLogs() . DIRECTORY_SEPARATOR . 'Auth';
    }

    public static function getRulesPassword(): array
    {
        return array(
            'MinimumLength' => 6,
            'RequireSimbols' => false,
            'RequireNumbers' => true,
            'RequireUppercase' => false,
            'RequireLowercase' => false,
        );
    }

    public static function getTypeUsersAdminPortal(): array
    {
        // Domínio predefinido e não removível; tipos dinâmicos podem coexistir.
        return array('ADMINISTRATOR ACCOUNT');
    }

    public static function getTypeUsersSupportPortal(): array
    {
        // Domínio predefinido e não removível; tipos dinâmicos podem coexistir.
        return array('SUPPORT ACCOUNT');
    }

    public static function getTypeUsersAdminLocal(): array
    {
        // Domínio predefinido e não removível; tipos dinâmicos podem coexistir.
        return array('MANAGER ACCOUNT');
    }

    public static function getTypeUsersUserLocal(): array
    {
        // Domínio predefinido e não removível; tipos dinâmicos podem coexistir.
        return array('USER ACCOUNT');
    }

    public static function getTypeUsersRestrictAccess(): array
    {
        // return type user //
        return array_merge(self::getTypeUsersAdminPortal(), self::getTypeUsersSupportPortal(), self::getTypeUsersAdminLocal());
    }

    // Descontinuar esta função //
    public static function writeConfig(string $dataContent): void
    {
        self::getInstance();

        $path = self::$DIR_BASE . '/.env';

        $bytesWritten = @file_put_contents($path, $dataContent, LOCK_EX);

        if ($bytesWritten === false) {
            throw new \RuntimeException("Não foi possível gravar o arquivo de configuração: {$path}");
        }
    }
    
    public static function getEnvs(): array
    {
        self::getInstance();

        return self::$envs;
    }

    private static function readConfig(): void
    {
        $path = self::$DIR_BASE . '/.env';
        $envs = [];

        if (! is_file($path)) {
            self::$envs = $envs;
            return;
        }

        $lines = @file($path, FILE_IGNORE_NEW_LINES);

        if ($lines === false) {
            throw new \RuntimeException("Não foi possível ler o arquivo de configuração: {$path}");
        }

        foreach ($lines as $lineNumber => $line) {
            $line = trim($line);

            if ($line === '' || str_starts_with($line, '#')) {
                continue;
            }

            if (str_starts_with($line, 'export ')) {
                $line = ltrim(substr($line, 7));
            }

            if (! str_contains($line, '=')) {
                throw new \RuntimeException(
                    sprintf('Configuração inválida na linha %d de %s.', $lineNumber + 1, $path)
                );
            }

            [$key, $value] = explode('=', $line, 2);
            $key = trim($key);

            if (! preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $key)) {
                throw new \RuntimeException(
                    sprintf('Chave de configuração inválida na linha %d de %s.', $lineNumber + 1, $path)
                );
            }

            $envs[$key] = self::parseEnvValue(trim($value), $key);
        }

        self::$envs = $envs;
    }

    private static function parseEnvValue(string $value, string $key): string
    {
        if ($value === '') {
            return '';
        }

        $quote = $value[0];

        if ($quote !== '"' && $quote !== "'") {
            return $value;
        }

        if (strlen($value) < 2 || substr($value, -1) !== $quote) {
            throw new \RuntimeException("Valor sem fechamento de aspas para a configuração {$key}.");
        }

        $value = substr($value, 1, -1);

        if ($quote === "'") {
            return str_replace(["\\'", '\\\\'], ["'", '\\'], $value);
        }

        return str_replace(
            ['\\n', '\\r', '\\t', '\\"', '\\\\'],
            ["\n", "\r", "\t", '"', '\\'],
            $value
        );
    }

    private static function resolveEnvironment(): string
    {
        $processEnvironment = getenv('APP_ENV');
        $environment = $processEnvironment !== false && trim($processEnvironment) !== ''
            ? $processEnvironment
            : (self::$envs['APP_ENV'] ?? 'local');
        $environment = strtolower(trim((string) $environment));

        if (! in_array($environment, EnvironmentVars::getSupportedEnvironments(), true)) {
            throw new \RuntimeException("Ambiente de aplicação inválido: {$environment}");
        }

        return $environment;
    }

    private static function applyProcessEnvironment(): void
    {
        $keys = array_unique(array_merge(
            array_keys(self::$envs),
            ['CONTACT_EMAIL', 'CONTACT_SUPPORT', 'CONTACT_SALES', 'CONTACT_GENERAL']
        ));

        foreach ($keys as $key) {
            $value = getenv($key);

            if ($value !== false) {
                self::$envs[$key] = $value;
            }
        }
    }

    private static function validateRequiredConfig(string $environment): void
    {
        self::$envs['DB_CONNECTION'] = self::normalizeDatabaseConnection('Default', self::$envs['DB_CONNECTION']);
        self::$envs['SAAS_DB_CONNECTION'] = self::normalizeDatabaseConnection('SAAS', self::$envs['SAAS_DB_CONNECTION']);

        foreach (['APP_URL', 'API_MANAGER_URL'] as $key) {
            if (! filter_var(self::$envs[$key], FILTER_VALIDATE_URL)) {
                throw new \RuntimeException("URL inválida na configuração {$key}.");
            }
        }

        if ($environment !== 'local') {
            $required = [
                'APP_NAME',
                'APP_VERSION',
                'APP_KEY',
                'APP_TOKEN',
                'APP_URL',
                'API_MANAGER_TOKEN',
                'API_MANAGER_URL',
                'DB_HOST',
                'DB_DATABASE',
                'DB_USERNAME',
                'DB_PASSWORD',
            ];

            foreach ($required as $key) {
                if (trim((string) self::$envs[$key]) === '') {
                    throw new \RuntimeException(
                        "A configuração obrigatória {$key} não foi definida para o ambiente {$environment}."
                    );
                }
            }
        }

        if (self::toBool(self::$envs['MAIL_SERVICE'], 'MAIL_SERVICE')) {
            foreach (['MAIL_HOST', 'MAIL_USERNAME', 'MAIL_PASSWORD', 'MAIL_FROM_ADDRESS'] as $key) {
                if (trim((string) self::$envs[$key]) === '') {
                    throw new \RuntimeException("A configuração obrigatória {$key} não foi definida.");
                }
            }
        }
    }

    private static function buildDatabaseStorage(): array
    {
        return [
            'Default' => [
                'DB_CONNECTION' => self::$envs['DB_CONNECTION'],
                'DB_HOST' => self::$envs['DB_HOST'],
                'DB_PORT' => self::$envs['DB_PORT'],
                'DB_DATABASE' => self::$envs['DB_DATABASE'],
                'DB_USERNAME' => self::$envs['DB_USERNAME'],
                'DB_PASSWORD' => self::$envs['DB_PASSWORD'],
                'DB_CHARSET' => self::$envs['DB_CHARSET'],
                'DB_COLLATION' => self::$envs['DB_COLLATION'],
                'DB_PREFIX' => self::$envs['DB_PREFIX'],
            ],
            'SAAS' => [
                'DB_CONNECTION' => self::$envs['SAAS_DB_CONNECTION'],
                'DB_HOST' => self::$envs['SAAS_DB_HOST'],
                'DB_PORT' => self::$envs['SAAS_DB_PORT'],
                'DB_DATABASE' => self::$envs['SAAS_DB_DATABASE'],
                'DB_USERNAME' => self::$envs['SAAS_DB_USERNAME'],
                'DB_PASSWORD' => self::$envs['SAAS_DB_PASSWORD'],
                'DB_CHARSET' => self::$envs['SAAS_DB_CHARSET'],
                'DB_COLLATION' => self::$envs['SAAS_DB_COLLATION'],
                'DB_PREFIX' => self::$envs['SAAS_DB_PREFIX'],
            ],
        ];
    }

    private static function validateDbStorage(string $storage, array $config, bool $strict): void
    {
        self::normalizeDatabaseConnection($storage, $config['DB_CONNECTION'] ?? '');

        foreach (['DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_CHARSET', 'DB_COLLATION'] as $key) {
            if ($strict && trim((string) ($config[$key] ?? '')) === '') {
                throw new \RuntimeException("A configuração {$key} do storage {$storage} não foi definida.");
            }
        }

        self::toPositiveInt($config['DB_PORT'] ?? null, "{$storage}.DB_PORT");
    }

    private static function normalizeDatabaseConnection(string $storage, mixed $connection): string
    {
        $databaseConnection = strtolower(trim((string) $connection));

        if ($databaseConnection !== EnvironmentVars::DATABASE_CONNECTION) {
            throw new \RuntimeException(
                sprintf(
                    'Conexão de banco de dados não suportada no storage %s: %s. Utilize somente %s.',
                    $storage,
                    (string) $connection,
                    EnvironmentVars::DATABASE_CONNECTION
                )
            );
        }

        return $databaseConnection;
    }

    private static function toBool(mixed $value, string $key): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        $parsedValue = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

        if ($parsedValue === null) {
            throw new \RuntimeException("Valor booleano inválido para a configuração {$key}.");
        }

        return $parsedValue;
    }

    private static function toPositiveInt(mixed $value, string $key): int
    {
        $parsedValue = filter_var(
            $value,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );

        if ($parsedValue === false) {
            throw new \RuntimeException("Valor inteiro positivo inválido para a configuração {$key}.");
        }

        return $parsedValue;
    }

    public static function getContact(string $type): string
    {
        $contact = '';

        switch ($type) {
            case 'support':
                if (isset(self::$envs['CONTACT_SUPPORT'])) {
                    $contact = self::$envs['CONTACT_SUPPORT'];
                }
                break;
            case 'sales':
                if (isset(self::$envs['CONTACT_SALES'])) {
                    $contact = self::$envs['CONTACT_SALES'];
                }
                break;
            case 'general':
                if (isset(self::$envs['CONTACT_GENERAL'])) {
                    $contact = self::$envs['CONTACT_GENERAL'];
                }
                break;
            default:
                if (isset(self::$envs['CONTACT_EMAIL'])) {
                    $contact = self::$envs['CONTACT_EMAIL'];
                }
                break;
        }

        if (empty($contact)) {
            $contact = 'contact@uorak.com';
        }

        return $contact;
    }
}
