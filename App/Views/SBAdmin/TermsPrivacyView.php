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
                    ?>

                    <div class="row">
                        <div class="col mb-3">
                            <div class="card card-body">
                                <?php
                                print_r($data['FormData']);
                                ?>
                            </div>
                        </div>
                    </div>

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