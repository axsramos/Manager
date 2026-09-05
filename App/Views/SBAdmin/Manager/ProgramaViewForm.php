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
                                        if (empty($data['FormData']['CasPrgCod'])) {
                                            echo 'Informe os dados do novo cadastro.';
                                        } else {
                                            echo 'CADASTRADO EM ' . App\Class\Pattern\FormatData::transformData('OnlyDate', $data['FormData']['CasPrgAudIns']);
                                            if ($data['FormData']['CasPrgBlq'] == 'S') {
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
                                                <label for="CasPrgCod"><?= $data['FormDesign']['Fields']['CasPrgCod']['LongLabel']; ?></label>
                                                <input class="form-control" id="CasPrgCod" name="CasPrgCod" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasPrgCod']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasPrgCod']; ?>" readonly />
                                                <small id="CasPrgCodHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasPrgCod']['TextHelp']; ?></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <label for="CasPrgAudIns"><?= $data['FormDesign']['Fields']['CasPrgAudIns']['LongLabel']; ?></label>
                                                <input class="form-control" id="CasPrgAudIns" name="CasPrgAudIns" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasPrgAudIns']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasPrgAudIns']; ?>" readonly />
                                                <small id="CasPrgAudInsHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasPrgAudIns']['TextHelp']; ?></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <label for="CasPrgBlq"><?= $data['FormDesign']['Fields']['CasPrgBlq']['LongLabel']; ?></label>
                                                <select class="form-control" id="CasPrgBlq" name="CasPrgBlq" type="text" <?= ($data['FormDesign']['FormDisable'] ? 'disabled' : ''); ?>>
                                                    <option value='N' <?= ($data['FormData']['CasPrgBlq'] == 'N' ? 'selected' : '') ?>>Não</option>
                                                    <option value='S' <?= ($data['FormData']['CasPrgBlq'] == 'S' ? 'selected' : '') ?>>Sim</option>
                                                </select>
                                                <small id="CasPrgBlqHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasPrgBlq']['TextHelp']; ?></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <label for="CasPrgBlqDtt"><?= $data['FormDesign']['Fields']['CasPrgBlqDtt']['LongLabel']; ?></label>
                                                <input class="form-control" id="CasPrgBlqDtt" name="CasPrgBlqDtt" type="datetime-local" placeholder="<?= $data['FormDesign']['Fields']['CasPrgBlqDtt']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasPrgBlqDtt']; ?>" readonly />
                                                <small id="CasPrgBlqDttHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasPrgBlqDtt']['TextHelp']; ?></small>
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
                        <label for="CasPrgDsc"><?= $data['FormDesign']['Fields']['CasPrgDsc']['LongLabel']; ?></label>
                        <input class="form-control" id="CasPrgDsc" name="CasPrgDsc" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasPrgDsc']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasPrgDsc']; ?>" autofocus required <?= ($data['FormDesign']['FormDisable'] ? 'readonly' : ''); ?> />
                        <small id="CasPrgDscHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasPrgDsc']['TextHelp']; ?></small>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <label for="CasPrgTst"><?= $data['FormDesign']['Fields']['CasPrgTst']['LongLabel']; ?></label>
                                <select class="form-control" id="CasPrgTst" name="CasPrgTst" type="text" <?= ($data['FormDesign']['FormDisable'] ? 'disabled' : ''); ?>>
                                    <option value='N' <?= ($data['FormData']['CasPrgTst'] == 'N' ? 'selected' : '') ?>>Não</option>
                                    <option value='S' <?= ($data['FormData']['CasPrgTst'] == 'S' ? 'selected' : '') ?>>Sim</option>
                                </select>
                                <small id="CasPrgTstHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasPrgTst']['TextHelp']; ?></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <label for="CasPrgTstDtt"><?= $data['FormDesign']['Fields']['CasPrgTstDtt']['LongLabel']; ?></label>
                                <input class="form-control" id="CasPrgTstDtt" name="CasPrgTstDtt" type="datetime-local" placeholder="<?= $data['FormDesign']['Fields']['CasPrgTstDtt']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasPrgTstDtt']; ?>" readonly />
                                <small id="CasPrgTstDttHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasPrgTstDtt']['TextHelp']; ?></small>
                            </div>
                        </div>
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