<?php

require __DIR__ . '/vendor/autoload.php';

use App\Class\Manager\CreateInitialData;
use App\Core\Config;
use App\Core\Logger;

function seedShowUsage(): void
{
    echo 'Uso: php seed.php [--storage=Default]' . PHP_EOL;
    echo 'Executa a carga inicial depois de configure.php e migrate.php.' . PHP_EOL;
}

function seedPrompt(string $label): string
{
    fwrite(STDOUT, $label . ': ');
    $value = fgets(STDIN);
    if ($value === false || trim($value) === '') {
        throw new RuntimeException("O campo {$label} é obrigatório.");
    }
    return trim($value);
}

function seedPromptPassword(string $label): string
{
    fwrite(STDOUT, $label . ': ');

    if (PHP_OS_FAMILY === 'Windows') {
        $command = 'powershell.exe -NoProfile -Command "$p=Read-Host -AsSecureString; '
            . '$b=[Runtime.InteropServices.Marshal]::SecureStringToBSTR($p); '
            . 'try {[Runtime.InteropServices.Marshal]::PtrToStringBSTR($b)} '
            . 'finally {[Runtime.InteropServices.Marshal]::ZeroFreeBSTR($b)}"';
        $value = shell_exec($command);
        fwrite(STDOUT, PHP_EOL);
        if ($value === null) {
            throw new RuntimeException('Não foi possível ler a senha sem eco neste terminal.');
        }
        return rtrim($value, "\r\n");
    }

    $sttyMode = shell_exec('stty -g');
    if ($sttyMode === null || trim($sttyMode) === '') {
        throw new RuntimeException('Não foi possível desabilitar o eco da senha neste terminal.');
    }
    try {
        shell_exec('stty -echo');
        $value = fgets(STDIN);
    } finally {
        shell_exec('stty ' . escapeshellarg(trim($sttyMode)));
        fwrite(STDOUT, PHP_EOL);
    }
    if ($value === false) {
        throw new RuntimeException('Não foi possível ler a senha.');
    }
    return rtrim($value, "\r\n");
}

function seedValidatePassword(string $password): void
{
    $rules = Config::getRulesPassword();
    if (strlen($password) < (int) $rules['MinimumLength']) {
        throw new RuntimeException('A senha não possui o tamanho mínimo de ' . $rules['MinimumLength'] . ' caracteres.');
    }
    if (($rules['RequireNumbers'] ?? false) && ! preg_match('/\d/', $password)) {
        throw new RuntimeException('A senha deve conter ao menos um número.');
    }
    if (($rules['RequireUppercase'] ?? false) && ! preg_match('/[A-Z]/', $password)) {
        throw new RuntimeException('A senha deve conter ao menos uma letra maiúscula.');
    }
    if (($rules['RequireLowercase'] ?? false) && ! preg_match('/[a-z]/', $password)) {
        throw new RuntimeException('A senha deve conter ao menos uma letra minúscula.');
    }
    if (($rules['RequireSimbols'] ?? false) && ! preg_match('/[^a-zA-Z0-9]/', $password)) {
        throw new RuntimeException('A senha deve conter ao menos um símbolo.');
    }
}

function seedReadAccount(string $accountName): array
{
    echo PHP_EOL . "Dados da conta {$accountName}" . PHP_EOL;
    $firstName = seedPrompt('Nome');
    $lastName = seedPrompt('Sobrenome');
    $email = strtolower(seedPrompt('E-mail'));
    if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        throw new RuntimeException("O e-mail da conta {$accountName} é inválido.");
    }
    $password = seedPromptPassword('Senha');
    $confirmation = seedPromptPassword('Confirme a senha');
    if (! hash_equals($password, $confirmation)) {
        throw new RuntimeException("As senhas da conta {$accountName} não conferem.");
    }
    seedValidatePassword($password);

    return [
        'FirstName' => $firstName,
        'LastName' => $lastName,
        'Account' => $email,
        'Password' => md5($password),
        'USR_LOGGED' => $firstName . ' ' . $lastName,
    ];
}

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit(1);
}

$options = getopt('', ['storage::', 'help']);
if (array_key_exists('help', $options)) {
    seedShowUsage();
    exit(0);
}
$storage = $options['storage'] ?? 'Default';
if (! is_string($storage) || trim($storage) === '') {
    fwrite(STDERR, 'Informe um storage válido.' . PHP_EOL);
    exit(2);
}

try {
    Config::getInstance();
    $initializer = new CreateInitialData($storage);
    $initializer->preflight();

    $completeState = $initializer->findCompleteState();
    if ($completeState !== null) {
        echo 'A carga principal já está completa; as credenciais não serão solicitadas novamente.' . PHP_EOL;
        $result = $initializer->finishExisting($completeState);
    } else {
        $admin = seedReadAccount('admin');
        $support = seedReadAccount('support');
        $result = $initializer->run($admin, $support);
    }

    echo PHP_EOL . 'Carga inicial concluída.' . PHP_EOL;
    foreach (($result['application_settings'] ?? []) as $account => $settings) {
        echo sprintf(
            'Configurações %s: %s%s',
            $account,
            $settings['status'] ?? 'unknown',
            PHP_EOL
        );
        if (($settings['status'] ?? '') === 'warning') {
            echo '  Aviso: ' . ($settings['message'] ?? 'falha não detalhada') . PHP_EOL;
        }
    }

    Logger::setLog(json_encode([
        'operation' => 'initial_seed',
        'storage' => $storage,
        'status' => 'completed',
    ]), 'seed', 'Setup');
    exit(0);
} catch (Throwable $throwable) {
    Logger::setLog(json_encode([
        'operation' => 'initial_seed',
        'storage' => $storage,
        'status' => 'error',
        'message' => $throwable->getMessage(),
    ]), 'error', 'Setup');
    fwrite(STDERR, 'Erro na carga inicial: ' . $throwable->getMessage() . PHP_EOL);
    exit(1);
}
