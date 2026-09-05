<?php

namespace App\Metadata\CAS;

use App\Metadata\AudMD;

class CasMnuMD
{
    public const LOGICAL_EXCLUSION = false;
    public const FIELDS_PK = ['CasRpsCod','CasMnuCod'];
    public const FIELDS_FK = array(
        array('FKCasMnu01' => array('FieldsKey'=>['CasRpsCod'], 'References' => 'CasRps', 'Fields' => ['CasRpsCod'])),
    );
    public const TABLE_IDX = array(
        array('IXCasMnu01' => array('CasRpsCod')),
        array('IXCasMnu02' => array('CasRpsCod', 'CasMnuGrp', 'CasMnuDsc')),
        array('IXCasMnu03' => array('CasRpsCod', 'CasMnuBlq', 'CasMnuDsc')),
    );
    public const FIELDS_REQUIRED = ['CasMnuDsc', 'CasMnuBlq', 'CasMnuGrp'];
    public const FIELDS_AUDIT = [
        'CasMnuAudIns',
        'CasMnuAudUpd',
        'CasMnuAudDlt',
        'CasMnuAudUsr',
    ];
    public const FIELDS = [
        // primary key //
        'CasRpsCod',
        'CasMnuCod',
        // fields //
        'CasMnuDsc',
        'CasMnuBlq',
        'CasMnuBlqDtt',
        'CasMnuTxt',
        'CasMnuGrp',
        // audit fields //
        'CasMnuAudIns',
        'CasMnuAudUpd',
        'CasMnuAudDlt',
        'CasMnuAudUsr',
    ];
    public const FIELDS_MD = [
        // primary key //
        'CasRpsCod' => CasRpsMD::FIELDS_MD['CasRpsCod'],
        'CasMnuCod' => [
            'Type' => 'string',
            'Length' => 65,
            'Required' => true,
            'Default' => null,
            'LongLabel' => 'Código do Menu Raiz',
            'ShortLabel' => 'Cód.Mnu',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        // fields //
        'CasMnuDsc' => [
            'Type' => 'string',
            'Length' => 255,
            'Required' => true,
            'Default' => null,
            'LongLabel' => 'Menu Raiz',
            'ShortLabel' => 'Menu Raiz',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasMnuBlq' => [
            'Type' => 'boolean',
            'Length' => 1,
            'Required' => true,
            'Default' => 'N',
            'LongLabel' => 'Bloqueado',
            'ShortLabel' => 'Bloqueado',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasMnuBlqDtt' => [
            'Type' => 'datetime',
            'Length' => 0,
            'Required' => false,
            'Default' => null,
            'LongLabel' => 'Data de Bloqueio',
            'ShortLabel' => 'Bloqueio',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasMnuTxt' => [
            'Type' => 'string',
            'Length' => 4000,
            'Required' => false,
            'Default' => null,
            'LongLabel' => 'Parametrização',
            'ShortLabel' => 'Parametrização',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasMnuGrp' => [
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
        'CasMnuAudIns' => AudMD::Audit_MD['AudIns'],
        'CasMnuAudUpd' => AudMD::Audit_MD['AudUpd'],
        'CasMnuAudDlt' => AudMD::Audit_MD['AudDlt'],
        'CasMnuAudUsr' => AudMD::Audit_MD['AudUsr'],
    ];
    // Foreign //
    public const FIELDS_FOREIGN = [
        'CasRps' => ['FIELDS' => CasRpsMD::FIELDS],
    ];
    public const FIELDS_MD_FOREIGN = [
        'CasRps' => ['FIELDS_MD' => CasRpsMD::FIELDS_MD],
    ];
}
