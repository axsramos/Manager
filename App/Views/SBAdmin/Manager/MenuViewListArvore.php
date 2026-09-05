<section>
    <form action="<?= $data['FormDesign']['Tabs']['Items'][2]['Link']; ?>" method="post">
        <div class="row row-cols-1 row-cols-md-2">

            <!-- Cadastro -->
            <div class="col">
                <div class="card-body">

                    <!-- Event Details  -->
                    <div class="accordion mb-3" id="accordionExample">
                        <div class="card">
                            <div class="card-header" id="headingOne">
                                <h5 class="mb-0">
                                    <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        <!-- Detalhes do cadastro -->
                                        <?php
                                        if (empty($data['FormData']['CasMnaCod'])) {
                                            echo 'Informe os dados do novo cadastro.';
                                        } else {
                                            echo 'CADASTRADO EM ' . App\Class\Pattern\FormatData::transformData('OnlyDate', $data['FormData']['CasMnaAudIns']);
                                            if ($data['FormData']['CasMnaBlq'] == 'S') {
                                                echo ' | <span class="badge badge-warning"> Bloqueado </span>';
                                            }
                                        }
                                        ?>
                                    </button>
                                </h5>
                            </div>
                            <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#accordionExample">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <label for="CasMnaCod"><?= $data['FormDesign']['Fields']['CasMnaCod']['LongLabel']; ?></label>
                                                <input class="form-control" id="CasMnaCod" name="CasMnaCod" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasMnaCod']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasMnaCod']; ?>" readonly />
                                                <small id="CasMnaCodHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasMnaCod']['TextHelp']; ?></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <label for="CasMnaAudIns"><?= $data['FormDesign']['Fields']['CasMnaAudIns']['LongLabel']; ?></label>
                                                <input class="form-control" id="CasMnaAudIns" name="CasMnaAudIns" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasMnaAudIns']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasMnaAudIns']; ?>" readonly />
                                                <small id="CasMnaAudInsHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasMnaAudIns']['TextHelp']; ?></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <label for="CasMnaBlq"><?= $data['FormDesign']['Fields']['CasMnaBlq']['LongLabel']; ?></label>
                                                <select class="form-control" id="CasMnaBlq" name="CasMnaBlq" type="text" <?= ($data['FormDesign']['FormDisable'] ? 'disabled' : ''); ?>>
                                                    <option value='N' <?= ($data['FormData']['CasMnaBlq'] == 'N' ? 'selected' : '') ?>>Não</option>
                                                    <option value='S' <?= ($data['FormData']['CasMnaBlq'] == 'S' ? 'selected' : '') ?>>Sim</option>
                                                </select>
                                                <small id="CasMnaBlqHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasMnaBlq']['TextHelp']; ?></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <label for="CasMnaBlqDtt"><?= $data['FormDesign']['Fields']['CasMnaBlqDtt']['LongLabel']; ?></label>
                                                <input class="form-control" id="CasMnaBlqDtt" name="CasMnaBlqDtt" type="datetime-local" placeholder="<?= $data['FormDesign']['Fields']['CasMnaBlqDtt']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasMnaBlqDtt']; ?>" readonly />
                                                <small id="CasMnaBlqDttHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasMnaBlqDtt']['TextHelp']; ?></small>
                                            </div>
                                        </div>
                                    </div>
                                    <a type="button" class="btn btn-secondary btn-block" href="<?= $data['FormDesign']['TransLinkRemove']; ?>" <?= (($data['FormDesign']['TransMode'] == 'Insert' || $data['FormDesign']['TransMode'] == 'Delete') ? 'hidden' : ''); ?>>Excluir Registro</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- General -->
                    <div class="form-floating mb-3">
                        <label for="CasMnuCod"><?= $data['FormDesign']['Fields']['CasMnuCod']['LongLabel']; ?></label>
                        <select class="form-control" id="CasMnuSearch" name="CasMnuCod" type="text" <?= ($data['FormDesign']['FormDisable'] ? 'disabled' : ''); ?>>
                            <?php
                            foreach ($data['FormDesign']['CasMnuSearch'] as $record) {
                                echo '<option value="' . $record['CasMnuCod'] . '" ' . $record['Selected'] . '>' . $record['CasMnuDsc'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-floating mb-3">
                        <label for="CasMnaDsc"><?= $data['FormDesign']['Fields']['CasMnaDsc']['LongLabel']; ?></label>
                        <input class="form-control" id="CasMnaDsc" name="CasMnaDsc" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasMnaDsc']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasMnaDsc']; ?>" autofocus required <?= ($data['FormDesign']['FormDisable'] ? 'readonly' : ''); ?> />
                        <small id="CasMnaDscHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasMnaDsc']['TextHelp']; ?></small>
                    </div>
                    <div class="form-floating mb-3">
                        <label for="CasPrgCod"><?= $data['FormDesign']['Fields']['CasPrgCod']['LongLabel']; ?></label>
                        <select class="form-control" id="CasPrgSearch" name="CasPrgCod" type="text" <?= ($data['FormDesign']['FormDisable'] ? 'disabled' : ''); ?>>
                            <?php
                            foreach ($data['FormDesign']['CasPrgSearch'] as $record) {
                                echo '<option value="' . $record['CasPrgCod'] . '" ' . $record['Selected'] . '>' . $record['CasPrgDsc'] . ($record['CasPrgTst'] == 'S' ? ' .:: EM TESTE ::. ' : '') . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-floating mb-3">
                        <label for="CasMnaLnk"><?= $data['FormDesign']['Fields']['CasMnaLnk']['LongLabel']; ?></label>
                        <input class="form-control" id="CasMnaLnk" name="CasMnaLnk" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasMnaLnk']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasMnaLnk']; ?>" autofocus <?= ($data['FormDesign']['FormDisable'] ? 'readonly' : ''); ?> />
                        <small id="CasMnaLnkHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasMnaLnk']['TextHelp']; ?></small>
                    </div>
                    <div class="form-floating mb-3">
                        <label for="CasMnaIco"><?= $data['FormDesign']['Fields']['CasMnaIco']['LongLabel']; ?></label>
                        <input class="form-control" id="CasMnaIco" name="CasMnaIco" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasMnaIco']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasMnaIco']; ?>" autofocus <?= ($data['FormDesign']['FormDisable'] ? 'readonly' : ''); ?> />
                        <small id="CasMnaIcoHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasMnaIco']['TextHelp']; ?></small>
                    </div>
                    <div class="form-floating mb-3">
                        <label for="CasMnaTxt"><?= $data['FormDesign']['Fields']['CasMnaTxt']['LongLabel']; ?></label>
                        <textarea class="form-control" id="CasMnaTxt" name="CasMnaTxt" rows="3" placeholder="<?= $data['FormDesign']['Fields']['CasMnaTxt']['TextPlaceholder']; ?>" <?= ($data['FormDesign']['FormDisable'] ? 'readonly' : ''); ?>><?= $data['FormData']['CasMnaTxt']; ?></textarea>
                        <small id="CasMnaTxtHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasMnaTxt']['TextHelp']; ?></small>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <label for="CasRpsCod"><?= $data['FormDesign']['Fields']['CasRpsCod']['LongLabel']; ?></label>
                                <input class="form-control" id="CasRpsCod" name="CasRpsCod" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasRpsCod']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasRpsCod']; ?>" readonly />
                                <small id="CasRpsCodcHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasRpsCod']['TextHelp']; ?></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <label for="CasMnaGrp"><?= $data['FormDesign']['Fields']['CasMnaGrp']['LongLabel']; ?></label>
                                <input class="form-control" id="CasMnaGrp" name="CasMnaGrp" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasMnaGrp']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasMnaGrp']; ?>" required <?= ($data['FormDesign']['FormDisable'] ? 'readonly' : ''); ?> />
                                <small id="CasMnaGrpcHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasMnaGrp']['TextHelp']; ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table Rows -->
            <div class="col">
                <div class="card h-100 mb-4">
                    <div class="card-header">
                        <i class="fas fa-table mr-1"></i>
                        Consulta retornou <?= count($data['FormDataRows']); ?> registro(s)
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <?php
                            if ($data['FormDataRows']) {
                                echo '<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">';
                                echo '    <thead>';
                                echo '        <tr>';
                                // echo '            <th>&nbsp;</th>'; // First column for action buttons //
                                foreach ($data['FormDesign']['Fields'] as $key => $field) {
                                    $hidden = (in_array($key, $data['FormDesign']['Hidden']) ? 'hidden' : '');
                                    if (isset($data['FormDataRows'][0][$key])) {
                                        echo '            <th ' . $hidden . '>' . $field['ShortLabel'] . '</th>';
                                    }
                                }
                                echo '        </tr>';
                                echo '    </thead>';
                                echo '    <tfoot>';
                                echo '        <tr>';
                                // echo '            <th>&nbsp;</th>'; // First column for action buttons //
                                foreach ($data['FormDesign']['Fields'] as $key => $field) {
                                    $hidden = (in_array($key, $data['FormDesign']['Hidden']) ? 'hidden' : '');
                                    if (isset($data['FormDataRows'][0][$key])) {
                                        echo '            <th ' . $hidden . '>' . $field['ShortLabel'] . '</th>';
                                    }
                                }
                                echo '        </tr>';
                                echo '    </tfoot>';
                                echo '    <tbody>';
                                foreach ($data['FormDataRows'] as $item) {
                                    echo '        <tr>';
                                    $isFirst = true;
                                    foreach ($data['FormDesign']['Fields'] as $key => $field) {
                                        $hidden = (in_array($key, $data['FormDesign']['Hidden']) ? 'hidden' : '');
                                        if (isset($data['FormDataRows'][0][$key])) {
                                            if ($isFirst) {
                                                // $isFirst = false;
                                                echo '<td ' . $hidden . '><a href="/Manager/Menu/Arvore/' . $item['CasMnuCod'] . '/' . $item['CasMnaCod'] . '">' . $item[$key] . '</a></td>';
                                            } else {
                                                echo '<td ' . $hidden . '>' . $item[$key] . '</td>';
                                            }
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
            </div>
        </div>

        <!-- Form Button Controls -->
        <?php
        include_once(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/FormButtonControls.php');
        ?>
    </form>
</section>