<!DOCTYPE html>
<html lang="pt-BR">

<?php
include_once(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/Head.php');
?>

<body class="sb-nav-fixed">
    <?php
    include_once(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/TopMenu.php');
    ?>
    <div id="layoutSidenav">
        <?php
        include_once(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/SideMenu.php');
        ?>

        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid">
                    <?php
                    include_once(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/Breadcrumb.php');
                    include_once(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/AlertMessage.php');
                    ?>

                    <!-- Package Key -->
                    <div class="card card-body mb-4">
                        <form action="<?= $data['FormDesign']['Tabs']['Items'][0]['Link']; ?>" method="post">
                            <div class="row row-cols-1 row-cols-md-2">
                                <!-- Search Key -->
                                <div class="col">
                                    <div class="card-body">
                                        <h4>Chave de Instalação</h4>
                                        <p>Obtenha a chave de instalação com o fornecedor do software.</p>
                                        <div class="input-group">
                                            <input id="CasAppCod" name="CasAppCod" type="text" class="form-control" placeholder="<?= $data['FormDesign']['Fields']['CasAppCod']['TextPlaceholder']; ?>" aria-label="Package Key" aria-describedby="button-package-key" autofocus>
                                            <div class="input-group-append">
                                                <button type="submit" class="btn btn-outline-secondary" name="btnSearchPackage">Pesquisar</button>
                                            </div>
                                            <small id="CasAppCodHelp" class="form-text text-muted"><?= $data['FormDesign']['Fields']['CasAppCod']['TextHelp']; ?></small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Instructions -->
                                <div class="col">
                                    <div class="card-body">
                                        <p>Siga as orientações fornecidas para atualização do seu produto.
                                            Em caso de dúvidas, solicite apoio ao suporte técnico.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Tabs -->
                    <div class="card mb-4">
                        <?php
                        include_once(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/Tabs.php');
                        ?>

                        <!-- Content -->
                        <div class="card-body">
                            <?php
                            if (!empty($data['FormDesign']['Tabs']['LoadFile'])) {
                                include_once($data['FormDesign']['Tabs']['LoadFile']);
                            }
                            ?>
                        </div>
                        <!-- End Content -->

                    </div>
                    <!-- End Tabs -->

                </div>
            </main>

            <?php
            include_once(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/Footer.php');
            ?>
        </div>
    </div>

    <?php
    include_once(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/BodyScripts.php');
    ?>
</body>

</html>