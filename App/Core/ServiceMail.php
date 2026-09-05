<?php

namespace App\Core;

use App\Core\Config;
use PHPMailer\PHPMailer\PHPMailer;

class ServiceMail
{
    use \App\Traits\LogToFile;

    private $mail;
    private $dataMail;
    public $message = '';
    private static $FROM_NAME = null;
    private static $FROM_MAIL = null;

    public function __construct() {}

    public function sendMailRegister($name, $email)
    {
        try {
            return $this->sendMailRegisterInternal($name, $email);
        } catch (\Throwable $exception) {
            $this->message = 'A mensagem não pode ser enviada.';
            self::setMailLog('error', [
                'event' => 'mail_register_exception',
                'mail_type' => 'register',
                'status' => 'exception',
                'mode' => Config::$MAIL_SERVICE === true ? 'smtp' : 'disabled',
                'to' => self::maskEmail($email),
                'to_hash' => self::hashEmail($email),
                'exception_class' => get_class($exception),
                'exception_message' => $exception->getMessage(),
                'exception_file' => $exception->getFile(),
                'exception_line' => $exception->getLine(),
            ]);

            return false;
        }
    }

    public function sendMailRecovery($name, $email, $token)
    {
        try {
            return $this->sendMailRecoveryInternal($name, $email, $token);
        } catch (\Throwable $exception) {
            $this->message = 'A mensagem não pode ser enviada.';
            self::setMailLog('error', [
                'event' => 'mail_recovery_exception',
                'mail_type' => 'recovery',
                'status' => 'exception',
                'mode' => Config::$MAIL_SERVICE === true ? 'smtp' : 'disabled',
                'to' => self::maskEmail($email),
                'to_hash' => self::hashEmail($email),
                'exception_class' => get_class($exception),
                'exception_message' => $exception->getMessage(),
                'exception_file' => $exception->getFile(),
                'exception_line' => $exception->getLine(),
            ]);

            return false;
        }
    }

