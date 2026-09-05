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
                                        if (empty($data['FormData']['CasTusCod'])) {
                                            echo 'Informe os dados do novo cadastro.';
                                        } else {
                                            echo 'CADASTRADO EM ' . App\Class\Pattern\FormatData::transformData('OnlyDate', $data['FormData']['CasTusAudIns']);
                                            if ($data['FormData']['CasTusBlq'] == 'S') {
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
                                                <input class="form-control" id="CasRpsCod" name="CasRpsCod" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasRpsCod']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasRpsCod']; ?>" required readonly />
                                                <small id="CasRpsCodcHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasRpsCod']['TextHelp']; ?></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <label for="CasRpsDsc"><?= $data['FormDesign']['Fields']['CasRpsDsc']['LongLabel']; ?></label>
                                                <input class="form-control" id="CasRpsDsc" name="CasRpsDsc" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasRpsDsc']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasRpsDsc']; ?>" readonly />
                                                <small id="CasRpsDscHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasRpsDsc']['TextHelp']; ?></small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <label for="CasTusCod"><?= $data['FormDesign']['Fields']['CasTusCod']['LongLabel']; ?></label>
                                                <input class="form-control" id="CasTusCod" name="CasTusCod" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasTusCod']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasTusCod']; ?>" readonly />
                                                <small id="CasTusCodHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasTusCod']['TextHelp']; ?></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <label for="CasTusAudIns"><?= $data['FormDesign']['Fields']['CasTusAudIns']['LongLabel']; ?></label>
                                                <input class="form-control" id="CasTusAudIns" name="CasTusAudIns" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasTusAudIns']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasTusAudIns']; ?>" readonly />
                                                <small id="CasTusAudInsHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasTusAudIns']['TextHelp']; ?></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <label for="CasTusBlq"><?= $data['FormDesign']['Fields']['CasTusBlq']['LongLabel']; ?></label>
                                                <select class="form-control" id="CasTusBlq" name="CasTusBlq" type="text" <?= ($data['FormDesign']['FormDisable'] ? 'disabled' : ''); ?>>
                                                    <option value='N' <?= ($data['FormData']['CasTusBlq'] == 'N' ? 'selected' : '') ?>>Não</option>
                                                    <option value='S' <?= ($data['FormData']['CasTusBlq'] == 'S' ? 'selected' : '') ?>>Sim</option>
                                                </select>
                                                <small id="CasTusBlqHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasTusBlq']['TextHelp']; ?></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <label for="CasTusBlqDtt"><?= $data['FormDesign']['Fields']['CasTusBlqDtt']['LongLabel']; ?></label>
                                                <input class="form-control" id="CasTusBlqDtt" name="CasTusBlqDtt" type="datetime-local" placeholder="<?= $data['FormDesign']['Fields']['CasTusBlqDtt']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasTusBlqDtt']; ?>" readonly />
                                                <small id="CasTusBlqDttHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasTusBlqDtt']['TextHelp']; ?></small>
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
                        <label for="CasTusDsc"><?= $data['FormDesign']['Fields']['CasTusDsc']['LongLabel']; ?></label>
                        <input class="form-control" id="CasTusDsc" name="CasTusDsc" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasTusDsc']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasTusDsc']; ?>" autofocus required <?= ($data['FormDesign']['FormDisable'] ? 'readonly' : ''); ?> />
                        <small id="CasTusDscHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasTusDsc']['TextHelp']; ?></small>
                    </div>
                    <div class="form-floating mb-3">
                        <label for="CasTusObs"><?= $data['FormDesign']['Fields']['CasTusObs']['LongLabel']; ?></label>
                        <textarea class="form-control" id="CasTusObs" name="CasTusObs" rows="3" placeholder="<?= $data['FormDesign']['Fields']['CasTusObs']['TextPlaceholder']; ?>" <?= ($data['FormDesign']['FormDisable'] ? 'readonly' : ''); ?>><?= $data['FormData']['CasTusObs']; ?></textarea>
                        <small id="CasTusObsHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasTusObs']['TextHelp']; ?></small>
                    </div>
                    <div class="form-floating mb-3">
                        <label for="CasTusLnk"><?= $data['FormDesign']['Fields']['CasTusLnk']['LongLabel']; ?></label>
                        <input class="form-control" id="CasTusLnk" name="CasTusLnk" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasTusLnk']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasTusLnk']; ?>" required <?= ($data['FormDesign']['FormDisable'] ? 'readonly' : ''); ?> />
                        <small id="CasTusLnkcHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasTusLnk']['TextHelp']; ?></small>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <label for="CasTusGrp"><?= $data['FormDesign']['Fields']['CasTusGrp']['LongLabel']; ?></label>
                                <input class="form-control" id="CasTusGrp" name="CasTusGrp" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasTusGrp']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasTusGrp']; ?>" required <?= ($data['FormDesign']['FormDisable'] ? 'readonly' : ''); ?> />
                                <small id="CasTusGrpcHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasTusGrp']['TextHelp']; ?></small>
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