<section>
    <form action="<?= $data['FormDesign']['Tabs']['Items'][1]['Link']; ?>" method="post">
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
                                        if (empty($data['FormData']['CasMdlCod'])) {
                                            echo 'Informe os dados do novo cadastro.';
                                        } else {
                                            echo 'CADASTRADO EM ' . App\Class\Pattern\FormatData::transformData('OnlyDate', $data['FormData']['CasMdlAudIns']);
                                            if ($data['FormData']['CasMdlBlq'] == 'S') {
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
                                                <label for="CasMdlCod"><?= $data['FormDesign']['Fields']['CasMdlCod']['LongLabel']; ?></label>
                                                <input class="form-control" id="CasMdlCod" name="CasMdlCod" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasMdlCod']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasMdlCod']; ?>" readonly />
                                                <small id="CasMdlCodHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasMdlCod']['TextHelp']; ?></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <label for="CasMdlAudIns"><?= $data['FormDesign']['Fields']['CasMdlAudIns']['LongLabel']; ?></label>
                                                <input class="form-control" id="CasMdlAudIns" name="CasMdlAudIns" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasMdlAudIns']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasMdlAudIns']; ?>" readonly />
                                                <small id="CasMdlAudInsHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasMdlAudIns']['TextHelp']; ?></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <label for="CasMdlBlq"><?= $data['FormDesign']['Fields']['CasMdlBlq']['LongLabel']; ?></label>
                                                <select class="form-control" id="CasMdlBlq" name="CasMdlBlq" type="text" <?= ($data['FormDesign']['FormDisable'] ? 'disabled' : ''); ?>>
                                                    <option value='N' <?= ($data['FormData']['CasMdlBlq'] == 'N' ? 'selected' : '') ?>>Não</option>
                                                    <option value='S' <?= ($data['FormData']['CasMdlBlq'] == 'S' ? 'selected' : '') ?>>Sim</option>
                                                </select>
                                                <small id="CasMdlBlqHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasMdlBlq']['TextHelp']; ?></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <label for="CasMdlBlqDtt"><?= $data['FormDesign']['Fields']['CasMdlBlqDtt']['LongLabel']; ?></label>
                                                <input class="form-control" id="CasMdlBlqDtt" name="CasMdlBlqDtt" type="datetime-local" placeholder="<?= $data['FormDesign']['Fields']['CasMdlBlqDtt']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasMdlBlqDtt']; ?>" readonly />
                                                <small id="CasMdlBlqDttHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasMdlBlqDtt']['TextHelp']; ?></small>
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
                        <label for="CasMdlDsc"><?= $data['FormDesign']['Fields']['CasMdlDsc']['LongLabel']; ?></label>
                        <input class="form-control" id="CasMdlDsc" name="CasMdlDsc" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasMdlDsc']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasMdlDsc']; ?>" autofocus required <?= ($data['FormDesign']['FormDisable'] ? 'readonly' : ''); ?> />
                        <small id="CasMdlDscHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasMdlDsc']['TextHelp']; ?></small>
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
                            &nbsp;
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