    public function sendMailActivation(string $name, string $email, string $activationUrl, string $expiresAt): bool
    {
        try {
            return $this->sendMailActivationInternal($name, $email, $activationUrl, $expiresAt);
        } catch (\Throwable $exception) {
            $this->message = 'A mensagem não pode ser enviada.';
            self::setMailLog('error', [
                'event' => 'mail_activation_exception',
                'mail_type' => 'activation',
                'status' => 'exception',
                'mode' => Config::$MAIL_SERVICE === true ? 'smtp' : 'disabled',
                'to' => self::maskEmail($email),
                'to_hash' => self::hashEmail($email),
                'exception_class' => get_class($exception),
                'exception_message' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    public function sendMailRegisterVerification(string $name, string $email, string $token, string $expiresAt): bool
    {
        try {
            return $this->sendMailRegisterVerificationInternal($name, $email, $token, $expiresAt);
        } catch (\Throwable $exception) {
            $this->message = 'A mensagem não pode ser enviada.';
            self::setMailLog('error', [
                'event' => 'mail_register_verification_exception',
                'mail_type' => 'register_verification',
                'status' => 'exception',
                'mode' => Config::$MAIL_SERVICE === true ? 'smtp' : 'disabled',
                'to' => self::maskEmail($email),
                'to_hash' => self::hashEmail($email),
                'exception_class' => get_class($exception),
                'exception_message' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    private function sendMailRegisterVerificationInternal(string $name, string $email, string $token, string $expiresAt): bool
    {
        $path = Config::getPathMailRegisterVerification();
        if (!is_file($path)) {
            $this->message = 'Template não localizado.';
            self::setMailLog('error', [
                'event' => 'mail_template_missing',
                'mail_type' => 'register_verification',
                'status' => 'error',
                'to' => self::maskEmail($email),
                'to_hash' => self::hashEmail($email),
                'template' => $path,
            ]);
            return false;
        }

        $content = file_get_contents($path);
        if ($content === false) {
            $this->message = 'Template não pode ser lido.';
            return false;
        }

        $this->mail = new PHPMailer();
        $this->setConfigMail();
        $appName = self::getAppName();
        $supportEmail = self::getSupportEmail();
        $displayExpiresAt = self::formatMailDateTime($expiresAt);

        if (!$this->mail->setFrom(self::$FROM_MAIL, self::$FROM_NAME)
            || !$this->mail->addAddress($email, $name)) {
            $this->message = 'Remetente ou destinatário de e-mail inválido.';
            self::setMailLog('error', [
                'event' => 'mail_register_verification_invalid_address',
                'mail_type' => 'register_verification',
                'status' => 'error',
                'to' => self::maskEmail($email),
                'to_hash' => self::hashEmail($email),
                'mailer_error' => $this->mail->ErrorInfo,
            ]);
            return false;
        }

        $this->mail->Subject = 'Código de verificação - ' . $appName;
        $this->mail->isHTML(true);
        $this->mail->Body = self::renderTemplate($content, [
            'app_name' => self::escapeHtml($appName),
            'recipient_name' => self::escapeHtml($name),
            'verification_token' => self::escapeHtml($token),
            'expires_at' => self::escapeHtml($displayExpiresAt),
            'support_email' => self::escapeHtml($supportEmail),
            'current_year' => date('Y'),
        ]);
        $this->mail->AltBody = self::getRegisterVerificationAltBody($name, $appName, $token, $displayExpiresAt, $supportEmail);

        $mode = Config::$MAIL_SERVICE === true ? 'smtp' : 'disabled';
        $sent = Config::$MAIL_SERVICE !== true || $this->mail->send();
        $this->message = $sent ? 'Mensagem enviada com sucesso.' : 'A mensagem não pode ser enviada.';
        self::setMailLog($sent ? 'sent' : 'error', [
            'event' => $sent ? 'mail_register_verification_sent' : 'mail_register_verification_error',
            'mail_type' => 'register_verification',
            'status' => $sent ? (Config::$MAIL_SERVICE === true ? 'sent' : 'simulated') : 'error',
            'mode' => $mode,
            'to' => self::maskEmail($email),
            'to_hash' => self::hashEmail($email),
            'from' => self::maskEmail(self::$FROM_MAIL),
            'subject' => $this->mail->Subject,
            'template' => $path,
            'expires_at' => $expiresAt,
            'mailer_error' => $sent ? '' : $this->mail->ErrorInfo,
            'message_id' => $sent && Config::$MAIL_SERVICE === true ? $this->mail->getLastMessageID() : '',
        ]);

        return $sent;
    }

    private function sendMailActivationInternal(string $name, string $email, string $activationUrl, string $expiresAt): bool
    {
        $path = Config::getPathMailActivation();
        if (!is_file($path)) {
            $this->message = 'Template não localizado.';
            self::setMailLog('error', [
                'event' => 'mail_template_missing',
                'mail_type' => 'activation',
                'status' => 'error',
                'to' => self::maskEmail($email),
                'to_hash' => self::hashEmail($email),
                'template' => $path,
            ]);
            return false;
        }

        $content = file_get_contents($path);
        if ($content === false) {
            $this->message = 'Template não pode ser lido.';
            return false;
        }

        $this->mail = new PHPMailer();
        $this->setConfigMail();
        $appName = self::getAppName();
        $supportEmail = self::getSupportEmail();
        $displayExpiresAt = self::formatMailDateTime($expiresAt);

        if (!$this->mail->setFrom(self::$FROM_MAIL, self::$FROM_NAME)
            || !$this->mail->addAddress($email, $name)) {
            $this->message = 'Remetente ou destinatário de e-mail inválido.';
            self::setMailLog('error', [
                'event' => 'mail_activation_invalid_address',
                'mail_type' => 'activation',
                'status' => 'error',
                'to' => self::maskEmail($email),
                'to_hash' => self::hashEmail($email),
                'mailer_error' => $this->mail->ErrorInfo,
            ]);
            return false;
        }

        $this->mail->Subject = 'Ative sua conta - ' . $appName;
        $this->mail->isHTML(true);
        $this->mail->Body = self::renderTemplate($content, [
            'app_name' => self::escapeHtml($appName),
            'recipient_name' => self::escapeHtml($name),
            'activation_url' => self::escapeHtml($activationUrl),
            'activation_url_text' => self::escapeHtml($activationUrl),
            'expires_at' => self::escapeHtml($displayExpiresAt),
            'support_email' => self::escapeHtml($supportEmail),
            'current_year' => date('Y'),
        ]);
        $this->mail->AltBody = self::getActivationAltBody($name, $appName, $activationUrl, $displayExpiresAt, $supportEmail);

        $mode = Config::$MAIL_SERVICE === true ? 'smtp' : 'disabled';
        $sent = Config::$MAIL_SERVICE !== true || $this->mail->send();
        $this->message = $sent ? 'Mensagem enviada com sucesso.' : 'A mensagem não pode ser enviada.';
        self::setMailLog($sent ? 'sent' : 'error', [
            'event' => $sent ? 'mail_activation_sent' : 'mail_activation_error',
            'mail_type' => 'activation',
            'status' => $sent ? (Config::$MAIL_SERVICE === true ? 'sent' : 'simulated') : 'error',
            'mode' => $mode,
            'to' => self::maskEmail($email),
            'to_hash' => self::hashEmail($email),
            'from' => self::maskEmail(self::$FROM_MAIL),
            'subject' => $this->mail->Subject,
            'template' => $path,
            'expires_at' => $expiresAt,
            'mailer_error' => $sent ? '' : $this->mail->ErrorInfo,
            'message_id' => $sent && Config::$MAIL_SERVICE === true ? $this->mail->getLastMessageID() : '',
        ]);

        return $sent;
    }

    private function sendMailRegisterInternal($name, $email): bool
    {
        $path = Config::getPathMailRegister();
        $result = false;

        if (is_file($path)) {
            $data_content = file_get_contents($path);

            if ($data_content === false) {
                $this->message = 'Template nao pode ser lido. ';
                self::setMailLog('error', [
                    'event' => 'mail_template_read_error',
                    'mail_type' => 'register',
                    'status' => 'error',
                    'mode' => Config::$MAIL_SERVICE === true ? 'smtp' : 'disabled',
                    'to' => self::maskEmail($email),
                    'to_hash' => self::hashEmail($email),
                    'template' => $path,
                    'message' => trim($this->message),
                ]);

                return false;
            }

            $this->mail = new PHPMailer();
            $this->setConfigMail();

            $appName = self::getAppName();
            $supportEmail = self::getSupportEmail();
            $appUrl = self::getAppUrl();

            if (! $this->mail->setFrom(self::$FROM_MAIL, self::$FROM_NAME)) {
                $this->message = 'Remetente de e-mail invalido.';
                self::setMailLog('error', [
                    'event' => 'mail_register_invalid_from',
                    'mail_type' => 'register',
                    'status' => 'error',
                    'mode' => Config::$MAIL_SERVICE === true ? 'smtp' : 'disabled',
                    'from' => self::maskEmail(self::$FROM_MAIL),
                    'mailer_error' => $this->mail->ErrorInfo,
                ]);

                return false;
            }

            if (! $this->mail->addAddress($email, $name)) {
                $this->message = 'Destinatario de e-mail invalido.';
                self::setMailLog('error', [
                    'event' => 'mail_register_invalid_recipient',
                    'mail_type' => 'register',
                    'status' => 'error',
                    'mode' => Config::$MAIL_SERVICE === true ? 'smtp' : 'disabled',
                    'to' => self::maskEmail($email),
                    'to_hash' => self::hashEmail($email),
                    'mailer_error' => $this->mail->ErrorInfo,
                ]);

                return false;
            }

            $this->mail->Subject = 'Registro da Conta - ' . $appName;
            $this->mail->isHTML(TRUE);
            $data_content = self::renderTemplate($data_content, [
                'app_name' => self::escapeHtml($appName),
                'app_url' => self::escapeHtml($appUrl),
                'app_url_text' => self::escapeHtml($appUrl),
                'recipient_name' => self::escapeHtml($name),
                'support_email' => self::escapeHtml($supportEmail),
                'current_year' => date('Y'),
            ]);
            $this->mail->Body = $data_content;
            $this->mail->AltBody = self::getRegisterAltBody($name, $appName, $appUrl, $supportEmail);

            if (Config::$MAIL_SERVICE === true) {
                if ($this->mail->send()) {
                    $this->message = 'Mensagem enviada com sucesso.';
                    self::setMailLog('sent', [
                        'event' => 'mail_register_sent',
                        'mail_type' => 'register',
                        'status' => 'sent',
                        'mode' => 'smtp',
                        'to' => self::maskEmail($email),
                        'to_hash' => self::hashEmail($email),
                        'from' => self::maskEmail(self::$FROM_MAIL),
                        'subject' => $this->mail->Subject,
                        'template' => $path,
                        'app_name' => $appName,
                        'app_url' => $appUrl,
                        'support_email' => self::maskEmail($supportEmail),
                        'smtp_host' => Config::$MAIL['MAIL_HOST'],
                        'smtp_port' => Config::$MAIL['MAIL_PORT'],
                        'smtp_encryption' => Config::$MAIL['MAIL_ENCRYPTION'],
                        'message_id' => $this->mail->getLastMessageID(),
                    ]);
                    $result = true;
                } else {
                    $this->message = 'A mensagem nao pode ser enviada. Mailer Error: ' . $this->mail->ErrorInfo;
                    self::setMailLog('error', [
                        'event' => 'mail_register_error',
                        'mail_type' => 'register',
                        'status' => 'error',
                        'mode' => 'smtp',
                        'to' => self::maskEmail($email),
                        'to_hash' => self::hashEmail($email),
                        'from' => self::maskEmail(self::$FROM_MAIL),
                        'subject' => $this->mail->Subject,
                        'template' => $path,
                        'app_name' => $appName,
                        'app_url' => $appUrl,
                        'support_email' => self::maskEmail($supportEmail),
                        'smtp_host' => Config::$MAIL['MAIL_HOST'],
                        'smtp_port' => Config::$MAIL['MAIL_PORT'],
                        'smtp_encryption' => Config::$MAIL['MAIL_ENCRYPTION'],
                        'mailer_error' => $this->mail->ErrorInfo,
                    ]);
                }
            } else {
                $this->message = 'Mensagem (simulada) enviada com sucesso.';
                self::setMailLog('info', [
                    'event' => 'mail_register_simulated',
                    'mail_type' => 'register',
                    'status' => 'simulated',
                    'mode' => 'disabled',
                    'to' => self::maskEmail($email),
                    'to_hash' => self::hashEmail($email),
                    'from' => self::maskEmail(self::$FROM_MAIL),
                    'subject' => $this->mail->Subject,
                    'template' => $path,
                    'app_name' => $appName,
                    'app_url' => $appUrl,
                    'support_email' => self::maskEmail($supportEmail),
                ]);
                $result = true;
            }
        } else {
            $this->message = 'Template nao localizado. ';
            self::setMailLog('error', [
                'event' => 'mail_template_missing',
                'mail_type' => 'register',
                'status' => 'error',
                'mode' => Config::$MAIL_SERVICE === true ? 'smtp' : 'disabled',
                'to' => self::maskEmail($email),
                'to_hash' => self::hashEmail($email),
                'template' => $path,
                'message' => trim($this->message),
            ]);
        }

        return $result;
    }

    private function sendMailRecoveryInternal($name, $email, $token): bool
    {
        $path = Config::getPathMailRecovery();
        $result = false;

        if (is_file($path)) {
            $data_content = file_get_contents($path);

            if ($data_content === false) {
                $this->message = 'Template nao pode ser lido. ';
                self::setMailLog('error', [
                    'event' => 'mail_template_read_error',
                    'mail_type' => 'recovery',
                    'status' => 'error',
                    'mode' => Config::$MAIL_SERVICE === true ? 'smtp' : 'disabled',
                    'to' => self::maskEmail($email),
                    'to_hash' => self::hashEmail($email),
                    'template' => $path,
                    'message' => trim($this->message),
                ]);

                return false;
            }

            $this->mail = new PHPMailer();
            $this->setConfigMail();

            $appName = self::getAppName();
            $supportEmail = self::getSupportEmail();

            if (! $this->mail->setFrom(self::$FROM_MAIL, self::$FROM_NAME)) {
                $this->message = 'Remetente de e-mail invalido.';
                self::setMailLog('error', [
                    'event' => 'mail_recovery_invalid_from',
                    'mail_type' => 'recovery',
                    'status' => 'error',
                    'mode' => Config::$MAIL_SERVICE === true ? 'smtp' : 'disabled',
                    'from' => self::maskEmail(self::$FROM_MAIL),
                    'mailer_error' => $this->mail->ErrorInfo,
                ]);

                return false;
            }

            if (! $this->mail->addAddress($email, $name)) {
                $this->message = 'Destinatario de e-mail invalido.';
                self::setMailLog('error', [
                    'event' => 'mail_recovery_invalid_recipient',
                    'mail_type' => 'recovery',
                    'status' => 'error',
                    'mode' => Config::$MAIL_SERVICE === true ? 'smtp' : 'disabled',
                    'to' => self::maskEmail($email),
                    'to_hash' => self::hashEmail($email),
                    'mailer_error' => $this->mail->ErrorInfo,
                ]);

                return false;
            }

            $this->mail->Subject = 'Recuperar Conta - ' . $appName;
            $this->mail->isHTML(TRUE);
            $data_content = self::renderTemplate($data_content, [
                'app_name' => self::escapeHtml($appName),
                'recipient_name' => self::escapeHtml($name),
                'access_token' => self::escapeHtml($token),
                'support_email' => self::escapeHtml($supportEmail),
                'current_year' => date('Y'),
            ]);
            $this->mail->Body = $data_content;
            $this->mail->AltBody = self::getRecoveryAltBody($name, $appName, $token, $supportEmail);

            if (Config::$MAIL_SERVICE === true) {
                if ($this->mail->send()) {
                    $this->message = 'Mensagem enviada com sucesso.';
                    self::setMailLog('sent', [
                        'event' => 'mail_recovery_sent',
                        'mail_type' => 'recovery',
                        'status' => 'sent',
                        'mode' => 'smtp',
                        'to' => self::maskEmail($email),
                        'to_hash' => self::hashEmail($email),
                        'from' => self::maskEmail(self::$FROM_MAIL),
                        'subject' => $this->mail->Subject,
                        'template' => $path,
                        'app_name' => $appName,
                        'support_email' => self::maskEmail($supportEmail),
                        'smtp_host' => Config::$MAIL['MAIL_HOST'],
                        'smtp_port' => Config::$MAIL['MAIL_PORT'],
                        'smtp_encryption' => Config::$MAIL['MAIL_ENCRYPTION'],
                        'message_id' => $this->mail->getLastMessageID(),
                    ]);
                    $result = true;
                } else {
                    $this->message = 'A mensagem nao pode ser enviada. Mailer Error: ' . $this->mail->ErrorInfo;
                    self::setMailLog('error', [
                        'event' => 'mail_recovery_error',
                        'mail_type' => 'recovery',
                        'status' => 'error',
                        'mode' => 'smtp',
                        'to' => self::maskEmail($email),
                        'to_hash' => self::hashEmail($email),
                        'from' => self::maskEmail(self::$FROM_MAIL),
                        'subject' => $this->mail->Subject,
                        'template' => $path,
                        'app_name' => $appName,
                        'support_email' => self::maskEmail($supportEmail),
                        'smtp_host' => Config::$MAIL['MAIL_HOST'],
                        'smtp_port' => Config::$MAIL['MAIL_PORT'],
                        'smtp_encryption' => Config::$MAIL['MAIL_ENCRYPTION'],
                        'mailer_error' => $this->mail->ErrorInfo,
                    ]);
                }
            } else {
                $this->message = 'Mensagem (simulada) enviada com sucesso.';
                self::setMailLog('info', [
                    'event' => 'mail_recovery_simulated',
                    'mail_type' => 'recovery',
                    'status' => 'simulated',
                    'mode' => 'disabled',
                    'to' => self::maskEmail($email),
                    'to_hash' => self::hashEmail($email),
                    'from' => self::maskEmail(self::$FROM_MAIL),
                    'subject' => $this->mail->Subject,
                    'template' => $path,
                    'app_name' => $appName,
                    'support_email' => self::maskEmail($supportEmail),
                ]);
                $result = true;
            }
        } else {
            $this->message = 'Template nao localizado. ';
            self::setMailLog('error', [
                'event' => 'mail_template_missing',
                'mail_type' => 'recovery',
                'status' => 'error',
                'mode' => Config::$MAIL_SERVICE === true ? 'smtp' : 'disabled',
                'to' => self::maskEmail($email),
                'to_hash' => self::hashEmail($email),
                'template' => $path,
                'message' => trim($this->message),
            ]);
        }

        return $result;
    }

    private function setConfigMail($account = 'default')
    {
        $this->mail->CharSet = PHPMailer::CHARSET_UTF8;

        // configurar um SMTP
        switch ($account) {
            case 'support':
                // $this->mail->isSMTP();
                // $this->mail->Host = Config::$MAIL_SUPPORT['MAIL_HOST'];
                // $this->mail->SMTPAuth = Config::$MAIL_SUPPORT['MAIL_AUTH'];
                // $this->mail->Username = Config::$MAIL_SUPPORT['MAIL_USERNAME'];
                // $this->mail->Password = Config::$MAIL_SUPPORT['MAIL_PASSWORD'];
                // $this->mail->SMTPSecure = (Config::$MAIL_SUPPORT['MAIL_ENCRYPTION'] == 'ssl' ? $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS : $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS);
                // $this->mail->Port = Config::$MAIL_SUPPORT['MAIL_PORT'];
                // self::$FROM_NAME = Config::$MAIL_SUPPORT['MAIL_FROM_NAME'];
                // self::$FROM_MAIL = Config::$MAIL_SUPPORT['MAIL_FROM_ADDRESS'];
                break;

            default:
                // 'default' //
                $this->mail->isSMTP();
                $this->mail->Host = Config::$MAIL['MAIL_HOST'];
                $this->mail->SMTPAuth = Config::$MAIL['MAIL_AUTH'];
                $this->mail->Username = Config::$MAIL['MAIL_USERNAME'];
                $this->mail->Password = Config::$MAIL['MAIL_PASSWORD'];
                $this->mail->SMTPSecure = (Config::$MAIL['MAIL_ENCRYPTION'] == 'ssl' ? $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS : $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS);
                $this->mail->Port = Config::$MAIL['MAIL_PORT'];
                self::$FROM_NAME = Config::$MAIL['MAIL_FROM_NAME'];
                self::$FROM_MAIL = Config::$MAIL['MAIL_FROM_ADDRESS'];
                break;
        }
    }

    private static function renderTemplate(string $template, array $values): string
    {
        $replace = [];

        foreach ($values as $key => $value) {
            $replace['{{' . $key . '}}'] = (string) $value;
        }

        return strtr($template, $replace);
    }

    private static function escapeHtml(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    private static function formatMailDateTime(mixed $value): string
    {
        $date = trim((string) $value);
        if ($date === '') {
            return '';
        }

        foreach (['Y-m-d H:i:s' => 'd/m/Y H:i:s', 'Y-m-d' => 'd/m/Y'] as $input => $output) {
            $parsed = \DateTimeImmutable::createFromFormat('!' . $input, $date);
            if ($parsed !== false && $parsed->format($input) === $date) {
                return $parsed->format($output);
            }
        }

        try {
            $parsed = new \DateTimeImmutable($date);
            return str_contains($date, ':') ? $parsed->format('d/m/Y H:i:s') : $parsed->format('d/m/Y');
        } catch (\Throwable) {
            return $date;
        }
    }

    private static function getAppName(): string
    {
        $appName = trim((string) Config::$APP_NAME);

        if ($appName === '') {
            $appName = trim((string) self::$FROM_NAME);
        }

        return $appName !== '' ? $appName : 'Manager';
    }

    private static function getAppUrl(): string
    {
        return trim((string) Config::$APP_URL);
    }

    private static function getSupportEmail(): string
    {
        $envs = Config::getEnvs();
        $supportEmail = trim((string) ($envs['CONTACT_SUPPORT'] ?? ''));

        if ($supportEmail === '') {
            $supportEmail = trim((string) ($envs['CONTACT_EMAIL'] ?? ''));
        }

        if ($supportEmail === '') {
            $supportEmail = trim((string) Config::$MAIL['MAIL_FROM_ADDRESS']);
        }

        return $supportEmail;
    }

    private static function getRegisterAltBody(string $name, string $appName, string $appUrl, string $supportEmail): string
    {
        return implode(PHP_EOL, [
            "Olá, {$name}!",
            '',
            "Seja bem-vindo ao {$appName}. Sua conta foi registrada com sucesso.",
            "Acesse o ambiente em: {$appUrl}",
            '',
            "Suporte: {$supportEmail}",
            '',
            "Atenciosamente,",
            "Equipe {$appName}",
        ]);
    }

    private static function getRecoveryAltBody(string $name, string $appName, string $token, string $supportEmail): string
    {
        return implode(PHP_EOL, [
            "Olá, {$name}!",
            '',
            'Recebemos uma solicitacao para redefinir sua senha.',
            "Codigo de acesso: {$token}",
            '',
            'Se você não solicitou a redefinição, ignore este e-mail.',
            "Suporte: {$supportEmail}",
            '',
            "Atenciosamente,",
            "Equipe {$appName}",
        ]);
    }

    private static function getActivationAltBody(
        string $name,
        string $appName,
        string $activationUrl,
        string $expiresAt,
        string $supportEmail
    ): string {
        return implode(PHP_EOL, [
            "Olá, {$name}!",
            '',
            "Confirme seu e-mail para ativar sua conta no {$appName}.",
            "Link de uso unico: {$activationUrl}",
            "Válido até: {$expiresAt}",
            '',
            'A conta terá o acesso bloqueado se não for confirmada em até 7 dias.',
            "Suporte: {$supportEmail}",
        ]);
    }

    private static function getRegisterVerificationAltBody(
        string $name,
        string $appName,
        string $token,
        string $expiresAt,
        string $supportEmail
    ): string {
        return implode(PHP_EOL, [
            "Olá, {$name}!",
            '',
            "Use o código abaixo para continuar seu cadastro no {$appName}.",
            "Código de verificação: {$token}",
            "Válido até: {$expiresAt}",
            '',
            'Se você não solicitou este cadastro, ignore este e-mail.',
            "Suporte: {$supportEmail}",
        ]);
    }

    private static function setMailLog(string $type, array $data): void
    {
        $data = array_merge([
            'environment' => Config::$APP_ENV,
            'mail_service' => Config::$MAIL_SERVICE,
        ], $data);
        $logContent = json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        if ($logContent === false) {
            $logContent = json_encode([
                'event' => 'mail_log_encode_error',
                'json_error' => json_last_error_msg(),
            ]);
        }

        try {
            self::setLog($logContent, $type, 'Mail');
        } catch (\Throwable $exception) {
            error_log('Mail log write failed: ' . $exception->getMessage());
        }
    }

    private static function maskEmail(mixed $email): string
    {
        $email = strtolower(trim((string) $email));

        if (! str_contains($email, '@')) {
            return $email !== '' ? '[invalid-email]' : '';
        }

        [$local, $domain] = explode('@', $email, 2);
        $first = substr($local, 0, 1);
        $last = strlen($local) > 1 ? substr($local, -1) : '';

        return $first . str_repeat('*', 3) . $last . '@' . $domain;
    }

    private static function hashEmail(mixed $email): string
    {
        $email = strtolower(trim((string) $email));

        return $email !== '' ? hash('sha256', $email) : '';
    }
}
