<?php

namespace App\Class\Contract;

use App\Core\Config;
use JsonException;
use RuntimeException;

class ContractPackageInstaller extends AbstractContractService
{
    public function install(string $repositoryId, string $packageHash): array
    {
        $this->assertRepositoryExists($repositoryId);
        $packagePath = $this->resolvePackagePathByHash($packageHash);
        $package = $this->loadPackage($packagePath);

        $apply = new ApplyContractSettings($this->storage);
        $result = $apply->run($repositoryId, $package, $packageHash);

        return $this->result($result['status'], $result['message'], [
            'repository_id' => $repositoryId,
            'package_path' => $packagePath,
            'result' => $result,
        ]);
    }

    public function availablePackages(): array
    {
        $packages = [];
        foreach ($this->packageFiles() as $file) {
            $package = $this->loadPackage($file);
            $packages[] = [
                'product_key' => $package['ProductKey'] ?? null,
                'product_name' => $package['ProductName'] ?? null,
                'version' => $package['Version'] ?? null,
                'hash' => hash_file('sha256', $file),
                'path' => $file,
            ];
        }

        return $packages;
    }

    private function assertRepositoryExists(string $repositoryId): void
    {
        if ($this->fetchOne('SELECT 1 FROM CasRps WHERE CasRpsCod = :id LIMIT 1', [':id' => $repositoryId]) === null) {
            throw new RuntimeException("Repositório {$repositoryId} não existe na base SAAS.");
        }
    }

    private function resolvePackagePathByHash(string $hash): string
    {
        $hash = strtolower(trim($hash));
        if (!preg_match('/^[a-f0-9]{64}$/', $hash)) {
            throw new RuntimeException('Hash de pacote inválido.');
        }

        foreach ($this->packageFiles() as $file) {
            if (hash_equals($hash, hash_file('sha256', $file))) {
                return $file;
            }
        }

        throw new RuntimeException('Pacote ContractFlow não localizado para o hash informado.');
    }

    private function packageFiles(): array
    {
        $basePath = realpath(Config::getPathRules('Contract'));
        if ($basePath === false || !is_dir($basePath)) {
            throw new RuntimeException('O diretório de regras do ContractFlow não existe.');
        }

        $files = glob($basePath . DIRECTORY_SEPARATOR . 'Settings_*.json') ?: [];
        sort($files);

        return array_values(array_filter($files, function (string $file) use ($basePath): bool {
            $real = realpath($file);
            return $real !== false && str_starts_with($real, $basePath . DIRECTORY_SEPARATOR);
        }));
    }

    private function loadPackage(string $path): array
    {
        $content = file_get_contents($path);
        if ($content === false) {
            throw new RuntimeException('Não foi possível ler o pacote ContractFlow.');
        }

        try {
            $data = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new RuntimeException('Pacote ContractFlow com JSON inválido.', 0, $exception);
        }

        if (!is_array($data)) {
            throw new RuntimeException('Pacote ContractFlow com estrutura inválida.');
        }

        return $data;
    }
}
