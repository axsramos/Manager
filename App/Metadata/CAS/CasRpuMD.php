<?php

namespace App\Metadata\CAS;

use App\Metadata\AudMD;

class CasRpuMD
{
    public const LOGICAL_EXCLUSION = false;
    public const FIELDS_PK = ['CasRpsCod','CasUsrCod'];
    public const FIELDS_FK = array(
        array('FKCasRpu01' => array('FieldsKey'=>['CasRpsCod'], 'References' => 'CasRps', 'Fields' => ['CasRpsCod'])),
        array('FKCasRpu02' => array('FieldsKey'=>['CasUsrCod'], 'References' => 'CasUsr', 'Fields' => ['CasUsrCod'])),
        array('FKCasRpu03' => array('FieldsKey'=>['CasRpsCod', 'CasTusCod'], 'References' => 'CasTus', 'Fields' => ['CasRpsCod', 'CasTusCod'])),
    );
    public const TABLE_IDX = array(
        array('IXCasRpu01' => array('CasRpsCod')),
        array('IXCasRpu02' => array('CasUsrCod')),
        array('IXCasRpu03' => array('CasRpsCod', 'CasTusCod')),
        array('IXCasRpu04' => array('CasRpsCod', 'CasRpuBlq', 'CasRpuDsc')),
    );
    public const FIELDS_REQUIRED = ['CasTusCod', 'CasRpuDsc', 'CasRpuBlq'];
    public const FIELDS_AUDIT = [
        'CasRpuAudIns',
        'CasRpuAudUpd',
        'CasRpuAudDlt',
        'CasRpuAudUsr',
    ];
    public const FIELDS = [
        // primary key //
        'CasRpsCod',
        'CasUsrCod',
        // fields //
        'CasTusCod',
        'CasRpuDsc',
        'CasRpuBlq',
        'CasRpuBlqDtt',
        // audit fields //
        'CasRpuAudIns',
        'CasRpuAudUpd',
        'CasRpuAudDlt',
        'CasRpuAudUsr',
    ];
    public const FIELDS_MD = [
        // primary key //
        'CasRpsCod' => CasRpsMD::FIELDS_MD['CasRpsCod'],
        'CasUsrCod' => CasUsrMD::FIELDS_MD['CasUsrCod'],
        // fields //
        'CasTusCod' => CasTusMD::FIELDS_MD['CasTusCod'],
        'CasRpuDsc' => [
            'Type' => 'string',
            'Length' => 255,
            'Required' => true,
            'Default' => null,
            'LongLabel' => 'Repositório & Usuário',
            'ShortLabel' => 'Rps&Usr',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasRpuBlq' => [
            'Type' => 'boolean',
            'Length' => 1,
            'Required' => true,
            'Default' => 'N',
            'LongLabel' => 'Bloqueado',
            'ShortLabel' => 'Bloqueado',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasRpuBlqDtt' => [
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
        'CasRpuAudIns' => AudMD::Audit_MD['AudIns'],
        'CasRpuAudUpd' => AudMD::Audit_MD['AudUpd'],
        'CasRpuAudDlt' => AudMD::Audit_MD['AudDlt'],
        'CasRpuAudUsr' => AudMD::Audit_MD['AudUsr'],
    ];
    // Foreign //
    public const FIELDS_FOREIGN = [
        'CasRps' => ['FIELDS' => CasRpsMD::FIELDS],
        'CasUsr' => ['FIELDS' => CasUsrMD::FIELDS],
        'CasTus' => ['FIELDS' => CasTusMD::FIELDS],
    ];
    public const FIELDS_MD_FOREIGN = [
        'CasRps' => ['FIELDS_MD' => CasRpsMD::FIELDS_MD],
        'CasUsr' => ['FIELDS_MD' => CasUsrMD::FIELDS_MD],
        'CasTus' => ['FIELDS_MD' => CasTusMD::FIELDS_MD],
    ];
}
