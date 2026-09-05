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
                                        if (empty($data['FormData']['CasFunCod'])) {
                                            echo 'Informe os dados do novo cadastro.';
                                        } else {
                                            echo 'CADASTRADO EM ' . App\Class\Pattern\FormatData::transformData('OnlyDate', $data['FormData']['CasFunAudIns']);
                                            if ($data['FormData']['CasFunBlq'] == 'S') {
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
                                                <label for="CasFunCod"><?= $data['FormDesign']['Fields']['CasFunCod']['LongLabel']; ?></label>
                                                <input class="form-control" id="CasFunCod" name="CasFunCod" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasFunCod']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasFunCod']; ?>" readonly />
                                                <small id="CasFunCodHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasFunCod']['TextHelp']; ?></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <label for="CasFunAudIns"><?= $data['FormDesign']['Fields']['CasFunAudIns']['LongLabel']; ?></label>
                                                <input class="form-control" id="CasFunAudIns" name="CasFunAudIns" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasFunAudIns']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasFunAudIns']; ?>" readonly />
                                                <small id="CasFunAudInsHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasFunAudIns']['TextHelp']; ?></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <label for="CasFunBlq"><?= $data['FormDesign']['Fields']['CasFunBlq']['LongLabel']; ?></label>
                                                <select class="form-control" id="CasFunBlq" name="CasFunBlq" type="text" <?= ($data['FormDesign']['FormDisable'] ? 'disabled' : ''); ?>>
                                                    <option value='N' <?= ($data['FormData']['CasFunBlq'] == 'N' ? 'selected' : '') ?>>Não</option>
                                                    <option value='S' <?= ($data['FormData']['CasFunBlq'] == 'S' ? 'selected' : '') ?>>Sim</option>
                                                </select>
                                                <small id="CasFunBlqHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasFunBlq']['TextHelp']; ?></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating mb-3">
                                                <label for="CasFunBlqDtt"><?= $data['FormDesign']['Fields']['CasFunBlqDtt']['LongLabel']; ?></label>
                                                <input class="form-control" id="CasFunBlqDtt" name="CasFunBlqDtt" type="datetime-local" placeholder="<?= $data['FormDesign']['Fields']['CasFunBlqDtt']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasFunBlqDtt']; ?>" readonly />
                                                <small id="CasFunBlqDttHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasFunBlqDtt']['TextHelp']; ?></small>
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
                        <label for="CasFunDsc"><?= $data['FormDesign']['Fields']['CasFunDsc']['LongLabel']; ?></label>
                        <input class="form-control" id="CasFunDsc" name="CasFunDsc" type="text" placeholder="<?= $data['FormDesign']['Fields']['CasFunDsc']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasFunDsc']; ?>" autofocus required <?= ($data['FormDesign']['FormDisable'] ? 'readonly' : ''); ?> />
                        <small id="CasFunDscHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasFunDsc']['TextHelp']; ?></small>
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