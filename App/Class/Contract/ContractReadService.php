<?php

namespace App\Class\Contract;

class ContractReadService extends AbstractContractService
{
    public function repository(string $repositoryId): ?array
    {
        return $this->fetchOne('SELECT CasRpsCod, CasRpsDsc FROM CasRps WHERE CasRpsCod = :repository_id LIMIT 1', [':repository_id' => $repositoryId]);
    }

    public function dashboard(string $repositoryId): array
    {
        $summary = $this->fetchOne("SELECT SUM(Status = 'Active') AS ActiveContracts, SUM(Status = 'Draft') AS DraftContracts, SUM(EndDate IS NOT NULL AND EndDate >= NOW() AND EndDate < DATE_ADD(NOW(), INTERVAL 30 DAY)) AS ExpiringContracts FROM CTRContract WHERE RepositoryId = :repository_id", [':repository_id' => $repositoryId]) ?: [];
        $usage = $this->fetchOne('SELECT COUNT(*) AS ItemsWithUsageLimit FROM CTRContractItem WHERE RepositoryId = :repository_id AND UsageLimit IS NOT NULL', [':repository_id' => $repositoryId]) ?: [];

        return [
            'ActiveContracts' => (int) ($summary['ActiveContracts'] ?? 0), 'DraftContracts' => (int) ($summary['DraftContracts'] ?? 0),
            'ExpiringContracts' => (int) ($summary['ExpiringContracts'] ?? 0), 'ItemsWithUsageLimit' => (int) ($usage['ItemsWithUsageLimit'] ?? 0),
            'RecentConsumptions' => $this->fetchAll('SELECT c.Id, c.ContractItemId, c.ConsumedQuantity, c.CurrentBalance, c.Description, c.ConsumedAt, i.Description AS ItemDescription FROM CTRContractItemConsumption c INNER JOIN CTRContractItem i ON i.Id = c.ContractItemId AND i.RepositoryId = c.RepositoryId WHERE c.RepositoryId = :repository_id ORDER BY c.ConsumedAt DESC, c.Id DESC LIMIT 8', [':repository_id' => $repositoryId]),
            'Checkpoints' => $this->fetchAll('SELECT ProductKey, Version, Task, Status, AppliedAt FROM CTRPackageCheckpoint WHERE RepositoryId = :repository_id ORDER BY AppliedAt DESC LIMIT 5', [':repository_id' => $repositoryId]),
        ];
    }

    public function contracts(string $repositoryId, array $filters = []): array
    {
        $conditions = ['RepositoryId = :repository_id']; $parameters = [':repository_id' => $repositoryId];
        foreach (['Status', 'UserId', 'ConcurrencyGroupId', 'ParentContractId', 'Client'] as $field) {
            if (($filters[$field] ?? '') !== '') { $parameter = ':' . strtolower($field); $conditions[] = $field . ' = ' . $parameter; $parameters[$parameter] = $filters[$field]; }
        }
        foreach (['Description'] as $field) {
            if (($filters[$field] ?? '') !== '') { $parameter = ':' . strtolower($field); $conditions[] = $field . ' LIKE ' . $parameter; $parameters[$parameter] = '%' . $filters[$field] . '%'; }
        }
        return $this->fetchAll('SELECT Id, RepositoryId, UserId, Description, Client, ParentContractId, ConcurrencyGroupId, Status, BillingCycle, StartDate, EndDate, CreatedAt FROM CTRContract WHERE ' . implode(' AND ', $conditions) . ' ORDER BY CreatedAt DESC', $parameters);
    }

    public function contract(string $repositoryId, string $contractId): ?array
    {
        return $this->fetchOne('SELECT c.*, d.TemplatePath, d.TemplateContent, d.CustomTerms FROM CTRContract c LEFT JOIN CTRContractDetail d ON d.ContractId = c.Id WHERE c.Id = :id AND c.RepositoryId = :repository_id LIMIT 1', [':id' => $contractId, ':repository_id' => $repositoryId]);
    }

    public function groups(string $repositoryId): array
    {
        return $this->fetchAll('SELECT Id, RepositoryId, Name, AllowMultipleActive FROM CTRConcurrencyGroup WHERE RepositoryId = :repository_id ORDER BY Name', [':repository_id' => $repositoryId]);
    }

    public function items(string $repositoryId, string $contractId): array
    {
        return $this->fetchAll('SELECT i.*, d.ItemAttributes, d.CustomNotes FROM CTRContractItem i LEFT JOIN CTRContractItemDetail d ON d.ContractItemId = i.Id WHERE i.RepositoryId = :repository_id AND i.ContractId = :contract_id ORDER BY i.DisplayOrder, i.Id', [':repository_id' => $repositoryId, ':contract_id' => $contractId]);
    }

    public function item(string $repositoryId, int $itemId): ?array
    {
        return $this->fetchOne('SELECT i.*, d.ItemAttributes, d.CustomNotes, c.Status AS ContractStatus FROM CTRContractItem i INNER JOIN CTRContract c ON c.Id = i.ContractId AND c.RepositoryId = i.RepositoryId LEFT JOIN CTRContractItemDetail d ON d.ContractItemId = i.Id WHERE i.Id = :id AND i.RepositoryId = :repository_id LIMIT 1', [':id' => $itemId, ':repository_id' => $repositoryId]);
    }

    public function ledger(string $repositoryId, ?int $itemId = null): array
    {
        $sql = 'SELECT l.*, i.Description AS ItemDescription, i.ContractId FROM CTRContractItemConsumption l INNER JOIN CTRContractItem i ON i.Id = l.ContractItemId AND i.RepositoryId = l.RepositoryId WHERE l.RepositoryId = :repository_id'; $parameters = [':repository_id' => $repositoryId];
        if ($itemId !== null) { $sql .= ' AND l.ContractItemId = :item_id'; $parameters[':item_id'] = $itemId; }
        return $this->fetchAll($sql . ' ORDER BY l.ConsumedAt DESC, l.Id DESC', $parameters);
    }

    public function signatories(string $repositoryId, string $contractId): array
    {
        return $this->fetchAll('SELECT Id, RepositoryId, ContractId, UserId, Role, SignedAt, IpAddress, DeviceFingerprint FROM CTRContractSignatory WHERE RepositoryId = :repository_id AND ContractId = :contract_id ORDER BY SignedAt DESC, Id DESC', [':repository_id' => $repositoryId, ':contract_id' => $contractId]);
    }

    public function checkpoints(string $repositoryId): array
    {
        return $this->fetchAll('SELECT RepositoryId, ProductKey, Version, Task, Status, ContentHash, Message, AppliedAt FROM CTRPackageCheckpoint WHERE RepositoryId = :repository_id ORDER BY AppliedAt DESC, Task DESC', [':repository_id' => $repositoryId]);
    }
}
