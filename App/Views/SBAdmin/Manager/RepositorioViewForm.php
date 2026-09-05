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
                                        if (empty($data['FormData']['CasRpsCod'])) {
                                            echo 'Informe os dados do novo cadastro.';
                                        } else {
                                            echo 'CADASTRADO EM ' . App\Class\Pattern\FormatData::transformData('OnlyDate', $data['FormData']['CasRpsAudIns']);
                                            if ($data['FormData']['CasRpsBlq'] == 'S') {
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
                                                <input class="form-control" id="CasRpsCod" name="CasRpsCod" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasRpsCod']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasRpsCod']; ?>" <?=($data['FormDesign']['TransMode'] == 'Insert' ? '' : 'readonly');?> />
                                                <small id="CasRpsCodHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasRpsCod']['TextHelp']; ?></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <label for="CasRpsAudIns"><?= $data['FormDesign']['Fields']['CasRpsAudIns']['LongLabel']; ?></label>
                                                <input class="form-control" id="CasRpsAudIns" name="CasRpsAudIns" type="datetime-local" placeholder="<?= $data['FormDesign']['Fields']['CasRpsAudIns']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasRpsAudIns']; ?>" readonly />
                                                <small id="CasRpsAudInsHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasRpsAudIns']['TextHelp']; ?></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <label for="CasRpsBlq"><?= $data['FormDesign']['Fields']['CasRpsBlq']['LongLabel']; ?></label>
                                                <select class="form-control" id="CasRpsBlq" name="CasRpsBlq" type="text" <?= ($data['FormDesign']['FormDisable'] ? 'disabled' : ''); ?>>
                                                    <option value='N' <?= ($data['FormData']['CasRpsBlq'] == 'N' ? 'selected' : '') ?>>Não</option>
                                                    <option value='S' <?= ($data['FormData']['CasRpsBlq'] == 'S' ? 'selected' : '') ?>>Sim</option>
                                                </select>
                                                <small id="CasRpsBlqHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasRpsBlq']['TextHelp']; ?></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <label for="CasRpsBlqDtt"><?= $data['FormDesign']['Fields']['CasRpsBlqDtt']['LongLabel']; ?></label>
                                                <input class="form-control" id="CasRpsBlqDtt" name="CasRpsBlqDtt" type="datetime-local" placeholder="<?= $data['FormDesign']['Fields']['CasRpsBlqDtt']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasRpsBlqDtt']; ?>" readonly />
                                                <small id="CasRpsBlqDttHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasRpsBlqDtt']['TextHelp']; ?></small>
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
                        <label for="CasRpsDsc"><?= $data['FormDesign']['Fields']['CasRpsDsc']['LongLabel']; ?></label>
                        <input class="form-control" id="CasRpsDsc" name="CasRpsDsc" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasRpsDsc']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasRpsDsc']; ?>" autofocus required <?= ($data['FormDesign']['FormDisable'] ? 'readonly' : ''); ?> />
                        <small id="CasRpsDscHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasRpsDsc']['TextHelp']; ?></small>
                    </div>
                    <div class="form-floating mb-3">
                        <label for="CasRpsObs"><?= $data['FormDesign']['Fields']['CasRpsObs']['LongLabel']; ?></label>
                        <textarea class="form-control" id="CasRpsObs" name="CasRpsObs" rows="3" placeholder="<?= $data['FormDesign']['Fields']['CasRpsObs']['TextPlaceholder']; ?>" <?= ($data['FormDesign']['FormDisable'] ? 'readonly' : ''); ?>><?= $data['FormData']['CasRpsObs']; ?></textarea>
                        <small id="CasRpsObsHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasRpsObs']['TextHelp']; ?></small>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <label for="CasRpsGrp"><?= $data['FormDesign']['Fields']['CasRpsGrp']['LongLabel']; ?></label>
                                <input class="form-control" id="CasRpsGrp" name="CasRpsGrp" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasRpsGrp']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasRpsGrp']; ?>" autofocus required <?= ($data['FormDesign']['FormDisable'] ? 'readonly' : ''); ?> />
                                <small id="CasRpsGrpHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasRpsGrp']['TextHelp']; ?></small>
                            </div>
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