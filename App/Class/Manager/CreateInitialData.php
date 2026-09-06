<?php

namespace App\Class\Manager;

use App\Core\AuthSession;
use App\Core\Config;
use App\Models\CAS\CasAppModel;
use App\Models\CAS\CasRpaModel;
use App\Models\CAS\CasUsrModel;
use App\Traits\DataPackage;
use JsonException;
use PDO;
use RuntimeException;
use Throwable;

class CreateInitialData
{
    use DataPackage;

    private const REQUIRED_TABLES = ['CasApp', 'CasUsr', 'CasRps', 'CasTus', 'CasRpa', 'CasRpu'];

    private PDO $connection;

    public function __construct(private readonly string $storage = 'Default')
    {
    }

    public function preflight(): void
    {
        if ($this->storage !== 'Default') {
            throw new RuntimeException('A carga atual usa os models do storage Default; informe --storage=Default.');
        }
        Config::getDbStorage($this->storage);
        foreach (['APP_KEY' => Config::$APP_KEY, 'APP_NAME' => Config::$APP_NAME, 'APP_VERSION' => Config::$APP_VERSION] as $key => $value) {
            if (trim((string) $value) === '') {
                throw new RuntimeException("A variável {$key} não foi configurada.");
            }
        }
        if (Config::$APP_VERSION !== '1.0.0') {
            throw new RuntimeException('APP_VERSION deve permanecer com o valor 1.0.0.');
        }

        $this->connection = $this->connect();
        $statement = $this->connection->prepare(
            'SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = :schema AND table_name = :table'
        );
        foreach (self::REQUIRED_TABLES as $table) {
            $statement->execute([
                'schema' => Config::getDbStorageDatabase($this->storage),
                'table' => $table,
            ]);
            if ((int) $statement->fetchColumn() !== 1) {
                throw new RuntimeException("A tabela {$table} não existe. Execute php migrate.php --storage={$this->storage} antes da carga.");
            }
        }

        $repositoryPath = Config::getPathRepositories();
        if (! is_dir($repositoryPath) && ! mkdir($repositoryPath, 0777, true) && ! is_dir($repositoryPath)) {
            throw new RuntimeException("Não foi possível criar o diretório-base {$repositoryPath}.");
        }
        if (! is_writable($repositoryPath)) {
            throw new RuntimeException("O diretório-base {$repositoryPath} não permite escrita.");
        }
        if (! is_file($this->getDataPackagePath())) {
            throw new RuntimeException('O pacote de configurações esperado não foi localizado: ' . $this->getDataPackagePath());
        }
    }

    public function findCompleteState(): ?array
    {
        $support = $this->readSupportMirror();
        $supportId = (string) ($support['CasUsrCod'] ?? '');
        if ($supportId === '') {
            return null;
        }

        $sql = <<<'SQL'
SELECT owner.CasUsrCod AS admin_id, owner.CasRpsCod AS admin_repository
FROM CasRpu owner
INNER JOIN CasTus owner_type
        ON owner_type.CasRpsCod = owner.CasRpsCod
       AND owner_type.CasTusCod = owner.CasTusCod
WHERE owner.CasUsrCod = owner.CasRpsCod
  AND owner_type.CasTusDsc = 'ADMINISTRATOR ACCOUNT'
LIMIT 1
SQL;
        $admin = $this->connection->query($sql)->fetch(PDO::FETCH_ASSOC);
        if (! is_array($admin)) {
            return null;
        }

        $adminId = (string) $admin['admin_id'];
        $checks = [
            ['SELECT COUNT(*) FROM CasApp WHERE CasAppCod = ? AND CasAppDsc = ? AND CasAppVer = ?', [Config::$APP_KEY, Config::$APP_NAME, Config::$APP_VERSION], 1],
            ['SELECT COUNT(*) FROM CasRpu r INNER JOIN CasTus t ON t.CasRpsCod=r.CasRpsCod AND t.CasTusCod=r.CasTusCod WHERE r.CasRpsCod=? AND r.CasUsrCod=? AND t.CasTusDsc=?', [$supportId, $supportId, UserTypeDomain::MANAGER], 1],
            ['SELECT COUNT(*) FROM CasRpu r INNER JOIN CasTus t ON t.CasRpsCod=r.CasRpsCod AND t.CasTusCod=r.CasTusCod WHERE r.CasRpsCod=? AND r.CasUsrCod=? AND t.CasTusDsc=?', [$adminId, $supportId, UserTypeDomain::SUPPORT], 1],
            ['SELECT COUNT(DISTINCT CasTusDsc) FROM CasTus WHERE CasRpsCod=? AND CasTusDsc IN (?,?,?,?)', array_merge([$adminId], UserTypeDomain::DESCRIPTIONS), 4],
            ['SELECT COUNT(DISTINCT CasTusDsc) FROM CasTus WHERE CasRpsCod=? AND CasTusDsc IN (?,?,?,?)', array_merge([$supportId], UserTypeDomain::DESCRIPTIONS), 4],
            ['SELECT COUNT(*) FROM CasRpa WHERE CasAppCod=? AND CasRpsCod IN (?,?)', [Config::$APP_KEY, $adminId, $supportId], 2],
        ];
        foreach ($checks as [$query, $parameters, $expected]) {
            $statement = $this->connection->prepare($query);
            $statement->execute($parameters);
            if ((int) $statement->fetchColumn() !== $expected) {
                return null;
            }
        }
        foreach ([$adminId, $supportId] as $repositoryId) {
            if (! is_dir(rtrim(Config::getPathRepositories(), '/\\') . DIRECTORY_SEPARATOR . $repositoryId)) {
                return null;
            }
        }

        return [
            'admin_id' => $adminId,
            'admin_repository' => $adminId,
            'support_id' => $supportId,
            'support_repository' => $supportId,
        ];
    }

