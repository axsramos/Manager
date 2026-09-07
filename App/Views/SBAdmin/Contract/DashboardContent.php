<?php require_once App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Contract/ContractViewHelpers.php'; ?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <div><strong><?= ctr_e($data['Repository']['CasRpsDsc'] ?? 'Repositório não identificado'); ?></strong><div class="text-muted small"><?= ctr_e($data['Repository']['CasRpsCod'] ?? ''); ?></div></div>
    <div class="btn-group" role="group"><a class="btn btn-primary" href="/Contract/Contract">Contratos</a><a class="btn btn-outline-primary" href="/Contract/Install">Instalação</a></div>
</div>
<div class="row">
    <?php foreach ([['Contratos ativos', 'ActiveContracts', 'success'], ['Rascunhos', 'DraftContracts', 'secondary'], ['Próximos vencimentos', 'ExpiringContracts', 'warning'], ['Itens com franquia', 'ItemsWithUsageLimit', 'info']] as [$label, $key, $color]): ?>
    <div class="col-xl-3 col-md-6 mb-3"><div class="card border-left-<?= $color; ?> h-100 py-2"><div class="card-body"><div class="text-xs font-weight-bold text-<?= $color; ?> text-uppercase mb-1"><?= $label; ?></div><div class="h4 mb-0 font-weight-bold text-gray-800"><?= (int) $data[$key]; ?></div></div></div></div>
    <?php endforeach; ?>
</div>
<div class="row"><div class="col-lg-7 mb-3"><h5>Consumos recentes</h5><div class="table-responsive"><table class="table table-sm"><thead><tr><th>Item</th><th>Movimento</th><th>Saldo</th><th>Data</th></tr></thead><tbody><?php foreach ($data['RecentConsumptions'] as $row): ?><tr><td><?= ctr_e($row['ItemDescription']); ?></td><td><?= (int) $row['ConsumedQuantity']; ?></td><td><?= (int) $row['CurrentBalance']; ?></td><td><?= ctr_date($row['ConsumedAt']); ?></td></tr><?php endforeach; ?></tbody></table></div></div><div class="col-lg-5 mb-3"><h5>Instalações recentes</h5><div class="table-responsive"><table class="table table-sm"><thead><tr><th>Pacote</th><th>Status</th><th>Aplicado</th></tr></thead><tbody><?php foreach ($data['Checkpoints'] as $row): ?><tr><td><?= ctr_e($row['ProductKey']); ?> <?= ctr_e($row['Version']); ?></td><td><?= ctr_e($row['Status']); ?></td><td><?= ctr_date($row['AppliedAt']); ?></td></tr><?php endforeach; ?></tbody></table></div></div></div>
