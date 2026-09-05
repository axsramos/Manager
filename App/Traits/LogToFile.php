<?php

namespace App\Traits;

use DateTime;
use App\Core\Config;
use InvalidArgumentException;
use RuntimeException;

trait LogToFile
{
    private const LOG_DIRECTORIES = [
        'DB',
        'Mail',
        'Others',
        'Auth',
        'Authorization',
        'Runtime',
        'Setup',
        'Audit',
    ];

    private const LOG_SEGMENT_PATTERN = '/\A[A-Za-z0-9_-]{1,65}\z/D';

    public static function setLog(string $dataContent, string $type = 'error', string $directory = '', string $subdirectory = ''): void
    {
        $logBaseDir = Config::getPathLogs();

        if (empty($directory)) {
            $directory = 'Others';
        }

        if (! in_array($directory, self::LOG_DIRECTORIES, true)) {
            throw new InvalidArgumentException("Diretório de log não permitido: {$directory}");
        }

        if (! preg_match(self::LOG_SEGMENT_PATTERN, $type)) {
            throw new InvalidArgumentException("Tipo de log inválido: {$type}");
        }

        if ($subdirectory !== '') {
            throw new InvalidArgumentException('Subdiretórios de log não são permitidos.');
        }

        $fullLogPath = $logBaseDir . DIRECTORY_SEPARATOR . $directory;

        if (! is_dir($fullLogPath)
            && ! mkdir($fullLogPath, 0750, true)
            && ! is_dir($fullLogPath)) {
            throw new RuntimeException("Não foi possível criar o diretório de log: {$fullLogPath}");
        }

        $dtnow = new DateTime('now');
        $dtnow_str = $dtnow->format('Y-m-d H:i:s');
        $sufix_name = $dtnow->format('Ymd');
        $path = $fullLogPath . DIRECTORY_SEPARATOR . $type . '_' . $sufix_name . '.log';

        $dataContent_log = $dtnow_str . ' - ' . $dataContent . PHP_EOL;

        if (file_put_contents($path, $dataContent_log, FILE_APPEND | LOCK_EX) === false) {
            throw new RuntimeException("Não foi possível gravar o arquivo de log: {$path}");
        }
    }
}
