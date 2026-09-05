<?php

namespace App\Shared;

class MessageCodeManager
{
    private static $message = array();

    public static function getMessage(int $code, string $lng, object $msg)
    {
        /**
         * code between [600 - 999]
         */
        self::$message['Code'] = $code;
        self::$message['Type'] = $msg::TYPE_MESSAGE_INFORMATION;
        self::$message['Title'] = '';
        self::$message['Description'] = '';

        /**
         * TYPE_MESSAGE_SUCCESS
         */
        // switch ($code) {
        //     case 99:
        //     self::$message['Code'] = $code;
        //     self::$message['Type'] = $msg::TYPE_MESSAGE_SUCCESS;
        //     self::$message['Title'] = 'SUCCESS';
        //     switch ($lng) {
        //         case 'en':
        //             self::$message['Description'] = '';
        //             break;

        //         default:
        //             self::$message['Description'] = '';
        //             break;
        //     }
        //     break;
        // }

        /**
         * TYPE_MESSAGE_INFORMATION
         */
        switch ($code) {
            case 611:
                self::$message['Code'] = $code;
                self::$message['Type'] = $msg::TYPE_MESSAGE_INFORMATION;
                self::$message['Title'] = '';
                switch ($lng) {
                    case 'en':
                        self::$message['Description'] = 'Enter the token sent to your email.';
                        break;

                    default:
                        self::$message['Description'] = 'Informe o token enviado para seu e-mail.';
                        break;
                }
                break;
        }

        /**
         * TYPE_MESSAGE_WARNING
         */
        switch ($code) {
            case 600:
                self::$message['Code'] = $code;
                self::$message['Type'] = $msg::TYPE_MESSAGE_WARNING;
                self::$message['Title'] = 'WARNING';
                switch ($lng) {
                    case 'en':
                        self::$message['Description'] = 'Email address and password are required.';
                        break;

                    default:
                        self::$message['Description'] = 'Endereço E-Mail e senha são obrigatórios.';
                        break;
                }
                break;
            case 601:
                self::$message['Code'] = $code;
                self::$message['Type'] = $msg::TYPE_MESSAGE_WARNING;
                self::$message['Title'] = 'WARNING';
                switch ($lng) {
                    case 'en':
                        self::$message['Description'] = 'Incorrect username and password or invalid account.';
                        break;

                    default:
                        self::$message['Description'] = 'Usuário e senha incorreto ou conta inválida.';
                        break;
                }
                break;
            case 603:
                self::$message['Code'] = $code;
                self::$message['Type'] = $msg::TYPE_MESSAGE_WARNING;
                self::$message['Title'] = '';
                switch ($lng) {
                    case 'en':
                        self::$message['Description'] = 'Enter your name.';
                        break;

                    default:
                        self::$message['Description'] = 'Informe o seu nome.';
                        break;
                }
                break;
            case 604:
                self::$message['Code'] = $code;
                self::$message['Type'] = $msg::TYPE_MESSAGE_WARNING;
                self::$message['Title'] = '';
                switch ($lng) {
                    case 'en':
                        self::$message['Description'] = 'Enter all the data in the form.';
                        break;

                    default:
                        self::$message['Description'] = 'Digite todos os dados do formulário.';
                        break;
                }
                break;
            case 605:
                self::$message['Code'] = $code;
                self::$message['Type'] = $msg::TYPE_MESSAGE_WARNING;
                self::$message['Title'] = '';
                switch ($lng) {
                    case 'en':
                        self::$message['Description'] = 'Different registration and confirmation passwords.';
                        break;

                    default:
                        self::$message['Description'] = 'Diferentes senhas de registro e confirmação.';
                        break;
                }
                break;
            case 606:
                self::$message['Code'] = $code;
                self::$message['Type'] = $msg::TYPE_MESSAGE_WARNING;
                self::$message['Title'] = '';
                switch ($lng) {
                    case 'en':
                        self::$message['Description'] = 'Email account already in use. Cannot be registered.';
                        break;

                    default:
                        self::$message['Description'] = 'Conta de e-mail já em está uso. Não pode ser cadastrado.';
                        break;
                }
                break;
            case 607:
                self::$message['Code'] = $code;
                self::$message['Type'] = $msg::TYPE_MESSAGE_WARNING;
                self::$message['Title'] = '';
                switch ($lng) {
                    case 'en':
                        self::$message['Description'] = 'Enter the e-mail.';
                        break;

                    default:
                        self::$message['Description'] = 'Digite o e-mail.';
                        break;
                }
                break;
            case 608:
                self::$message['Code'] = $code;
                self::$message['Type'] = $msg::TYPE_MESSAGE_WARNING;
                self::$message['Title'] = '';
                switch ($lng) {
                    case 'en':
                        self::$message['Description'] = 'Account not found.';
                        break;

                    default:
                        self::$message['Description'] = 'Conta não localizada.';
                        break;
                }
                break;
            case 610:
                self::$message['Code'] = $code;
                self::$message['Type'] = $msg::TYPE_MESSAGE_WARNING;
                self::$message['Title'] = '';
                switch ($lng) {
                    case 'en':
                        self::$message['Description'] = 'Invalid token.';
                        break;

                    default:
                        self::$message['Description'] = 'Token inválido.';
                        break;
                }
                break;
            case 614:
                self::$message['Code'] = $code;
                self::$message['Type'] = $msg::TYPE_MESSAGE_WARNING;
                self::$message['Title'] = '';
                switch ($lng) {
                    case 'en':
                        self::$message['Description'] = 'The email is not valid.';
                        break;

                    default:
                        self::$message['Description'] = 'O e-mail não é válido.';
                        break;
                }
                break;
            case 615:
                self::$message['Code'] = $code;
                self::$message['Type'] = $msg::TYPE_MESSAGE_WARNING;
                self::$message['Title'] = '';
                switch ($lng) {
                    case 'en':
                        self::$message['Description'] = 'This account is blocked.';
                        break;

                    default:
                        self::$message['Description'] = 'Esta conta está bloqueada.';
                        break;
                }
                break;
            case 616:
                self::$message['Code'] = $code;
                self::$message['Type'] = $msg::TYPE_MESSAGE_WARNING;
                self::$message['Title'] = '';
                switch ($lng) {
                    case 'en':
                        self::$message['Description'] = 'This repository is blocked.';
                        break;

                    default:
                        self::$message['Description'] = 'Este repositório está bloqueado.';
                        break;
                }
                break;
            case 625:
                self::$message['Code'] = $code;
                self::$message['Type'] = $msg::TYPE_MESSAGE_WARNING;
                self::$message['Title'] = '';
                self::$message['Description'] = $lng === 'en'
                    ? 'Confirm your email to restore access to this account.'
                    : 'Confirme seu e-mail para restabelecer o acesso a esta conta.';
                break;
            case 626:
                self::$message['Code'] = $code;
                self::$message['Type'] = $msg::TYPE_MESSAGE_WARNING;
                self::$message['Title'] = '';
                self::$message['Description'] = $lng === 'en'
                    ? 'Password recovery is available only after email confirmation.'
                    : 'A recuperacao de senha esta disponivel somente apos a confirmacao do e-mail.';
                break;
            case 617:
                self::$message['Code'] = $code;
                self::$message['Type'] = $msg::TYPE_MESSAGE_WARNING;
                self::$message['Title'] = '';
                switch ($lng) {
                    case 'en':
                        self::$message['Description'] = 'This user repository is blocked.';
                        break;

                    default:
                        self::$message['Description'] = 'Este repositório de usuário está bloqueado.';
                        break;
                }
                break;
            case 618:
                self::$message['Code'] = $code;
                self::$message['Type'] = $msg::TYPE_MESSAGE_WARNING;
                self::$message['Title'] = '';
                switch ($lng) {
                    case 'en':
                        self::$message['Description'] = 'Access type profile is blocked.';
                        break;

                    default:
                        self::$message['Description'] = 'O perfil do tipo de acesso está bloqueado.';
                        break;
                }
                break;
        }

        /**
         * TYPE_MESSAGE_ERROR
         */
        switch ($code) {
            case 602:
                self::$message['Code'] = $code;
                self::$message['Type'] = $msg::TYPE_MESSAGE_ERROR;
                self::$message['Title'] = '';
                switch ($lng) {
                    case 'en':
                        self::$message['Description'] = 'Error loading users accounts.';
                        break;

                    default:
                        self::$message['Description'] = 'Erro ao carregar contas de usuários.';
                        break;
                }
                break;
            case 609:
                self::$message['Code'] = $code;
                self::$message['Type'] = $msg::TYPE_MESSAGE_ERROR;
                self::$message['Title'] = '';
                switch ($lng) {
                    case 'en':
                        self::$message['Description'] = 'Error writing user token.';
                        break;

                    default:
                        self::$message['Description'] = 'Erro ao gravar token de usuário.';
                        break;
                }
                break;
            case 612:
                self::$message['Code'] = $code;
                self::$message['Type'] = $msg::TYPE_MESSAGE_ERROR;
                self::$message['Title'] = '';
                switch ($lng) {
                    case 'en':
                        self::$message['Description'] = 'Email service error. Please try again later.';
                        break;

                    default:
                        self::$message['Description'] = 'Erro no serviço de e-mail. Tente novamente mais tarde.';
                        break;
                }
                break;
            case 613:
                self::$message['Code'] = $code;
                self::$message['Type'] = $msg::TYPE_MESSAGE_ERROR;
                self::$message['Title'] = '';
                switch ($lng) {
                    case 'en':
                        self::$message['Description'] = 'Error loading form.';
                        break;

                    default:
                        self::$message['Description'] = 'Erro ao carregar formulário.';
                        break;
                }
                break;
            case 619:
                self::$message['Code'] = $code;
                self::$message['Type'] = $msg::TYPE_MESSAGE_ERROR;
                self::$message['Title'] = '';
                switch ($lng) {
                    case 'en':
                        self::$message['Description'] = 'Invalid repository.';
                        break;

                    default:
                        self::$message['Description'] = 'Repositório inválido.';
                        break;
                }
                break;
            case 620:
                self::$message['Code'] = $code;
                self::$message['Type'] = $msg::TYPE_MESSAGE_ERROR;
                self::$message['Title'] = '';
                switch ($lng) {
                    case 'en':
                        self::$message['Description'] = 'Error creating user session.';
                        break;

                    default:
                        self::$message['Description'] = 'Erro ao criar sessão de usuário.';
                        break;
                }
                break;
            case 621:
                self::$message['Code'] = $code;
                self::$message['Type'] = $msg::TYPE_MESSAGE_ERROR;
                self::$message['Title'] = '';
                switch ($lng) {
                    case 'en':
                        self::$message['Description'] = 'Terminal with blocked access';
                        break;

                    default:
                        self::$message['Description'] = 'Terminal com acesso bloqueado.';
                        break;
                }
                break;
            case 622:
                self::$message['Code'] = $code;
                self::$message['Type'] = $msg::TYPE_MESSAGE_ERROR;
                self::$message['Title'] = '';
                switch ($lng) {
                    case 'en':
                        self::$message['Description'] = 'Denied License to use this terminal';
                        break;

                    default:
                        self::$message['Description'] = 'Negado Licença de uso neste terminal.';
                        break;
                }
                break;

            case 623:
                self::$message['Code'] = $code;
                self::$message['Type'] = $msg::TYPE_MESSAGE_ERROR;
                self::$message['Title'] = '';
                switch ($lng) {
                    case 'en':
                        self::$message['Description'] = 'Could not create repository directory';
                        break;

                    default:
                        self::$message['Description'] = 'Não foi possível criar o diretório do repositório.';
                        break;
                }
                break;

            case 624:
                self::$message['Code'] = $code;
                self::$message['Type'] = $msg::TYPE_MESSAGE_ERROR;
                self::$message['Title'] = '';
                switch ($lng) {
                    case 'en':
                        self::$message['Description'] = 'Token with blocked access';
                        break;

                    default:
                        self::$message['Description'] = 'Token com acesso bloqueado.';
                        break;
                }
                break;
        }

        return self::$message;
    }
}
