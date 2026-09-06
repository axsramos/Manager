<?php

namespace App\Class\Contract;

use RuntimeException;

class ContractLedgerService extends AbstractContractService
{
    public function consume(string $repositoryId, int $contractItemId, int $quantity, array $data = []): array
    {
        if ($quantity === 0) {
            throw new RuntimeException('A quantidade de consumo não pode ser zero.');
        }

        $this->pdo->beginTransaction();
        try {
            $item = $this->fetchOne(
                'SELECT Id, UsageLimit FROM CTRContractItem WHERE Id = :id AND RepositoryId = :repository_id FOR UPDATE',
                [':id' => $contractItemId, ':repository_id' => $repositoryId]
            );
            if ($item === null) {
                throw new RuntimeException('Item de contrato não localizado.');
            }
            if ($item['UsageLimit'] === null) {
                throw new RuntimeException('Item de contrato não possui franquia de consumo.');
            }

            $last = $this->fetchOne(
                'SELECT CurrentBalance FROM CTRContractItemConsumption WHERE ContractItemId = :item_id ORDER BY ConsumedAt DESC, Id DESC LIMIT 1 FOR UPDATE',
                [':item_id' => $contractItemId]
            );

            $previousBalance = $last === null ? (int) $item['UsageLimit'] : (int) $last['CurrentBalance'];
            $currentBalance = $previousBalance - $quantity;
            if ($currentBalance < 0) {
                throw new RuntimeException('Saldo de franquia insuficiente.');
            }

            $this->execute(
                'INSERT INTO CTRContractItemConsumption
                (RepositoryId, ContractItemId, PreviousBalance, ConsumedQuantity, CurrentBalance, ExternalReference, Description)
                VALUES (:repository_id, :item_id, :previous_balance, :quantity, :current_balance, :external_reference, :description)',
                [
                    ':repository_id' => $repositoryId,
                    ':item_id' => $contractItemId,
                    ':previous_balance' => $previousBalance,
                    ':quantity' => $quantity,
                    ':current_balance' => $currentBalance,
                    ':external_reference' => $data['ExternalReference'] ?? null,
                    ':description' => $data['Description'] ?? null,
                ]
            );
            $ledgerId = (int) $this->pdo->lastInsertId();
            $this->pdo->commit();
        } catch (\Throwable $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }

        return $this->result('created', 'Consumo registrado no ledger.', [
            'consumption_id' => $ledgerId,
            'previous_balance' => $previousBalance,
            'current_balance' => $currentBalance,
        ]);
    }

    public function reverse(string $repositoryId, int $contractItemId, int $quantity, array $data = []): array
    {
        if ($quantity <= 0) {
            throw new RuntimeException('A quantidade de estorno deve ser positiva.');
        }

        return $this->consume($repositoryId, $contractItemId, -$quantity, $data);
    }
}
