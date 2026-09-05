<?php

namespace App\Metadata\CAS;

use App\Metadata\AudMD;

class CasMnaMD
{
    public const LOGICAL_EXCLUSION = false;
    public const FIELDS_PK = ['CasRpsCod', 'CasMnuCod', 'CasMnaCod'];
    public const FIELDS_FK = array(
        array('FKCasMna01' => array('FieldsKey'=>['CasRpsCod', 'CasMnuCod'], 'References' => 'CasMnu', 'Fields' => ['CasRpsCod', 'CasMnuCod'])),
        array('FKCasMna02' => array('FieldsKey'=>['CasRpsCod', 'CasPrgCod'], 'References' => 'CasPrg', 'Fields' => ['CasRpsCod', 'CasPrgCod'])),
    );
    public const TABLE_IDX = array(
        array('IXCasMna01' => array('CasRpsCod', 'CasMnuCod')),
        array('IXCasMna02' => array('CasRpsCod', 'CasMnuCod', 'CasMnaGrp', 'CasMnaDsc')),
        array('IXCasMna03' => array('CasRpsCod', 'CasMnuCod', 'CasMnaBlq', 'CasMnaDsc')),
        array('IXCasMna04' => array('CasRpsCod', 'CasPrgCod')),
    );
    public const FIELDS_REQUIRED = ['CasPrgCod', 'CasMnaDsc', 'CasMnaBlq', 'CasMnaGrp'];
    public const FIELDS_AUDIT = [
        'CasMnaAudIns',
        'CasMnaAudUpd',
        'CasMnaAudDlt',
        'CasMnaAudUsr',
    ];
    public const FIELDS = [
        // primary key //
        'CasRpsCod',
        'CasMnuCod',
        'CasMnaCod',
        'CasPrgCod',
        // fields //
        'CasMnaDsc',
        'CasMnaBlq',
        'CasMnaBlqDtt',
        'CasMnaTxt',
        'CasMnaLnk',
        'CasMnaIco',
        'CasMnaGrp',
        // audit fields //
        'CasMnaAudIns',
        'CasMnaAudUpd',
        'CasMnaAudDlt',
        'CasMnaAudUsr',
    ];
    public const FIELDS_MD = [
        // primary key //
        'CasRpsCod' => CasRpsMD::FIELDS_MD['CasRpsCod'],
        'CasMnuCod' => CasMnuMD::FIELDS_MD['CasMnuCod'],
        'CasMnaCod' => [
            'Type' => 'string',
            'Length' => 65,
            'Required' => true,
            'Default' => null,
            'LongLabel' => 'Código do Menu Árvore',
            'ShortLabel' => 'Cód.Mna',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasPrgCod' => CasPrgMD::FIELDS_MD['CasPrgCod'],
        // fields //
        'CasMnaDsc' => [
            'Type' => 'string',
            'Length' => 255,
            'Required' => true,
            'Default' => null,
            'LongLabel' => 'Menu Árvore',
            'ShortLabel' => 'Menu Árvore',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasMnaBlq' => [
            'Type' => 'boolean',
            'Length' => 1,
            'Required' => true,
            'Default' => 'N',
            'LongLabel' => 'Bloqueado',
            'ShortLabel' => 'Bloqueado',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasMnaBlqDtt' => [
            'Type' => 'datetime',
            'Length' => 0,
            'Required' => false,
            'Default' => null,
            'LongLabel' => 'Data de Bloqueio',
            'ShortLabel' => 'Bloqueio',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasMnaLnk' => [
            'Type' => 'string',
            'Length' => 999,
            'Required' => false,
            'Default' => null,
            'LongLabel' => 'Link',
            'ShortLabel' => 'Link',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasMnaIco' => [
            'Type' => 'string',
            'Length' => 999,
            'Required' => false,
            'Default' => null,
            'LongLabel' => 'Ícone',
            'ShortLabel' => 'Ícone',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasMnaTxt' => [
            'Type' => 'string',
            'Length' => 4000,
            'Required' => false,
            'Default' => null,
            'LongLabel' => 'Parametrização',
            'ShortLabel' => 'Parametrização',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasMnaGrp' => [
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
        'CasMnaAudIns' => AudMD::Audit_MD['AudIns'],
        'CasMnaAudUpd' => AudMD::Audit_MD['AudUpd'],
        'CasMnaAudDlt' => AudMD::Audit_MD['AudDlt'],
        'CasMnaAudUsr' => AudMD::Audit_MD['AudUsr'],
    ];
    // Foreign //
    public const FIELDS_FOREIGN = [
        'CasMnu' => ['FIELDS' => CasMnuMD::FIELDS],
        'CasPrg' => ['FIELDS' => CasPrgMD::FIELDS],
    ];
    public const FIELDS_MD_FOREIGN = [
        'CasMnu' => ['FIELDS_MD' => CasMnuMD::FIELDS_MD],
        'CasPrg' => ['FIELDS_MD' => CasPrgMD::FIELDS_MD],
    ];
}
