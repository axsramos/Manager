<?php

namespace App\Metadata\CAS;

use App\Metadata\AudMD;

class CasRpsMD
{
    public const LOGICAL_EXCLUSION = false;
    public const FIELDS_PK = ['CasRpsCod'];
    public const FIELDS_FK = [];
    public const TABLE_IDX = array(
        array('IXCasRps01' => array('CasRpsAudIns')),
        array('IXCasRps02' => array('CasRpsGrp', 'CasRpsDsc')),
        array('IXCasRps03' => array('CasRpsBlq', 'CasRpsDsc')),
    );
    public const FIELDS_REQUIRED = ['CasRpsDsc', 'CasRpsBlq', 'CasRpsGrp'];
    public const FIELDS_AUDIT = [
        'CasRpsAudIns',
        'CasRpsAudUpd',
        'CasRpsAudDlt',
        'CasRpsAudUsr',
    ];
    public const FIELDS = [
        // primary key //
        'CasRpsCod',
        // fields //
        'CasRpsDsc',
        'CasRpsObs',
        'CasRpsBlq',
        'CasRpsBlqDtt',
        'CasRpsGrp',
        // audit fields //
        'CasRpsAudIns',
        'CasRpsAudUpd',
        'CasRpsAudDlt',
        'CasRpsAudUsr',
    ];
    public const FIELDS_MD = [
        // primary key //
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
        // fields //
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
        'CasRpsObs' => [
            'Type' => 'string',
            'Length' => 999,
            'Required' => false,
            'Default' => null,
            'LongLabel' => 'Observações',
            'ShortLabel' => 'Observações',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasRpsBlq' => [
            'Type' => 'boolean',
            'Length' => 1,
            'Required' => true,
            'Default' => 'N',
            'LongLabel' => 'Bloqueado',
            'ShortLabel' => 'Bloqueado',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasRpsBlqDtt' => [
            'Type' => 'datetime',
            'Length' => 0,
            'Required' => false,
            'Default' => null,
            'LongLabel' => 'Data de Bloqueio',
            'ShortLabel' => 'Bloqueio',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasRpsGrp' => [
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
        'CasRpsAudIns' => AudMD::Audit_MD['AudIns'],
        'CasRpsAudUpd' => AudMD::Audit_MD['AudUpd'],
        'CasRpsAudDlt' => AudMD::Audit_MD['AudDlt'],
        'CasRpsAudUsr' => AudMD::Audit_MD['AudUsr'],
    ];
    public const FIELDS_FOREIGN = [];
    public const FIELDS_MD_FOREIGN = [];
}
