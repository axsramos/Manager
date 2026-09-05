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
                <div class="container-fluid px-4">
                    <?php
                    // include_once(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/Breadcrumb.php');
                    ?>

                    <div class="row justify-content-center mt-4 mt-md-5">
                        <div class="col-lg-7 col-md-9">
                            <div class="card shadow-lg border-0 rounded-lg">
                                <div class="card-header bg-danger text-white">
                                    <h3 class="text-center font-weight-light my-3">
                                        <i class="fas fa-exclamation-triangle me-2"></i>&nbsp;Acesso Negado
                                    </h3>
                                </div>
                                <div class="card-body text-center p-4">
                                    <div class="my-3">
                                        <i class="fas fa-user-lock fa-5x text-warning mb-3"></i>
                                        <!-- Alternativa com imagem SVG/PNG (descomente e ajuste o caminho): -->
                                        <!-- <img src="/img/icons/access_denied.svg" alt="Acesso Negado" style="width: 100px; height: 100px;" class="mb-3"> -->
                                    </div>
                                    <p class="lead">
                                        Oops! Parece que você não tem permissão para acessar este recurso.
                                    </p>
                                    <p class="text-muted small">
                                        Isso pode acontecer se sua sessão expirou, suas credenciais não concedem acesso
                                        ou a página solicitada requer um nível de autorização diferente.
                                    </p>
                                    <p class="mt-4">
                                        Se você acredita que deveria ter acesso, por favor, entre em contato com o administrador do sistema.
                                    </p>
                                </div>
                                <div class="card-footer text-center py-3">
                                    <a href="/" class="btn btn-primary">
                                        <i class="fas fa-home me-1"></i> Voltar para a Página Inicial
                                    </a>
                                    <button onclick="if (window.history.length > 1) { window.history.back(); } else { window.location.href = '/'; }" class="btn btn-outline-secondary ms-2">
                                        <i class="fas fa-arrow-left me-1"></i> Voltar
                                    </button>
                                </div>
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