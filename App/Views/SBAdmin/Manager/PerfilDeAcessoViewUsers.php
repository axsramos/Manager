<section>
    <form action="<?= $data['FormDesign']['Tabs']['Items'][2]['Link']; ?>" method="post">
        <div class="row row-cols-1 row-cols-md-2">

            <!-- Cadastro -->
            <div class="col">
                <div class="card-body">

                    <!-- Perfil Selecionado -->
                    <div class="form-floating mb-3">
                        <label for="CasPfiDsc">Perfil Selecionado</label>
                        <input class="form-control" id="CasPfiDsc" name="CasPfiDsc" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasPfiDsc']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasPfiDsc']; ?>" readonly />
                        <small id="CasPfiDscHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasPfiDsc']['TextHelp']; ?></small>
                    </div>

                    <!-- Seleção do Usuário -->
                    <div class="input-group mb-4">
                        <select class="custom-select" id="inputGroupSelectCasUsrCod" name="inputGroupSelectCasUsrCod" aria-label="Exemplo de select com botão addon">
                            <?php
                            if (isset($data['FormData']['AllUsers'])) {
                                foreach ($data['FormData']['AllUsers'] as $key => $value) {
                                    if ($key == 0) {
                                        echo '<option value"" selected>Selecione uma opção</option>';
                                    }
                                    if ($value['CasUsrBlq'] == 'N') {
                                        echo '<option value="' . $value['CasUsrCod'] . '" >' . $value['CasUsrDsc'] . '</option>';
                                    }
                                }
                            }
                            ?>
                        </select>
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-outline-secondary" name="btnActionAdd">Adicionar</button>
                            <button type="submit" class="btn btn-outline-secondary" name="btnActionRemove">Remover</button>
                            <button type="submit" class="btn btn-outline-secondary" name="btnActionBlolock">Bloqueio</button>
                        </div>
                    </div>

                    <hr>

                    <!-- Usuários -->
                    <?php
                    if (isset($data['FormData']['UsersInProfile'])) {
                        echo '<div class="input-group mb-3">';
                        echo '    <p class="h4">Usuários do Perfil</p>';
                        echo '</div>';
                        echo '<table class="table table-hover">';
                        echo '    <thead>';
                        echo '        <tr>';
                        echo '            <th scope="col">Usuário</th>';
                        echo '            <th scope="col">Tipo de Usuário</th>';
                        echo '            <th scope="col">Bloqueado</th>';
                        echo '        </tr>';
                        echo '    </thead>';
                        echo '    <tbody>';
                        foreach ($data['FormData']['UsersInProfile'] as $key => $value) {
                            # code...
                            echo '        <tr>';
                            echo '            <td>' . $value['CasUsrDsc'] . '</td>';
                            echo '            <td>' . $value['CasTusDsc'] . '</td>';
                            echo '            <td>' . $value['CasRpuBlq'] . '</td>';
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