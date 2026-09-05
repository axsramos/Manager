<?php

require __DIR__ . '/vendor/autoload.php';

use App\Core\Logger;
use App\Core\UnconfirmedAccountCleaner;

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit(1);
}
$options = getopt('', ['storage::', 'days:', 'delete', 'help']);
if (isset($options['help'])) {
    echo 'Uso: php cleanup-unconfirmed-accounts.php [--storage=Default] [--days=30] [--delete]' . PHP_EOL;
    echo 'Sem --delete, o comando apenas apresenta as contas elegiveis.' . PHP_EOL;
    exit(0);
}
$days = filter_var($options['days'] ?? 30, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 3650]]);
if ($days === false) {
    fwrite(STDERR, 'Informe --days com um inteiro entre 1 e 3650.' . PHP_EOL);
    exit(2);
}
$storage = (string) ($options['storage'] ?? 'Default');
$delete = isset($options['delete']);
try {
    $result = (new UnconfirmedAccountCleaner($storage))->clean($days, $delete);
    echo 'Modo: ' . $result['mode'] . PHP_EOL;
    echo 'Retencao: ' . $result['retention_days'] . ' dias' . PHP_EOL;
    echo 'Contas elegiveis: ' . $result['eligible'] . PHP_EOL;
    echo 'Contas removidas: ' . $result['deleted_accounts'] . PHP_EOL;
    echo 'Registros removidos: ' . $result['deleted_rows'] . PHP_EOL;
    Logger::setLog(json_encode([
        'operation' => 'cleanup_unconfirmed_accounts', 'mode' => $result['mode'],
        'retention_days' => $result['retention_days'], 'eligible' => $result['eligible'],
        'deleted_accounts' => $result['deleted_accounts'], 'deleted_rows' => $result['deleted_rows'],
        'account_hashes' => $result['account_hashes'],
    ], JSON_UNESCAPED_SLASHES), 'account_cleanup', 'Audit');
    exit(0);
} catch (Throwable $exception) {
    try {
        Logger::setLog(json_encode(['operation' => 'cleanup_unconfirmed_accounts', 'status' => 'error', 'message' => $exception->getMessage()]), 'account_cleanup_error', 'Audit');
    } catch (Throwable) {
    }
    fwrite(STDERR, 'Erro ao limpar contas nao confirmadas: ' . $exception->getMessage() . PHP_EOL);
    exit(1);
}
