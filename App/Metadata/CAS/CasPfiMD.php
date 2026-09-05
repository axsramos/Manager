<?php

namespace App\Metadata\CAS;

use App\Metadata\AudMD;

class CasPfiMD
{
    public const LOGICAL_EXCLUSION = false;
    public const FIELDS_PK = ['CasRpsCod','CasPfiCod'];
    public const FIELDS_FK = array(
        array('FKCasPfi01' => array('FieldsKey'=>['CasRpsCod'], 'References' => 'CasRps', 'Fields' => ['CasRpsCod'])),
    );
    public const TABLE_IDX = array(
        array('IXCasPfi01' => array('CasRpsCod')),
        array('IXCasPfi02' => array('CasRpsCod', 'CasPfiBlq', 'CasPfiDsc')),
    );
    public const FIELDS_REQUIRED = ['CasPfiDsc', 'CasPfiBlq'];
    public const FIELDS_AUDIT = [
        'CasPfiAudIns',
        'CasPfiAudUpd',
        'CasPfiAudDlt',
        'CasPfiAudUsr',
    ];
    public const FIELDS = [
        // primary key //
        'CasRpsCod',
        'CasPfiCod',
        // fields //
        'CasPfiDsc',
        'CasPfiBlq',
        'CasPfiBlqDtt',
        // audit fields //
        'CasPfiAudIns',
        'CasPfiAudUpd',
        'CasPfiAudDlt',
        'CasPfiAudUsr',
    ];
    public const FIELDS_MD = [
        // primary key //
        'CasRpsCod' => CasRpsMD::FIELDS_MD['CasRpsCod'],
        'CasPfiCod' => [
            'Type' => 'string',
            'Length' => 65,
            'Required' => true,
            'Default' => null,
            'LongLabel' => 'Código da Perfil',
            'ShortLabel' => 'Cód.Fun',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        // fields //
        'CasPfiDsc' => [
            'Type' => 'string',
            'Length' => 255,
            'Required' => true,
            'Default' => null,
            'LongLabel' => 'Perfil',
            'ShortLabel' => 'Perfil',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasPfiBlq' => [
            'Type' => 'boolean',
            'Length' => 1,
            'Required' => true,
            'Default' => 'N',
            'LongLabel' => 'Bloqueado',
            'ShortLabel' => 'Bloqueado',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasPfiBlqDtt' => [
            'Type' => 'datetime',
            'Length' => 0,
            'Required' => false,
            'Default' => null,
            'LongLabel' => 'Data de Bloqueio',
            'ShortLabel' => 'Bloqueio',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        // audit fields //
        'CasPfiAudIns' => AudMD::Audit_MD['AudIns'],
        'CasPfiAudUpd' => AudMD::Audit_MD['AudUpd'],
        'CasPfiAudDlt' => AudMD::Audit_MD['AudDlt'],
        'CasPfiAudUsr' => AudMD::Audit_MD['AudUsr'],
    ];
    // Foreign //
    public const FIELDS_FOREIGN = [
        'CasRps' => ['FIELDS' => CasRpsMD::FIELDS],
    ];
    public const FIELDS_MD_FOREIGN = [
        'CasRps' => ['FIELDS_MD' => CasRpsMD::FIELDS_MD],
    ];
}
