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
                                        if (empty($data['FormData']['CasParCod'])) {
                                            echo 'Informe os dados do novo cadastro.';
                                        } else {
                                            echo 'CADASTRADO EM ' . App\Class\Pattern\FormatData::transformData('OnlyDate', $data['FormData']['CasParAudIns']);
                                            if ($data['FormData']['CasParBlq'] == 'S') {
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
                                                <label for="CasParCod"><?= $data['FormDesign']['Fields']['CasParCod']['LongLabel']; ?></label>
                                                <input class="form-control" id="CasParCod" name="CasParCod" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasParCod']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasParCod']; ?>" readonly />
                                                <small id="CasParCodHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasParCod']['TextHelp']; ?></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <label for="CasParAudIns"><?= $data['FormDesign']['Fields']['CasParAudIns']['LongLabel']; ?></label>
                                                <input class="form-control" id="CasParAudIns" name="CasParAudIns" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasParAudIns']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasParAudIns']; ?>" readonly />
                                                <small id="CasParAudInsHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasParAudIns']['TextHelp']; ?></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <label for="CasParBlq"><?= $data['FormDesign']['Fields']['CasParBlq']['LongLabel']; ?></label>
                                                <select class="form-control" id="CasParBlq" name="CasParBlq" type="text" <?= ($data['FormDesign']['FormDisable'] ? 'disabled' : ''); ?>>
                                                    <option value='N' <?= ($data['FormData']['CasParBlq'] == 'N' ? 'selected' : '') ?>>Não</option>
                                                    <option value='S' <?= ($data['FormData']['CasParBlq'] == 'S' ? 'selected' : '') ?>>Sim</option>
                                                </select>
                                                <small id="CasParBlqHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasParBlq']['TextHelp']; ?></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <label for="CasParBlqDtt"><?= $data['FormDesign']['Fields']['CasParBlqDtt']['LongLabel']; ?></label>
                                                <input class="form-control" id="CasParBlqDtt" name="CasParBlqDtt" type="datetime-local" placeholder="<?= $data['FormDesign']['Fields']['CasParBlqDtt']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasParBlqDtt']; ?>" readonly />
                                                <small id="CasParBlqDttHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasParBlqDtt']['TextHelp']; ?></small>
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
                        <label for="CasParDsc"><?= $data['FormDesign']['Fields']['CasParDsc']['LongLabel']; ?></label>
                        <input class="form-control" id="CasParDsc" name="CasParDsc" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasParDsc']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasParDsc']; ?>" autofocus required <?= ($data['FormDesign']['FormDisable'] ? 'readonly' : ''); ?> />
                        <small id="CasParDscHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasParDsc']['TextHelp']; ?></small>
                    </div>
                    <div class="form-floating mb-3">
                        <label for="CasParTxt"><?= $data['FormDesign']['Fields']['CasParTxt']['LongLabel']; ?></label>
                        <textarea class="form-control" id="CasParTxt" name="CasParTxt" rows="3" placeholder="<?= $data['FormDesign']['Fields']['CasParTxt']['TextPlaceholder']; ?>" <?= ($data['FormDesign']['FormDisable'] ? 'readonly' : ''); ?>><?= $data['FormData']['CasParTxt']; ?></textarea>
                        <small id="CasParTxtHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasParTxt']['TextHelp']; ?></small>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <label for="CasParTbl"><?= $data['FormDesign']['Fields']['CasParTbl']['LongLabel']; ?></label>
                                <input class="form-control" id="CasParTbl" name="CasParTbl" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasParTbl']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasParTbl']; ?>" required <?= ($data['FormDesign']['FormDisable'] ? 'readonly' : ''); ?> />
                                <small id="CasParTblcHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasParTbl']['TextHelp']; ?></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <label for="CasParSeq"><?= $data['FormDesign']['Fields']['CasParSeq']['LongLabel']; ?></label>
                                <input class="form-control" id="CasParSeq" name="CasParSeq" type="number" placeholder="<?= $data['FormDesign']['Fields']['CasParSeq']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasParSeq']; ?>" required <?= ($data['FormDesign']['FormDisable'] ? 'readonly' : ''); ?> />
                                <small id="CasParSeqcHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasParSeq']['TextHelp']; ?></small>
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
                            <div class="form-floating mb-3">
                                <label for="CasParGrp"><?= $data['FormDesign']['Fields']['CasParGrp']['LongLabel']; ?></label>
                                <input class="form-control" id="CasParGrp" name="CasParGrp" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasParGrp']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasParGrp']; ?>" required <?= ($data['FormDesign']['FormDisable'] ? 'readonly' : ''); ?> />
                                <small id="CasParGrpcHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasParGrp']['TextHelp']; ?></small>
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