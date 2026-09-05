<?php

namespace App\Core;

class AuthSession
{
    private static $ACCEPTED_ELEMENTS = [
        'LANGUAGE',
        'USR_LOGGED',
        'USR_ID',
        'RPS_ID',
        'RPS_DSC',
        'SSW_ID',
        'PROFILE',
        'PRESENTATION',
        'HOME_PAGE',
        'EXPIRES',
        'REPOSITORIES',
        'AUTHORIZATION',
        'REGISTER_PENDING',
    ];

    private static function load($id = null)
    {
        if (isset($_SESSION['DATA_AUTH']) && !empty($_SESSION['DATA_AUTH'])) {
            return $_SESSION['DATA_AUTH'];
        }

        $data = array(
            'USR_ID' => 'anonymous',
            'USR_LOGGED' => 'anonymous',
            'LANGUAGE' => 'pt',
            'HOME_PAGE' => '/Home',
        );

        if (!empty($id)) {
            $data['SSW_ID'] = $id;
        }

        $_SESSION['DATA_AUTH'] = $data;

        return $data;
    }

    public static function create(bool $regenerate = false)
    {
        session_set_cookie_params([
            'lifetime' => 0, // Sessão expira quando o navegador é fechado
            'path' => '/', // A sessão está disponível em todo o domínio
            'domain' => '', // Domínio padrão
            'secure' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'), // Não usar HTTPS por padrão (ajuste conforme necessário)
            'httponly' => true, // Impede acesso via JavaScript
            'samesite' => 'Lax', // Protege contra CSRF
        ]);
        session_start();

        if ($regenerate) {
            // Regenera o ID da sessão, crucial para segurança e para obter um novo ID.
            // O parâmetro true deleta o arquivo da sessão antiga.
            session_regenerate_id(true);

            // Limpa completamente o array $_SESSION para um estado inicial.
            $_SESSION = [];
        }

        self::load(session_id());
    }

    public static function logout()
    {
        // Inicia a sessão se ainda não estiver para poder manipulá-la corretamente.
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // 1. Limpa todas as variáveis de sessão.
        $_SESSION = [];

        // 2. Se estiver usando cookies de sessão, invalida o cookie de sessão.
        //    Isso ajuda a garantir que o navegador não envie o cookie antigo.
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        // 3. Finalmente, destrói a sessão no servidor.
        session_destroy();

        // 4. Cria uma nova sessão completamente limpa.
        //    A chamada self::create() agora irá:
        //    - Chamar session_start() (que iniciará uma nova sessão pois a antiga foi destruída e o cookie invalidado)
        //    - Chamar session_regenerate_id(true) garantindo um ID novo.
        //    - Limpar $_SESSION.
        //    - Carregar os dados padrão com o novo session_id().
        self::create(true);
    }

    public static function get()
    {
        $session = self::load();

        return $session;
    }

    public static function set($element, $value)
    {
        $session = self::load();

        if (in_array($element, self::$ACCEPTED_ELEMENTS)) {
            $session[$element] = $value;
        }

        $_SESSION['DATA_AUTH'] = $session;
    }
}
