<!DOCTYPE html>
<html lang="pt-BR">
<?php include_once(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/Head.php'); ?>
<body class="sb-nav-fixed">
<?php include_once(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/TopMenu.php'); ?>
<div id="layoutSidenav">
    <?php include_once(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/SideMenu.php'); ?>
    <div id="layoutSidenav_content"><main><div class="container-fluid">
        <?php include_once(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/Breadcrumb.php'); ?>
        <?php include_once(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/AlertMessage.php'); ?>
        <div class="card mb-4">
            <?php include_once(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/Tabs.php'); ?>
            <div class="card-body">
                <?php
                $loadFile = $data['FormDesign']['Tabs']['LoadFile'] ?? '';
                $basePath = App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Contract/';
                if (str_starts_with($loadFile, 'App/Views/SBAdmin/Contract/') && is_file($loadFile)) { include $loadFile; }
                ?>
            </div>
        </div>
    </div></main><?php include_once(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/Footer.php'); ?></div>
</div>
<?php include_once(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/BodyScripts.php'); ?>
</body></html>
