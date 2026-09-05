<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <a class="navbar-brand" href="/Home"><?= App\Core\Config::$APP_NAME; ?></a>
    <button class="btn btn-link btn-sm order-1 order-lg-0" id="sidebarToggle" href="#"><i class="fas fa-bars"></i></button>

    <?php
    include_once(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/NavbarSearch.php');
    ?>

    <?php
    include_once(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/RepositorySelected.php');
    ?>

    <?php
    include_once(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/NavbarLogin.php');
    ?>

</nav>