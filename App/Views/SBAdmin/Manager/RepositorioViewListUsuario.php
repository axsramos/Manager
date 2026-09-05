<section>
    <!-- Repository & Usuario -->
    <form action="<?= $data['FormDesign']['Tabs']['Items'][3]['Link']; ?>" method="post">
        <div class="card mb-3">
            <div class="card-header">
                <i class="fas fa-table mr-1"></i>
                Cadastro
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group mb-3">
                            <input type="text" name="CasRpsCodSearch" class="form-control" placeholder="Digite o código do repositório" value="<?= $data['FormDataSearch']['Repository']['CasRpsCod']; ?>" aria-label="Repositório" aria-describedby="btnRpsSearch">
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
                            <input type="text" name="CasUsrCodSearch" class="form-control" placeholder="Digite o código do usuário" aria-label="Usuario" value="<?= $data['FormDataSearch']['Usuario']['CasUsrCod']; ?>" aria-describedby="btnAppSearch">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="submit" id="btnAppSearch" name="btnAppSearch">Pesquisar</button>
                            </div>
                        </div>
                        <?php
                        echo '<div class="card bg-light mb-3" ' . (empty($data['FormDataSearch']['Usuario']['CasUsrCod']) ? 'hidden' : '') . '>';
                        echo '    <div class="card-header">Usuário selecionado';
                        echo '    </div>';
                        echo '    <div class="card-body">';
                        echo '        <h5 class="card-title">' . $data['FormDataSearch']['Usuario']['CasUsrDsc'] .  '</h5>';
                        echo '        <div class="row row-cols-1 row-cols-md-3 g-4">';
                        echo '            <div class="col">';
                        echo '                <div class="form-group">';
                        echo '                    <label for="FilCasUsrCod" class="ml-1">' . $data['FormDesign']['FieldsUsuarioSearch']['CasUsrCod']['ShortLabel'] . '</label>';
                        echo '                    <input type="text" class="form-control" id="FilCasUsrCod" value="' . $data['FormDataSearch']['Usuario']['CasUsrCod'] . '" readonly>';
                        echo '                </div>';
                        echo '            </div>';
                        echo '            <div class="col">';
                        echo '                <div class="form-group">';
                        echo '                    <label for="FilCasUsrBlq" class="ml-1">' . $data['FormDesign']['FieldsUsuarioSearch']['CasUsrBlq']['LongLabel'] . '</label>';
                        echo '                    <input type="text" class="form-control" id="FilCasUsrBlq" value="' . $data['FormDataSearch']['Usuario']['CasUsrBlq'] . '" readonly>';
                        echo '                </div>';
                        echo '            </div>';
                        echo '            <div class="col">';
                        echo '                <div class="form-group">';
                        echo '                    <label for="CasTusCodSearch" class="ml-1">' . $data['FormDesign']['FieldsUsuarioSearch']['CasTusCod']['ShortLabel'] . '</label>';
                        echo '                    <select class="form-control" id="CasTusCodSearch" name="CasTusCodSearch" type="text" ' . $data['FormDataSearch']['Usuario']['CasTusCod'] . ' ' . ($data['FormDesign']['FormDisable'] ? 'disabled' : '') . '>';
                        foreach ($data['FormDataSearch']['FilCasTus'] as $item) {
                            echo '                        <option value="' . $item['CasTusCod'] . '">' . $item['CasTusDsc'] . '</option>';
                        }
                        echo '                    </select>';
                        echo '                </div>';
                        echo '            </div>';
                        echo '        </div>';
                        echo '    </div>';
                        echo '</div>';
                        ?>
                    </div>
                </div>
                <?php
                if (!(empty($data['FormDataSearch']['Repository']['CasRpsCodCod']) && empty($data['FormDataSearch']['Usuario']['CasUsrCod']))) {
                    echo '<div class="row">';
                    echo '    <div class="card-body">';
                    echo '        <button type="submit" name="btnUsrAdd" class="btn btn-primary mr-2">Adicionar</button>';
                    echo '        <button type="submit" name="btnUsrRemove" class="btn btn-danger mr-2">Remover</button>';
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
                            if ($isFirst) {
                                $isFirst = false;
                                echo '<td ' . $hidden . '><a href="' . $data['FormDesign']['Tabs']['Items'][3]['Link'] . $item['CasUsrCod'] . '">' . $item[$key] . '</a></td>';
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