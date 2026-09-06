<?php

namespace App\Core;

use DateTimeImmutable;
use PDO;
use RuntimeException;
use Throwable;

final class VerifiedAccountActivator
{
    private const LOG_FILE_PATTERN = '/\Aregister_verification_(\d{8})\.log\z/D';
    private const LOG_EVENT = 'register_email_verified';
    private const AUDIT_USER = 'activate_verified_users';

    private PDO $pdo;

    public function __construct(private readonly string $storage = 'Default')
    {
        Config::getInstance();

        $config = Config::getDbStorage($storage);
        $dsn = "mysql:host={$config['DB_HOST']};dbname={$config['DB_DATABASE']};port={$config['DB_PORT']};charset={$config['DB_CHARSET']}";
        $this->pdo = new PDO($dsn, $config['DB_USERNAME'], $config['DB_PASSWORD'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);
    }

    public function run(bool $execute = false, int $limit = 500): array
    {
        if ($limit < 1 || $limit > 10000) {
            throw new \InvalidArgumentException('O limite deve estar entre 1 e 10000.');
        }

        $now = new DateTimeImmutable('now');
        $activationHours = (int) Config::$ACCOUNT_ACTIVATION_HOURS;
        $cutoff = $now->modify("-{$activationHours} hours");
        $processedAt = $now->format('Y-m-d H:i:s');
        $verifiedLogData = $this->verifiedHashesFromLogs($cutoff, $now);
        $eligibleUsers = $this->eligibleUsers($cutoff, $limit);
        $activatedHashes = [];
        $skippedUsers = 0;
        $activatedUsers = 0;

        foreach ($eligibleUsers as $user) {
            $email = strtolower(trim((string) $user['CasUsrLgn'] . (string) $user['CasUsrDmn']));
            $emailHash = hash('sha256', $email);

            if (!isset($verifiedLogData['hashes'][$emailHash])) {
                $skippedUsers++;
                continue;
            }

            $activatedHashes[] = $emailHash;

            if ($execute && $this->activateUser((string) $user['CasUsrCod'], $processedAt, $cutoff)) {
                $activatedUsers++;
            }
        }

        if (!$execute) {
            $activatedUsers = count($activatedHashes);
        }

        return [
            'operation' => 'activate_verified_users',
            'mode' => $execute ? 'execute' : 'dry-run',
            'storage' => $this->storage,
            'activation_hours' => $activationHours,
            'cutoff' => $cutoff->format('Y-m-d H:i:s'),
            'processed_at' => $processedAt,
            'auth_log_path' => Config::getPathAuthLogs(),
            'log_files_read' => $verifiedLogData['files_read'],
            'invalid_log_lines' => $verifiedLogData['invalid_lines'],
            'verified_hashes_found' => count($verifiedLogData['hashes']),
            'eligible_users' => count($eligibleUsers),
            'activated_users' => $activatedUsers,
            'skipped_users' => $skippedUsers,
            'account_hashes' => $activatedHashes,
        ];
    }

    private function eligibleUsers(DateTimeImmutable $cutoff, int $limit): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT CasUsrCod, CasUsrLgn, CasUsrDmn
             FROM CasUsr
             WHERE CasUsrActDtt IS NULL
               AND CasUsrBlq = :blocked
               AND CasUsrAudIns >= :cutoff
             ORDER BY CasUsrAudIns
             LIMIT ' . $limit
        );
        $stmt->execute([
            ':blocked' => 'N',
            ':cutoff' => $cutoff->format('Y-m-d H:i:s'),
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function activateUser(string $userId, string $processedAt, DateTimeImmutable $cutoff): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE CasUsr
             SET CasUsrActDtt = :processed_at,
                 CasUsrAudUpd = :processed_at,
                 CasUsrAudUsr = :audit_user
             WHERE CasUsrCod = :user_id
               AND CasUsrActDtt IS NULL
               AND CasUsrAudIns >= :cutoff'
        );
        $stmt->execute([
            ':processed_at' => $processedAt,
            ':audit_user' => self::AUDIT_USER,
            ':user_id' => $userId,
            ':cutoff' => $cutoff->format('Y-m-d H:i:s'),
        ]);

        return $stmt->rowCount() > 0;
    }

    private function verifiedHashesFromLogs(DateTimeImmutable $cutoff, DateTimeImmutable $now): array
    {
        $hashes = [];
        $filesRead = 0;
        $invalidLines = 0;
        $authLogPath = Config::getPathAuthLogs();

        if (!is_dir($authLogPath)) {
            return ['hashes' => $hashes, 'files_read' => 0, 'invalid_lines' => 0];
        }

        foreach ($this->recentLogFiles($authLogPath, $cutoff, $now) as $path) {
            $handle = @fopen($path, 'rb');
            if ($handle === false) {
                $invalidLines++;
                continue;
            }

            $filesRead++;
            while (($line = fgets($handle)) !== false) {
                $parsed = $this->parseLogLine($line, $cutoff, $now);
                if ($parsed === 'ignore') {
                    continue;
                }

                if ($parsed === null) {
                    $invalidLines++;
                    continue;
                }

                if (($parsed['event'] ?? '') !== self::LOG_EVENT) {
                    continue;
                }

                $emailHash = (string) ($parsed['email_hash'] ?? '');
                if (preg_match('/\A[a-f0-9]{64}\z/D', $emailHash)) {
                    $hashes[$emailHash] = true;
                }
            }
            fclose($handle);
        }

        return ['hashes' => $hashes, 'files_read' => $filesRead, 'invalid_lines' => $invalidLines];
    }

    private function recentLogFiles(string $authLogPath, DateTimeImmutable $cutoff, DateTimeImmutable $now): array
    {
        $files = [];
        $entries = scandir($authLogPath);
        if ($entries === false) {
            return $files;
        }

        $cutoffDay = $cutoff->setTime(0, 0, 0);
        $nowDay = $now->setTime(23, 59, 59);

        foreach ($entries as $entry) {
            if (!preg_match(self::LOG_FILE_PATTERN, $entry, $matches)) {
                continue;
            }

            $fileDay = DateTimeImmutable::createFromFormat('!Ymd', $matches[1]);
            if ($fileDay === false || $fileDay < $cutoffDay || $fileDay > $nowDay) {
                continue;
            }

            $path = $authLogPath . DIRECTORY_SEPARATOR . $entry;
            if (is_file($path)) {
                $files[$matches[1]] = $path;
            }
        }

        ksort($files);

        return array_values($files);
    }

    private function parseLogLine(string $line, DateTimeImmutable $cutoff, DateTimeImmutable $now): array|string|null
    {
        $parts = explode(' - ', trim($line), 2);
        if (count($parts) !== 2) {
            return null;
        }

        $loggedAt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $parts[0]);
        if ($loggedAt === false) {
            return null;
        }

        if ($loggedAt < $cutoff || $loggedAt > $now) {
            return 'ignore';
        }

        $payload = json_decode($parts[1], true);
        if (!is_array($payload)) {
            return null;
        }

        return $payload;
    }

    public static function logResult(array $result): void
    {
        Logger::setLog(
            (string) json_encode($result, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'verified_account_activation',
            'Auth'
        );
    }

    public static function logError(Throwable $exception): void
    {
        Logger::setLog(
            (string) json_encode([
                'operation' => 'activate_verified_users',
                'status' => 'error',
                'exception' => get_class($exception),
                'message' => $exception->getMessage(),
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'verified_account_activation_error',
            'Auth'
        );
    }
}
