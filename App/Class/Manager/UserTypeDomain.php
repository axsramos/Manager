<?php

namespace App\Class\Manager;

use InvalidArgumentException;

final class UserTypeDomain
{
    public const ADMINISTRATOR = 'ADMINISTRATOR ACCOUNT';
    public const MANAGER = 'MANAGER ACCOUNT';
    public const SUPPORT = 'SUPPORT ACCOUNT';
    public const USER = 'USER ACCOUNT';

    public const DESCRIPTIONS = [
        self::ADMINISTRATOR,
        self::MANAGER,
        self::SUPPORT,
        self::USER,
    ];

    public static function definitions(string $repositoryId): array
    {
        $repositoryId = trim($repositoryId);
        if ($repositoryId === '') {
            throw new InvalidArgumentException('O ID do repositório é obrigatório para gerar os tipos de usuário.');
        }

        $definitions = [];
        foreach (self::DESCRIPTIONS as $description) {
            $id = substr(hash('sha256', $repositoryId . '|' . $description), 0, 32);
            $definitions[$description] = [
                'CasTusCod' => $id,
                'CasTusDsc' => $description,
                'CasTusLnk' => '/Home',
                'CasTusBlq' => 'N',
            ];
        }

        return $definitions;
    }
}
