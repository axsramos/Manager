<?php

namespace App\Class\Auth;

use App\Core\Config;
use App\Core\ServiceMail;
use App\Models\CAS\CasTknModel;
use App\Models\CAS\CasUsrModel;
use DateTimeImmutable;
use Throwable;

final class AccountActivationService extends AuthClass
{
    public const ACTIVATION_HOURS = 168;
    public const CLEANUP_DAYS = 30;
    private const TOKEN_DESCRIPTION_PREFIX = 'ACCOUNT_ACTIVATION ';

    public function request(string $email, string $name = ''): array
    {
        $email = self::normalizeEmail($email);

        if (!self::isValidEmail($email)) {
                return self::result(false, 'invalid_email', 'Informe um endereço de e-mail válido.');
        }

        try {
            $user = $this->findUserByEmail($email);
            if ($user !== null && !empty($user->CasUsrActDtt)) {
                return self::result(false, 'already_active', 'Este e-mail já pertence a uma conta ativada.');
            }
            if ($user !== null && $user->CasUsrBlq === 'S') {
                return self::result(false, 'account_blocked', 'Esta conta esta bloqueada.');
            }

            $token = $this->issueToken($email);
            $recipientName = trim($name);
            if ($recipientName === '' && $user !== null) {
                $recipientName = trim($user->CasUsrNme . ' ' . $user->CasUsrSnm);
            }
            if ($recipientName === '') {
                $recipientName = 'Usuário';
            }

            $activationUrl = rtrim((string) Config::$APP_URL, '/')
                . '/Auth/Register/activate/' . rawurlencode($token['key']);
            $mail = new ServiceMail();
            if (!$mail->sendMailActivation($recipientName, $email, $activationUrl, $token['expires_at'])) {
                $this->logEvent('activation_mail_failed', $email, ['existing_account' => $user !== null]);
                return self::result(false, 'mail_failed', 'Não foi possível enviar o e-mail de ativação. Tente novamente.');
            }

            $this->logEvent('activation_token_created', $email, [
                'existing_account' => $user !== null,
                'expires_at' => $token['expires_at'],
            ]);

            return self::result(true, $user === null ? 'sent' : 'resent',
                $user === null
                    ? 'Enviamos o link de ativação. Continue o cadastro e confirme o e-mail em até 7 dias.'
                    : 'A conta já estava cadastrada. Enviamos um novo link de ativação para o e-mail informado.',
                ['existing_account' => $user !== null, 'expires_at' => $token['expires_at']]
            );
        } catch (Throwable $exception) {
            $this->logEvent('activation_request_exception', $email, [
                'exception' => get_class($exception),
                'message' => $exception->getMessage(),
            ]);
            return self::result(false, 'error', 'Não foi possível iniciar a ativação da conta.');
        }
    }

    public function activate(string $token): array
    {
        $token = trim($token);
        if (!preg_match('/\A[A-Za-z0-9_-]{32,65}\z/D', $token)) {
            return self::result(false, 'invalid', 'O link de ativação é inválido.');
        }

        try {
            $tokenModel = new CasTknModel();
            if (!$tokenModel->findByKey($token)
                || !str_starts_with((string) $tokenModel->CasTknDsc, self::TOKEN_DESCRIPTION_PREFIX)) {
                return self::result(false, 'invalid', 'O link de ativação é inválido.');
            }

            $email = self::normalizeEmail(substr(
                (string) $tokenModel->CasTknDsc,
                strlen(self::TOKEN_DESCRIPTION_PREFIX)
            ));
            $user = $this->findUserByEmail($email);

            if ($tokenModel->CasTknBlq === 'S') {
                if ($user !== null && !empty($user->CasUsrActDtt)) {
                    return self::result(true, 'already_active', 'Esta conta já foi ativada. Você pode entrar normalmente.');
                }
                $this->logEvent('activation_token_reused', $email);
                return self::result(false, 'used', 'Este link de ativação já foi utilizado ou invalidado.');
            }

            $expiresAt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', (string) $tokenModel->CasTknKeyExp);
            if ($expiresAt === false || $expiresAt < new DateTimeImmutable('now')) {
                $this->logEvent('activation_token_expired', $email);
                if ($user === null) {
                    return self::result(false, 'account_missing', 'O link expirou e a conta não existe mais. Inicie um novo cadastro.');
                }
                if (!empty($user->CasUsrActDtt)) {
                    return self::result(true, 'already_active', 'Esta conta já foi ativada. Você pode entrar normalmente.');
                }
                if ($user->CasUsrBlq === 'S') {
                    return self::result(false, 'account_blocked', 'A conta esta bloqueada. Entre em contato com o suporte.');
                }

                $resent = $this->request($email, trim($user->CasUsrNme . ' ' . $user->CasUsrSnm));
                if ($resent['success']) {
                    return self::result(false, 'expired_resent', 'O link expirou. Enviamos um novo link de ativação para o e-mail cadastrado.');
                }
                return self::result(false, 'expired_resend_failed', 'O link expirou e não foi possível enviar outro agora. Tente novamente pelo cadastro.');
            }

            if ($user === null) {
                return self::result(false, 'account_missing', 'A conta ainda não foi concluída. Finalize o cadastro e acesse novamente este link.');
            }
            if ($user->CasUsrBlq === 'S') {
                return self::result(false, 'account_blocked', 'A conta esta bloqueada. Entre em contato com o suporte.');
            }

            if (empty($user->CasUsrActDtt)) {
                $user->setSelectedFields(['CasUsrActDtt']);
                $user->CasUsrActDtt = (new DateTimeImmutable('now'))->format('Y-m-d H:i:s');
                if (!$user->updateRegister()) {
                    return self::result(false, 'error', 'Não foi possível ativar a conta. Tente novamente.');
                }
            }

            $tokenModel->setSelectedFields(['CasTknBlq', 'CasTknBlqDtt']);
            $tokenModel->CasTknBlq = 'S';
            $tokenModel->CasTknBlqDtt = (new DateTimeImmutable('now'))->format('Y-m-d H:i:s');
            $tokenModel->updateRegister();

            $this->logEvent('account_activated', $email);
            return self::result(true, 'activated', 'Sua conta foi ativada com sucesso.');
        } catch (Throwable $exception) {
            $this->logEvent('activation_exception', '', [
                'exception' => get_class($exception),
                'message' => $exception->getMessage(),
            ]);
            return self::result(false, 'error', 'Não foi possível processar a ativação da conta.');
        }
    }

