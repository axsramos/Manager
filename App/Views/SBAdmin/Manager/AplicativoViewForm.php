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
                                        if (empty($data['FormData']['CasAppCod'])) {
                                            echo 'Informe os dados do novo cadastro.';
                                        } else {
                                            echo 'CADASTRADO EM ' . App\Class\Pattern\FormatData::transformData('OnlyDate', $data['FormData']['CasAppAudIns']);
                                            if ($data['FormData']['CasAppBlq'] == 'S') {
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
                                                <label for="CasAppCod"><?= $data['FormDesign']['Fields']['CasAppCod']['LongLabel']; ?></label>
                                                <input class="form-control" id="CasAppCod" name="CasAppCod" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasAppCod']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasAppCod']; ?>" readonly />
                                                <small id="CasAppCodHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasAppCod']['TextHelp']; ?></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <label for="CasAppAudIns"><?= $data['FormDesign']['Fields']['CasAppAudIns']['LongLabel']; ?></label>
                                                <input class="form-control" id="CasAppAudIns" name="CasAppAudIns" type="datetime-local" placeholder="<?= $data['FormDesign']['Fields']['CasAppAudIns']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasAppAudIns']; ?>" readonly />
                                                <small id="CasAppAudInsHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasAppAudIns']['TextHelp']; ?></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <label for="CasAppBlq"><?= $data['FormDesign']['Fields']['CasAppBlq']['LongLabel']; ?></label>
                                                <select class="form-control" id="CasAppBlq" name="CasAppBlq" type="text" <?= ($data['FormDesign']['FormDisable'] ? 'disabled' : ''); ?>>
                                                    <option value='N' <?= ($data['FormData']['CasAppBlq'] == 'N' ? 'selected' : '') ?>>Não</option>
                                                    <option value='S' <?= ($data['FormData']['CasAppBlq'] == 'S' ? 'selected' : '') ?>>Sim</option>
                                                </select>
                                                <small id="CasAppBlqHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasAppBlq']['TextHelp']; ?></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <label for="CasAppBlqDtt"><?= $data['FormDesign']['Fields']['CasAppBlqDtt']['LongLabel']; ?></label>
                                                <input class="form-control" id="CasAppBlqDtt" name="CasAppBlqDtt" type="datetime-local" placeholder="<?= $data['FormDesign']['Fields']['CasAppBlqDtt']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasAppBlqDtt']; ?>" readonly />
                                                <small id="CasAppBlqDttHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasAppBlqDtt']['TextHelp']; ?></small>
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
                        <label for="CasAppDsc"><?= $data['FormDesign']['Fields']['CasAppDsc']['LongLabel']; ?></label>
                        <input class="form-control" id="CasAppDsc" name="CasAppDsc" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasAppDsc']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasAppDsc']; ?>" autofocus required <?= ($data['FormDesign']['FormDisable'] ? 'readonly' : ''); ?> />
                        <small id="CasAppDscHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasAppDsc']['TextHelp']; ?></small>
                    </div>
                    <div class="form-floating mb-3">
                        <label for="CasAppObs"><?= $data['FormDesign']['Fields']['CasAppObs']['LongLabel']; ?></label>
                        <textarea class="form-control" id="CasAppObs" name="CasAppObs" rows="3" placeholder="<?= $data['FormDesign']['Fields']['CasAppObs']['TextPlaceholder']; ?>" <?= ($data['FormDesign']['FormDisable'] ? 'readonly' : ''); ?>><?= $data['FormData']['CasAppObs']; ?></textarea>
                        <small id="CasAppObsHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasAppObs']['TextHelp']; ?></small>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <label for="CasAppTst"><?= $data['FormDesign']['Fields']['CasAppTst']['LongLabel']; ?></label>
                                <select class="form-control" id="CasAppTst" name="CasAppTst" type="text" <?= ($data['FormDesign']['FormDisable'] ? 'disabled' : ''); ?>>
                                    <option value='S' <?= ($data['FormData']['CasAppTst'] == 'S' ? 'selected' : '') ?>>Sim</option>
                                    <option value='N' <?= ($data['FormData']['CasAppTst'] == 'N' ? 'selected' : '') ?>>Não</option>
                                </select>
                                <small id="CasAppTstHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasAppTst']['TextHelp']; ?></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <label for="CasAppTstDtt"><?= $data['FormDesign']['Fields']['CasAppTstDtt']['LongLabel']; ?></label>
                                <input class="form-control" id="CasAppTstDtt" name="CasAppTstDtt" type="datetime-local" placeholder="<?= $data['FormDesign']['Fields']['CasAppTstDtt']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasAppTstDtt']; ?>" readonly />
                                <small id="CasAppTstDttHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasAppTstDtt']['TextHelp']; ?></small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <label for="CasAppVer"><?= $data['FormDesign']['Fields']['CasAppVer']['LongLabel']; ?></label>
                                <input class="form-control" id="CasAppVer" name="CasAppVer" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasAppVer']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasAppVer']; ?>" <?= ($data['FormDesign']['FormDisable'] ? 'readonly' : ''); ?> />
                                <small id="CasAppVerHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasAppVer']['TextHelp']; ?></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <label for="CasAppVerDtt"><?= $data['FormDesign']['Fields']['CasAppVerDtt']['LongLabel']; ?></label>
                                <input class="form-control" id="CasAppVerDtt" name="CasAppVerDtt" type="datetime-local" placeholder="<?= $data['FormDesign']['Fields']['CasAppVerDtt']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasAppVerDtt']; ?>" <?= ($data['FormDesign']['FormDisable'] ? 'readonly' : ''); ?> />
                                <small id="CasAppVerDttHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasAppVerDtt']['TextHelp']; ?></small>
                            </div>
                        </div>
                    </div>
                    <div class="form-floating mb-3">
                        <label for="CasAppVerLnk"><?= $data['FormDesign']['Fields']['CasAppVerLnk']['LongLabel']; ?></label>
                        <input class="form-control" id="CasAppVerLnk" name="CasAppVerLnk" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasAppVerLnk']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasAppVerLnk']; ?>" <?= ($data['FormDesign']['FormDisable'] ? 'readonly' : ''); ?> />
                        <small id="CasAppVerLnkHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasAppVerLnk']['TextHelp']; ?></small>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <label for="CasAppKey"><?= $data['FormDesign']['Fields']['CasAppKey']['LongLabel']; ?></label>
                                <input class="form-control" id="CasAppKey" name="CasAppKey" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasAppKey']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasAppKey']; ?>" required <?= ($data['FormDesign']['FormDisable'] ? 'readonly' : ''); ?> />
                                <small id="CasAppKeyHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasAppKey']['TextHelp']; ?></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <label for="CasAppKeyExp"><?= $data['FormDesign']['Fields']['CasAppKeyExp']['LongLabel']; ?></label>
                                <input class="form-control" id="CasAppKeyExp" name="CasAppKeyExp" type="datetime-local" placeholder="<?= $data['FormDesign']['Fields']['CasAppKeyExp']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasAppKeyExp']; ?>" <?= ($data['FormDesign']['FormDisable'] ? 'readonly' : ''); ?> />
                                <small id="CasAppKeyExpHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasAppKeyExp']['TextHelp']; ?></small>
                            </div>
                        </div>
                    </div>
                    <div class="form-floating mb-3">
                        <label for="CasAppGrp"><?= $data['FormDesign']['Fields']['CasAppGrp']['LongLabel']; ?></label>
                        <input class="form-control" id="CasAppGrp" name="CasAppGrp" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasAppGrp']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasAppGrp']; ?>" autofocus required <?= ($data['FormDesign']['FormDisable'] ? 'readonly' : ''); ?> />
                        <small id="CasAppGrpHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasAppGrp']['TextHelp']; ?></small>
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