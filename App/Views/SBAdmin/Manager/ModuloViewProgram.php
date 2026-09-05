<section>
    <form action="<?= $data['FormDesign']['Tabs']['Items'][2]['Link']; ?>" method="post">
        <div class="row row-cols-1 row-cols-md-2">

            <!-- Cadastro -->
            <div class="col">
                <div class="card-body">

                    <!-- Módulo Selecionado -->
                    <div class="form-floating mb-3">
                        <label for="CasMdlDsc">Módulo Selecionado</label>
                        <input class="form-control" id="CasMdlDsc" name="CasMdlDsc" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasMdlDsc']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasMdlDsc']; ?>" readonly />
                        <small id="CasMdlDscHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasMdlDsc']['TextHelp']; ?></small>
                    </div>

                    <!-- Seleção do Programa -->
                    <div class="input-group mb-4">
                        <select class="custom-select" id="inputGroupSelectCasPrgCod" name="inputGroupSelectCasPrgCod" aria-label="Exemplo de select com botão addon">
                            <?php
                            if (isset($data['FormData']['AllPrograms'])) {
                                foreach ($data['FormData']['AllPrograms'] as $key => $value) {
                                    if ($key == 0) {
                                        echo '<option value"" selected>Selecione uma opção</option>';
                                    }
                                    if ($value['CasPrgBlq'] == 'N') {
                                        echo '<option value="' . $value['CasPrgCod'] . '" >' . $value['CasPrgDsc'] . '</option>';
                                    }
                                }
                            }
                            ?>
                        </select>
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-outline-secondary" name="btnActionAdd">Adicionar</button>
                            <button type="submit" class="btn btn-outline-secondary" name="btnActionRemove">Remover</button>
                        </div>
                    </div>

                    <hr>

                    <!-- Programas -->
                    <?php
                    if (isset($data['FormData']['ProgramsInModule'])) {
                        echo '<div class="input-group mb-3">';
                        echo '    <p class="h4">Programas do Módulo</p>';
                        echo '</div>';
                        echo '<table class="table table-hover">';
                        echo '    <thead>';
                        echo '        <tr>';
                        echo '            <th scope="col">Programa</th>';
                        echo '            <th scope="col">Bloqueado</th>';
                        echo '        </tr>';
                        echo '    </thead>';
                        echo '    <tbody>';
                        foreach ($data['FormData']['ProgramsInModule'] as $key => $value) {
                            # code...
                            echo '        <tr>';
                            echo '            <td>' . $value['CasPrgDsc'] . '</td>';
                            echo '            <td>' . $value['CasPrgBlq'] . '</td>';
                            echo '        </tr>';
                        }
                        echo '    </tbody>';
                        echo '</table>';
                    }
                    ?>
                </div>
            </div>
        </div>

        <!-- Form Button Controls -->
        <?php
        // include_once(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/FormButtonControls.php');
        ?>
    </form>
</section>