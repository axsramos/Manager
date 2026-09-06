<?php

namespace App\Class\Contract;

use App\Core\Config;
use PDO;
use RuntimeException;

abstract class AbstractContractService
{
    protected PDO $pdo;

    public function __construct(protected readonly string $storage = 'SAAS')
    {
        $config = Config::getDbStorage($storage);
        $dsn = "mysql:host={$config['DB_HOST']};dbname={$config['DB_DATABASE']};port={$config['DB_PORT']};charset={$config['DB_CHARSET']}";
        $this->pdo = new PDO($dsn, $config['DB_USERNAME'], $config['DB_PASSWORD'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
    }

    protected function uuidV4(): string
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    protected function id(string $identifier): string
    {
        if (!preg_match('/^[A-Za-z0-9_]+$/', $identifier)) {
            throw new RuntimeException("Identificador SQL inválido: {$identifier}");
        }

        return "`{$identifier}`";
    }

    protected function fetchOne(string $sql, array $parameters = []): array|null
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($parameters);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row === false ? null : $row;
    }

    protected function execute(string $sql, array $parameters = []): int
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($parameters);

        return $stmt->rowCount();
    }

    protected function result(string $status, string $message, array $data = []): array
    {
        return array_merge(['status' => $status, 'message' => $message], $data);
    }
}
