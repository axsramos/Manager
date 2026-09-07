<?php

namespace App\Class\Manager;

use App\Class\Contract\ApplyContractSettings;
use App\Core\AuthSession;
use App\Models\CAS\CasAppModel;
use App\Models\CAS\CasRpaModel;
use App\Models\CAS\CasRpsModel;
use App\Models\CAS\CasTknModel;
use RuntimeException;
use Throwable;

class ApplicationActivationService
{
    public function __construct(
        private readonly ApplicationCatalogService $catalog = new ApplicationCatalogService(),
        private readonly ApplicationPackageLocator $packages = new ApplicationPackageLocator(),
        private readonly ApplicationActivationTokenService $tokens = new ApplicationActivationTokenService(),
        private readonly ApplicationActivationCheckpointService $checkpoints = new ApplicationActivationCheckpointService()
    ) {
    }

    public function activate(string $identifier, ?string $token = null): array
    {
        try {
            $repositoryId = $this->repositoryId();
            $product = $this->product($identifier);
            $packageInfo = $this->packages->locate($product);
            $package = $packageInfo['data'];
            $packageHash = $packageInfo['hash'];
            $applicationId = (string) ($package['ProductKey'] ?? $product['product_key'] ?? $product['app_id'] ?? '');

            $this->assertRepositoryExists($repositoryId);
            $this->checkpoints->assertCanApply($repositoryId, $applicationId, $packageHash);
            $tokenModel = $this->validateRightOfUse($repositoryId, $applicationId, $product, $token);

            $application = $this->ensureApplicationExists($applicationId, $package, $product);
            $this->ensureRepositoryApplication($repositoryId, $applicationId, $application, $product);

            $applyResult = $this->applyPackage($repositoryId, $applicationId, $package, $packageHash);
            $status = (string) ($applyResult['status'] ?? 'applied');

            $this->checkpoints->save($repositoryId, $applicationId, $product, $package, $packageHash, $status, $applyResult);

            if ($tokenModel instanceof CasTknModel) {
                $this->tokens->block($tokenModel);
            }

            return [
                'status' => $status,
                'message' => $status === 'warning'
                    ? 'Aplicativo ativado com avisos. Revise os detalhes antes do primeiro uso.'
                    : 'Aplicativo ativado com sucesso.',
                'product' => $product,
                'application_id' => $applicationId,
                'repository_id' => $repositoryId,
                'success_url' => (string) ($product['success_url'] ?? $product['launch_url'] ?? ''),
                'details' => $applyResult,
            ];
        } catch (Throwable $exception) {
            return [
                'status' => 'error',
                'message' => $exception->getMessage(),
            ];
        }
    }

    private function repositoryId(): string
    {
        $session = AuthSession::get();
        if (($session['USR_LOGGED'] ?? 'anonymous') === 'anonymous') {
            throw new RuntimeException('É necessário autenticar-se para ativar aplicativos.');
        }

        $repositoryId = trim((string) ($session['RPS_ID'] ?? ''));
        if ($repositoryId === '') {
            throw new RuntimeException('Repositório ativo não identificado na sessão.');
        }

        return $repositoryId;
    }

    private function product(string $identifier): array
    {
        $product = $this->catalog->findProduct($identifier);
        if ($product === null) {
            throw new RuntimeException('Produto não encontrado no catálogo.');
        }

        return $product;
    }

    private function assertRepositoryExists(string $repositoryId): void
    {
        $repository = new CasRpsModel();
        $repository->setSelectedFields(['CasRpsCod']);
        $repository->CasRpsCod = $repositoryId;

        if (! $repository->readRegister()) {
            throw new RuntimeException('Repositório inválido para ativação do aplicativo.');
        }
    }

    private function ensureApplicationExists(string $applicationId, array $package, array $product): CasAppModel
    {
        $applicationData = $this->packageApplicationData($applicationId, $package);
        $application = new CasAppModel();
        $application->setSelectedFields(CasAppModel::FIELDS);
        $application->CasAppCod = $applicationId;

        if (! $application->readRegister()) {
            $application->CasAppDsc = (string) ($applicationData['CasAppDsc'] ?? $package['ProductName'] ?? $product['title'] ?? $applicationId);
            $application->CasAppObs = (string) ($applicationData['CasAppObs'] ?? $package['Description'] ?? $product['description'] ?? '');
            $application->CasAppBlq = (string) ($applicationData['CasAppBlq'] ?? 'N');
            $application->CasAppTst = (string) ($applicationData['CasAppTst'] ?? 'N');
            $application->CasAppVer = (string) ($applicationData['CasAppVer'] ?? $package['Version'] ?? $product['package_version'] ?? '');
            $application->CasAppVerLnk = (string) ($product['learn_more_url'] ?? '');
            $application->CasAppKey = (string) ($applicationData['CasAppKey'] ?? $applicationId);
            $application->CasAppKeyExp = null;
            $application->CasAppGrp = (string) ($applicationData['CasAppGrp'] ?? $applicationId);

            if (! $application->createRegister()) {
                throw new RuntimeException('Não foi possível cadastrar o aplicativo em CasApp.');
            }

            $application = new CasAppModel();
            $application->setSelectedFields(CasAppModel::FIELDS);
            $application->CasAppCod = $applicationId;
            if (! $application->readRegister()) {
                throw new RuntimeException('Aplicativo cadastrado, mas não foi possível recarregar CasApp.');
            }
        }

        if ($application->CasAppBlq === 'S') {
            throw new RuntimeException('Aplicativo bloqueado em CasApp.');
        }

        $packageVersion = (string) ($package['Version'] ?? '');
        if ($packageVersion !== '' && (string) $application->CasAppVer !== '' && ! hash_equals((string) $application->CasAppVer, $packageVersion)) {
            throw new RuntimeException('Versão do aplicativo em CasApp incompatível com o pacote técnico.');
        }

        return $application;
    }

