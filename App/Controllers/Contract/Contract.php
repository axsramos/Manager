<?php

namespace App\Controllers\Contract;

use App\Class\Contract\ContractService;

class Contract extends AbstractContractController
{
    public function __construct() { $this->boot('Contract'); }

    public function index(): void
    {
        try { $repositoryId = $this->repositoryId(); $records = $this->read->contracts($repositoryId, ['Status' => $_GET['Status'] ?? '', 'UserId' => $_GET['UserId'] ?? '', 'ConcurrencyGroupId' => $_GET['ConcurrencyGroupId'] ?? '', 'ParentContractId' => $_GET['ParentContractId'] ?? '']); $groups = $this->read->groups($repositoryId); }
        catch (\Throwable $exception) { $this->error($exception); $records = []; $groups = []; }
        $design = $this->design('Contratos', 'ContractFlow > Contratos', [$this->tab('Consulta', '/Contract/Contract'), $this->tab('Novo contrato', '/Contract/Contract/Show')], 0, 'ContractViewList.php');
        $this->view('SBAdmin/Contract/ContractView', ['FormDesign' => $design, 'FormData' => $records, 'Groups' => $groups, 'Filters' => $_GET]);
    }

    public function show(string $id = ''): void
    {
        $contract = null; $groups = [];
        try {
            $repositoryId = $this->repositoryId(); $groups = $this->read->groups($repositoryId); $service = new ContractService();
            if (isset($_POST['btnSaveDraft'])) {
                $payload = $this->payload();
                if ($id === '') { $result = $service->createDraft($repositoryId, $payload['UserId'], $payload['ConcurrencyGroupId'], $payload); $this->redirect('/Contract/Contract/Show/' . $result['contract_id']); }
                $service->updateDraft($repositoryId, $id, $payload); $this->success('Contrato em rascunho atualizado.');
            }
            if ($id !== '' && isset($_POST['btnActivate'])) { $service->activate($repositoryId, $id); $this->success('Contrato ativado.'); }
            if ($id !== '' && isset($_POST['btnCancel'])) { $service->setStatus($repositoryId, $id, ContractService::STATUS_CANCELED); $this->success('Contrato cancelado.'); }
            if ($id !== '' && isset($_POST['btnExpire'])) { $service->setStatus($repositoryId, $id, ContractService::STATUS_EXPIRED); $this->success('Contrato expirado.'); }
            if ($id !== '') { $contract = $this->read->contract($repositoryId, $id); if ($contract === null) { throw new \RuntimeException('Contrato não localizado.'); } }
        } catch (\Throwable $exception) { $this->error($exception); }
        $design = $this->design('Contratos', 'ContractFlow > Contratos', [$this->tab('Consulta', '/Contract/Contract'), $this->tab($id === '' ? 'Novo contrato' : 'Contrato', '/Contract/Contract/Show/' . $id)], 1, 'ContractViewForm.php');
        $this->view('SBAdmin/Contract/ContractView', ['FormDesign' => $design, 'FormData' => $contract ?? $this->emptyContract(), 'Groups' => $groups, 'ContractId' => $id]);
    }

    public function detail(string $id): void
    {
        try { $repositoryId = $this->repositoryId(); $contract = $this->read->contract($repositoryId, $id); if ($contract === null) { throw new \RuntimeException('Contrato não localizado.'); } $items = $this->read->items($repositoryId, $id); $ledger = $this->read->ledger($repositoryId); $ledger = array_values(array_filter($ledger, static fn(array $row): bool => $row['ContractId'] === $id)); $signatories = $this->read->signatories($repositoryId, $id); }
        catch (\Throwable $exception) { $this->error($exception); $contract = []; $items = []; $ledger = []; $signatories = []; }
        $design = $this->design('Detalhe do contrato', 'ContractFlow > Contratos > Detalhe', [$this->tab('Contrato', '/Contract/Contract/Show/' . $id), $this->tab('Detalhe', '/Contract/Contract/Detail/' . $id)], 1, 'ContractViewDetail.php');
        $this->view('SBAdmin/Contract/ContractView', ['FormDesign' => $design, 'FormData' => $contract, 'Items' => $items, 'Ledger' => $ledger, 'Signatories' => $signatories, 'ContractId' => $id]);
    }

    private function payload(): array
    {
        return ['UserId' => $this->integer('UserId', 0), 'ConcurrencyGroupId' => $this->integer('ConcurrencyGroupId', 0), 'ParentContractId' => $this->value('ParentContractId'), 'BillingCycle' => $this->value('BillingCycle'), 'StartDate' => $this->value('StartDate'), 'EndDate' => $this->value('EndDate'), 'ContractHash' => $this->value('ContractHash'), 'Detail' => ['TemplatePath' => $this->value('TemplatePath'), 'TemplateContent' => $_POST['TemplateContent'] ?? null, 'CustomTerms' => $_POST['CustomTerms'] ?? null]];
    }

    private function emptyContract(): array
    {
        return ['UserId' => '', 'ConcurrencyGroupId' => '', 'ParentContractId' => '', 'BillingCycle' => '', 'StartDate' => '', 'EndDate' => '', 'ContractHash' => '', 'TemplatePath' => '', 'TemplateContent' => '', 'CustomTerms' => '', 'Status' => 'Draft'];
    }
}
