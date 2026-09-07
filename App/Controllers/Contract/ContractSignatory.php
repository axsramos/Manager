<?php

namespace App\Controllers\Contract;

use App\Class\Contract\ContractSignatoryService;

class ContractSignatory extends AbstractContractController
{
    public function __construct() { $this->boot('ContractSignatory'); }

    public function index(string $contractId): void
    {
        try { $repositoryId = $this->repositoryId(); $contract = $this->read->contract($repositoryId, $contractId); if ($contract === null) { throw new \RuntimeException('Contrato não localizado.'); }
            if (isset($_POST['btnSign'])) { $result = (new ContractSignatoryService())->sign($repositoryId, $contractId, $this->integer('UserId', 0) ?? 0, $this->value('Role') ?? 'Customer', (string) ($_SERVER['REMOTE_ADDR'] ?? ''), $this->value('DeviceFingerprint')); $this->success($result['message']); }
            $records = $this->read->signatories($repositoryId, $contractId);
        } catch (\Throwable $exception) { $this->error($exception); $contract = []; $records = []; }
        $design = $this->design('Signatários', 'ContractFlow > Contratos > Signatários', [$this->tab('Signatários', '/Contract/ContractSignatory/Index/' . $contractId)], 0, 'ContractSignatoryViewList.php');
        $this->view('SBAdmin/Contract/ContractSignatoryView', ['FormDesign' => $design, 'FormData' => $records, 'Contract' => $contract, 'ContractId' => $contractId]);
    }
}
