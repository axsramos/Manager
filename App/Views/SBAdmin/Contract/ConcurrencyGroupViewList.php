<?php require_once App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Contract/ContractViewHelpers.php'; ?>
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
                            if ($key === 'AllowMultipleActive') {
                                $value = ((int) $value === 1) ? 'Sim' : 'Não';
                            }
                            $value = ctr_e((string) $value);
                            if ($isFirst) {
                                $isFirst = false;
                                echo '<td ' . $hidden . '><a href="/Contract/ConcurrencyGroup/Show/' . rawurlencode((string) $item[$key]) . '">' . $value . '</a></td>';
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
