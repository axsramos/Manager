<section>
    <form action="<?= $data['FormDesign']['Tabs']['Items'][1]['Link']; ?>" method="post">
        <div class="row row-cols-1 row-cols-md-2">

            <!-- Cadastro -->
            <div class="col">
                <div class="card-body">
                    <div class="form-floating mb-3">
                        <p class="h4">Informações Pessoais </p>
                        <p class="lead">
                            Não vendemos nem compartilhamos seus dados sem sua permissão.
                        </p>

                        <!-- Conta de Acesso -->
                        <div class="mb-3" hidden>
                            <div class="form-group row">
                                <label for="staticCasRpsCod" class="col-sm-2 col-form-label">Repositório</label>
                                <div class="col-sm-10">
                                    <input type="text" readonly class="form-control-plaintext" id="staticCasRpsCod" value="<?= $data['FormData']['CasRpsCod']; ?>">
                                </div>
                            </div>
                        </div>

                        <!-- Informações Pessoais -->
                        <div class="mb-3">
                            <div class="form-group">
                                <label for="CasUsrDsc"><?= $data['FormDesign']['Fields']['CasUsrDsc']['LongLabel']; ?></label>
                                <input type="text" class="form-control" id="CasUsrDsc" aria-describedby="CasUsrNmeHelp" placeholder="<?= $data['FormDesign']['Fields']['CasUsrDsc']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasUsrDsc']; ?>" readonly>
                                <small id="CasUsrDscHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasUsrDsc']['TextHelp']; ?></small>
                            </div>
                            <div class="form-group">
                                <label for="CasUsrNck"><?= $data['FormDesign']['Fields']['CasUsrNck']['LongLabel']; ?></label>
                                <input type="text" class="form-control" id="CasUsrNck" aria-describedby="CasUsrNckHelp" placeholder="<?= $data['FormDesign']['Fields']['CasUsrNck']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasUsrNck']; ?>" readonly>
                                <small id="CasUsrNckHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasUsrNck']['TextHelp']; ?></small>
                            </div>

                            <div class="form-group">
                                <label for="CasUsrMai"><?= $data['FormDesign']['Fields']['CasUsrLgn']['LongLabel']; ?></label>
                                <input type="email" class="form-control" id="CasUsrMai" aria-describedby="CasUsrMaiHelp" placeholder="<?= $data['FormDesign']['Fields']['CasUsrLgn']['TextPlaceholder']; ?>" value="<?= $data['FormData']['CasUsrMai']; ?>" readonly>
                                <small id="CasUsrMaiHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasUsrLgn']['TextHelp']; ?></small>
                            </div>
                            <a type="button" href="/Manager/MeuPerfil/Register" class="btn btn-primary mt-2">Editar</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Button Controls -->
        <?php
        // include_once(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/FormButtonControls.php');
        ?>
    </form>
</section>