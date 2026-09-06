<?php

namespace App\Class\Contract;

class ContractItemService extends AbstractContractService
{
    public function addItem(string $repositoryId, string $contractId, array $data): array
    {
        $attributes = $data['ItemAttributes'] ?? null;
        $customNotes = $data['CustomNotes'] ?? null;

        $this->pdo->beginTransaction();
        try {
            $this->execute(
                'INSERT INTO CTRContractItem
                (RepositoryId, ContractId, ServiceCode, Description, CollaboratorReference, IsIncluded, DisplayOrder, ItemType, ValidUntil, IsBillable, Quantity, UnitPrice, DiscountValue, ReplacementValue, UsageLimit)
                VALUES
                (:repository_id, :contract_id, :service_code, :description, :collaborator_reference, :is_included, :display_order, :item_type, :valid_until, :is_billable, :quantity, :unit_price, :discount_value, :replacement_value, :usage_limit)',
                [
                    ':repository_id' => $repositoryId,
                    ':contract_id' => $contractId,
                    ':service_code' => $data['ServiceCode'] ?? null,
                    ':description' => $data['Description'],
                    ':collaborator_reference' => $data['CollaboratorReference'] ?? null,
                    ':is_included' => $data['IsIncluded'] ?? 1,
                    ':display_order' => $data['DisplayOrder'] ?? 0,
                    ':item_type' => $data['ItemType'] ?? 'Base',
                    ':valid_until' => $data['ValidUntil'] ?? null,
                    ':is_billable' => $data['IsBillable'] ?? 0,
                    ':quantity' => $data['Quantity'] ?? null,
                    ':unit_price' => $data['UnitPrice'] ?? null,
                    ':discount_value' => $data['DiscountValue'] ?? null,
                    ':replacement_value' => $data['ReplacementValue'] ?? 0,
                    ':usage_limit' => $data['UsageLimit'] ?? null,
                ]
            );
            $itemId = (int) $this->pdo->lastInsertId();

            if ($attributes !== null || $customNotes !== null) {
                $this->execute(
                    'INSERT INTO CTRContractItemDetail (ContractItemId, ItemAttributes, CustomNotes)
                     VALUES (:item_id, :attributes, :notes)',
                    [
                        ':item_id' => $itemId,
                        ':attributes' => is_array($attributes) ? json_encode($attributes, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : $attributes,
                        ':notes' => $customNotes,
                    ]
                );
            }

            $item = $this->fetchOne('SELECT Id, LineTotal FROM CTRContractItem WHERE Id = :id', [':id' => $itemId]);
            $this->pdo->commit();
        } catch (\Throwable $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }

        return $this->result('created', 'Item de contrato criado.', [
            'contract_item_id' => $itemId,
            'line_total' => isset($item['LineTotal']) ? (int) $item['LineTotal'] : null,
        ]);
    }
}
