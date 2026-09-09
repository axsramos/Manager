<?php require_once App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Contract/ContractViewHelpers.php'; ?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <div><strong><?= ctr_e($data['Repository']['CasRpsDsc'] ?? 'Repositório não identificado'); ?></strong><div class="text-muted small"><?= ctr_e($data['Repository']['CasRpsCod'] ?? ''); ?></div></div>
    <div class="btn-group" role="group"><a class="btn btn-primary" href="/Contract/Contract">Contratos</a><a class="btn btn-outline-primary" href="/Contract/Install">Instalação</a></div>
</div>
<div class="row">
    <?php foreach ([['Contratos ativos', 'ActiveContracts', 'success', 'fas fa-file-contract'], ['Rascunhos', 'DraftContracts', 'secondary', 'fas fa-edit'], ['Próximos vencimentos', 'ExpiringContracts', 'warning', 'fas fa-calendar-times'], ['Itens com franquia', 'ItemsWithUsageLimit', 'info', 'fas fa-tachometer-alt']] as [$label, $key, $color, $icon]): ?>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card bg-<?= $color; ?> text-white h-100">
            <div class="card-body text-center">
                <i class="<?= $icon; ?> fa-4x mb-3"></i>
                <div class="font-weight-bold text-uppercase mb-3"><?= $label; ?></div>
                <hr class="bg-white my-3">
                <div class="small mb-0"><?= (int) $data[$key]; ?> item(ns)</div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<div class="row"><div class="col-lg-7 mb-3"><h5>Consumos recentes</h5><div class="table-responsive"><table class="table table-sm"><thead><tr><th>Item</th><th>Movimento</th><th>Saldo</th><th>Data</th></tr></thead><tbody><?php foreach ($data['RecentConsumptions'] as $row): ?><tr><td><?= ctr_e($row['ItemDescription']); ?></td><td><?= (int) $row['ConsumedQuantity']; ?></td><td><?= (int) $row['CurrentBalance']; ?></td><td><?= ctr_date($row['ConsumedAt']); ?></td></tr><?php endforeach; ?></tbody></table></div></div><div class="col-lg-5 mb-3"><h5>Instalações recentes</h5><div class="table-responsive"><table class="table table-sm"><thead><tr><th>Pacote</th><th>Status</th><th>Aplicado</th></tr></thead><tbody><?php foreach ($data['Checkpoints'] as $row): ?><tr><td><?= ctr_e($row['ProductKey']); ?> <?= ctr_e($row['Version']); ?></td><td><?= ctr_e($row['Status']); ?></td><td><?= ctr_date($row['AppliedAt']); ?></td></tr><?php endforeach; ?></tbody></table></div></div></div>
