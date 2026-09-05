<?php

namespace App\Core;

class EnvironmentFileGenerator
{
    private const GENERATED_KEYS = ['APP_KEY', 'APP_TOKEN'];

    public function generate(string $environment, string $targetPath, bool $overwrite = false): array
    {
        $environment = strtolower(trim($environment));

        if (! in_array($environment, EnvironmentVars::getSupportedEnvironments(), true)) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Ambiente inválido: %s. Valores permitidos: %s.',
                    $environment,
                    implode(', ', EnvironmentVars::getSupportedEnvironments())
                )
            );
        }

        if (is_file($targetPath) && ! $overwrite) {
            throw new \RuntimeException(
                "O arquivo {$targetPath} já existe. Use --force para sobrescrevê-lo."
            );
        }

        $targetDirectory = dirname($targetPath);
        if (! is_dir($targetDirectory)) {
            throw new \RuntimeException("O diretório de destino não existe: {$targetDirectory}");
        }

        $values = EnvironmentVars::setEnvironmentVariables($environment);
        $values['APP_ENV'] = $environment;
        $values['APP_KEY'] = GenerateKey::generateKey();
        $values['APP_TOKEN'] = GenerateKey::generateKey();

        $content = $this->render($values);
        $bytesWritten = @file_put_contents($targetPath, $content, LOCK_EX);

        if ($bytesWritten === false || $bytesWritten !== strlen($content)) {
            throw new \RuntimeException("Não foi possível gravar o arquivo de configuração: {$targetPath}");
        }

        return [
            'path' => $targetPath,
            'environment' => $environment,
            'generated_keys' => self::GENERATED_KEYS,
            'empty_values' => array_keys(array_filter($values, static fn (mixed $value): bool => $value === '')),
        ];
    }

    private function render(array $values): string
    {
        $lines = [
            '# Arquivo gerado automaticamente.',
            '# Revise os valores específicos do ambiente antes de publicar a aplicação.',
            '',
        ];

        foreach ($values as $key => $value) {
            $lines[] = $key . '=' . $this->formatValue($value);
        }

        return implode(PHP_EOL, $lines) . PHP_EOL;
    }

    private function formatValue(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_int($value) || is_float($value)) {
            return (string) $value;
        }

        $value = (string) $value;

        if ($value === '') {
            return '';
        }

        if (preg_match('/^[A-Za-z0-9_@.:+\/=\-]+$/', $value)) {
            return $value;
        }

        return '"' . str_replace(
            ["\\", "\n", "\r", "\t", '"'],
            ["\\\\", '\\n', '\\r', '\\t', '\\"'],
            $value
        ) . '"';
    }
}
