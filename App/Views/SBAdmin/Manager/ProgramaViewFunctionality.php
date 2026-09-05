<section>
    <form action="<?= $data['FormDesign']['Tabs']['Items'][2]['Link']; ?>" method="post">
        <div class="row row-cols-1 row-cols-md-2">

            <!-- Cadastro -->
            <div class="col">
                <div class="card-body">

                    <!-- Programa Selecionado -->
                    <div class="form-floating mb-3">
                        <label for="CasPrgDsc">Programa Selecionado</label>
                        <input class="form-control" id="CasPrgDsc" name="CasPrgDsc" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasPrgDsc']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasPrgDsc']; ?>" readonly />
                        <small id="CasPrgDscHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasPrgDsc']['TextHelp']; ?></small>
                    </div>

                    <!-- Seleção da Funcionalidade -->
                    <div class="input-group mb-4">
                        <select class="custom-select" id="inputGroupSelectCasFunCod" name="inputGroupSelectCasFunCod" aria-label="Exemplo de select com botão addon">
                            <?php
                            if (isset($data['FormData']['AllFunctionalities'])) {
                                foreach ($data['FormData']['AllFunctionalities'] as $key => $value) {
                                    if ($key == 0) {
                                        echo '<option value"" selected>Selecione uma opção</option>';
                                    }
                                    if ($value['CasFunBlq'] == 'N') {
                                        echo '<option value="' . $value['CasFunCod'] . '" >' . $value['CasFunDsc'] . '</option>';
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

                    <!-- Funcionalidades -->
                    <?php
                    if (isset($data['FormData']['FunctionalitiesInProgram'])) {
                        echo '<div class="input-group mb-3">';
                        echo '    <p class="h4">Funcionalidades do Programa</p>';
                        echo '</div>';
                        echo '<table class="table table-hover">';
                        echo '    <thead>';
                        echo '        <tr>';
                        echo '            <th scope="col">Funcionalidade</th>';
                        echo '            <th scope="col">Bloqueado</th>';
                        echo '        </tr>';
                        echo '    </thead>';
                        echo '    <tbody>';
                        foreach ($data['FormData']['FunctionalitiesInProgram'] as $key => $value) {
                            # code...
                            echo '        <tr>';
                            echo '            <td>' . $value['CasFunDsc'] . '</td>';
                            echo '            <td>' . $value['CasFunBlq'] . '</td>';
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