<?php

namespace App\Controllers\Contract;

use App\Class\Contract\ContractService;
use App\Metadata\CTR\CTRContractMD;

class Contract extends AbstractContractController
{
    public function __construct() { $this->boot('Contract'); }

    public function index(): void
    {
        try { $repositoryId = $this->repositoryId(); $records = $this->read->contracts($repositoryId); $groups = $this->read->groups($repositoryId); }
        catch (\Throwable $exception) { $this->error($exception); $records = []; $groups = []; }
        $design = $this->listDesign(0);
        $this->view('SBAdmin/Contract/ContractView', ['FormDesign' => $design, 'FormData' => $records, 'Groups' => $groups, 'Filters' => $_GET, 'SelectedFilter' => null]);
    }

    public function user(string $userId): void
    {
        $userId = rawurldecode($userId);
        try { $repositoryId = $this->repositoryId(); $records = $this->read->contracts($repositoryId, ['UserId' => $userId]); $groups = $this->read->groups($repositoryId); }
        catch (\Throwable $exception) { $this->error($exception); $records = []; $groups = []; }
        $design = $this->listDesign(2, 'UserId', $userId);
        $this->view('SBAdmin/Contract/ContractView', ['FormDesign' => $design, 'FormData' => $records, 'Groups' => $groups, 'Filters' => ['UserId' => $userId], 'SelectedFilter' => ['Label' => 'Usuário selecionado', 'Field' => 'UserId', 'Value' => $userId]]);
    }

    public function client(string $client): void
    {
        $client = rawurldecode($client);
        try { $repositoryId = $this->repositoryId(); $records = $this->read->contracts($repositoryId, ['Client' => $client]); $groups = $this->read->groups($repositoryId); }
        catch (\Throwable $exception) { $this->error($exception); $records = []; $groups = []; }
        $design = $this->listDesign(3, 'Client', $client);
        $this->view('SBAdmin/Contract/ContractView', ['FormDesign' => $design, 'FormData' => $records, 'Groups' => $groups, 'Filters' => ['Client' => $client], 'SelectedFilter' => ['Label' => 'Cliente selecionado', 'Field' => 'Client', 'Value' => $client]]);
    }

    public function group(string $groupId): void
    {
        $groupId = rawurldecode($groupId);
        try { $repositoryId = $this->repositoryId(); $records = $this->read->contracts($repositoryId, ['ConcurrencyGroupId' => $groupId]); $groups = $this->read->groups($repositoryId); }
        catch (\Throwable $exception) { $this->error($exception); $records = []; $groups = []; }
        $design = $this->listDesign(4, 'ConcurrencyGroupId', $groupId);
        $this->view('SBAdmin/Contract/ContractView', ['FormDesign' => $design, 'FormData' => $records, 'Groups' => $groups, 'Filters' => ['ConcurrencyGroupId' => $groupId], 'SelectedFilter' => ['Label' => 'Grupo selecionado', 'Field' => 'ConcurrencyGroupId', 'Value' => $this->groupName($groups, $groupId)]]);
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
        $design = $this->design('Contratos', 'ContractFlow > Contratos', [$this->tab('Consulta', '/Contract/Contract'), $this->tab($id === '' ? 'Novo contrato' : 'Contrato', '/Contract/Contract/Show/' . $id), $this->tab('Usuário', '#', true), $this->tab('Cliente', '#', true), $this->tab('Grupo', '#', true)], 1, 'ContractViewForm.php');
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
        return ['UserId' => $this->value('UserId') ?? '', 'Description' => $this->value('Description'), 'Client' => $this->value('Client'), 'ConcurrencyGroupId' => $this->integer('ConcurrencyGroupId', 0), 'ParentContractId' => $this->value('ParentContractId'), 'BillingCycle' => $this->value('BillingCycle'), 'StartDate' => $this->value('StartDate'), 'EndDate' => $this->value('EndDate'), 'ContractHash' => $this->value('ContractHash'), 'Detail' => ['TemplatePath' => $this->value('TemplatePath'), 'TemplateContent' => $_POST['TemplateContent'] ?? null, 'CustomTerms' => $_POST['CustomTerms'] ?? null]];
    }

    private function emptyContract(): array
    {
        return ['UserId' => '', 'Description' => '', 'Client' => '', 'ConcurrencyGroupId' => '', 'ParentContractId' => '', 'BillingCycle' => '', 'StartDate' => '', 'EndDate' => '', 'ContractHash' => '', 'TemplatePath' => '', 'TemplateContent' => '', 'CustomTerms' => '', 'Status' => 'Draft'];
    }

    private function listDesign(int $current, string $selectedField = '', string $selectedValue = ''): array
    {
        $tabs = [
            'UserId' => ['Index' => 2, 'Name' => 'Usuário', 'Route' => '/Contract/Contract/User/'],
            'Client' => ['Index' => 3, 'Name' => 'Cliente', 'Route' => '/Contract/Contract/Client/'],
            'ConcurrencyGroupId' => ['Index' => 4, 'Name' => 'Grupo', 'Route' => '/Contract/Contract/Group/'],
        ];
        $design = $this->design('Contratos', 'ContractFlow > Contratos', [
            $this->tab('Consulta', '/Contract/Contract'),
            $this->tab('Novo contrato', '/Contract/Contract/Show/'),
            $this->tab('Usuário', $selectedField === 'UserId' ? $tabs['UserId']['Route'] . rawurlencode($selectedValue) : '#', $selectedField !== 'UserId'),
            $this->tab('Cliente', $selectedField === 'Client' ? $tabs['Client']['Route'] . rawurlencode($selectedValue) : '#', $selectedField !== 'Client'),
            $this->tab('Grupo', $selectedField === 'ConcurrencyGroupId' ? $tabs['ConcurrencyGroupId']['Route'] . rawurlencode($selectedValue) : '#', $selectedField !== 'ConcurrencyGroupId'),
        ], $current, 'ContractViewList.php');
        $fields = ['Id', 'UserId', 'Description', 'Client', 'Status', 'ConcurrencyGroupId', 'BillingCycle', 'StartDate', 'EndDate', 'CreatedAt'];
        $design['Styles']['CSSFiles'] = ['dataTables'];
        $design['Scripts']['Body'] = ['dataTables'];
        $design['Fields'] = array_intersect_key(CTRContractMD::FIELDS_MD, array_flip($fields));
        $design['Hidden'] = [];

        return $design;
    }

    private function groupName(array $groups, string $groupId): string
    {
        foreach ($groups as $group) {
            if ((string) ($group['Id'] ?? '') === $groupId) {
                return (string) ($group['Name'] ?? $groupId);
            }
        }

        return $groupId;
    }
}
