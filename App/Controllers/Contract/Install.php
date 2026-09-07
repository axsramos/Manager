<?php

namespace App\Controllers\Contract;

use App\Class\Contract\ContractPackageInstaller;

class Install extends AbstractContractController
{
    public function __construct() { $this->boot('ContractInstall'); }

    public function index(): void
    {
        $repository = null; $checkpoints = []; $packages = []; $result = null;
        try {
            $repositoryId = $this->repositoryId(); $installer = new ContractPackageInstaller();
            $repository = $this->read->repository($repositoryId); $checkpoints = $this->read->checkpoints($repositoryId); $packages = $installer->availablePackages();
            if (isset($_POST['btnApply']) || isset($_POST['btnValidate'])) {
                $hash = $this->value('PackageHash') ?? '';
                if (isset($_POST['btnApply'])) { $result = $installer->install($repositoryId, $hash); $this->success($result['message']); $checkpoints = $this->read->checkpoints($repositoryId); }
                else { $result = ['status' => 'validated', 'message' => 'Hash informado para validação.']; foreach ($packages as $package) { if (hash_equals($package['hash'], strtolower($hash))) { $result['package'] = $package; break; } } if (!isset($result['package'])) { throw new \RuntimeException('Pacote ContractFlow não localizado para o hash informado.'); } }
            }
        } catch (\Throwable $exception) { $this->error($exception); }
        $design = $this->design('Instalação', 'ContractFlow > Instalação', [$this->tab('Instalação', '/Contract/Install'), $this->tab('Checkpoints', '/Contract/Install/Checkpoints')], 0, 'InstallViewForm.php');
        $this->view('SBAdmin/Contract/InstallView', ['FormDesign' => $design, 'Repository' => $repository, 'Packages' => $packages, 'Result' => $result, 'FormData' => ['PackageHash' => $this->value('PackageHash') ?? '']]);
    }

    public function checkpoints(): void
    {
        try { $repositoryId = $this->repositoryId(); $records = $this->read->checkpoints($repositoryId); } catch (\Throwable $exception) { $this->error($exception); $records = []; }
        $design = $this->design('Instalação', 'ContractFlow > Instalação > Checkpoints', [$this->tab('Instalação', '/Contract/Install'), $this->tab('Checkpoints', '/Contract/Install/Checkpoints')], 1, 'InstallViewCheckpointList.php');
        $this->view('SBAdmin/Contract/InstallView', ['FormDesign' => $design, 'FormData' => $records]);
    }
}
