<?php

namespace App\Class\Manager;

use App\Core\Config;
use App\Models\CAS\CasRpsModel;
use App\Models\CAS\CasRpuModel;
use App\Models\CAS\CasTusModel;
use App\Models\CAS\CasUsrModel;
use RuntimeException;

class CreateUserRepositoryAccount
{
    /**
     * @param array $dataAccount FirstName, LastName, Account, Password (hashed) and optional USR_LOGGED/Nickname.
     * @param array $typeDefinitions UserTypeDomain-compatible definitions indexed by description.
     * @param array|null $supportContext user_id and type_id for the support relationship.
     */
    public function run(
        array $dataAccount,
        string $repositoryId,
        string $userId,
        array $typeDefinitions,
        string $ownerTypeId,
        ?array $supportContext = null
    ): array {
        $dataAccount = $this->validateAccount($dataAccount);
        $this->validateIdentifiers($repositoryId, $userId, $ownerTypeId);

        $result = [
            'user' => $this->createUser($dataAccount, $userId),
            'repository' => $this->createRepository($dataAccount, $repositoryId),
            'types' => $this->ensureUserTypes($repositoryId, $typeDefinitions),
        ];

        if (! in_array($ownerTypeId, array_column($typeDefinitions, 'CasTusCod'), true)) {
            throw new RuntimeException('O tipo proprietário não pertence ao domínio informado.');
        }

        $result['owner_relation'] = $this->associateOwner(
            $repositoryId,
            $userId,
            $ownerTypeId,
            $dataAccount['Account']
        );
        $result['directory'] = $this->ensureRepositoryDirectory($repositoryId);

        if ($supportContext !== null && ($supportContext['user_id'] ?? '') !== $userId) {
            $supportUserId = (string) ($supportContext['user_id'] ?? '');
            $supportTypeId = (string) ($supportContext['type_id'] ?? '');
            if ($supportUserId === '' || $supportTypeId === '') {
                throw new RuntimeException('O contexto de suporte deve informar user_id e type_id.');
            }

            $result['support_relation'] = (new CreateUserSupportClass())->run(
                $repositoryId,
                $supportUserId,
                $supportTypeId
            );
        } else {
            $result['support_relation'] = ['status' => 'skipped'];
        }

        return $result;
    }

    private function validateAccount(array $dataAccount): array
    {
        foreach (['FirstName', 'LastName', 'Account', 'Password'] as $field) {
            if (! isset($dataAccount[$field]) || trim((string) $dataAccount[$field]) === '') {
                throw new RuntimeException("O campo {$field} é obrigatório para criar a conta.");
            }
        }

        $dataAccount['FirstName'] = trim((string) $dataAccount['FirstName']);
        $dataAccount['LastName'] = trim((string) $dataAccount['LastName']);
        $dataAccount['Account'] = strtolower(trim((string) $dataAccount['Account']));
        $dataAccount['Password'] = trim((string) $dataAccount['Password']);

        if (filter_var($dataAccount['Account'], FILTER_VALIDATE_EMAIL) === false) {
            throw new RuntimeException('A conta deve ser um endereço de e-mail válido.');
        }

        $dataAccount['USR_LOGGED'] = trim((string) ($dataAccount['USR_LOGGED']
            ?? ($dataAccount['FirstName'] . ' ' . $dataAccount['LastName'])));
        $dataAccount['Nickname'] = trim((string) ($dataAccount['Nickname'] ?? $dataAccount['FirstName']));

        return $dataAccount;
    }

    private function validateIdentifiers(string ...$identifiers): void
    {
        foreach ($identifiers as $identifier) {
            if ($identifier === '' || strlen($identifier) > 65) {
                throw new RuntimeException('Foi informado um identificador vazio ou maior que 65 caracteres.');
            }
        }
    }

    private function createUser(array $dataAccount, string $userId): string
    {
        [$login, $domain] = explode('@', $dataAccount['Account'], 2);
        $domain = '@' . $domain;

        $byEmail = new CasUsrModel();
        $byEmail->CasUsrDmn = $domain;
        $byEmail->CasUsrLgn = $login;
        if ($byEmail->existsMailAccount()) {
            if ($byEmail->CasUsrCod !== $userId) {
                throw new RuntimeException('O e-mail informado já pertence a outro ID de usuário.');
            }

            $existing = new CasUsrModel();
            $existing->setSelectedFields([
                'CasUsrCod',
                'CasUsrNme',
                'CasUsrSnm',
                'CasUsrNck',
                'CasUsrDsc',
                'CasUsrDmn',
                'CasUsrLgn',
                'CasUsrPwd',
            ]);
            $existing->CasUsrCod = $userId;
            if (! $existing->readRegister()
                || $existing->CasUsrNme !== $dataAccount['FirstName']
                || $existing->CasUsrSnm !== $dataAccount['LastName']
                || $existing->CasUsrNck !== $dataAccount['Nickname']
                || $existing->CasUsrDsc !== $dataAccount['USR_LOGGED']
                || $existing->CasUsrDmn !== $domain
                || $existing->CasUsrLgn !== $login
                || $existing->CasUsrPwd !== $dataAccount['Password']) {
                throw new RuntimeException('O usuário existente possui dados incompatíveis com a conta informada.');
            }

            return 'existing';
        }

        $byId = new CasUsrModel();
        $byId->setSelectedFields(['CasUsrCod', 'CasUsrDmn', 'CasUsrLgn']);
        $byId->CasUsrCod = $userId;
        if ($byId->readRegister()) {
            throw new RuntimeException('O ID de usuário informado já pertence a outro e-mail.');
        }

        $user = new CasUsrModel();
        $user->CasUsrCod = $userId;
        $user->CasUsrNme = $dataAccount['FirstName'];
        $user->CasUsrSnm = $dataAccount['LastName'];
        $user->CasUsrNck = $dataAccount['Nickname'];
        $user->CasUsrDsc = $dataAccount['USR_LOGGED'];
        $user->CasUsrDmn = $domain;
        $user->CasUsrLgn = $login;
        $user->CasUsrPwd = $dataAccount['Password'];
        $user->CasUsrActDtt = $dataAccount['ActivatedAt'] ?? null;
        $user->CasUsrBlq = 'N';

        if (! $user->createRegister()) {
            throw new RuntimeException('Não foi possível criar o usuário.');
        }

        return 'created';
    }

