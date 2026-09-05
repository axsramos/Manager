<?php

namespace App\Metadata\CAS;

use App\Metadata\AudMD;

class CasFunMD
{
    public const LOGICAL_EXCLUSION = false;
    public const FIELDS_PK = ['CasRpsCod','CasFunCod'];
    public const FIELDS_FK = array(
        array('FKCasFun01' => array('FieldsKey'=>['CasRpsCod'], 'References' => 'CasRps', 'Fields' => ['CasRpsCod'])),
    );
    public const TABLE_IDX = array(
        array('IXCasFun01' => array('CasRpsCod')),
        array('IXCasFun02' => array('CasRpsCod', 'CasFunBlq', 'CasFunDsc')),
    );
    public const FIELDS_REQUIRED = ['CasFunDsc', 'CasFunBlq'];
    public const FIELDS_AUDIT = [
        'CasFunAudIns',
        'CasFunAudUpd',
        'CasFunAudDlt',
        'CasFunAudUsr',
    ];
    public const FIELDS = [
        // primary key //
        'CasRpsCod',
        'CasFunCod',
        // fields //
        'CasFunDsc',
        'CasFunBlq',
        'CasFunBlqDtt',
        // audit fields //
        'CasFunAudIns',
        'CasFunAudUpd',
        'CasFunAudDlt',
        'CasFunAudUsr',
    ];
    public const FIELDS_MD = [
        // primary key //
        'CasRpsCod' => CasRpsMD::FIELDS_MD['CasRpsCod'],
        'CasFunCod' => [
            'Type' => 'string',
            'Length' => 65,
            'Required' => true,
            'Default' => null,
            'LongLabel' => 'Código da Funcionalidade',
            'ShortLabel' => 'Cód.Fun',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        // fields //
        'CasFunDsc' => [
            'Type' => 'string',
            'Length' => 255,
            'Required' => true,
            'Default' => null,
            'LongLabel' => 'Funcionalidade',
            'ShortLabel' => 'Funcionalidade',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasFunBlq' => [
            'Type' => 'boolean',
            'Length' => 1,
            'Required' => true,
            'Default' => 'N',
            'LongLabel' => 'Bloqueado',
            'ShortLabel' => 'Bloqueado',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasFunBlqDtt' => [
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
        'CasFunAudIns' => AudMD::Audit_MD['AudIns'],
        'CasFunAudUpd' => AudMD::Audit_MD['AudUpd'],
        'CasFunAudDlt' => AudMD::Audit_MD['AudDlt'],
        'CasFunAudUsr' => AudMD::Audit_MD['AudUsr'],
    ];
    // Foreign //
    public const FIELDS_FOREIGN = [
        'CasRps' => ['FIELDS' => CasRpsMD::FIELDS],
    ];
    public const FIELDS_MD_FOREIGN = [
        'CasRps' => ['FIELDS_MD' => CasRpsMD::FIELDS_MD],
    ];
}
