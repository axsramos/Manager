<?php

namespace App\Class\Contract;

class ContractSignatoryService extends AbstractContractService
{
    public function sign(string $repositoryId, string $contractId, int $userId, string $role, string $ipAddress, ?string $deviceFingerprint = null): array
    {
        $this->execute(
            'INSERT INTO CTRContractSignatory
            (RepositoryId, ContractId, UserId, Role, IpAddress, DeviceFingerprint)
            VALUES (:repository_id, :contract_id, :user_id, :role, :ip_address, :device_fingerprint)',
            [
                ':repository_id' => $repositoryId,
                ':contract_id' => $contractId,
                ':user_id' => $userId,
                ':role' => $role,
                ':ip_address' => $ipAddress,
                ':device_fingerprint' => $deviceFingerprint,
            ]
        );

        return $this->result('created', 'Signatário registrado.', [
            'signatory_id' => (int) $this->pdo->lastInsertId(),
        ]);
    }
}
