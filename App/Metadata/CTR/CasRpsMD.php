<?php

namespace App\Metadata\CTR;

class CasRpsMD
{
    public const LOGICAL_EXCLUSION = false;
    public const FIELDS_PK = ['CasRpsCod'];
    public const FIELDS_FK = [];
    public const TABLE_IDX = [
        ['IXCasRps01' => ['CasRpsDsc']],
    ];
    public const TABLE_CHECK = [];
    public const FIELDS_REQUIRED = ['CasRpsCod', 'CasRpsDsc'];
    public const FIELDS = [
        'CasRpsCod',
        'CasRpsDsc',
    ];
    public const FIELDS_MD = [
        'CasRpsCod' => [
            'Type' => 'string',
            'Length' => 65,
            'Required' => true,
            'Default' => null,
            'LongLabel' => 'Código do Repositório',
            'ShortLabel' => 'Cód.Rps',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasRpsDsc' => [
            'Type' => 'string',
            'Length' => 255,
            'Required' => true,
            'Default' => null,
            'LongLabel' => 'Descrição do Repositório',
            'ShortLabel' => 'Repositório',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
    ];
    public const FIELDS_FOREIGN = [];
    public const FIELDS_MD_FOREIGN = [];
}
