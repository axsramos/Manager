<?php

require __DIR__ . '/vendor/autoload.php';

use App\Core\LogCleaner;
use App\Core\Logger;

function cleanupLogsShowUsage(): void
{
    echo 'Uso: php cleanup-logs.php [--days=30] [--delete]' . PHP_EOL;
    echo '  Sem --delete, apenas informa quantos arquivos seriam removidos.' . PHP_EOL;
    echo '  --days deve ser um inteiro entre 1 e 3650.' . PHP_EOL;
}

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit(1);
}

$options = getopt('', ['days:', 'delete', 'help']);
if (array_key_exists('help', $options)) {
    cleanupLogsShowUsage();
    exit(0);
}

$retentionDays = filter_var(
    $options['days'] ?? 30,
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1, 'max_range' => 3650]]
);

if ($retentionDays === false) {
    fwrite(STDERR, 'Informe --days com um inteiro entre 1 e 3650.' . PHP_EOL);
    exit(2);
}

$delete = array_key_exists('delete', $options);

try {
    $result = (new LogCleaner())->clean($retentionDays, $delete);

    echo 'Modo: ' . $result['mode'] . PHP_EOL;
    echo 'Retenção: ' . $result['retention_days'] . ' dias' . PHP_EOL;
    echo 'Arquivos .log analisados: ' . $result['scanned'] . PHP_EOL;
    echo 'Arquivos acima da retenção: ' . $result['eligible'] . PHP_EOL;
    echo 'Arquivos removidos: ' . $result['deleted'] . PHP_EOL;

    if (! $result['directory_exists']) {
        echo 'O diretório de logs ainda não existe.' . PHP_EOL;
    }

    if ($result['skipped_unsafe'] > 0) {
        fwrite(STDERR, 'Arquivos ignorados por segurança: ' . $result['skipped_unsafe'] . PHP_EOL);
    }

    foreach ($result['failed'] as $path) {
        fwrite(STDERR, 'Não foi possível remover: ' . $path . PHP_EOL);
    }

    Logger::setLog(
        json_encode([
            'operation' => 'cleanup_logs',
            'mode' => $result['mode'],
            'retention_days' => $result['retention_days'],
            'scanned' => $result['scanned'],
            'eligible' => $result['eligible'],
            'deleted' => $result['deleted'],
            'skipped_unsafe' => $result['skipped_unsafe'],
            'failures' => count($result['failed']),
        ]),
        'maintenance',
        'Setup'
    );

    exit($result['failed'] === [] ? 0 : 1);
} catch (Throwable $throwable) {
    fwrite(STDERR, 'Erro ao limpar logs: ' . $throwable->getMessage() . PHP_EOL);
    exit(1);
}
