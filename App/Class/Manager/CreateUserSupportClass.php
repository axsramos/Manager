<?php

namespace App\Class\Manager;

use App\Models\CAS\CasRpsModel;
use App\Models\CAS\CasRpuModel;
use App\Models\CAS\CasTusModel;
use App\Models\CAS\CasUsrModel;
use RuntimeException;

class CreateUserSupportClass
{
    public function run(string $repositoryId, string $supportUserId, string $supportTypeId): array
    {
        $repository = new CasRpsModel();
        $repository->setSelectedFields(['CasRpsCod']);
        $repository->CasRpsCod = $repositoryId;
        if (! $repository->readRegister()) {
            throw new RuntimeException('O repositório informado para suporte não existe.');
        }

        $support = new CasUsrModel();
        $support->setSelectedFields(['CasUsrCod', 'CasUsrDsc']);
        $support->CasUsrCod = $supportUserId;
        if (! $support->readRegister()) {
            throw new RuntimeException('O usuário de suporte informado não existe.');
        }

        $typeStatus = $this->ensureSupportType($repositoryId, $supportTypeId);
        if ($repositoryId === $supportUserId) {
            return ['status' => 'skipped', 'type' => $typeStatus, 'reason' => 'own_repository'];
        }

        $relation = new CasRpuModel();
        $relation->setSelectedFields(['CasRpsCod', 'CasUsrCod', 'CasTusCod', 'CasRpuDsc', 'CasRpuBlq']);
        $relation->CasRpsCod = $repositoryId;
        $relation->CasUsrCod = $supportUserId;
        if ($relation->readRegister()) {
            if ($relation->CasTusCod !== $supportTypeId) {
                throw new RuntimeException('O suporte já está associado ao repositório com outro tipo de usuário.');
            }

            return ['status' => 'existing', 'type' => $typeStatus];
        }

        $relation->CasTusCod = $supportTypeId;
        $relation->CasRpuDsc = $support->CasUsrDsc;
        $relation->CasRpuBlq = 'N';
        if (! $relation->createRegister()) {
            throw new RuntimeException('Não foi possível associar o suporte ao repositório.');
        }

        return ['status' => 'created', 'type' => $typeStatus];
    }

    private function ensureSupportType(string $repositoryId, string $supportTypeId): string
    {
        $type = new CasTusModel();
        $type->setSelectedFields(['CasRpsCod', 'CasTusCod', 'CasTusDsc', 'CasTusGrp']);
        $type->CasRpsCod = $repositoryId;
        $type->CasTusCod = $supportTypeId;
        if ($type->readRegister()) {
            if ($type->CasTusDsc !== UserTypeDomain::SUPPORT || $type->CasTusGrp !== $supportTypeId) {
                throw new RuntimeException('O tipo de suporte existente possui conteúdo conflitante.');
            }

            return 'existing';
        }

        $type->CasTusDsc = UserTypeDomain::SUPPORT;
        $type->CasTusBlq = 'N';
        $type->CasTusLnk = '/Home';
        $type->CasTusGrp = $supportTypeId;
        if (! $type->createRegister()) {
            throw new RuntimeException('Não foi possível criar o tipo de suporte.');
        }

        return 'created';
    }
}