    private function packageApplicationData(string $applicationId, array $package): array
    {
        foreach (($package['BaseTableData'] ?? []) as $dataTable) {
            if (! is_array($dataTable) || ! isset($dataTable['CasApp']) || ! is_array($dataTable['CasApp'])) {
                continue;
            }

            foreach ($dataTable['CasApp'] as $record) {
                if (! is_array($record)) {
                    continue;
                }

                if (hash_equals($applicationId, (string) ($record['CasAppCod'] ?? ''))) {
                    return $record;
                }
            }
        }

        return [];
    }

    private function validateRightOfUse(string $repositoryId, string $applicationId, array $product, ?string $token): ?CasTknModel
    {
        if ($this->hasActiveRepositoryApplication($repositoryId, $applicationId)) {
            return null;
        }

        $mode = (string) ($product['activation_mode'] ?? 'external');
        if (in_array($mode, ['free', 'contract'], true)) {
            return null;
        }

        if ($mode === 'token') {
            return $this->tokens->validate((string) $token, $repositoryId, $applicationId);
        }

        throw new RuntimeException('Este produto ainda não possui ativação automática disponível.');
    }

    private function hasActiveRepositoryApplication(string $repositoryId, string $applicationId): bool
    {
        $relation = new CasRpaModel();
        $relation->setSelectedFields(['CasRpsCod', 'CasAppCod', 'CasRpaBlq']);
        $relation->CasRpsCod = $repositoryId;
        $relation->CasAppCod = $applicationId;

        return $relation->readRegister() && $relation->CasRpaBlq === 'N';
    }

    private function ensureRepositoryApplication(string $repositoryId, string $applicationId, CasAppModel $application, array $product): void
    {
        $relation = new CasRpaModel();
        $relation->setSelectedFields(CasRpaModel::FIELDS);
        $relation->CasRpsCod = $repositoryId;
        $relation->CasAppCod = $applicationId;

        if ($relation->readRegister()) {
            if ($relation->CasRpaBlq === 'S') {
                $relation->CasRpaBlq = 'N';
                $relation->CasRpaBlqDtt = null;
                $relation->updateRegister();
            }
            return;
        }

        $relation->CasRpaDsc = (string) ($application->CasAppDsc ?: $product['title'] ?? $applicationId);
        $relation->CasRpaObs = 'Ativado pela loja de aplicativos.';
        $relation->CasRpaBlq = 'N';
        $relation->CasRpaBlqDtt = null;
        $relation->CasRpaGrp = (string) ($application->CasAppGrp ?: $applicationId);
        $relation->createRegister();
    }

    private function applyPackage(string $repositoryId, string $applicationId, array $package, string $packageHash): array
    {
        $results = [];
        $status = 'applied';

        foreach ($this->runScripts($package) as $runScript) {
            $result = $this->applyRunScript($runScript, $repositoryId, $applicationId, $package, $packageHash);
            $results[$runScript] = $result;

            if (($result['status'] ?? 'applied') === 'warning') {
                $status = 'warning';
            }
        }

        return [
            'status' => $status,
            'message' => $status === 'warning'
                ? 'Pacote técnico aplicado com avisos.'
                : 'Pacote técnico aplicado.',
            'scripts' => $results,
        ];
    }

    private function runScripts(array $package): array
    {
        $scripts = $package['RunScripts'] ?? $package['run_scripts'] ?? null;
        if ($scripts === null) {
            return [(string) ($package['RunScript'] ?? '')];
        }

        if (! is_array($scripts) || $scripts === []) {
            throw new RuntimeException('RunScripts do pacote técnico deve ser uma lista não vazia.');
        }

        $normalized = [];
        foreach ($scripts as $script) {
            if (! is_string($script)) {
                throw new RuntimeException('RunScripts do pacote técnico contém aplicador inválido.');
            }

            $script = trim($script);
            if ($script === '') {
                throw new RuntimeException('RunScripts do pacote técnico contém aplicador inválido.');
            }

            $normalized[] = $script;
        }

        return $normalized;
    }

    private function applyRunScript(string $runScript, string $repositoryId, string $applicationId, array $package, string $packageHash): array
    {
        return match ($runScript) {
            'ApplyApplicationSettings' => (new ApplyApplicationSettings())->runWithPackage($repositoryId, $applicationId, $package),
            'ApplyContractSettings' => (new ApplyContractSettings())->run($repositoryId, $package, $packageHash),
            default => throw new RuntimeException('RunScript do pacote técnico não permitido.'),
        };
    }
}
