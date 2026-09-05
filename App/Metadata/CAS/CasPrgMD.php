<?php

namespace App\Metadata\CAS;

use App\Metadata\AudMD;

class CasPrgMD
{
    public const LOGICAL_EXCLUSION = false;
    public const FIELDS_PK = ['CasRpsCod','CasPrgCod'];
    public const FIELDS_FK = array(
        array('FKCasPrg01' => array('FieldsKey'=>['CasRpsCod'], 'References' => 'CasRps', 'Fields' => ['CasRpsCod'])),
    );
    public const TABLE_IDX = array(
        array('IXCasPrg01' => array('CasRpsCod')),
        array('IXCasPrg02' => array('CasRpsCod', 'CasPrgBlq', 'CasPrgDsc')),
    );
    public const FIELDS_REQUIRED = ['CasPrgDsc', 'CasPrgBlq', 'CasPrgTst'];
    public const FIELDS_AUDIT = [
        'CasPrgAudIns',
        'CasPrgAudUpd',
        'CasPrgAudDlt',
        'CasPrgAudUsr',
    ];
    public const FIELDS = [
        // primary key //
        'CasRpsCod',
        'CasPrgCod',
        // fields //
        'CasPrgDsc',
        'CasPrgBlq',
        'CasPrgBlqDtt',
        'CasPrgTst',
        'CasPrgTstDtt',
        // audit fields //
        'CasPrgAudIns',
        'CasPrgAudUpd',
        'CasPrgAudDlt',
        'CasPrgAudUsr',
    ];
    public const FIELDS_MD = [
        // primary key //
        'CasRpsCod' => CasRpsMD::FIELDS_MD['CasRpsCod'],
        'CasPrgCod' => [
            'Type' => 'string',
            'Length' => 65,
            'Required' => true,
            'Default' => null,
            'LongLabel' => 'Código da Programa',
            'ShortLabel' => 'Cód.Prg',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        // fields //
        'CasPrgDsc' => [
            'Type' => 'string',
            'Length' => 255,
            'Required' => true,
            'Default' => null,
            'LongLabel' => 'Programa',
            'ShortLabel' => 'Programa',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasPrgBlq' => [
            'Type' => 'boolean',
            'Length' => 1,
            'Required' => true,
            'Default' => 'N',
            'LongLabel' => 'Bloqueado',
            'ShortLabel' => 'Bloqueado',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasPrgBlqDtt' => [
            'Type' => 'datetime',
            'Length' => 0,
            'Required' => false,
            'Default' => null,
            'LongLabel' => 'Data de Bloqueio',
            'ShortLabel' => 'Bloqueio',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasPrgTst' => [
            'Type' => 'boolean',
            'Length' => 1,
            'Required' => true,
            'Default' => 'N',
            'LongLabel' => 'Em Teste',
            'ShortLabel' => 'Em Teste',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasPrgTstDtt' => [
            'Type' => 'datetime',
            'Length' => 0,
            'Required' => false,
            'Default' => null,
            'LongLabel' => 'Data em Teste',
            'ShortLabel' => 'Em Teste',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        // audit fields //
        'CasPrgAudIns' => AudMD::Audit_MD['AudIns'],
        'CasPrgAudUpd' => AudMD::Audit_MD['AudUpd'],
        'CasPrgAudDlt' => AudMD::Audit_MD['AudDlt'],
        'CasPrgAudUsr' => AudMD::Audit_MD['AudUsr'],
    ];
    // Foreign //
    public const FIELDS_FOREIGN = [
        'CasRps' => ['FIELDS' => CasRpsMD::FIELDS],
    ];
    public const FIELDS_MD_FOREIGN = [
        'CasRps' => ['FIELDS_MD' => CasRpsMD::FIELDS_MD],
    ];
}
