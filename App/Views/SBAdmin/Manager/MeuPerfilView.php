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

                    <!-- Profile -->
                    <div class="mb-4">
                        <div class="row">
                            <div class="col-sm-4 col-md-3 col-xl-2">
                                <img src="<?= $data['FormData']['PRESENTATION']['AVATAR']; ?>" class="img-fluid rounded" alt="...">
                            </div>
                            <div class="col-sm-8 col-md-9 col-xl-10">
                                <div class="card-body">
                                    <?php
                                    echo '<h1 class="display-4">' . $data['FormData']['PRESENTATION']['USER'] . '</h1>';
                                    echo '<p>' . $data['FormData']['PRESENTATION']['INTEGRATED'] . '</p>';
                                    echo '<p><small>Sua conta: ' . $data['FormData']['PRESENTATION']['ACCOUNT'];
                                    echo ' | ' . $data['FormData']['PRESENTATION']['ACCESS_TYPE'] . '</small></p>';
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="mb-4">
                        <table>
                            <tr>
                                <td>
                                    <img src="<?= $data['FormData']['PRESENTATION']['AVATAR']; ?>" class="img-fluid rounded float-left" alt="...">
                                </td>
                                <td class="align-top">
                                    <div class="card-body">
                                        <?php
                                        echo '<h1 class="display-4">' . $data['FormData']['PRESENTATION']['USER'] . '</h1>';
                                        echo '<p>' . $data['FormData']['PRESENTATION']['INTEGRATED'] . '</p>';
                                        echo 'Sua conta: <small>' . $data['FormData']['PRESENTATION']['ACCOUNT'] . '</small>';
                                        ?>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div> -->

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