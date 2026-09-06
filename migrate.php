<?php

require __DIR__ . '/vendor/autoload.php';

use App\Core\SimpleMigrator;
use App\Core\Logger;

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit(1);
}

$options = getopt('', ['storage::', 'module::', 'dry-run', 'force', 'help']);

if (array_key_exists('help', $options)) {
    echo 'Uso: php migrate.php [--storage=Default|SAAS] [--module=CAS] [--dry-run] [--force]' . PHP_EOL;
    exit(0);
}

$storage = $options['storage'] ?? 'Default';
$module = $options['module'] ?? 'CAS';
$dryRun = array_key_exists('dry-run', $options);
$force = array_key_exists('force', $options);

try {
    $migrator = new SimpleMigrator($storage, $dryRun);
    $result = $migrator->runMetadata($module, $force);

    Logger::setLog(json_encode([
        'operation' => 'migration',
        'storage' => $storage,
        'module' => $result['module'],
        'status' => $result['status'],
        'checksum' => $result['checksum'],
        'statements' => count($result['sql']),
    ]), 'migration', 'Setup');

    echo $result['message'] . PHP_EOL;
    echo 'Storage: ' . $result['storage'] . PHP_EOL;
    echo 'Module: ' . $result['module'] . PHP_EOL;
    echo 'Status: ' . $result['status'] . PHP_EOL;
    echo 'Checksum: ' . $result['checksum'] . PHP_EOL;
    echo 'SQL statements: ' . count($result['sql']) . PHP_EOL;

    if ($dryRun) {
        echo PHP_EOL . implode(';' . PHP_EOL . PHP_EOL, $result['sql']) . ';' . PHP_EOL;
    }

    exit(0);
} catch (Throwable $th) {
    Logger::setLog(json_encode([
        'operation' => 'migration',
        'storage' => $storage,
        'status' => 'error',
        'message' => $th->getMessage(),
    ]), 'error', 'Setup');
    fwrite(STDERR, 'Erro ao executar migrations: ' . $th->getMessage() . PHP_EOL);
    exit(1);
}
