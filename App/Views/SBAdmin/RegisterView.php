<!DOCTYPE html>
<html lang="pt-BR">
<?php include_once(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/HeadLogin.php'); ?>
<body class="bg-primary">
<div id="layoutAuthentication">
  <div id="layoutAuthentication_content"><main><div class="container"><div class="row justify-content-center"><div class="col-lg-7">
    <div class="card shadow-lg border-0 rounded-lg mt-5">
      <div class="card-header"><h3 class="text-center font-weight-light my-4"><a href="/Home" class="text-dark"><?=htmlspecialchars(App\Core\Config::$APP_NAME, ENT_QUOTES, 'UTF-8');?></a></h3></div>
      <div class="card-body">
        <h4 class="text-center font-weight-light mb-2">Complete seu cadastro</h4>
        <p class="small text-muted text-center mb-4">E-mail verificado. Informe seus dados para criar a conta.</p>
        <?php if (!empty($data['FormDesign']['Message']['Description'])): ?>
          <div class="alert alert-warning" role="alert"><?=htmlspecialchars($data['FormDesign']['Message']['Description'], ENT_QUOTES, 'UTF-8');?></div>
        <?php endif; ?>
        <form action="/Auth/Register" method="post">
          <div class="row mb-3">
            <div class="col-md-6"><label for="inputFirstName">Primeiro nome</label><input class="form-control" id="inputFirstName" name="inputFirstName" type="text" value="<?=htmlspecialchars($data['FormData']['inputFirstName'], ENT_QUOTES, 'UTF-8');?>" required></div>
            <div class="col-md-6"><label for="inputLastName">Sobrenome</label><input class="form-control" id="inputLastName" name="inputLastName" type="text" value="<?=htmlspecialchars($data['FormData']['inputLastName'], ENT_QUOTES, 'UTF-8');?>" required></div>
          </div>
          <div class="mb-3"><label for="inputEmail">Endereço de e-mail</label><input class="form-control" id="inputEmail" name="inputEmail" type="email" value="<?=htmlspecialchars($data['FormData']['inputEmail'], ENT_QUOTES, 'UTF-8');?>" readonly required></div>
          <div class="row mb-3">
            <div class="col-md-6"><label for="inputPassword">Crie uma senha</label><input class="form-control" id="inputPassword" name="inputPassword" type="password" autocomplete="new-password" required></div>
            <div class="col-md-6"><label for="inputPasswordConfirm">Confirme a senha</label><input class="form-control" id="inputPasswordConfirm" name="inputPasswordConfirm" type="password" autocomplete="new-password" required></div>
          </div>
          <button class="btn btn-primary btn-block w-100" id="btnConfirm" name="btnConfirm" type="submit">Criar conta</button>
          <div class="text-center mt-3"><a class="small" href="/Auth/Register?restart=1">Usar outro e-mail</a></div>
        </form>
      </div>
      <div class="card-footer text-center py-3"><div class="small">Já tem uma conta? <a href="/Auth/Login">Entrar</a></div></div>
    </div>
  </div></div></div></main></div>
  <div id="layoutAuthentication_footer"><?php include(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/Footer.php'); ?></div>
</div>
<?php include(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/BodyScriptsLogin.php'); ?>
</body>
</html>
