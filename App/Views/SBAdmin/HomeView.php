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
                        <div class="col-xl-12">
                            <div class="jumbotron">
                                <h1 class="display-4">Manager</h1>
                                <p class="lead">O Manager é um sistema de gestão de contas de usuário com arquitetura <strong>multi-tenant</strong>, projetado para simplificar a administração de usuários em múltiplos clientes (empresas ou projetos) a partir de uma única plataforma.</p>
                                <hr class="my-4">
                                <p>A plataforma centraliza o gerenciamento do ciclo de vida das contas, aumenta a segurança e otimiza a eficiência operacional.</p>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-xl-12">
                            <h2 class="mt-4"><i class="fas fa-star mr-2"></i>Principais Funcionalidades</h2>
                            <hr>
                        </div>
                    </div>
                    <div class="row">
                        <div class="card-deck">
                            <div class="card bg-light mb-3">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-users mr-2"></i>Gerenciamento Multi-Tenant</h5>
                                    <p class="card-text">Administre diversos inquilinos de forma centralizada, com isolamento de dados garantido para cada cliente.</p>
                                </div>
                            </div>
                            <div class="card bg-light mb-3">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-user-cog mr-2"></i>Gestão do Ciclo de Vida do Usuário</h5>
                                    <p class="card-text">Crie, edite, suspenda e desative contas de usuário de forma simples e intuitiva.</p>
                                </div>
                            </div>
                            <div class="card bg-light mb-3">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-shield-alt mr-2"></i>Controle de Acesso</h5>
                                    <p class="card-text">Atribua papéis e permissões granulares para controlar exatamente quem tem acesso a quais recursos.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="card-deck">
                            <div class="card bg-light mb-3">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-history mr-2"></i>Auditoria e Monitoramento</h5>
                                    <p class="card-text">Registre e acompanhe todas as atividades dos usuários para auditorias e detecção de comportamentos suspeitos.</p>
                                </div>
                            </div>
                            <div class="card bg-light mb-3">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-power-off mr-2"></i>Modo de Manutenção</h5>
                                    <p class="card-text">O sistema pode operar em um estado offline limitado, permitindo acesso a conteúdo estático durante manutenções.</p>
                                </div>
                            </div>
                            <div class="card bg-light mb-3">
                                <div class="card-body">
                                    <h5 class="card-title"><i class="fas fa-key mr-2"></i>Autenticação Avançada</h5>
                                    <p class="card-text">Mecanismos robustos como a Autenticação de Dois Fatores (2FA).</p>
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