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
                                        if (empty($data['FormData']['CasTknCod'])) {
                                            echo 'Informe os dados do novo cadastro.';
                                        } else {
                                            echo 'CADASTRADO EM ' . APP\Class\Pattern\FormatData::transformData('OnlyDate', $data['FormData']['CasTknAudIns']);
                                            if ($data['FormData']['CasTknBlq'] == 'S') {
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
                                                <label for="CasTknCod"><?= $data['FormDesign']['Fields']['CasTknCod']['LongLabel']; ?></label>
                                                <input class="form-control" id="CasTknCod" name="CasTknCod" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasTknCod']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasTknCod']; ?>" readonly />
                                                <small id="CasTknCodHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasTknCod']['TextHelp']; ?></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <label for="CasTknAudIns"><?= $data['FormDesign']['Fields']['CasTknAudIns']['LongLabel']; ?></label>
                                                <input class="form-control" id="CasTknAudIns" name="CasTknAudIns" type="datetime-local" placeholder="<?= $data['FormDesign']['Fields']['CasTknAudIns']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasTknAudIns']; ?>" readonly />
                                                <small id="CasTknAudInsHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasTknAudIns']['TextHelp']; ?></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <label for="CasTknBlq"><?= $data['FormDesign']['Fields']['CasTknBlq']['LongLabel']; ?></label>
                                                <select class="form-control" id="CasTknBlq" name="CasTknBlq" type="text" <?= ($data['FormDesign']['FormDisable'] ? 'disabled': ''); ?>>
                                                    <option value='N' <?= ($data['FormData']['CasTknBlq'] == 'N' ? 'selected' : '') ?>>Não</option>
                                                    <option value='S' <?= ($data['FormData']['CasTknBlq'] == 'S' ? 'selected' : '') ?>>Sim</option>
                                                </select>
                                                <small id="CasTknBlqHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasTknBlq']['TextHelp']; ?></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <label for="CasTknBlqDtt"><?= $data['FormDesign']['Fields']['CasTknBlqDtt']['LongLabel']; ?></label>
                                                <input class="form-control" id="CasTknBlqDtt" name="CasTknBlqDtt" type="datetime-local" placeholder="<?= $data['FormDesign']['Fields']['CasTknBlqDtt']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasTknBlqDtt']; ?>" readonly />
                                                <small id="CasTknBlqDttHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasTknBlqDtt']['TextHelp']; ?></small>
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
                        <label for="CasTknDsc"><?= $data['FormDesign']['Fields']['CasTknDsc']['LongLabel']; ?></label>
                        <input class="form-control" id="CasTknDsc" name="CasTknDsc" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasTknDsc']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasTknDsc']; ?>" autofocus required <?= ($data['FormDesign']['FormDisable'] ? 'readonly': ''); ?> />
                        <small id="CasTknDscHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasTknDsc']['TextHelp']; ?></small>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <label for="CasTknKey"><?= $data['FormDesign']['Fields']['CasTknKey']['LongLabel']; ?></label>
                                <input class="form-control" id="CasTknKey" name="CasTknKey" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasTknKey']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasTknKey']; ?>" required <?= ($data['FormDesign']['FormDisable'] ? 'readonly': ''); ?> />
                                <small id="CasTknKeyHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasTknKey']['TextHelp']; ?></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating mb-3">
                                <label for="CasTknKeyExp"><?= $data['FormDesign']['Fields']['CasTknKeyExp']['LongLabel']; ?></label>
                                <input class="form-control" id="CasTknKeyExp" name="CasTknKeyExp" type="datetime-local" placeholder="<?= $data['FormDesign']['Fields']['CasTknKeyExp']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasTknKeyExp']; ?>" readonly />
                                <small id="CasTknKeyExpHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasTknKeyExp']['TextHelp']; ?></small>
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