    public function run(array $adminAccount, array $supportAccount): array
    {
        $activatedAt = date('Y-m-d H:i:s');
        $adminAccount['ActivatedAt'] = $activatedAt;
        $supportAccount['ActivatedAt'] = $activatedAt;
        $adminId = md5(strtolower($adminAccount['Account']));
        $supportId = md5(strtolower($supportAccount['Account']));
        if ($adminId === $supportId) {
            throw new RuntimeException('Admin e support devem usar contas diferentes.');
        }

        $result = ['application' => $this->ensureApplication()];
        $accountCreator = new CreateUserRepositoryAccount();

        $adminTypes = UserTypeDomain::definitions($adminId);
        $result['admin'] = $accountCreator->run(
            $adminAccount,
            $adminId,
            $adminId,
            $adminTypes,
            $adminTypes[UserTypeDomain::ADMINISTRATOR]['CasTusCod']
        );

        $supportTypes = UserTypeDomain::definitions($supportId);
        $result['support'] = $accountCreator->run(
            $supportAccount,
            $supportId,
            $supportId,
            $supportTypes,
            $supportTypes[UserTypeDomain::MANAGER]['CasTusCod']
        );
        $result['support_file'] = $this->writeSupportMirror($supportId);

        $result['support_admin_relation'] = (new CreateUserSupportClass())->run(
            $adminId,
            $supportId,
            $adminTypes[UserTypeDomain::SUPPORT]['CasTusCod']
        );
        $result['repositories_application'] = [
            'admin' => $this->ensureRepositoryApplication($adminId),
            'support' => $this->ensureRepositoryApplication($supportId),
        ];

        if ($this->findCompleteState() === null) {
            throw new RuntimeException('A verificação do checkpoint principal da carga não foi concluída.');
        }

        return array_merge($result, $this->finish($adminId, $supportId));
    }

    public function finishExisting(array $state): array
    {
        $supportFile = $this->writeSupportMirror((string) $state['support_id']);
        $result = $this->finish((string) $state['admin_repository'], (string) $state['support_repository']);
        $result['support_file'] = $supportFile;
        return $result;
    }

