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