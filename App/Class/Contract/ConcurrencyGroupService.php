<?php

namespace App\Class\Contract;

use RuntimeException;

class ConcurrencyGroupService extends AbstractContractService
{
    public function create(string $repositoryId, string $name, bool $allowMultipleActive): array
    {
        $name = trim($name);
        if ($name === '') { throw new RuntimeException('O nome do grupo de concorrência é obrigatório.'); }
        $this->execute('INSERT INTO CTRConcurrencyGroup (RepositoryId, Name, AllowMultipleActive) VALUES (:repository_id, :name, :allow_multiple_active)', [':repository_id' => $repositoryId, ':name' => $name, ':allow_multiple_active' => $allowMultipleActive ? 1 : 0]);
        return $this->result('created', 'Grupo de concorrência criado.', ['group_id' => (int) $this->pdo->lastInsertId()]);
    }

    public function update(string $repositoryId, int $groupId, string $name, bool $allowMultipleActive): array
    {
        $name = trim($name);
        if ($name === '') { throw new RuntimeException('O nome do grupo de concorrência é obrigatório.'); }
        $rows = $this->execute('UPDATE CTRConcurrencyGroup SET Name = :name, AllowMultipleActive = :allow_multiple_active WHERE Id = :id AND RepositoryId = :repository_id', [':name' => $name, ':allow_multiple_active' => $allowMultipleActive ? 1 : 0, ':id' => $groupId, ':repository_id' => $repositoryId]);
        return $this->result($rows > 0 ? 'updated' : 'skipped', 'Grupo de concorrência atualizado.', ['group_id' => $groupId]);
    }
}
