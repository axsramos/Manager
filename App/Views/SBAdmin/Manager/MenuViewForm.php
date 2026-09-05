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
                                        if (empty($data['FormData']['CasMnuCod'])) {
                                            echo 'Informe os dados do novo cadastro.';
                                        } else {
                                            echo 'CADASTRADO EM ' . App\Class\Pattern\FormatData::transformData('OnlyDate', $data['FormData']['CasMnuAudIns']);
                                            if ($data['FormData']['CasMnuBlq'] == 'S') {
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
                                                <label for="CasMnuCod"><?= $data['FormDesign']['Fields']['CasMnuCod']['LongLabel']; ?></label>
                                                <input class="form-control" id="CasMnuCod" name="CasMnuCod" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasMnuCod']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasMnuCod']; ?>" readonly />
                                                <small id="CasMnuCodHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasMnuCod']['TextHelp']; ?></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <label for="CasMnuAudIns"><?= $data['FormDesign']['Fields']['CasMnuAudIns']['LongLabel']; ?></label>
                                                <input class="form-control" id="CasMnuAudIns" name="CasMnuAudIns" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasMnuAudIns']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasMnuAudIns']; ?>" readonly />
                                                <small id="CasMnuAudInsHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasMnuAudIns']['TextHelp']; ?></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <label for="CasMnuBlq"><?= $data['FormDesign']['Fields']['CasMnuBlq']['LongLabel']; ?></label>
                                                <select class="form-control" id="CasMnuBlq" name="CasMnuBlq" type="text" <?= ($data['FormDesign']['FormDisable'] ? 'disabled' : ''); ?>>
                                                    <option value='N' <?= ($data['FormData']['CasMnuBlq'] == 'N' ? 'selected' : '') ?>>Não</option>
                                                    <option value='S' <?= ($data['FormData']['CasMnuBlq'] == 'S' ? 'selected' : '') ?>>Sim</option>
                                                </select>
                                                <small id="CasMnuBlqHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasMnuBlq']['TextHelp']; ?></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <label for="CasMnuBlqDtt"><?= $data['FormDesign']['Fields']['CasMnuBlqDtt']['LongLabel']; ?></label>
                                                <input class="form-control" id="CasMnuBlqDtt" name="CasMnuBlqDtt" type="datetime-local" placeholder="<?= $data['FormDesign']['Fields']['CasMnuBlqDtt']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasMnuBlqDtt']; ?>" readonly />
                                                <small id="CasMnuBlqDttHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasMnuBlqDtt']['TextHelp']; ?></small>
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
                        <label for="CasMnuDsc"><?= $data['FormDesign']['Fields']['CasMnuDsc']['LongLabel']; ?></label>
                        <input class="form-control" id="CasMnuDsc" name="CasMnuDsc" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasMnuDsc']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasMnuDsc']; ?>" autofocus required <?= ($data['FormDesign']['FormDisable'] ? 'readonly' : ''); ?> />
                        <small id="CasMnuDscHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasMnuDsc']['TextHelp']; ?></small>
                    </div>
                    <div class="form-floating mb-3">
                        <label for="CasMnuTxt"><?= $data['FormDesign']['Fields']['CasMnuTxt']['LongLabel']; ?></label>
                        <textarea class="form-control" id="CasMnuTxt" name="CasMnuTxt" rows="3" placeholder="<?= $data['FormDesign']['Fields']['CasMnuTxt']['TextPlaceholder']; ?>" <?= ($data['FormDesign']['FormDisable'] ? 'readonly' : ''); ?>><?= $data['FormData']['CasMnuTxt']; ?></textarea>
                        <small id="CasMnuTxtHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasMnuTxt']['TextHelp']; ?></small>
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
                                <label for="CasMnuGrp"><?= $data['FormDesign']['Fields']['CasMnuGrp']['LongLabel']; ?></label>
                                <input class="form-control" id="CasMnuGrp" name="CasMnuGrp" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasMnuGrp']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasMnuGrp']; ?>" required <?= ($data['FormDesign']['FormDisable'] ? 'readonly' : ''); ?> />
                                <small id="CasMnuGrpcHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasMnuGrp']['TextHelp']; ?></small>
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