<?php

namespace App\Metadata\CAS;

use App\Metadata\AudMD;

class CasParMD
{
    public const LOGICAL_EXCLUSION = false;
    public const FIELDS_PK = ['CasRpsCod', 'CasParCod'];
    public const FIELDS_FK = array(
        array('FKCasPar01' => array('FieldsKey' => ['CasRpsCod'], 'References' => 'CasRps', 'Fields' => ['CasRpsCod'])),
    );
    public const TABLE_IDX = array(
        array('IXCasPar01' => array('CasRpsCod')),
        array('IXCasPar02' => array('CasRpsCod', 'CasParTbl')),
        array('IXCasPar03' => array('CasRpsCod', 'CasParGrp', 'CasParDsc')),
        array('IXCasPar04' => array('CasRpsCod', 'CasParBlq', 'CasParDsc')),
    );
    public const FIELDS_REQUIRED = ['CasParDsc', 'CasParBlq', 'CasParGrp'];
    public const FIELDS_AUDIT = [
        'CasParAudIns',
        'CasParAudUpd',
        'CasParAudDlt',
        'CasParAudUsr',
    ];
    public const FIELDS = [
        // primary key //
        'CasRpsCod',
        'CasParCod',
        // fields //
        'CasParTbl',
        'CasParDsc',
        'CasParBlq',
        'CasParBlqDtt',
        'CasParSeq',
        'CasParTxt',
        'CasParGrp',
        // audit fields //
        'CasParAudIns',
        'CasParAudUpd',
        'CasParAudDlt',
        'CasParAudUsr',
    ];
    public const FIELDS_MD = [
        // primary key //
        'CasRpsCod' => CasRpsMD::FIELDS_MD['CasRpsCod'],
        'CasParCod' => [
            'Type' => 'string',
            'Length' => 65,
            'Required' => true,
            'Default' => null,
            'LongLabel' => 'Código do Parâmetro',
            'ShortLabel' => 'Cód.Par',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        // fields //
        'CasParTbl' => [
            'Type' => 'string',
            'Length' => 65,
            'Required' => true,
            'Default' => null,
            'LongLabel' => 'Tabela',
            'ShortLabel' => 'Tabela',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasParDsc' => [
            'Type' => 'string',
            'Length' => 255,
            'Required' => true,
            'Default' => null,
            'LongLabel' => 'Parâmetro',
            'ShortLabel' => 'Parâmetro',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasParBlq' => [
            'Type' => 'boolean',
            'Length' => 1,
            'Required' => true,
            'Default' => 'N',
            'LongLabel' => 'Bloqueado',
            'ShortLabel' => 'Bloqueado',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasParBlqDtt' => [
            'Type' => 'datetime',
            'Length' => 0,
            'Required' => false,
            'Default' => null,
            'LongLabel' => 'Data de Bloqueio',
            'ShortLabel' => 'Bloqueio',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasParSeq' => [
            'Type' => 'int',
            'Length' => 10,
            'Required' => false,
            'Default' => null,
            'LongLabel' => 'Sequêncial',
            'ShortLabel' => 'Sequêncial',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasParTxt' => [
            'Type' => 'string',
            'Length' => 4000,
            'Required' => false,
            'Default' => null,
            'LongLabel' => 'Parametrização',
            'ShortLabel' => 'Parametrização',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasParGrp' => [
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
        'CasParAudIns' => AudMD::Audit_MD['AudIns'],
        'CasParAudUpd' => AudMD::Audit_MD['AudUpd'],
        'CasParAudDlt' => AudMD::Audit_MD['AudDlt'],
        'CasParAudUsr' => AudMD::Audit_MD['AudUsr'],
    ];
    // Foreign //
    public const FIELDS_FOREIGN = [
        'CasRps' => ['FIELDS' => CasRpsMD::FIELDS],
    ];
    public const FIELDS_MD_FOREIGN = [
        'CasRps' => ['FIELDS_MD' => CasRpsMD::FIELDS_MD],
    ];
}
