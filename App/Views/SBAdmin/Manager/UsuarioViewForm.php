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
                                        if (empty($data['FormData']['CasUsrCod'])) {
                                            echo 'Informe os dados do novo cadastro.';
                                        } else {
                                            echo 'CADASTRADO EM ' . App\Class\Pattern\FormatData::transformData('OnlyDate', $data['FormData']['CasUsrAudIns']);
                                            if ($data['FormData']['CasUsrBlq'] == 'S') {
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
                                                <label for="CasUsrCod"><?= $data['FormDesign']['Fields']['CasUsrCod']['LongLabel']; ?></label>
                                                <input class="form-control" id="CasUsrCod" name="CasUsrCod" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasUsrCod']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasUsrCod']; ?>" readonly />
                                                <small id="CasUsrCodHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasUsrCod']['TextHelp']; ?></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <label for="CasUsrAudIns"><?= $data['FormDesign']['Fields']['CasUsrAudIns']['LongLabel']; ?></label>
                                                <input class="form-control" id="CasUsrAudIns" name="CasUsrAudIns" type="datetime-local" placeholder="<?= $data['FormDesign']['Fields']['CasUsrAudIns']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasUsrAudIns']; ?>" readonly />
                                                <small id="CasUsrAudInsHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasUsrAudIns']['TextHelp']; ?></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <label for="CasUsrBlq"><?= $data['FormDesign']['Fields']['CasUsrBlq']['LongLabel']; ?></label>
                                                <select class="form-control" id="CasUsrBlq" name="CasUsrBlq" type="text" <?= ($data['FormDesign']['FormDisable'] ? 'disabled' : ''); ?>>
                                                    <option value='N' <?= ($data['FormData']['CasUsrBlq'] == 'N' ? 'selected' : '') ?>>Não</option>
                                                    <option value='S' <?= ($data['FormData']['CasUsrBlq'] == 'S' ? 'selected' : '') ?>>Sim</option>
                                                </select>
                                                <small id="CasUsrBlqHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasUsrBlq']['TextHelp']; ?></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <label for="CasUsrBlqDtt"><?= $data['FormDesign']['Fields']['CasUsrBlqDtt']['LongLabel']; ?></label>
                                                <input class="form-control" id="CasUsrBlqDtt" name="CasUsrBlqDtt" type="datetime-local" placeholder="<?= $data['FormDesign']['Fields']['CasUsrBlqDtt']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasUsrBlqDtt']; ?>" readonly />
                                                <small id="CasUsrBlqDttHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasUsrBlqDtt']['TextHelp']; ?></small>
                                            </div>
                                        </div>
                                    </div>
                                    <a type="button" class="btn btn-secondary btn-block" href="<?= $data['FormDesign']['TransLinkRemove']; ?>" <?= (($data['FormDesign']['TransMode'] == 'Insert' || $data['FormDesign']['TransMode'] == 'Delete') ? 'hidden' : ''); ?>>Excluir Registro</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- General -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <label for="CasUsrNme"><?= $data['FormDesign']['Fields']['CasUsrNme']['LongLabel']; ?></label>
                                <input class="form-control" id="CasUsrNme" name="CasUsrNme" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasUsrNme']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasUsrNme']; ?>" autofocus required <?= ($data['FormDesign']['FormDisable'] ? 'readonly' : ''); ?> />
                                <small id="CasUsrNmeHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasUsrNme']['TextHelp']; ?></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <label for="CasUsrSnm"><?= $data['FormDesign']['Fields']['CasUsrSnm']['LongLabel']; ?></label>
                                <input class="form-control" id="CasUsrSnm" name="CasUsrSnm" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasUsrSnm']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasUsrSnm']; ?>" <?= ($data['FormDesign']['FormDisable'] ? 'readonly' : ''); ?> />
                                <small id="CasUsrSnmHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasUsrSnm']['TextHelp']; ?></small>
                            </div>
                        </div>
                    </div>
                    <div class="form-floating mb-3">
                        <label for="CasUsrDsc"><?= $data['FormDesign']['Fields']['CasUsrDsc']['LongLabel']; ?></label>
                        <input class="form-control" id="CasUsrDsc" name="CasUsrDsc" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasUsrDsc']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasUsrDsc']; ?>" required <?= ($data['FormDesign']['FormDisable'] ? 'readonly' : ''); ?> />
                        <small id="CasUsrDscHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasUsrDsc']['TextHelp']; ?></small>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <label for="CasUsrLgn"><?= $data['FormDesign']['Fields']['CasUsrLgn']['LongLabel']; ?></label>
                                <input class="form-control" id="CasUsrLgn" name="CasUsrLgn" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasUsrLgn']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasUsrLgn']; ?>" <?= ($data['FormDesign']['FormDisable'] ? 'readonly' : ''); ?> />
                                <small id="CasUsrLgnHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasUsrLgn']['TextHelp']; ?></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <label for="CasUsrDmn"><?= $data['FormDesign']['Fields']['CasUsrDmn']['LongLabel']; ?></label>
                                <input class="form-control" id="CasUsrDmn" name="CasUsrDmn" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasUsrDmn']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasUsrDmn']; ?>" required <?= ($data['FormDesign']['FormDisable'] ? 'readonly' : ''); ?> />
                                <small id="CasUsrDmnHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasUsrDmn']['TextHelp']; ?></small>
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