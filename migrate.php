<?php

require __DIR__ . '/vendor/autoload.php';

use App\Core\SimpleMigrator;
use App\Core\Logger;

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit(1);
}

$options = getopt('', ['storage::', 'dry-run', 'force']);

$storage = $options['storage'] ?? 'Default';
$dryRun = array_key_exists('dry-run', $options);
$force = array_key_exists('force', $options);

try {
    $migrator = new SimpleMigrator($storage, $dryRun);
    $result = $migrator->runCasMetadata($force);

    Logger::setLog(json_encode([
        'operation' => 'migration',
        'storage' => $storage,
        'status' => $result['status'],
        'checksum' => $result['checksum'],
        'statements' => count($result['sql']),
    ]), 'migration', 'Setup');

    echo $result['message'] . PHP_EOL;
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