    private function createRepository(array $dataAccount, string $repositoryId): string
    {
        $repository = new CasRpsModel();
        $repository->setSelectedFields(['CasRpsCod', 'CasRpsDsc', 'CasRpsBlq', 'CasRpsGrp']);
        $repository->CasRpsCod = $repositoryId;
        if ($repository->readRegister()) {
            if ($repository->CasRpsDsc !== $dataAccount['Account'] || $repository->CasRpsGrp !== $repositoryId) {
                throw new RuntimeException('O repositório existente é incompatível com a conta informada.');
            }

            return 'existing';
        }

        $repository->CasRpsDsc = $dataAccount['Account'];
        $repository->CasRpsBlq = 'N';
        $repository->CasRpsGrp = $repositoryId;
        if (! $repository->createRegister()) {
            throw new RuntimeException('Não foi possível criar o repositório.');
        }

        return 'created';
    }

    private function ensureUserTypes(string $repositoryId, array $typeDefinitions): array
    {
        foreach (UserTypeDomain::DESCRIPTIONS as $description) {
            if (! isset($typeDefinitions[$description])) {
                throw new RuntimeException("O domínio não contém o tipo obrigatório {$description}.");
            }
        }

        $result = [];
        foreach ($typeDefinitions as $description => $definition) {
            $typeId = trim((string) ($definition['CasTusCod'] ?? ''));
            if ($typeId === '' || ($definition['CasTusDsc'] ?? '') !== $description) {
                throw new RuntimeException("Definição inválida para o tipo {$description}.");
            }

            $type = new CasTusModel();
            $type->setSelectedFields(['CasRpsCod', 'CasTusCod', 'CasTusDsc', 'CasTusGrp', 'CasTusLnk', 'CasTusBlq']);
            $type->CasRpsCod = $repositoryId;
            $type->CasTusCod = $typeId;
            if ($type->readRegister()) {
                if ($type->CasTusDsc !== $description || $type->CasTusGrp !== $typeId) {
                    throw new RuntimeException("O tipo {$description} existente possui conteúdo conflitante.");
                }
                $result[$description] = 'existing';
                continue;
            }

            $type->CasTusDsc = $description;
            $type->CasTusBlq = (string) ($definition['CasTusBlq'] ?? 'N');
            $type->CasTusLnk = (string) ($definition['CasTusLnk'] ?? '/Home');
            $type->CasTusGrp = $typeId;
            if (! $type->createRegister()) {
                throw new RuntimeException("Não foi possível criar o tipo {$description}.");
            }
            $result[$description] = 'created';
        }

        return $result;
    }

    private function associateOwner(
        string $repositoryId,
        string $userId,
        string $ownerTypeId,
        string $description
    ): string {
        $relation = new CasRpuModel();
        $relation->setSelectedFields(['CasRpsCod', 'CasUsrCod', 'CasTusCod', 'CasRpuDsc', 'CasRpuBlq']);
        $relation->CasRpsCod = $repositoryId;
        $relation->CasUsrCod = $userId;
        if ($relation->readRegister()) {
            if ($relation->CasTusCod !== $ownerTypeId) {
                throw new RuntimeException('A relação proprietário/repositório possui outro tipo de usuário.');
            }

            return 'existing';
        }

        $relation->CasTusCod = $ownerTypeId;
        $relation->CasRpuDsc = $description;
        $relation->CasRpuBlq = 'N';
        if (! $relation->createRegister()) {
            throw new RuntimeException('Não foi possível associar o proprietário ao repositório.');
        }

        return 'created';
    }

    private function ensureRepositoryDirectory(string $repositoryId): string
    {
        if (! preg_match('/^[A-Za-z0-9._-]+$/', $repositoryId)) {
            throw new RuntimeException('O ID do repositório não é seguro para uso como diretório.');
        }

        $basePath = rtrim(Config::getPathRepositories(), '/\\');
        if (! is_dir($basePath) && ! mkdir($basePath, 0777, true) && ! is_dir($basePath)) {
            throw new RuntimeException("Não foi possível criar o diretório-base {$basePath}.");
        }

        $resolvedBase = realpath($basePath);
        if ($resolvedBase === false) {
            throw new RuntimeException('Não foi possível resolver o diretório-base de repositórios.');
        }

        $targetPath = $resolvedBase . DIRECTORY_SEPARATOR . $repositoryId;
        if (is_dir($targetPath)) {
            return 'existing';
        }

        if (! mkdir($targetPath, 0777, true) && ! is_dir($targetPath)) {
            throw new RuntimeException("Não foi possível criar o diretório {$targetPath}.");
        }

        $resolvedTarget = realpath($targetPath);
        if ($resolvedTarget === false || ! str_starts_with($resolvedTarget, $resolvedBase . DIRECTORY_SEPARATOR)) {
            throw new RuntimeException('O diretório de repositório foi criado fora da raiz permitida.');
        }

        return 'created';
    }
}
