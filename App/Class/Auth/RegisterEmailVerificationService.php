<?php

namespace App\Class\Auth;

use App\Core\ServiceMail;
use App\Class\Auth\AccountActivationService;
use App\Models\CAS\CasTknModel;
use App\Models\CAS\CasUsrModel;
use DateTimeImmutable;
use RuntimeException;
use Throwable;

final class RegisterEmailVerificationService extends AuthClass
{
    private const TOKEN_DESCRIPTION_PREFIX = 'REGISTER_EMAIL_VERIFICATION ';

    public function request(string $email): array
    {
        $email = self::normalizeEmail($email);

        if (!self::isValidEmail($email)) {
            return self::result(false, 'invalid_email', 'Informe um endereço de e-mail válido.');
        }

        try {
            $user = $this->findUserByEmail($email);
            if ($user !== null) {
                return self::result(false, 'already_registered', 'Este e-mail já está cadastrado.');
            }

            $token = $this->issueToken($email);
            $mail = new ServiceMail();
            if (!$mail->sendMailRegisterVerification('Usuário', $email, $token['key'], $token['expires_at'])) {
                $this->logEvent('register_verification_mail_failed', $email);
                return self::result(false, 'mail_failed', 'Não foi possível enviar o código de verificação. Tente novamente.');
            }

            $this->logEvent('register_verification_token_created', $email, [
                'expires_at' => $token['expires_at'],
            ]);

            return self::result(true, 'sent', 'Enviamos um código de verificação para o e-mail informado.', [
                'expires_at' => $token['expires_at'],
            ]);
        } catch (Throwable $exception) {
            $this->logEvent('register_verification_request_exception', $email, [
                'exception' => get_class($exception),
                'message' => $exception->getMessage(),
            ]);

            return self::result(false, 'error', 'Não foi possível iniciar a verificação do e-mail.');
        }
    }

    public function verify(string $email, string $code): array
    {
        $email = self::normalizeEmail($email);
        $code = trim($code);

        if (!self::isValidEmail($email)) {
            return self::result(false, 'invalid_email', 'Informe um endereço de e-mail válido.');
        }

        if (!preg_match('/\A\d{6}\z/D', $code)) {
            return self::result(false, 'invalid_code', 'Informe o código de verificação com 6 dígitos.');
        }

        try {
            $description = self::TOKEN_DESCRIPTION_PREFIX . $email;
            $tokenModel = new CasTknModel();

            if (!$tokenModel->findByDescription($description)) {
                return self::result(false, 'invalid_code', 'Código de verificação inválido.');
            }

            if ($tokenModel->CasTknBlq === 'S') {
                $this->logEvent('register_verification_token_reused', $email);
                return self::result(false, 'used', 'Este código já foi utilizado ou invalidado.');
            }

            $expiresAt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', (string) $tokenModel->CasTknKeyExp);
            if ($expiresAt === false || $expiresAt < new DateTimeImmutable('now')) {
                $this->logEvent('register_verification_token_expired', $email);
                return self::result(false, 'expired', 'O código expirou. Solicite um novo código para continuar.');
            }

            if (!hash_equals((string) $tokenModel->CasTknKey, $code)) {
                $this->logEvent('register_verification_token_mismatch', $email);
                return self::result(false, 'invalid_code', 'Código de verificação inválido.');
            }

            $tokenModel->setSelectedFields(['CasTknBlq', 'CasTknBlqDtt']);
            $tokenModel->CasTknBlq = 'S';
            $tokenModel->CasTknBlqDtt = (new DateTimeImmutable('now'))->format('Y-m-d H:i:s');
            $tokenModel->updateRegister();

            $this->logEvent('register_email_verified', $email);
            return self::result(true, 'verified', 'E-mail verificado. Complete seu cadastro.');
        } catch (Throwable $exception) {
            $this->logEvent('register_verification_exception', $email, [
                'exception' => get_class($exception),
                'message' => $exception->getMessage(),
            ]);

            return self::result(false, 'error', 'Não foi possível validar o código de verificação.');
        }
    }

    private function issueToken(string $email): array
    {
        $description = self::TOKEN_DESCRIPTION_PREFIX . $email;
        $tokenModel = new CasTknModel();
        $tokenModel->CasTknCod = $this->tokenId($email);
        $exists = $tokenModel->readRegister();

        $tokenModel->CasTknDsc = $description;
        $tokenModel->CasTknBlq = 'N';
        $tokenModel->CasTknBlqDtt = null;
        $tokenModel->CasTknKey = (string) random_int(100000, 999999);
        $tokenModel->CasTknKeyExp = (new DateTimeImmutable('now'))
            ->modify('+' . AccountActivationService::activationHours() . ' hours')
            ->format('Y-m-d H:i:s');

        if ($exists) {
            $tokenModel->setSelectedFields(['CasTknDsc', 'CasTknBlq', 'CasTknBlqDtt', 'CasTknKey', 'CasTknKeyExp']);
            if (!$tokenModel->updateRegister()) {
                throw new RuntimeException('Não foi possível atualizar o código de verificação.');
            }
        } elseif (!$tokenModel->createRegister()) {
            throw new RuntimeException('Não foi possível criar o código de verificação.');
        }

        return ['key' => $tokenModel->CasTknKey, 'expires_at' => $tokenModel->CasTknKeyExp];
    }

    private function tokenId(string $email): string
    {
        return hash('sha256', self::TOKEN_DESCRIPTION_PREFIX . $email);
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

        return $user;
    }

    private function logEvent(string $event, string $email, array $context = []): void
    {
        $payload = array_merge([
            'event' => $event,
            'email_hash' => $email !== '' ? hash('sha256', self::normalizeEmail($email)) : '',
        ], $context);

        try {
            self::setLog((string) json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), 'register_verification', 'Auth');
        } catch (Throwable) {
        }
    }

    private static function normalizeEmail(string $email): string
    {
        return strtolower(trim($email));
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
