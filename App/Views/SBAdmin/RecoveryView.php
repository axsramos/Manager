<!DOCTYPE html>
<html lang="pt-BR">

<?php
include_once(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/HeadLogin.php');
?>

<body class="bg-primary">
    <div id="layoutAuthentication">
        <div id="layoutAuthentication_content">
            <main>
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-5">
                            <div class="card shadow-lg border-0 rounded-lg mt-5">
                                <div class="card-header">
                                    <h3 class="text-center font-weight-light my-4"><a href="/Home" class="text-dark"><?=App\Core\Config::$APP_NAME;?></a></h3>
                                </div>
                                <div class="card-body">
                                    <h4 class="text-center font-weight-light mb-4">Recuperar Senha</h4>
                                    <?php
                                    if (!empty($data['FormDesign']['Message']['Description'])) {
                                        echo '<div class="alert alert-warning" role="alert">';
                                        echo $data['FormDesign']['Message']['Description'];
                                        echo '</div>';
                                    }
                                    ?>
                                    <div class="small mb-3 text-muted">Enviaremos para o seu e-mail cadastrado o link para recuperar o seu acesso.</div>
                                    <form action="Recovery" method="post">
                                        <div class="form-floating mb-3">
                                            <label for="inputEmail">Endereço de E-Mail</label>
                                            <input class="form-control" id="inputEmail" name="inputEmail" type="email" placeholder="name@example.com" required />
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                            <a class="small" href="/Auth/Login">Retornar para Login</a>
                                            <button class="btn btn-primary" id="btnConfirm" name="btnConfirm">Solicitar Token</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="card-footer text-center py-3">
                                    <div class="small">Não tem uma conta? <a href="/Auth/Register">Cadastre-se!</a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <div id="layoutAuthentication_footer">
            <?
            include(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/Footer.php');
            ?>
        </div>
    </div>
    <?
    include(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/BodyScriptsLogin.php');
    ?>
</body>

</html>