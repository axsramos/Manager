<!DOCTYPE html>
<html lang="pt-BR">

<?php include_once(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/Head.php'); ?>

<body class="sb-nav-fixed">
    <?php include_once(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/TopMenu.php'); ?>
    <div id="layoutSidenav">
        <?php include_once(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/SideMenu.php'); ?>

        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid">
                    <?php
                    include_once(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/Breadcrumb.php');
                    include_once(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/AlertMessage.php');
                    ?>

                    <div class="card mb-4 app-store-shell">
                        <?php include_once(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/Tabs.php'); ?>

                        <div class="card-body">
                            <?php
                            $loadFile = $data['FormDesign']['Tabs']['LoadFile'] ?? '';
                            $basePath = App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/AppStore/';
                            $realLoadFile = realpath(App\Core\Config::$DIR_BASE . '/' . $loadFile);
                            $realBasePath = realpath($basePath);

                            if ($realLoadFile !== false && $realBasePath !== false && str_starts_with($realLoadFile, $realBasePath) && is_file($realLoadFile)) {
                                include $realLoadFile;
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </main>

            <?php include_once(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/Footer.php'); ?>
        </div>
    </div>

    <?php include_once(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/BodyScripts.php'); ?>
    <script>
        (function() {
            var cardsButton = document.getElementById('appStoreCardsButton');
            var listButton = document.getElementById('appStoreListButton');
            var cardsView = document.getElementById('appStoreCardsView');
            var listView = document.getElementById('appStoreListView');

            if (!cardsButton || !listButton || !cardsView || !listView) {
                return;
            }

            function setView(mode) {
                var isCards = mode === 'cards';
                cardsView.hidden = !isCards;
                listView.hidden = isCards;
                cardsButton.classList.toggle('active', isCards);
                listButton.classList.toggle('active', !isCards);
                cardsButton.setAttribute('aria-pressed', isCards ? 'true' : 'false');
                listButton.setAttribute('aria-pressed', isCards ? 'false' : 'true');
                try {
                    window.localStorage.setItem('manager.appStore.view', mode);
                } catch (error) {}
            }

            cardsButton.addEventListener('click', function() { setView('cards'); });
            listButton.addEventListener('click', function() { setView('list'); });

            try {
                setView(window.localStorage.getItem('manager.appStore.view') === 'list' ? 'list' : 'cards');
            } catch (error) {
                setView('cards');
            }
        })();
    </script>
</body>

</html>
