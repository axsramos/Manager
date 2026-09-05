<?php

require __DIR__ . '/vendor/autoload.php';

use App\Core\Config;
use App\Core\VerifiedAccountActivator;

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit(1);
}

function showUsage(): void
{
    echo 'Uso: php activate-verified-users.php [--storage=Default] [--execute] [--limit=500]' . PHP_EOL;
    echo 'Sem --execute, o comando opera em modo dry-run e não altera o banco.' . PHP_EOL;
}

$options = getopt('', ['storage::', 'execute', 'limit::', 'help']);
if (isset($options['help'])) {
    showUsage();
    exit(0);
}

$storage = (string) ($options['storage'] ?? 'Default');
$execute = isset($options['execute']);
$limit = filter_var($options['limit'] ?? 500, FILTER_VALIDATE_INT, [
    'options' => ['min_range' => 1, 'max_range' => 10000],
]);

if ($limit === false) {
    fwrite(STDERR, 'Informe --limit com um inteiro entre 1 e 10000.' . PHP_EOL);
    exit(2);
}

try {
    Config::getInstance();

    $result = (new VerifiedAccountActivator($storage))->run($execute, $limit);
    VerifiedAccountActivator::logResult($result);

    echo 'Modo: ' . $result['mode'] . PHP_EOL;
    echo 'Storage: ' . $result['storage'] . PHP_EOL;
    echo 'Prazo de ativação: ' . $result['activation_hours'] . ' horas' . PHP_EOL;
    echo 'Corte: ' . $result['cutoff'] . PHP_EOL;
    echo 'Logs lidos: ' . $result['log_files_read'] . PHP_EOL;
    echo 'Hashes verificados: ' . $result['verified_hashes_found'] . PHP_EOL;
    echo 'Usuários elegíveis: ' . $result['eligible_users'] . PHP_EOL;
    echo 'Usuários ativados: ' . $result['activated_users'] . PHP_EOL;
    echo 'Usuários ignorados: ' . $result['skipped_users'] . PHP_EOL;
    exit(0);
} catch (Throwable $exception) {
    try {
        VerifiedAccountActivator::logError($exception);
    } catch (Throwable) {
    }

    fwrite(STDERR, 'Erro ao ativar usuários verificados: ' . $exception->getMessage() . PHP_EOL);
    exit(1);
}
