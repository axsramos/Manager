<?php

namespace App\Controllers\Contract;

use App\Class\Contract\ConcurrencyGroupService;

class ConcurrencyGroup extends AbstractContractController
{
    public function __construct() { $this->boot('ContractConcurrencyGroup'); }

    public function index(): void
    {
        try { $repositoryId = $this->repositoryId(); $records = $this->read->groups($repositoryId); } catch (\Throwable $exception) { $this->error($exception); $records = []; }
        $design = $this->design('Grupos de concorrência', 'ContractFlow > Grupos de concorrência', [$this->tab('Consulta', '/Contract/ConcurrencyGroup'), $this->tab('Novo grupo', '/Contract/ConcurrencyGroup/Show')], 0, 'ConcurrencyGroupViewList.php');
        $this->view('SBAdmin/Contract/ConcurrencyGroupView', ['FormDesign' => $design, 'FormData' => $records]);
    }

    public function show(string $id = ''): void
    {
        $group = null;
        try { $repositoryId = $this->repositoryId(); $service = new ConcurrencyGroupService();
            if (isset($_POST['btnSaveGroup'])) { if ($id === '') { $result = $service->create($repositoryId, $this->value('Name') ?? '', $this->checkbox('AllowMultipleActive') === 1); $this->redirect('/Contract/ConcurrencyGroup/Show/' . $result['group_id']); } $service->update($repositoryId, (int) $id, $this->value('Name') ?? '', $this->checkbox('AllowMultipleActive') === 1); $this->success('Grupo atualizado.'); }
            foreach ($this->read->groups($repositoryId) as $candidate) { if ((int) $candidate['Id'] === (int) $id) { $group = $candidate; break; } } if ($id !== '' && $group === null) { throw new \RuntimeException('Grupo não localizado.'); }
        } catch (\Throwable $exception) { $this->error($exception); }
        $design = $this->design('Grupos de concorrência', 'ContractFlow > Grupos de concorrência', [$this->tab('Consulta', '/Contract/ConcurrencyGroup'), $this->tab($id === '' ? 'Novo grupo' : 'Grupo', '/Contract/ConcurrencyGroup/Show/' . $id)], 1, 'ConcurrencyGroupViewForm.php');
        $this->view('SBAdmin/Contract/ConcurrencyGroupView', ['FormDesign' => $design, 'FormData' => $group ?? ['Name' => '', 'AllowMultipleActive' => 0], 'GroupId' => $id]);
    }
}
