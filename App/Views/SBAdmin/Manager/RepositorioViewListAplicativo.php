<section>
    <!-- Repository & Application -->
    <form action="<?= $data['FormDesign']['Tabs']['Items'][2]['Link']; ?>" method="post">
        <div class="card mb-3">
            <div class="card-header">
                <i class="fas fa-table mr-1"></i>
                Cadastro
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group mb-3">
                            <input type="text" name="CasRpsCodSearch" class="form-control" placeholder="Digite o código do repositório" value="<?=$data['FormDataSearch']['Repository']['CasRpsCod'];?>" aria-label="Repositório" aria-describedby="btnRpsSearch">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="submit" id="btnRpsSearch" name="btnRpsSearch">Pesquisar</button>
                            </div>
                        </div>
                        <?php
                        echo '<div class="card bg-light mb-3" ' . (empty($data['FormDataSearch']['Repository']['CasRpsCod']) ? 'hidden' : '') . '>';
                        echo '    <div class="card-header">Repositório selecionado</div>';
                        echo '    <div class="card-body">';
                        echo '        <h5 class="card-title">' . $data['FormDataSearch']['Repository']['CasRpsDsc'] . '</h5>';
                        echo '        <div class="row row-cols-1 row-cols-md-3 g-4">';
                        echo '            <div class="col">';
                        echo '                <div class="form-group">';
                        echo '                    <label for="FilCasRpsCod" class="ml-1">' . $data['FormDesign']['FieldsRepositorySearch']['CasRpsCod']['ShortLabel'] . '</label>';
                        echo '                    <input type="text" class="form-control" id="FilCasRpsCod" value="' . $data['FormDataSearch']['Repository']['CasRpsCod'] . '" readonly>';
                        echo '                </div>';
                        echo '            </div>';
                        echo '            <div class="col">';
                        echo '                <div class="form-group">';
                        echo '                    <label for="FilCasRpsBlq" class="ml-1">' . $data['FormDesign']['FieldsRepositorySearch']['CasRpsBlq']['LongLabel'] . '</label>';
                        echo '                    <input type="text" class="form-control" id="FilCasRpsBlq" value="' . $data['FormDataSearch']['Repository']['CasRpsBlq'] . '" readonly>';
                        echo '                </div>';
                        echo '            </div>';
                        echo '            <div class="col">';
                        echo '                <div class="form-group">';
                        echo '                    <label for="FilCasRpsGrp" class="ml-1">' . $data['FormDesign']['FieldsRepositorySearch']['CasRpsGrp']['LongLabel'] . '</label>';
                        echo '                    <input type="text" class="form-control" id="FilCasRpsGrp" value="' . $data['FormDataSearch']['Repository']['CasRpsGrp'] . '" readonly>';
                        echo '                </div>';
                        echo '            </div>';
                        echo '        </div>';
                        echo '    </div>';
                        echo '</div>';
                        ?>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group mb-3">
                            <input type="text" name="CasAppCodSearch" class="form-control" placeholder="Digite o código do aplicativo" aria-label="Aplicativo" value="<?=$data['FormDataSearch']['Application']['CasAppCod'];?>" aria-describedby="btnAppSearch">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="submit" id="btnAppSearch" name="btnAppSearch">Pesquisar</button>
                            </div>
                        </div>
                        <?php
                        echo '<div class="card bg-light mb-3" ' . (empty($data['FormDataSearch']['Application']['CasAppCod']) ? 'hidden' : '') . '>';
                        echo '    <div class="card-header">Aplicativo selecionado';
                        echo '        <span class="badge badge-warning" ' . ($data['FormDataSearch']['Application']['CasAppTst'] == 'S' ? '' : 'hidden') . '>Em Teste</span>';
                        echo '        <span class="badge badge-light" ' . (empty($data['FormDataSearch']['Application']['CasAppVer']) ? 'hidden' : '') . '>Versão ' . $data['FormDataSearch']['Application']['CasAppVer'] . '</span>';
                        echo '    </div>';
                        echo '    <div class="card-body">';
                        echo '        <h5 class="card-title">' . $data['FormDataSearch']['Application']['CasAppDsc'] .  '</h5>';
                        echo '        <div class="row row-cols-1 row-cols-md-3 g-4">';
                        echo '            <div class="col">';
                        echo '                <div class="form-group">';
                        echo '                    <label for="FilCasAppCod" class="ml-1">' . $data['FormDesign']['FieldsApplicationSearch']['CasAppCod']['ShortLabel'] . '</label>';
                        echo '                    <input type="text" class="form-control" id="FilCasAppCod" value="' . $data['FormDataSearch']['Application']['CasAppCod'] . '" readonly>';
                        echo '                </div>';
                        echo '            </div>';
                        echo '            <div class="col">';
                        echo '                <div class="form-group">';
                        echo '                    <label for="FilCasAppBlq" class="ml-1">' . $data['FormDesign']['FieldsApplicationSearch']['CasAppBlq']['LongLabel'] . '</label>';
                        echo '                    <input type="text" class="form-control" id="FilCasAppBlq" value="' . $data['FormDataSearch']['Application']['CasAppBlq'] . '" readonly>';
                        echo '                </div>';
                        echo '            </div>';
                        echo '            <div class="col">';
                        echo '                <div class="form-group">';
                        echo '                    <label for="FilCasAppGrp" class="ml-1">' . $data['FormDesign']['FieldsApplicationSearch']['CasAppGrp']['LongLabel'] . '</label>';
                        echo '                    <input type="text" class="form-control" id="FilCasAppGrp" value="' . $data['FormDataSearch']['Application']['CasAppGrp'] . '" readonly>';
                        echo '                </div>';
                        echo '            </div>';
                        echo '        </div>';
                        echo '    </div>';
                        echo '</div>';
                        ?>
                    </div>
                </div>
                <?php
                if (!(empty($data['FormDataSearch']['Repository']['CasRpsCodCod']) && empty($data['FormDataSearch']['Application']['CasAppCod']))) {
                    echo '<div class="row">';
                    echo '    <div class="card-body">';
                    echo '        <button type="submit" name="btnAppAdd" class="btn btn-primary mr-2">Adicionar</button>';
                    echo '        <button type="submit" name="btnAppRemove" class="btn btn-danger mr-2">Remover</button>';
                    echo '    </div>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </form>
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
                        $isFirst = true;
                        foreach ($data['FormDesign']['Fields'] as $key => $field) {
                            $hidden = (in_array($key, $data['FormDesign']['Hidden']) ? 'hidden' : '');
                            // include('app\views\SBAdmin\Partials\ActionButtonsFirstColumn.php');
                            if ($isFirst && !$hidden) {
                                $isFirst = false;
                                echo '<td ' . $hidden . '><a href="' . $data['FormDesign']['Tabs']['Items'][2]['Link'] . $item['CasAppCod'] . '">' . $item[$key] . '</a></td>';
                            } else {
                                echo '<td ' . $hidden . '>' . $item[$key] . '</td>';
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
                    echo '<p>Os dados serão exibidas aqui.</p>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </div>
</section>