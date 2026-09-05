<!DOCTYPE html>
<html lang="pt-BR">
<?php include_once(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/HeadLogin.php'); ?>
<body class="bg-primary">
<div id="layoutAuthentication">
  <div id="layoutAuthentication_content"><main><div class="container"><div class="row justify-content-center"><div class="col-lg-5">
    <div class="card shadow-lg border-0 rounded-lg mt-5">
      <div class="card-header"><h3 class="text-center font-weight-light my-4"><?=htmlspecialchars(App\Core\Config::$APP_NAME, ENT_QUOTES, 'UTF-8');?></h3></div>
      <div class="card-body text-center">
        <h4 class="font-weight-light mb-3"><?=$data['Activation']['success'] ? 'Ativação concluída' : 'Não foi possível ativar';?></h4>
        <div class="alert <?=$data['Activation']['success'] ? 'alert-success' : 'alert-warning';?>" role="alert"><?=htmlspecialchars($data['Activation']['message'], ENT_QUOTES, 'UTF-8');?></div>
        <a class="btn btn-primary" href="<?=$data['Activation']['status'] === 'account_missing' ? '/Auth/Register' : '/Auth/Login';?>"><?=$data['Activation']['status'] === 'account_missing' ? 'Iniciar cadastro' : 'Ir para o login';?></a>
      </div>
    </div>
  </div></div></div></main></div>
  <div id="layoutAuthentication_footer"><?php include(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/Footer.php'); ?></div>
</div>
<?php include(App\Core\Config::$DIR_BASE . '/App/Views/SBAdmin/Partials/BodyScriptsLogin.php'); ?>
</body>
</html>
