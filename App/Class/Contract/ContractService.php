<?php

namespace App\Class\Contract;

use RuntimeException;

class ContractService extends AbstractContractService
{
    public const STATUS_DRAFT = 'Draft';
    public const STATUS_ACTIVE = 'Active';
    public const STATUS_SUPERSEDED = 'Superseded';
    public const STATUS_EXPIRED = 'Expired';
    public const STATUS_CANCELED = 'Canceled';

    public function createDraft(string $repositoryId, string $userId, int $concurrencyGroupId, array $data = []): array
    {
        $contractId = $data['Id'] ?? $this->uuidV4();

        $this->pdo->beginTransaction();
        try {
            $this->execute(
                'INSERT INTO CTRContract
                (Id, RepositoryId, UserId, Description, Client, ParentContractId, ConcurrencyGroupId, Status, BillingCycle, StartDate, EndDate, ContractHash)
                VALUES (:id, :repository_id, :user_id, :description, :client, :parent_id, :group_id, :status, :billing_cycle, :start_date, :end_date, :contract_hash)',
                [
                    ':id' => $contractId,
                    ':repository_id' => $repositoryId,
                    ':user_id' => $userId,
                    ':description' => $data['Description'] ?? null,
                    ':client' => $data['Client'] ?? null,
                    ':parent_id' => $data['ParentContractId'] ?? null,
                    ':group_id' => $concurrencyGroupId,
                    ':status' => self::STATUS_DRAFT,
                    ':billing_cycle' => $data['BillingCycle'] ?? null,
                    ':start_date' => $data['StartDate'] ?? null,
                    ':end_date' => $data['EndDate'] ?? null,
                    ':contract_hash' => $data['ContractHash'] ?? null,
                ]
            );

            if (isset($data['Detail']) && is_array($data['Detail'])) {
                $this->upsertDetail($contractId, $data['Detail']);
            }

            $this->pdo->commit();
        } catch (\Throwable $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }

        return $this->result('created', 'Contrato criado em rascunho.', ['contract_id' => $contractId]);
    }

    public function activate(string $repositoryId, string $contractId): array
    {
        $this->pdo->beginTransaction();
        try {
            $contract = $this->fetchOne('SELECT * FROM CTRContract WHERE Id = :id AND RepositoryId = :repository_id FOR UPDATE', [
                ':id' => $contractId,
                ':repository_id' => $repositoryId,
            ]);
            if ($contract === null) {
                throw new RuntimeException('Contrato não localizado.');
            }

            $group = $this->fetchOne('SELECT * FROM CTRConcurrencyGroup WHERE Id = :id AND RepositoryId = :repository_id FOR UPDATE', [
                ':id' => $contract['ConcurrencyGroupId'],
                ':repository_id' => $repositoryId,
            ]);
            if ($group === null) {
                throw new RuntimeException('Grupo de concorrência não localizado.');
            }

            $superseded = 0;
            if ((int) $group['AllowMultipleActive'] === 0) {
                $superseded = $this->execute(
                    'UPDATE CTRContract
                     SET Status = :superseded
                     WHERE RepositoryId = :repository_id
                       AND UserId = :user_id
                       AND ConcurrencyGroupId = :group_id
                       AND Status = :active
                       AND Id <> :contract_id',
                    [
                        ':superseded' => self::STATUS_SUPERSEDED,
                        ':repository_id' => $repositoryId,
                        ':user_id' => $contract['UserId'],
                        ':group_id' => $contract['ConcurrencyGroupId'],
                        ':active' => self::STATUS_ACTIVE,
                        ':contract_id' => $contractId,
                    ]
                );
            }

            $this->execute(
                'UPDATE CTRContract SET Status = :active WHERE Id = :id AND RepositoryId = :repository_id',
                [':active' => self::STATUS_ACTIVE, ':id' => $contractId, ':repository_id' => $repositoryId]
            );

            $this->pdo->commit();
        } catch (\Throwable $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }

        return $this->result('updated', 'Contrato ativado.', ['contract_id' => $contractId, 'superseded_contracts' => $superseded]);
    }

    public function setStatus(string $repositoryId, string $contractId, string $status): array
    {
        $allowed = [self::STATUS_DRAFT, self::STATUS_ACTIVE, self::STATUS_SUPERSEDED, self::STATUS_EXPIRED, self::STATUS_CANCELED];
        if (!in_array($status, $allowed, true)) {
            throw new RuntimeException("Status de contrato inválido: {$status}");
        }

        $rows = $this->execute(
            'UPDATE CTRContract SET Status = :status WHERE Id = :id AND RepositoryId = :repository_id',
            [':status' => $status, ':id' => $contractId, ':repository_id' => $repositoryId]
        );

        return $this->result($rows > 0 ? 'updated' : 'skipped', 'Status do contrato processado.', ['contract_id' => $contractId]);
    }

    public function updateDraft(string $repositoryId, string $contractId, array $data): array
    {
        $this->pdo->beginTransaction();
        try {
            $contract = $this->fetchOne('SELECT Status FROM CTRContract WHERE Id = :id AND RepositoryId = :repository_id FOR UPDATE', [':id' => $contractId, ':repository_id' => $repositoryId]);
            if ($contract === null) { throw new RuntimeException('Contrato não localizado.'); }
            if ($contract['Status'] !== self::STATUS_DRAFT) { throw new RuntimeException('Somente contratos em rascunho podem ser alterados.'); }

            $this->execute('UPDATE CTRContract SET UserId = :user_id, Description = :description, Client = :client, ParentContractId = :parent_id, ConcurrencyGroupId = :group_id, BillingCycle = :billing_cycle, StartDate = :start_date, EndDate = :end_date, ContractHash = :contract_hash WHERE Id = :id AND RepositoryId = :repository_id', [
                ':user_id' => $data['UserId'], ':description' => $data['Description'] ?? null, ':client' => $data['Client'] ?? null, ':parent_id' => $data['ParentContractId'] ?? null, ':group_id' => $data['ConcurrencyGroupId'],
                ':billing_cycle' => $data['BillingCycle'] ?? null, ':start_date' => $data['StartDate'] ?? null, ':end_date' => $data['EndDate'] ?? null,
                ':contract_hash' => $data['ContractHash'] ?? null, ':id' => $contractId, ':repository_id' => $repositoryId,
            ]);
            if (isset($data['Detail']) && is_array($data['Detail'])) { $this->upsertDetail($contractId, $data['Detail']); }
            $this->pdo->commit();
        } catch (\Throwable $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }
        return $this->result('updated', 'Contrato em rascunho atualizado.', ['contract_id' => $contractId]);
    }

    private function upsertDetail(string $contractId, array $detail): void
    {
        $this->execute(
            'INSERT INTO CTRContractDetail (ContractId, TemplatePath, TemplateContent, CustomTerms)
             VALUES (:contract_id, :template_path, :template_content, :custom_terms)
             ON DUPLICATE KEY UPDATE
                TemplatePath = VALUES(TemplatePath),
                TemplateContent = VALUES(TemplateContent),
                CustomTerms = VALUES(CustomTerms)',
            [
                ':contract_id' => $contractId,
                ':template_path' => $detail['TemplatePath'] ?? null,
                ':template_content' => $detail['TemplateContent'] ?? null,
                ':custom_terms' => $detail['CustomTerms'] ?? null,
            ]
        );
    }
}
