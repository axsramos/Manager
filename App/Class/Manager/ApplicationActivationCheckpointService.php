<?php

namespace App\Class\Manager;

use App\Models\CAS\CasParModel;
use DateTimeImmutable;
use RuntimeException;

class ApplicationActivationCheckpointService
{
    public function read(string $repositoryId, string $applicationId): ?array
    {
        $checkpoint = new CasParModel();
        $checkpoint->CasRpsCod = $repositoryId;
        $checkpoint->CasParCod = $this->code($applicationId);

        if (! $checkpoint->readRegister()) {
            return null;
        }

        $data = json_decode((string) $checkpoint->CasParTxt, true);

        return is_array($data) ? $data : [];
    }

    public function assertCanApply(string $repositoryId, string $applicationId, string $packageHash): void
    {
        $checkpoint = $this->read($repositoryId, $applicationId);
        if ($checkpoint === null) {
            return;
        }

        if (($checkpoint['Status'] ?? '') !== 'applied') {
            return;
        }

        $currentHash = (string) ($checkpoint['PackageHash'] ?? '');
        if ($currentHash !== '' && ! hash_equals($currentHash, $packageHash)) {
            throw new RuntimeException('Existe checkpoint com pacote diferente. Use um fluxo de atualização controlado.');
        }
    }

    public function save(string $repositoryId, string $applicationId, array $product, array $package, string $packageHash, string $status, array $result = []): void
    {
        $content = [
            'RepositoryId' => $repositoryId,
            'ApplicationId' => $applicationId,
            'ProductKey' => (string) ($package['ProductKey'] ?? $product['product_key'] ?? ''),
            'Version' => (string) ($package['Version'] ?? $product['package_version'] ?? ''),
            'Status' => $status,
            'LastUpdated' => (new DateTimeImmutable('now'))->format('Y-m-d H:i:s'),
            'PackageHash' => $packageHash,
            'RunScript' => (string) ($package['RunScript'] ?? $product['run_script'] ?? ''),
            'RunScripts' => $package['RunScripts'] ?? $package['run_scripts'] ?? $product['run_scripts'] ?? null,
            'Result' => $result,
        ];

        $checkpoint = new CasParModel();
        $checkpoint->CasRpsCod = $repositoryId;
        $checkpoint->CasParCod = $this->code($applicationId);
        $checkpoint->CasParTbl = '_blank';
        $checkpoint->CasParDsc = 'Ativação de aplicativo';
        $checkpoint->CasParBlq = 'N';
        $checkpoint->CasParGrp = $applicationId;
        $checkpoint->CasParTxt = json_encode($content, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        if ($checkpoint->CasParTxt === false) {
            throw new RuntimeException('Não foi possível serializar o checkpoint de ativação.');
        }

        if ($checkpoint->readRegister()) {
            $checkpoint->CasParTbl = '_blank';
            $checkpoint->CasParDsc = 'Ativação de aplicativo';
            $checkpoint->CasParBlq = 'N';
            $checkpoint->CasParGrp = $applicationId;
            $checkpoint->CasParTxt = json_encode($content, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $checkpoint->updateRegister();
            return;
        }

        $checkpoint->createRegister();
    }

    private function code(string $applicationId): string
    {
        return substr($applicationId, 0, 36) . '-ACTIVATION';
    }
}
