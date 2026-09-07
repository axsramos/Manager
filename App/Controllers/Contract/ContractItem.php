<?php

namespace App\Controllers\Contract;

use App\Class\Contract\ContractItemService;

class ContractItem extends AbstractContractController
{
    public function __construct() { $this->boot('ContractItem'); }

    public function index(string $contractId = ''): void
    {
        try { $repositoryId = $this->repositoryId(); $contract = $this->read->contract($repositoryId, $contractId); if ($contract === null) { throw new \RuntimeException('Contrato não localizado.'); } $records = $this->read->items($repositoryId, $contractId); }
        catch (\Throwable $exception) { $this->error($exception); $contract = []; $records = []; }
        $design = $this->design('Itens do contrato', 'ContractFlow > Contratos > Itens', [$this->tab('Itens', '/Contract/ContractItem/Index/' . $contractId), $this->tab('Novo item', '/Contract/ContractItem/Show/' . $contractId)], 0, 'ContractItemViewList.php');
        $this->view('SBAdmin/Contract/ContractItemView', ['FormDesign' => $design, 'FormData' => $records, 'Contract' => $contract, 'ContractId' => $contractId]);
    }

    public function show(string $contractId, string $itemId = ''): void
    {
        $item = null; $contract = null;
        try {
            $repositoryId = $this->repositoryId(); $contract = $this->read->contract($repositoryId, $contractId); if ($contract === null) { throw new \RuntimeException('Contrato não localizado.'); }
            $service = new ContractItemService();
            if (isset($_POST['btnSaveItem'])) {
                $payload = $this->payload();
                if ($itemId === '') { $result = $service->addItem($repositoryId, $contractId, $payload); $this->redirect('/Contract/ContractItem/Show/' . $contractId . '/' . $result['contract_item_id']); }
                $service->updateItem($repositoryId, (int) $itemId, $payload); $this->success('Item atualizado.');
            }
            if ($itemId !== '') { $item = $this->read->item($repositoryId, (int) $itemId); if ($item === null || $item['ContractId'] !== $contractId) { throw new \RuntimeException('Item não localizado.'); } }
        } catch (\Throwable $exception) { $this->error($exception); }
        $design = $this->design('Itens do contrato', 'ContractFlow > Contratos > Itens', [$this->tab('Itens', '/Contract/ContractItem/Index/' . $contractId), $this->tab($itemId === '' ? 'Novo item' : 'Item', '/Contract/ContractItem/Show/' . $contractId . '/' . $itemId)], 1, 'ContractItemViewForm.php');
        $this->view('SBAdmin/Contract/ContractItemView', ['FormDesign' => $design, 'FormData' => $item ?? $this->emptyItem(), 'Contract' => $contract, 'ContractId' => $contractId, 'ItemId' => $itemId]);
    }

    private function payload(): array
    {
        $attributes = $this->value('ItemAttributes');
        if ($attributes !== null) { json_decode($attributes, true); if (json_last_error() !== JSON_ERROR_NONE) { throw new \RuntimeException('Atributos do item devem conter JSON válido.'); } }
        return ['ServiceCode' => $this->value('ServiceCode'), 'Description' => $this->value('Description') ?? '', 'CollaboratorReference' => $this->value('CollaboratorReference'), 'IsIncluded' => $this->checkbox('IsIncluded'), 'DisplayOrder' => $this->integer('DisplayOrder', 0), 'ItemType' => $this->value('ItemType', 'Base'), 'ValidUntil' => $this->value('ValidUntil'), 'IsBillable' => $this->checkbox('IsBillable'), 'Quantity' => $this->integer('Quantity'), 'UnitPrice' => $this->money('UnitPrice'), 'DiscountValue' => $this->money('DiscountValue'), 'ReplacementValue' => $this->money('ReplacementValue') ?? 0, 'UsageLimit' => $this->integer('UsageLimit'), 'ItemAttributes' => $attributes, 'CustomNotes' => $_POST['CustomNotes'] ?? null];
    }

    private function emptyItem(): array
    {
        return ['ServiceCode' => '', 'Description' => '', 'CollaboratorReference' => '', 'IsIncluded' => 1, 'DisplayOrder' => 0, 'ItemType' => 'Base', 'ValidUntil' => '', 'IsBillable' => 0, 'Quantity' => '', 'UnitPrice' => '', 'DiscountValue' => '', 'ReplacementValue' => 0, 'UsageLimit' => '', 'ItemAttributes' => '', 'CustomNotes' => '', 'LineTotal' => 0, 'ContractStatus' => 'Draft'];
    }
}
