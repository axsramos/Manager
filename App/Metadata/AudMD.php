<?php

namespace App\Metadata;

class AudMD
{
    public const Audit_MD = [
        'AudIns' => [
            'Type' => 'datetime',
            'Length' => 0,
            'Required' => true,
            'Default' => null,
            'LongLabel' => 'Data de Inclusão',
            'ShortLabel' => 'Inclusão',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'AudUpd' => [
            'Type' => 'datetime',
            'Length' => 0,
            'Required' => false,
            'Default' => null,
            'LongLabel' => 'Data de Atualização',
            'ShortLabel' => 'Atualização',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'AudDlt' => [
            'Type' => 'datetime',
            'Length' => 0,
            'Required' => false,
            'Default' => null,
            'LongLabel' => 'Data de Exclusão',
            'ShortLabel' => 'Exclusão',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'AudUsr' => [
            'Type' => 'string',
            'Length' => 255,
            'Required' => false,
            'Default' => null,
            'LongLabel' => 'Usuário que fez a operação',
            'ShortLabel' => 'Usuário',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
    ];
}
