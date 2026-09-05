<?php

namespace App\Metadata\CAS;

use App\Metadata\AudMD;
use App\Metadata\CAS\CasRpsMD;

class CasTusMD
{
    public const LOGICAL_EXCLUSION = false;
    public const FIELDS_PK = ['CasRpsCod', 'CasTusCod'];
    public const FIELDS_FK = array(
        array('FKCasTus01' => array('FieldsKey'=>['CasRpsCod'], 'References' => 'CasRps', 'Fields' => ['CasRpsCod'])),
    );
    public const TABLE_IDX = array(
        array('IXCasTus01' => array('CasRpsCod')),
        array('IXCasTus02' => array('CasRpsCod', 'CasTusGrp', 'CasTusDsc')),
        array('IXCasTus03' => array('CasRpsCod', 'CasTusBlq', 'CasTusDsc')),
    );
    public const FIELDS_REQUIRED = ['CasTusDsc', 'CasTusBlq', 'CasTusGrp'];
    public const FIELDS_AUDIT = [
        'CasTusAudIns',
        'CasTusAudUpd',
        'CasTusAudDlt',
        'CasTusAudUsr',
    ];
    public const FIELDS = [
        // primary key //
        'CasRpsCod',
        'CasTusCod',
        // fields //
        'CasTusDsc',
        'CasTusObs',
        'CasTusBlq',
        'CasTusBlqDtt',
        'CasTusLnk',
        'CasTusGrp',
        // audit fields //
        'CasTusAudIns',
        'CasTusAudUpd',
        'CasTusAudDlt',
        'CasTusAudUsr',
    ];
    public const FIELDS_MD = [
        // primary key //
        'CasRpsCod' => CasRpsMD::FIELDS_MD['CasRpsCod'],
        'CasTusCod' => [
            'Type' => 'string',
            'Length' => 65,
            'Required' => true,
            'Default' => null,
            'LongLabel' => 'Código do Tipo de Usuário',
            'ShortLabel' => 'Cód.Tus',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        // fields //
        'CasTusDsc' => [
            'Type' => 'string',
            'Length' => 255,
            'Required' => true,
            'Default' => null,
            'LongLabel' => 'Tipo de Usuário',
            'ShortLabel' => 'Tipo',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasTusObs' => [
            'Type' => 'string',
            'Length' => 999,
            'Required' => false,
            'Default' => null,
            'LongLabel' => 'Observações',
            'ShortLabel' => 'Observações',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasTusBlq' => [
            'Type' => 'boolean',
            'Length' => 1,
            'Required' => true,
            'Default' => 'N',
            'LongLabel' => 'Bloqueado',
            'ShortLabel' => 'Bloqueado',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasTusBlqDtt' => [
            'Type' => 'datetime',
            'Length' => 0,
            'Required' => false,
            'Default' => null,
            'LongLabel' => 'Data de Bloqueio',
            'ShortLabel' => 'Bloqueio',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasTusLnk' => [
            'Type' => 'string',
            'Length' => 999,
            'Required' => false,
            'Default' => null,
            'LongLabel' => 'Link',
            'ShortLabel' => 'Link',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasTusGrp' => [
            'Type' => 'string',
            'Length' => 65,
            'Required' => true,
            'Default' => null,
            'LongLabel' => 'Grupo',
            'ShortLabel' => 'Grupo',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        // audit fields //
        'CasTusAudIns' => AudMD::Audit_MD['AudIns'],
        'CasTusAudUpd' => AudMD::Audit_MD['AudUpd'],
        'CasTusAudDlt' => AudMD::Audit_MD['AudDlt'],
        'CasTusAudUsr' => AudMD::Audit_MD['AudUsr'],
    ];
    // Foreign //
    public const FIELDS_FOREIGN = [
        'CasRps' => ['FIELDS' => CasRpsMD::FIELDS],
    ];
    public const FIELDS_MD_FOREIGN = [
        'CasRps' => ['FIELDS_MD' => CasRpsMD::FIELDS_MD],
    ];
}
