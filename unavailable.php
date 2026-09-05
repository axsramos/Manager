<?php
  require_once('App/Core/Config.php');
  $mail = App\Core\Config::getContact('CONTACT_EMAIL');
?>
<!DOCTYPE html>
<html lang="pt-BR">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Serviço Indisponível</title>
    <style>
      body {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto,
          "Helvetica Neue", Arial, sans-serif;
        background-color: #f8f9fa;
        color: #343a40;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        text-align: center;
      }
      .container {
        max-width: 600px;
        padding: 40px;
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      }
      h1 {
        color: #dc3545;
        font-size: 2.2em;
        margin-bottom: 15px;
      }
      p {
        font-size: 1.1em;
        line-height: 1.6;
      }
      .icon {
        width: 70px;
        height: 70px;
        margin-bottom: 20px;
        fill: #dc3545;
      }
    </style>
  </head>
  <body>
    <div class="container">
      <svg class="icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
        <path
          d="M19.4,12.5c0.1-0.3,0.1-0.6,0.1-0.9s0-0.6-0.1-0.9l1.7-1.3c0.2-0.1,0.2-0.4,0.1-0.6l-1.7-3c-0.1-0.2-0.4-0.3-0.6-0.2l-2,0.8c-0.5-0.4-1-0.6-1.6-0.8L14.2,3.2C14.2,3,13.9,2.8,13.7,2.8h-3.4c-0.3,0-0.5,0.2-0.5,0.4l-0.3,2.4C8.9,6,8.4,6.2,7.9,6.6l-2-0.8c-0.2-0.1-0.5,0-0.6,0.2l-1.7,3c-0.1,0.2-0.1,0.5,0.1,0.6l1.7,1.3c-0.1,0.3-0.1,0.6-0.1,0.9s0,0.6,0.1,0.9L3.7,14c-0.2,0.1-0.2,0.4-0.1,0.6l1.7,3c0.1,0.2,0.4,0.3,0.6,0.2l2-0.8c0.5,0.4,1,0.6,1.6,0.8l0.3,2.4c0,0.2,0.2,0.4,0.5,0.4h3.4c0.3,0,0.5-0.2,0.5-0.4l0.3-2.4c0.6-0.2,1.2-0.4,1.6-0.8l2,0.8c0.2,0.1,0.5,0,0.6-0.2l1.7-3c0.1-0.2,0.1-0.5-0.1-0.6L19.4,12.5z M12,15.1c-1.7,0-3.1-1.4-3.1-3.1s1.4-3.1,3.1-3.1s3.1,1.4,3.1,3.1S13.7,15.1,12,15.1z"
        />
      </svg>
      <h1>Serviço Temporariamente Indisponível</h1>
      <p>
        Desculpe, esta página não está disponível no momento. <br>
        Por favor, tente novamente mais tarde.
      </p>
      <p>
        Se você acredita que isso é um erro, entre em contato com o suporte. <br>
        <?php
        echo '<a href="mailto:' . $mail . '">' . $mail . '</a>';
        
        ?>
      </p>
      <p>Agradecemos a sua paciência!</p>
      <p>
        <a href="/">Voltar para a página inicial</a>
      </p>
    </div>
  </body>
</html>