    public static function isAccessExpired(mixed $activatedAt, mixed $createdAt): bool
    {
        if (!empty($activatedAt)) {
            return false;
        }

        try {
            $created = new DateTimeImmutable((string) $createdAt);
            return $created->modify('+' . self::activationHours() . ' hours') < new DateTimeImmutable('now');
        } catch (Throwable) {
            return true;
        }
    }

    private function issueToken(string $email): array
    {
        $description = self::TOKEN_DESCRIPTION_PREFIX . $email;
        $tokenModel = new CasTknModel();
        $tokenModel->CasTknCod = hash('sha256', $description);
        $exists = $tokenModel->readRegister();

        $tokenModel->CasTknDsc = $description;
        $tokenModel->CasTknBlq = 'N';
        $tokenModel->CasTknBlqDtt = null;
        $tokenModel->CasTknKey = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
        $tokenModel->CasTknKeyExp = (new DateTimeImmutable('now'))
            ->modify('+' . self::activationHours() . ' hours')
            ->format('Y-m-d H:i:s');

        if ($exists) {
            $tokenModel->setSelectedFields(['CasTknDsc', 'CasTknBlq', 'CasTknBlqDtt', 'CasTknKey', 'CasTknKeyExp']);
            if (!$tokenModel->updateRegister()) {
                throw new \RuntimeException('Não foi possível atualizar o token de ativação.');
            }
        } elseif (!$tokenModel->createRegister()) {
            throw new \RuntimeException('Não foi possível criar o token de ativação.');
        }

        return ['key' => $tokenModel->CasTknKey, 'expires_at' => $tokenModel->CasTknKeyExp];
    }

    private function findUserByEmail(string $email): ?CasUsrModel
    {
        [$login, $domain] = explode('@', $email, 2);
        $user = new CasUsrModel();
        $user->CasUsrDmn = '@' . $domain;
        $user->CasUsrLgn = $login;
        if (!$user->existsMailAccount()) {
            return null;
        }
        $user->setSelectedFields(['CasUsrCod', 'CasUsrNme', 'CasUsrSnm', 'CasUsrActDtt', 'CasUsrBlq', 'CasUsrAudIns']);
        return $user->readRegister() ? $user : null;
    }

    private function logEvent(string $event, string $email, array $context = []): void
    {
        $payload = array_merge([
            'event' => $event,
            'email_hash' => $email !== '' ? hash('sha256', self::normalizeEmail($email)) : '',
        ], $context);

        try {
            self::setLog((string) json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 'activation', 'Auth');
        } catch (Throwable) {
        }
    }

    private static function normalizeEmail(string $email): string
    {
        return strtolower(trim($email));
    }

    public static function activationHours(): int
    {
        Config::getInstance();

        return Config::$ACCOUNT_ACTIVATION_HOURS ?? self::ACTIVATION_HOURS;
    }

    private static function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false && strlen($email) <= 130;
    }

    private static function result(bool $success, string $status, string $message, array $extra = []): array
    {
        return array_merge([
            'success' => $success,
            'status' => $status,
            'message' => $message,
        ], $extra);
    }
}
