<?php

namespace App\Metadata\CAS;

use App\Metadata\AudMD;

class CasMdlMD
{
    public const LOGICAL_EXCLUSION = false;
    public const FIELDS_PK = ['CasRpsCod','CasMdlCod'];
    public const FIELDS_FK = array(
        array('FKCasMdl01' => array('FieldsKey'=>['CasRpsCod'], 'References' => 'CasRps', 'Fields' => ['CasRpsCod'])),
    );
    public const TABLE_IDX = array(
        array('IXCasMdl01' => array('CasRpsCod')),
        array('IXCasMdl02' => array('CasRpsCod', 'CasMdlBlq', 'CasMdlDsc')),
    );
    public const FIELDS_REQUIRED = ['CasMdlDsc', 'CasMdlBlq'];
    public const FIELDS_AUDIT = [
        'CasMdlAudIns',
        'CasMdlAudUpd',
        'CasMdlAudDlt',
        'CasMdlAudUsr',
    ];
    public const FIELDS = [
        // primary key //
        'CasRpsCod',
        'CasMdlCod',
        // fields //
        'CasMdlDsc',
        'CasMdlBlq',
        'CasMdlBlqDtt',
        // audit fields //
        'CasMdlAudIns',
        'CasMdlAudUpd',
        'CasMdlAudDlt',
        'CasMdlAudUsr',
    ];
    public const FIELDS_MD = [
        // primary key //
        'CasRpsCod' => CasRpsMD::FIELDS_MD['CasRpsCod'],
        'CasMdlCod' => [
            'Type' => 'string',
            'Length' => 65,
            'Required' => true,
            'Default' => null,
            'LongLabel' => 'Código da Módulo',
            'ShortLabel' => 'Cód.Fun',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        // fields //
        'CasMdlDsc' => [
            'Type' => 'string',
            'Length' => 255,
            'Required' => true,
            'Default' => null,
            'LongLabel' => 'Módulo',
            'ShortLabel' => 'Módulo',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasMdlBlq' => [
            'Type' => 'boolean',
            'Length' => 1,
            'Required' => true,
            'Default' => 'N',
            'LongLabel' => 'Bloqueado',
            'ShortLabel' => 'Bloqueado',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasMdlBlqDtt' => [
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
        'CasMdlAudIns' => AudMD::Audit_MD['AudIns'],
        'CasMdlAudUpd' => AudMD::Audit_MD['AudUpd'],
        'CasMdlAudDlt' => AudMD::Audit_MD['AudDlt'],
        'CasMdlAudUsr' => AudMD::Audit_MD['AudUsr'],
    ];
    // Foreign //
    public const FIELDS_FOREIGN = [
        'CasRps' => ['FIELDS' => CasRpsMD::FIELDS],
    ];
    public const FIELDS_MD_FOREIGN = [
        'CasRps' => ['FIELDS_MD' => CasRpsMD::FIELDS_MD],
    ];
}
