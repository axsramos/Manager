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
                    // echo '            <th>&nbsp;</th>'; // First column for action buttons //
                    foreach ($data['FormDesign']['Fields'] as $key => $field) {
                        $hidden = (in_array($key, $data['FormDesign']['Hidden']) ? 'hidden' : '');
                        echo '            <th ' . $hidden . '>' . $field['ShortLabel'] . '</th>';
                    }
                    echo '        </tr>';
                    echo '    </thead>';
                    echo '    <tfoot>';
                    echo '        <tr>';
                    // echo '            <th>&nbsp;</th>'; // First column for action buttons //
                    foreach ($data['FormDesign']['Fields'] as $key => $field) {
                        $hidden = (in_array($key, $data['FormDesign']['Hidden']) ? 'hidden' : '');
                        echo '            <th ' . $hidden . '>' . $field['ShortLabel'] . '</th>';
                    }
                    echo '        </tr>';
                    echo '    </tfoot>';
                    echo '    <tbody>';
                    foreach ($data['FormData'] as $item) {
                        echo '        <tr>';
                        foreach ($data['FormDesign']['Fields'] as $key => $field) {
                            $hidden = (in_array($key, $data['FormDesign']['Hidden']) ? 'hidden' : '');
                            // include('/App/Views/SBAdmin/Partials/ActionButtonsFirstColumn.php');
                            echo '<td ' . $hidden . '>' . $item[$key] . '</td>';
                        }
                        echo '        </tr>';
                    }
                    echo '    </tbody>';
                    echo '</table>';
                } else {
                    echo '<div class="text-center text-muted mb-3">';
                    echo '<div><i class="fas fa-inbox fa-6x"></i></div>';
                    echo '<p><h3>Este repositório está vazio.</h3></p>';
                    echo '<p>Os dados serão exibidas aqui.</p>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </div>
</section>