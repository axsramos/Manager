<?php

namespace App\Controllers\Contract;

class Dashboard extends AbstractContractController
{
    public function __construct() { $this->boot('ContractDashboard'); }

    public function index(): void
    {
        try { $repositoryId = $this->repositoryId(); $data = $this->read->dashboard($repositoryId); $data['Repository'] = $this->read->repository($repositoryId); }
        catch (\Throwable $exception) { $this->error($exception); $data = ['Repository' => null, 'RecentConsumptions' => [], 'Checkpoints' => [], 'ActiveContracts' => 0, 'DraftContracts' => 0, 'ExpiringContracts' => 0, 'ItemsWithUsageLimit' => 0]; }
        $data['FormDesign'] = $this->design('Dashboard', 'ContractFlow > Dashboard', [$this->tab('Dashboard', '/Contract/Dashboard'), $this->tab('Contratos', '/Contract/Contract'), $this->tab('Instalação', '/Contract/Install')], 0, 'DashboardContent.php');
        $this->view('SBAdmin/Contract/DashboardView', $data);
    }
}
