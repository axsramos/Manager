<?php

namespace App\Controllers\Contract;

use App\Class\Contract\ContractLedgerService;

class ContractLedger extends AbstractContractController
{
    public function __construct() { $this->boot('ContractLedger'); }

    public function index(string $itemId = ''): void
    {
        try { $repositoryId = $this->repositoryId(); $records = $this->read->ledger($repositoryId, $itemId === '' ? null : (int) $itemId); $item = $itemId === '' ? null : $this->read->item($repositoryId, (int) $itemId); }
        catch (\Throwable $exception) { $this->error($exception); $records = []; $item = null; }
        $design = $this->design('Extrato de consumo', 'ContractFlow > Extrato de consumo', [$this->tab('Extrato', '/Contract/ContractLedger/Index/' . $itemId), $this->tab('Registrar', '/Contract/ContractLedger/Show/' . $itemId, $itemId === '')], 0, 'ContractLedgerViewList.php');
        $this->view('SBAdmin/Contract/ContractLedgerView', ['FormDesign' => $design, 'FormData' => $records, 'Item' => $item, 'ItemId' => $itemId]);
    }

    public function show(string $itemId): void
    {
        $item = null;
        try {
            $repositoryId = $this->repositoryId(); $item = $this->read->item($repositoryId, (int) $itemId); if ($item === null) { throw new \RuntimeException('Item não localizado.'); }
            if (isset($_POST['btnConsume']) || isset($_POST['btnReverse'])) {
                $quantity = $this->integer('Quantity', 0) ?? 0; $service = new ContractLedgerService();
                $result = isset($_POST['btnReverse']) ? $service->reverse($repositoryId, (int) $itemId, $quantity, $this->ledgerData()) : $service->consume($repositoryId, (int) $itemId, $quantity, $this->ledgerData());
                $this->success($result['message']); $this->redirect('/Contract/ContractLedger/Index/' . $itemId);
            }
        } catch (\Throwable $exception) { $this->error($exception); }
        $design = $this->design('Registrar consumo', 'ContractFlow > Extrato de consumo > Registrar', [$this->tab('Extrato', '/Contract/ContractLedger/Index/' . $itemId), $this->tab('Registrar', '/Contract/ContractLedger/Show/' . $itemId)], 1, 'ContractLedgerViewForm.php');
        $this->view('SBAdmin/Contract/ContractLedgerView', ['FormDesign' => $design, 'Item' => $item, 'ItemId' => $itemId, 'FormData' => ['Quantity' => '', 'ExternalReference' => '', 'Description' => '']]);
    }

    private function ledgerData(): array { return ['ExternalReference' => $this->value('ExternalReference'), 'Description' => $this->value('Description')]; }
}
