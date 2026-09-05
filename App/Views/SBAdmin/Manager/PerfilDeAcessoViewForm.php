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
                                        if (empty($data['FormData']['CasPfiCod'])) {
                                            echo 'Informe os dados do novo cadastro.';
                                        } else {
                                            echo 'CADASTRADO EM ' . App\Class\Pattern\FormatData::transformData('OnlyDate', $data['FormData']['CasPfiAudIns']);
                                            if ($data['FormData']['CasPfiBlq'] == 'S') {
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
                                                <label for="CasRpsCod"><?= $data['FormDesign']['Fields']['CasRpsCod']['LongLabel']; ?></label>
                                                <input class="form-control" id="CasRpsCod" name="CasRpsCod" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasRpsCod']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasRpsCod']; ?>" readonly />
                                                <small id="CasRpsCodcHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasRpsCod']['TextHelp']; ?></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <label for="CasRpsDsc"><?= $data['FormDesign']['Fields']['CasRpsDsc']['LongLabel']; ?></label>
                                                <input class="form-control" id="CasRpsDsc" name="CasRpsDsc" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasRpsDsc']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasRpsDsc']; ?>" readonly />
                                                <small id="CasRpsDsccHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasRpsDsc']['TextHelp']; ?></small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <label for="CasPfiCod"><?= $data['FormDesign']['Fields']['CasPfiCod']['LongLabel']; ?></label>
                                                <input class="form-control" id="CasPfiCod" name="CasPfiCod" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasPfiCod']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasPfiCod']; ?>" readonly />
                                                <small id="CasPfiCodHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasPfiCod']['TextHelp']; ?></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <label for="CasPfiAudIns"><?= $data['FormDesign']['Fields']['CasPfiAudIns']['LongLabel']; ?></label>
                                                <input class="form-control" id="CasPfiAudIns" name="CasPfiAudIns" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasPfiAudIns']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasPfiAudIns']; ?>" readonly />
                                                <small id="CasPfiAudInsHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasPfiAudIns']['TextHelp']; ?></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <label for="CasPfiBlq"><?= $data['FormDesign']['Fields']['CasPfiBlq']['LongLabel']; ?></label>
                                                <select class="form-control" id="CasPfiBlq" name="CasPfiBlq" type="text" <?= ($data['FormDesign']['FormDisable'] ? 'disabled' : ''); ?>>
                                                    <option value='N' <?= ($data['FormData']['CasPfiBlq'] == 'N' ? 'selected' : '') ?>>Não</option>
                                                    <option value='S' <?= ($data['FormData']['CasPfiBlq'] == 'S' ? 'selected' : '') ?>>Sim</option>
                                                </select>
                                                <small id="CasPfiBlqHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasPfiBlq']['TextHelp']; ?></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <label for="CasPfiBlqDtt"><?= $data['FormDesign']['Fields']['CasPfiBlqDtt']['LongLabel']; ?></label>
                                                <input class="form-control" id="CasPfiBlqDtt" name="CasPfiBlqDtt" type="datetime-local" placeholder="<?= $data['FormDesign']['Fields']['CasPfiBlqDtt']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasPfiBlqDtt']; ?>" readonly />
                                                <small id="CasPfiBlqDttHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasPfiBlqDtt']['TextHelp']; ?></small>
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
                        <label for="CasPfiDsc"><?= $data['FormDesign']['Fields']['CasPfiDsc']['LongLabel']; ?></label>
                        <input class="form-control" id="CasPfiDsc" name="CasPfiDsc" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasPfiDsc']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasPfiDsc']; ?>" autofocus required <?= ($data['FormDesign']['FormDisable'] ? 'readonly' : ''); ?> />
                        <small id="CasPfiDscHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasPfiDsc']['TextHelp']; ?></small>
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