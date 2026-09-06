<?php

namespace App\Core;

use DateTimeImmutable;
use PDO;
use RuntimeException;
use Throwable;

final class UnconfirmedAccountCleaner
{
    private PDO $pdo;
    private string $database;

    public function __construct(string $storage = 'Default')
    {
        Config::getInstance();
        $config = Config::getDbStorage($storage);
        $this->database = $config['DB_DATABASE'];
        $dsn = "mysql:host={$config['DB_HOST']};dbname={$config['DB_DATABASE']};port={$config['DB_PORT']};charset={$config['DB_CHARSET']}";
        $this->pdo = new PDO($dsn, $config['DB_USERNAME'], $config['DB_PASSWORD'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    }

    public function clean(int $days = 30, bool $delete = false): array
    {
        if ($days < 1 || $days > 3650) {
            throw new \InvalidArgumentException('O periodo deve estar entre 1 e 3650 dias.');
        }
        $cutoff = (new DateTimeImmutable('now'))->modify("-{$days} days")->format('Y-m-d H:i:s');
        $stmt = $this->pdo->prepare('SELECT CasUsrCod, CasUsrLgn, CasUsrDmn FROM CasUsr WHERE CasUsrActDtt IS NULL AND CasUsrAudIns < :cutoff ORDER BY CasUsrAudIns');
        $stmt->execute([':cutoff' => $cutoff]);
        $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $result = [
            'mode' => $delete ? 'delete' : 'dry-run',
            'retention_days' => $days,
            'cutoff' => $cutoff,
            'eligible' => count($accounts),
            'deleted_accounts' => 0,
            'deleted_rows' => 0,
            'account_hashes' => array_map(static fn (array $row): string => hash('sha256', strtolower($row['CasUsrLgn'] . $row['CasUsrDmn'])), $accounts),
        ];
        if (!$delete || $accounts === []) {
            return $result;
        }

        $tables = $this->accountTables();
        $foreignKeysDisabled = false;
        try {
            $this->pdo->beginTransaction();
            $this->pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
            $foreignKeysDisabled = true;
            foreach ($accounts as $account) {
                $userId = (string) $account['CasUsrCod'];
                $email = strtolower((string) $account['CasUsrLgn'] . (string) $account['CasUsrDmn']);
                $token = $this->pdo->prepare('DELETE FROM CasTkn WHERE CasTknDsc = :description');
                $token->execute([':description' => 'ACCOUNT_ACTIVATION ' . $email]);
                $result['deleted_rows'] += $token->rowCount();

                foreach ($tables as $table => $columns) {
                    $conditions = [];
                    $parameters = [];
                    foreach ($columns as $column) {
                        $conditions[] = $this->id($column) . ' = :' . $column;
                        $parameters[':' . $column] = $userId;
                    }
                    $deleteStmt = $this->pdo->prepare('DELETE FROM ' . $this->id($table) . ' WHERE ' . implode(' OR ', $conditions));
                    $deleteStmt->execute($parameters);
                    $result['deleted_rows'] += $deleteStmt->rowCount();
                }
                $result['deleted_accounts']++;
            }
            $this->pdo->commit();
        } catch (Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $exception;
        } finally {
            if ($foreignKeysDisabled) {
                $this->pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
            }
        }
        return $result;
    }

    private function accountTables(): array
    {
        $stmt = $this->pdo->prepare("SELECT TABLE_NAME, COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = :schema AND COLUMN_NAME IN ('CasUsrCod', 'CasRpsCod')");
        $stmt->execute([':schema' => $this->database]);
        $tables = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $tables[$row['TABLE_NAME']][] = $row['COLUMN_NAME'];
        }
        $ordered = [];
        foreach ($tables as $table => $columns) {
            if (!in_array($table, ['CasUsr', 'CasRps'], true)) {
                $ordered[$table] = $columns;
            }
        }
        foreach (['CasRps', 'CasUsr'] as $table) {
            if (isset($tables[$table])) {
                $ordered[$table] = $tables[$table];
            }
        }
        return $ordered;
    }

    private function id(string $identifier): string
    {
        if (!preg_match('/\A[A-Za-z0-9_]+\z/D', $identifier)) {
            throw new RuntimeException("Identificador SQL invalido: {$identifier}");
        }
        return "`{$identifier}`";
    }
}
