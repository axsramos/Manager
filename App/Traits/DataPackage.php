<?php

namespace App\Traits;

use App\Core\Config;
use App\Models\CAS\CasAppModel;
use JsonException;
use RuntimeException;

trait DataPackage
{
    public function getDataPackage(string $appId): array
    {
        $path = $this->getDataPackagePath();
        if (! is_file($path)) {
            return [];
        }

        $data = $this->decodeDataPackage($path);

        $application = new CasAppModel();
        $application->setSelectedFields(['CasAppCod', 'CasAppVer']);
        $application->CasAppCod = $appId;
        if (! $application->readRegister()) {
            return [];
        }

        if (($data['ProductKey'] ?? null) !== $application->CasAppCod) {
            return [];
        }

        if (($data['Version'] ?? null) !== $application->CasAppVer) {
            return [];
        }

        return $data;
    }

    public function getDataPackagePath(): string
    {
        $applicationName = $this->validatePackageComponent((string) Config::$APP_NAME, 'APP_NAME');
        $applicationVersion = $this->validatePackageComponent((string) Config::$APP_VERSION, 'APP_VERSION');
        $basePath = realpath(Config::getPathRules('Manager'));

        if ($basePath === false || ! is_dir($basePath)) {
            throw new RuntimeException('O diretório de regras do Manager não existe.');
        }

        $fileName = 'Settings_' . $applicationName . '_V' . $applicationVersion . '.json';
        $path = $basePath . DIRECTORY_SEPARATOR . $fileName;
        if (dirname($path) !== $basePath) {
            throw new RuntimeException('O caminho calculado para o pacote é inválido.');
        }

        return $path;
    }

    public function synchronizeDataPackage(string $appId): array
    {
        $path = $this->getDataPackagePath();
        if (! is_file($path)) {
            throw new RuntimeException("O pacote esperado não foi encontrado: {$path}");
        }

        $data = $this->decodeDataPackage($path);
        $data['ProductKey'] = $appId;
        $data['Version'] = (string) Config::$APP_VERSION;

        try {
            $content = json_encode(
                $data,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
            ) . PHP_EOL;
            json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException('Não foi possível serializar o pacote Manager.', 0, $exception);
        }

        $written = @file_put_contents($path, $content, LOCK_EX);
        if ($written === false || $written !== strlen($content)) {
            throw new RuntimeException("Não foi possível atualizar o pacote {$path}.");
        }

        return [
            'path' => $path,
            'product_key' => $appId,
            'version' => Config::$APP_VERSION,
        ];
    }

    private function decodeDataPackage(string $path): array
    {
        $json = @file_get_contents($path);
        if ($json === false) {
            throw new RuntimeException("Não foi possível ler o pacote {$path}.");
        }

        try {
            $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException("O pacote {$path} não contém JSON válido.", 0, $exception);
        }

        if (! is_array($data)) {
            throw new RuntimeException("O pacote {$path} não possui uma estrutura válida.");
        }

        return $data;
    }

    private function validatePackageComponent(string $value, string $key): string
    {
        $value = trim($value);
        if ($value === '' || ! preg_match('/^[A-Za-z0-9][A-Za-z0-9._-]*$/', $value)) {
            throw new RuntimeException("A configuração {$key} não é segura para compor o nome do pacote.");
        }

        return $value;
    }
}
