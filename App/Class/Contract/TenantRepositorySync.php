<?php

namespace App\Class\Contract;

use App\Core\Config;
use PDO;
use RuntimeException;

class TenantRepositorySync extends AbstractContractService
{
    private PDO $defaultPdo;

    public function __construct(string $storage = 'SAAS', string $defaultStorage = 'Default')
    {
        parent::__construct($storage);

        $config = Config::getDbStorage($defaultStorage);
        $dsn = "mysql:host={$config['DB_HOST']};dbname={$config['DB_DATABASE']};port={$config['DB_PORT']};charset={$config['DB_CHARSET']}";
        $this->defaultPdo = new PDO($dsn, $config['DB_USERNAME'], $config['DB_PASSWORD'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
    }

    public function sync(string $repositoryId): array
    {
        $repository = $this->fetchDefaultRepository($repositoryId);
        if ($repository === null) {
            throw new RuntimeException("Repositório {$repositoryId} não localizado no storage Default.");
        }

        $existing = $this->fetchOne('SELECT CasRpsCod, CasRpsDsc FROM CasRps WHERE CasRpsCod = :id', [
            ':id' => $repositoryId,
        ]);

        if ($existing === null) {
            $this->execute(
                'INSERT INTO CasRps (CasRpsCod, CasRpsDsc) VALUES (:id, :description)',
                [':id' => $repository['CasRpsCod'], ':description' => $repository['CasRpsDsc']]
            );

            return $this->result('created', 'Repositório sincronizado no storage SAAS.', ['repository_id' => $repositoryId]);
        }

        if ($existing['CasRpsDsc'] !== $repository['CasRpsDsc']) {
            $this->execute(
                'UPDATE CasRps SET CasRpsDsc = :description WHERE CasRpsCod = :id',
                [':id' => $repositoryId, ':description' => $repository['CasRpsDsc']]
            );

            return $this->result('updated', 'Repositório atualizado no storage SAAS.', ['repository_id' => $repositoryId]);
        }

        return $this->result('existing', 'Repositório já sincronizado no storage SAAS.', ['repository_id' => $repositoryId]);
    }

    public function existsInSaas(string $repositoryId): bool
    {
        return $this->fetchOne('SELECT 1 FROM CasRps WHERE CasRpsCod = :id LIMIT 1', [':id' => $repositoryId]) !== null;
    }

    private function fetchDefaultRepository(string $repositoryId): array|null
    {
        $stmt = $this->defaultPdo->prepare('SELECT CasRpsCod, CasRpsDsc FROM CasRps WHERE CasRpsCod = :id LIMIT 1');
        $stmt->execute([':id' => $repositoryId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row === false ? null : $row;
    }
}
