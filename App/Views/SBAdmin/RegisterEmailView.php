<!DOCTYPE html>
<html lang="pt-BR">
<?php include_once(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/HeadLogin.php'); ?>
<body class="bg-primary">
<div id="layoutAuthentication">
  <div id="layoutAuthentication_content"><main><div class="container"><div class="row justify-content-center"><div class="col-lg-5">
    <div class="card shadow-lg border-0 rounded-lg mt-5">
      <div class="card-header"><h3 class="text-center font-weight-light my-4"><a href="/Home" class="text-dark"><?=htmlspecialchars(App\Core\Config::$APP_NAME, ENT_QUOTES, 'UTF-8');?></a></h3></div>
      <div class="card-body">
        <h4 class="text-center font-weight-light mb-2">Crie sua conta</h4>
        <p class="small text-muted text-center mb-4">Informe seu e-mail para receber o código de verificação.</p>
        <?php if (!empty($data['FormDesign']['Message']['Description'])): ?>
          <div class="alert alert-warning" role="alert"><?=htmlspecialchars($data['FormDesign']['Message']['Description'], ENT_QUOTES, 'UTF-8');?></div>
        <?php endif; ?>
        <form action="/Auth/Register" method="post">
          <div class="mb-3"><label for="inputEmail">Endereço de e-mail</label><input class="form-control" id="inputEmail" name="inputEmail" type="email" autocomplete="email" placeholder="nome@exemplo.com" value="<?=htmlspecialchars($data['FormData']['inputEmail'], ENT_QUOTES, 'UTF-8');?>" required></div>
          <button class="btn btn-primary btn-block w-100" id="btnContinue" name="btnContinue" type="submit">Avançar</button>
        </form>
        <?php if (App\Core\Config::$GOOGLE_AUTH_SERVICE === true): ?>
          <div class="position-relative my-4"><hr><span class="position-absolute top-50 start-50 translate-middle bg-white px-2 small text-muted">ou</span></div>
          <button class="btn btn-outline-secondary btn-block w-100" type="button" disabled aria-disabled="true">Continuar com Google (em breve)</button>
        <?php endif; ?>
      </div>
      <div class="card-footer text-center py-3"><div class="small">Já tem uma conta? <a href="/Auth/Login">Entrar</a></div></div>
    </div>
  </div></div></div></main></div>
  <div id="layoutAuthentication_footer"><?php include(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/Footer.php'); ?></div>
</div>
<?php include(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/BodyScriptsLogin.php'); ?>
</body>
</html>
