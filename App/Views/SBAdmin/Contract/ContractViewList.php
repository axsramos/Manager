<?php require_once App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Contract/ContractViewHelpers.php'; ?>
<?php $groupNames = array_column($data['Groups'] ?? [], 'Name', 'Id'); ?>
<?php if (($data['SelectedFilter']['Value'] ?? '') !== ''): ?><div class="form-group"><label><?= ctr_e($data['SelectedFilter']['Label']); ?></label><input class="form-control" name="<?= ctr_e($data['SelectedFilter']['Field']); ?>" value="<?= ctr_e($data['SelectedFilter']['Value']); ?>" readonly></div><?php endif; ?>
<section>
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table mr-1"></i>
            Consulta retornou <?= count($data['FormData']); ?> registro(s)
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <?php
                if ($data['FormData']) {
                    echo '<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">';
                    echo '    <thead>';
                    echo '        <tr>';
                    foreach ($data['FormDesign']['Fields'] as $key => $field) {
                        $hidden = (in_array($key, $data['FormDesign']['Hidden']) ? 'hidden' : '');
                        echo '            <th ' . $hidden . '>' . $field['ShortLabel'] . '</th>';
                    }
                    echo '        </tr>';
                    echo '    </thead>';
                    echo '    <tfoot>';
                    echo '        <tr>';
                    foreach ($data['FormDesign']['Fields'] as $key => $field) {
                        $hidden = (in_array($key, $data['FormDesign']['Hidden']) ? 'hidden' : '');
                        echo '            <th ' . $hidden . '>' . $field['ShortLabel'] . '</th>';
                    }
                    echo '        </tr>';
                    echo '    </tfoot>';
                    echo '    <tbody>';
                    foreach ($data['FormData'] as $item) {
                        echo '        <tr>';
                        $isFirst = true;
                        foreach ($data['FormDesign']['Fields'] as $key => $field) {
                            $hidden = (in_array($key, $data['FormDesign']['Hidden']) ? 'hidden' : '');
                            $value = $item[$key] ?? '';
                            if ($key === 'ConcurrencyGroupId') {
                                $value = $groupNames[$item[$key] ?? ''] ?? $value;
                            }
                            if (in_array($key, ['StartDate', 'EndDate', 'CreatedAt'], true)) {
                                $value = ctr_date($value);
                            }
                            if ($key === 'Status') {
                                $value = ctr_status((string) $value);
                            } else {
                                $value = ctr_e((string) $value);
                            }
                            if ($key === 'UserId') {
                                echo '<td ' . $hidden . '><a href="/Contract/Contract/User/' . rawurlencode((string) $item[$key]) . '">' . $value . '</a></td>';
                            } elseif ($key === 'Client' && (string) ($item[$key] ?? '') !== '') {
                                echo '<td ' . $hidden . '><a href="/Contract/Contract/Client/' . rawurlencode((string) $item[$key]) . '">' . $value . '</a></td>';
                            } elseif ($key === 'ConcurrencyGroupId') {
                                echo '<td ' . $hidden . '><a href="/Contract/Contract/Group/' . rawurlencode((string) $item[$key]) . '">' . $value . '</a></td>';
                            } elseif ($isFirst) {
                                $isFirst = false;
                                echo '<td ' . $hidden . '><a href="' . $data['FormDesign']['Tabs']['Items'][1]['Link'] . rawurlencode((string) $item[$key]) . '">' . $value . '</a></td>';
                            } else {
                                echo '<td ' . $hidden . '>' . $value . '</td>';
                            }
                        }
                        echo '        </tr>';
                    }
                    echo '    </tbody>';
                    echo '</table>';
                } else {
                    echo '<div class="text-center text-muted mb-3">';
                    echo '<div><i class="fas fa-inbox fa-6x"></i></div>';
                    echo '<p><h3>Este repositório está vazio.</h3></p>';
                    echo '<p>Os dados serão exibidos aqui.</p>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </div>
</section>
