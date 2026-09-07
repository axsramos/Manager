<?php

namespace App\Class\Contract;

use RuntimeException;

class ApplyContractSettings extends AbstractContractService
{
    public function run(string $repositoryId, array $package, string $packageHash): array
    {
        $this->validatePackage($package);

        $applied = [];
        $warnings = [];
        $tableData = $package['OperationalTableData'] ?? $package['BaseTableData'] ?? [];
        $tasks = $package['Packages'] ?? [['Task' => 1, 'Description' => 'Configurações iniciais']];
        $contentHash = hash('sha256', json_encode($tableData, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

        $this->pdo->beginTransaction();
        try {
            foreach ($tasks as $task) {
                $taskId = (int) ($task['Task'] ?? 1);
                $checkpoint = $this->checkpoint($repositoryId, $package, $taskId);
                if ($checkpoint !== null && hash_equals((string) $checkpoint['ContentHash'], $contentHash)) {
                    $applied[] = ['task' => $taskId, 'status' => 'skipped'];
                    continue;
                }
                if ($checkpoint !== null) {
                    throw new RuntimeException("Checkpoint divergente para a task {$taskId} do ContractFlow.");
                }

                $tableResult = $this->applyOperationalTableData($repositoryId, $tableData);
                $this->saveCheckpoint($repositoryId, $package, $taskId, $contentHash, 'Pacote aplicado. Hash: ' . $packageHash);
                $applied[] = ['task' => $taskId, 'status' => 'applied', 'tables' => $tableResult];
            }

            $this->pdo->commit();
        } catch (\Throwable $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }

        return $this->result($warnings === [] ? 'applied' : 'warning', 'Configurações do ContractFlow processadas.', [
            'product_key' => $package['ProductKey'],
            'version' => $package['Version'],
            'package_hash' => $packageHash,
            'tasks' => $applied,
            'warnings' => $warnings,
        ]);
    }

    private function validatePackage(array $package): void
    {
        foreach (['ProductKey', 'ProductName', 'Version', 'RunScript'] as $field) {
            if (trim((string) ($package[$field] ?? '')) === '') {
                throw new RuntimeException("Pacote ContractFlow sem o campo obrigatório {$field}.");
            }
        }

        if (($package['ProductName'] ?? null) !== 'ContractFlow') {
            throw new RuntimeException('Pacote não pertence ao ContractFlow.');
        }

        if (($package['RunScript'] ?? null) !== 'ApplyContractSettings') {
            throw new RuntimeException('RunScript incompatível com o instalador do ContractFlow.');
        }
    }

    private function applyOperationalTableData(string $repositoryId, array $tableData): array
    {
        $result = [];

        foreach ($tableData as $dataTable) {
            foreach ($dataTable as $table => $records) {
                $result[$table] = match ($table) {
                    'CTRConcurrencyGroup' => $this->applyConcurrencyGroups($repositoryId, $records),
                    default => throw new RuntimeException("Tabela não permitida no pacote ContractFlow: {$table}"),
                };
            }
        }

        return $result;
    }

    private function applyConcurrencyGroups(string $repositoryId, array $records): array
    {
        $created = 0;
        $existing = 0;
        $updated = 0;

        foreach ($records as $record) {
            $name = trim((string) ($record['Name'] ?? ''));
            if ($name === '') {
                throw new RuntimeException('Grupo de concorrência sem nome.');
            }

            $current = $this->fetchOne(
                'SELECT Id, AllowMultipleActive FROM CTRConcurrencyGroup WHERE RepositoryId = :repository_id AND Name = :name LIMIT 1',
                [':repository_id' => $repositoryId, ':name' => $name]
            );
            $allowMultipleActive = (int) ($record['AllowMultipleActive'] ?? 0);

            if ($current === null) {
                $this->execute(
                    'INSERT INTO CTRConcurrencyGroup (RepositoryId, Name, AllowMultipleActive)
                     VALUES (:repository_id, :name, :allow_multiple_active)',
                    [
                        ':repository_id' => $repositoryId,
                        ':name' => $name,
                        ':allow_multiple_active' => $allowMultipleActive,
                    ]
                );
                $created++;
                continue;
            }

            if ((int) $current['AllowMultipleActive'] !== $allowMultipleActive) {
                $this->execute(
                    'UPDATE CTRConcurrencyGroup SET AllowMultipleActive = :allow_multiple_active WHERE Id = :id',
                    [':allow_multiple_active' => $allowMultipleActive, ':id' => $current['Id']]
                );
                $updated++;
                continue;
            }

            $existing++;
        }

        return ['created' => $created, 'existing' => $existing, 'updated' => $updated];
    }

    private function checkpoint(string $repositoryId, array $package, int $task): array|null
    {
        return $this->fetchOne(
            'SELECT ContentHash FROM CTRPackageCheckpoint
             WHERE RepositoryId = :repository_id AND ProductKey = :product_key AND Version = :version AND Task = :task
             LIMIT 1',
            [
                ':repository_id' => $repositoryId,
                ':product_key' => $package['ProductKey'],
                ':version' => $package['Version'],
                ':task' => $task,
            ]
        );
    }

    private function saveCheckpoint(string $repositoryId, array $package, int $task, string $contentHash, string $message): void
    {
        $this->execute(
            'INSERT INTO CTRPackageCheckpoint
            (RepositoryId, ProductKey, Version, Task, Status, ContentHash, Message)
            VALUES (:repository_id, :product_key, :version, :task, :status, :content_hash, :message)',
            [
                ':repository_id' => $repositoryId,
                ':product_key' => $package['ProductKey'],
                ':version' => $package['Version'],
                ':task' => $task,
                ':status' => 'applied',
                ':content_hash' => $contentHash,
                ':message' => $message,
            ]
        );
    }
}