    private function finish(string $adminRepository, string $supportRepository): array
    {
        $package = $this->synchronizeDataPackage((string) Config::$APP_KEY);
        if ($this->getDataPackage((string) Config::$APP_KEY) === []) {
            throw new RuntimeException('O pacote não permaneceu compatível após atualizar ProductKey e Version.');
        }
        $settings = [];
        foreach (['admin' => $adminRepository, 'support' => $supportRepository] as $account => $repositoryId) {
            try {
                AuthSession::set('RPS_ID', $repositoryId);
                AuthSession::set('USR_ID', $repositoryId);
                AuthSession::set('USR_LOGGED', 'seed.php');
                $settings[$account] = (new ApplyApplicationSettings())->run($repositoryId, (string) Config::$APP_KEY);
            } catch (Throwable $throwable) {
                $settings[$account] = [
                    'status' => 'warning',
                    'message' => $throwable->getMessage(),
                ];
            }
        }

        return ['package' => $package, 'application_settings' => $settings];
    }

    private function ensureApplication(): string
    {
        $application = new CasAppModel();
        $application->setSelectedFields(['CasAppCod', 'CasAppDsc', 'CasAppVer', 'CasAppGrp']);
        $application->CasAppCod = Config::$APP_KEY;
        if ($application->readRegister()) {
            if ($application->CasAppDsc !== Config::$APP_NAME
                || $application->CasAppVer !== Config::$APP_VERSION
                || $application->CasAppGrp !== Config::$APP_KEY) {
                throw new RuntimeException('O aplicativo existente é incompatível com APP_KEY, APP_NAME ou APP_VERSION.');
            }
            return 'existing';
        }

        $application->CasAppDsc = Config::$APP_NAME;
        $application->CasAppVer = Config::$APP_VERSION;
        $application->CasAppGrp = Config::$APP_KEY;
        $application->CasAppBlq = 'N';
        $application->CasAppTst = 'N';
        if (! $application->createRegister()) {
            throw new RuntimeException('Não foi possível cadastrar o aplicativo.');
        }
        return 'created';
    }

    private function ensureRepositoryApplication(string $repositoryId): string
    {
        $relation = new CasRpaModel();
        $relation->setSelectedFields(['CasRpsCod', 'CasAppCod', 'CasRpaDsc', 'CasRpaGrp']);
        $relation->CasRpsCod = $repositoryId;
        $relation->CasAppCod = Config::$APP_KEY;
        if ($relation->readRegister()) {
            if ($relation->CasRpaDsc !== Config::$APP_NAME || trim((string) $relation->CasRpaGrp) === '') {
                throw new RuntimeException('A relação existente entre repositório e aplicativo é incompatível.');
            }
            return 'existing';
        }

        $relation->CasRpaDsc = Config::$APP_NAME;
        $relation->CasRpaBlq = 'N';
        if (! $relation->createRegister()) {
            throw new RuntimeException('Não foi possível relacionar o aplicativo ao repositório.');
        }
        return 'created';
    }

    private function writeSupportMirror(string $supportId): string
    {
        $user = new CasUsrModel();
        $user->CasUsrCod = $supportId;
        $user->setSelectedFields(CasUsrModel::FIELDS);
        if (! $user->readRegister()) {
            throw new RuntimeException('Não foi possível reler a conta support para gerar o espelho JSON.');
        }
        $record = [];
        foreach (CasUsrModel::FIELDS as $field) {
            $record[$field] = $user->{$field};
        }

        try {
            $content = json_encode([$record], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) . PHP_EOL;
            json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException('Não foi possível serializar SupportUser.json.', 0, $exception);
        }

        $path = Config::getPathRules('Manager') . 'SupportUser.json';
        $written = @file_put_contents($path, $content, LOCK_EX);
        if ($written === false || $written !== strlen($content)) {
            throw new RuntimeException("Não foi possível sobrescrever {$path}.");
        }
        return $path;
    }

    private function readSupportMirror(): ?array
    {
        $path = Config::getPathRules('Manager') . 'SupportUser.json';
        if (! is_file($path)) {
            return null;
        }
        try {
            $data = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return null;
        }
        return isset($data[0]) && is_array($data[0]) ? $data[0] : null;
    }

    private function connect(): PDO
    {
        $config = Config::getDbStorage($this->storage);
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $config['DB_HOST'],
            $config['DB_PORT'],
            $config['DB_DATABASE'],
            $config['DB_CHARSET']
        );
        try {
            return new PDO($dsn, $config['DB_USERNAME'], $config['DB_PASSWORD'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (Throwable $throwable) {
            throw new RuntimeException('Falha ao conectar à base de dados configurada.', 0, $throwable);
        }
    }
}
