<?php

namespace App\Class\Manager;

use App\Core\Config;
use JsonException;
use RuntimeException;

class ApplicationPackageLocator
{
    private const ALLOWED_RUN_SCRIPTS = [
        'ApplyApplicationSettings',
        'ApplyContractSettings',
    ];

    public function locate(array $product): array
    {
        $directory = $this->component((string) ($product['package_directory'] ?? $product['app_id'] ?? ''), 'package_directory');
        $basePath = realpath(Config::getPathRules($directory));

        if ($basePath === false || ! is_dir($basePath)) {
            throw new RuntimeException('Diretório de regras do aplicativo não localizado.');
        }

        $fileName = trim((string) ($product['package_name'] ?? ''));
        if ($fileName === '') {
            $productName = $this->component((string) ($product['package_product_name'] ?? $product['title'] ?? $product['app_id'] ?? ''), 'package_product_name');
            $version = $this->component((string) ($product['package_version'] ?? '1.0.0'), 'package_version');
            $fileName = 'Settings_' . $productName . '_V' . $version . '.json';
        }

        if (! preg_match('/^Settings_[A-Za-z0-9._-]+_V[A-Za-z0-9._-]+\.json$/', $fileName)) {
            throw new RuntimeException('Nome do pacote técnico inválido.');
        }

        $path = $basePath . DIRECTORY_SEPARATOR . $fileName;
        $realPath = realpath($path);

        if ($realPath === false || ! is_file($realPath) || ! str_starts_with($realPath, $basePath . DIRECTORY_SEPARATOR)) {
            throw new RuntimeException('Pacote técnico do aplicativo não localizado.');
        }

        $content = file_get_contents($realPath);
        if ($content === false) {
            throw new RuntimeException('Não foi possível ler o pacote técnico do aplicativo.');
        }

        try {
            $package = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException('Pacote técnico com JSON inválido.', 0, $exception);
        }

        if (! is_array($package)) {
            throw new RuntimeException('Pacote técnico com estrutura inválida.');
        }

        $this->validatePackage($product, $package);

        return [
            'path' => $realPath,
            'hash' => hash('sha256', $content),
            'data' => $package,
        ];
    }

    private function validatePackage(array $product, array $package): void
    {
        foreach (['ProductKey', 'Version', 'RunScript'] as $field) {
            if (trim((string) ($package[$field] ?? '')) === '') {
                throw new RuntimeException("Pacote técnico sem o campo obrigatório {$field}.");
            }
        }

        $expectedProductKey = (string) ($product['product_key'] ?? $product['app_id'] ?? '');
        if ($expectedProductKey !== '' && ! hash_equals($expectedProductKey, (string) $package['ProductKey'])) {
            throw new RuntimeException('Pacote técnico incompatível com o produto selecionado.');
        }

        $expectedVersion = (string) ($product['package_version'] ?? '');
        if ($expectedVersion !== '' && ! hash_equals($expectedVersion, (string) $package['Version'])) {
            throw new RuntimeException('Versão do pacote técnico incompatível com o catálogo.');
        }

        $expectedRunScript = (string) ($product['run_script'] ?? '');
        if ($expectedRunScript !== '' && ! hash_equals($expectedRunScript, (string) $package['RunScript'])) {
            throw new RuntimeException('RunScript do pacote técnico incompatível com o catálogo.');
        }

        $runScripts = $this->runScripts($package);
        $expectedRunScripts = $this->expectedRunScripts($product);
        if ($expectedRunScripts !== [] && $expectedRunScripts !== $runScripts) {
            throw new RuntimeException('RunScripts do pacote técnico incompatíveis com o catálogo.');
        }

        foreach ($runScripts as $runScript) {
            if (! in_array($runScript, self::ALLOWED_RUN_SCRIPTS, true)) {
                throw new RuntimeException('RunScript do pacote técnico não permitido.');
            }
        }
    }

    private function runScripts(array $package): array
    {
        $scripts = $package['RunScripts'] ?? $package['run_scripts'] ?? null;
        if ($scripts === null) {
            return [(string) $package['RunScript']];
        }

        if (! is_array($scripts) || $scripts === []) {
            throw new RuntimeException('RunScripts do pacote técnico deve ser uma lista não vazia.');
        }

        $normalized = [];
        foreach ($scripts as $script) {
            if (! is_string($script)) {
                throw new RuntimeException('RunScripts do pacote técnico contém aplicador inválido.');
            }

            $script = trim((string) $script);
            if ($script === '') {
                throw new RuntimeException('RunScripts do pacote técnico contém aplicador inválido.');
            }
            $normalized[] = $script;
        }

        return $normalized;
    }

    private function expectedRunScripts(array $product): array
    {
        $scripts = $product['run_scripts'] ?? $product['RunScripts'] ?? null;
        if ($scripts === null) {
            return [];
        }

        if (! is_array($scripts) || $scripts === []) {
            throw new RuntimeException('RunScripts do catálogo deve ser uma lista não vazia.');
        }

        $normalized = [];
        foreach ($scripts as $script) {
            if (! is_string($script)) {
                throw new RuntimeException('RunScripts do catálogo contém aplicador inválido.');
            }

            $script = trim($script);
            if ($script === '') {
                throw new RuntimeException('RunScripts do catálogo contém aplicador inválido.');
            }

            $normalized[] = $script;
        }

        return $normalized;
    }

    private function component(string $value, string $name): string
    {
        $value = trim($value);
        if ($value === '' || ! preg_match('/^[A-Za-z0-9][A-Za-z0-9._-]*$/', $value)) {
            throw new RuntimeException("Campo {$name} inválido para localizar o pacote técnico.");
        }

        return $value;
    }
}
