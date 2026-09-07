<?php

namespace App\Class\Manager;

use App\Models\CAS\CasTknModel;
use DateTimeImmutable;
use RuntimeException;

class ApplicationActivationTokenService
{
    public function validate(string $token, string $repositoryId, string $applicationId): CasTknModel
    {
        $token = trim($token);
        if ($token === '') {
            throw new RuntimeException('Informe a chave de ativação para continuar.');
        }

        $tokenModel = new CasTknModel();
        if (! $tokenModel->findByKey($token)) {
            throw new RuntimeException('Chave de ativação não localizada.');
        }

        if ($tokenModel->CasTknBlq === 'S') {
            throw new RuntimeException('Chave de ativação já utilizada ou bloqueada.');
        }

        if (! empty($tokenModel->CasTknKeyExp)) {
            $expiresAt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', (string) $tokenModel->CasTknKeyExp);
            if ($expiresAt === false || new DateTimeImmutable('now') > $expiresAt) {
                throw new RuntimeException('Chave de ativação expirada.');
            }
        }

        $expectedDescription = $this->description($repositoryId, $applicationId);
        if (! hash_equals($expectedDescription, (string) $tokenModel->CasTknDsc)) {
            throw new RuntimeException('Chave de ativação incompatível com o repositório ou aplicativo selecionado.');
        }

        return $tokenModel;
    }

    public function block(CasTknModel $token): void
    {
        $token->setSelectedFields(['CasTknBlq', 'CasTknBlqDtt']);
        $token->CasTknBlq = 'S';
        $token->CasTknBlqDtt = (new DateTimeImmutable('now'))->format('Y-m-d H:i:s');
        $token->updateRegister();
    }

    public function description(string $repositoryId, string $applicationId): string
    {
        return 'APP_ACTIVATION:' . $repositoryId . ':' . $applicationId;
    }
}
