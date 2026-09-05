<section>
    <div>
        <div class="row row-cols-1 row-cols-md-2">

            <!-- Cadastro -->
            <div class="col">
                <div class="card-body">

                    <!-- Perfil Selecionado -->
                    <div class="form-floating mb-3">
                        <label for="CasPfiDsc">Autorização de acesso do perfil</label>
                        <input class="form-control" id="CasPfiDsc" name="CasPfiDsc" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasPfiDsc']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasPfiDsc']; ?>" readonly />
                        <small id="CasPfiDscHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasPfiDsc']['TextHelp']; ?></small>
                    </div>

                    <!-- Módulos -->
                    <?php
                    if (isset($data['Authorizations'])) {
                        echo '<div class="accordion" id="accordionModules" >';
                        foreach ($data['Authorizations'] as $module) {
                            $idAccordion = 'heading_' . $module['CasMdlCod'];
                            $idAccordionCollapse = 'collapse_' . $module['CasMdlCod'];
                            echo '    <div class="card">';
                            echo '        <div class="card-header" id="' . $idAccordion . '">';
                            echo '            <h5 class="mb-0">';
                            echo '                <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#' . $idAccordionCollapse . '" aria-expanded="false" aria-controls="' . $idAccordionCollapse . '">';
                            echo '                    Programas: ' . str_pad(count($module['Programs']), 4, "0", STR_PAD_LEFT) . ' | Módulo: ' . $module['CasMdlDsc'];
                            if ($module['CasMdlBlq'] == 'S') {
                                echo '                    <span class="badge badge-pill badge-warning" data-toggle="tooltip" data-placement="top" title="Módulo bloqueado." >Bloqueado</span>';
                            }
                            echo '                </button>';
                            echo '            </h5>';
                            echo '        </div>';
                            if ($module['Programs']) {
                                echo '        <div id="' . $idAccordionCollapse . '" class="collapse " aria-labelledby="' . $idAccordion . '" data-parent="#accordionModules">';
                                echo '            <div class="card-body">';
                                echo '                <table class="table table-hover">';
                                echo '                    <thead>';
                                echo '                        <tr>';
                                echo '                            <th scope="col">#</th>';
                                echo '                            <th scope="col">Programa</th>';
                                echo '                            <th scope="col">Status</th>';
                                echo '                            <th scope="col">Funcionalidades</th>';
                                echo '                        </tr>';
                                echo '                    </thead>';
                                echo '                    <tbody>';
                                foreach ($module['Programs'] as $key => $value) {
                                    echo '                        <tr>';
                                    echo '                            <th scope="row">' . (1 + $key) . '</th>';
                                    echo '                            <td>' . $value['CasPrgDsc'] . '</td>';
                                    echo '                            <td>';
                                    if ($value['CasPrgBlq'] == 'N') {
                                        echo '                                <span class="badge badge-pill badge-primary" data-toggle="tooltip" data-placement="top" title="Habilitado para o uso.">Ativo</span>';
                                    } else {
                                        echo '                                <span class="badge badge-pill badge-secondary" data-toggle="tooltip" data-placement="top" title="Bloqueado para o uso.">Inativo</span>';
                                    }
                                    if ($value['AutorizedProgram'] == 'S') {
                                        echo '                                <span class="badge badge-pill badge-success" data-toggle="tooltip" data-placement="top" title="Autorizado para o uso.">Autorizado</span>';
                                    } else {
                                        echo '                                <span class="badge badge-pill badge-danger" data-toggle="tooltip" data-placement="top" title="Negado para o uso.">Negado</span>';
                                    }
                                    if ($value['CasPrgTst'] == 'S') {
                                        echo '                                <span class="badge badge-pill badge-warning" data-toggle="tooltip" data-placement="top" title="Em teste. Seu uso é restrito.">EM TESTE</span>';
                                    }
                                    echo '                            </td>';
                                    echo '                            <td>';
                                    echo '                                <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#modalActions" value="' . $value['CasPrgCod'] . '">';
                                    echo '                                    ' . $value['Features'] . ' de <span class="badge badge-light">' . (empty($value['AutorizedFeatures']) ? '0' : $value['AutorizedFeatures']) . ' liberado(s)</span>';
                                    echo '                                </button>';
                                    echo '                            </td>';
                                    echo '                        </tr>';
                                }
                                echo '                    </tbody>';
                                echo '                </table>';
                                echo '            </div>';
                                echo '        </div>';
                            }
                            echo '    </div>';
                        }
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- Form Button Controls -->
        <?php
        // include_once(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/FormButtonControls.php');
        ?>
    </div>
    
</section>