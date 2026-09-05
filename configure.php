<?php

require __DIR__ . '/vendor/autoload.php';

use App\Core\EnvironmentFileGenerator;
use App\Core\EnvironmentVars;

function showUsage(): void
{
    $environments = implode('|', EnvironmentVars::getSupportedEnvironments());

    echo "Uso: php configure.php --env=<{$environments}> [--force]" . PHP_EOL;
    echo '  --env    Ambiente utilizado para preencher o arquivo .env.' . PHP_EOL;
    echo '  --force  Sobrescreve o arquivo .env caso ele já exista.' . PHP_EOL;
}

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit(1);
}

$options = getopt('', ['env:', 'force', 'help']);

if (array_key_exists('help', $options)) {
    showUsage();
    exit(0);
}

$environment = $options['env'] ?? '';
$force = array_key_exists('force', $options);

if (! is_string($environment) || trim($environment) === '') {
    fwrite(STDERR, 'Informe o ambiente por meio da opção --env.' . PHP_EOL);
    showUsage();
    exit(2);
}

try {
    $generator = new EnvironmentFileGenerator();
    $result = $generator->generate($environment, __DIR__ . '/.env', $force);

    echo sprintf(
        'Arquivo %s gerado para o ambiente %s.',
        $result['path'],
        $result['environment']
    ) . PHP_EOL;
    echo 'Chaves geradas: ' . implode(', ', $result['generated_keys']) . '.' . PHP_EOL;

    if ($result['empty_values']) {
        echo 'Valores que ainda precisam ser revisados: '
            . implode(', ', $result['empty_values'])
            . '.' . PHP_EOL;
    }

    exit(0);
} catch (Throwable $throwable) {
    fwrite(STDERR, 'Erro ao gerar configuração: ' . $throwable->getMessage() . PHP_EOL);
    exit(1);
}
