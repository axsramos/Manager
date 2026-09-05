<?php

namespace App\Metadata\CAS;

use App\Metadata\AudMD;

class CasSwnMD
{
    public const LOGICAL_EXCLUSION = false;
    public const FIELDS_PK = ['CasRpsCod', 'CasSwbCod', 'CasSwnCod'];
    public const FIELDS_FK = ['CasRpsCod', 'CasSwbCod'];
    public const TABLE_IDX = array(
        array('IXCasSwn01' => array('CasRpsCod', 'CasSwbCod')),
        array('IXCasSwn02' => array('CasRpsCod', 'CasSwbCod', 'CasSwnCod')),
    );
    public const FIELDS_REQUIRED = [];
    public const FIELDS_AUDIT = [
        'CasSwnAudIns',
        'CasSwnAudUpd',
        'CasSwnAudDlt',
        'CasSwnAudUsr',
    ];
    public const FIELDS = [
        // primary key //
        'CasRpsCod',
        'CasSwbCod',
        'CasSwnCod',
        // fields //
        'CasSwnDsc',
        // audit fields //
        'CasSwnAudIns',
        'CasSwnAudUpd',
        'CasSwnAudDlt',
        'CasSwnAudUsr',
    ];
    public const FIELDS_MD = [
        // primary key //
        'CasRpsCod' => CasRpsMD::FIELDS_MD['CasRpsCod'],
        'CasSwbCod' => CasSwbMD::FIELDS_MD['CasSwbCod'],
        'CasSwnCod' => [
            'Type' => 'int',
            'Length' => 18,
            'Required' => true,
            'Default' => null,
            'LongLabel' => 'Histórico de Navegação',
            'ShortLabel' => 'Cód.Navegação',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasSwnDsc' => [
            'Type' => 'string',
            'Length' => 255,
            'Required' => true,
            'Default' => null,
            'LongLabel' => 'Descrição de Navegação',
            'ShortLabel' => 'Descrição',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        // fields //
        // audit fields //
        'CasSwnAudIns' => AudMD::Audit_MD['AudIns'],
        'CasSwnAudUpd' => AudMD::Audit_MD['AudUpd'],
        'CasSwnAudDlt' => AudMD::Audit_MD['AudDlt'],
        'CasSwnAudUsr' => AudMD::Audit_MD['AudUsr'],
    ];
    public const FIELDS_FOREIGN = [
        'CasRps' => ['FIELDS' => CasRpsMD::FIELDS],
        'CasSwb' => ['FIELDS' => CasSwbMD::FIELDS],
    ];
    public const FIELDS_MD_FOREIGN = [
        'CasRps' => ['FIELDS_MD' => CasRpsMD::FIELDS_MD],
        'CasSwb' => ['FIELDS_MD' => CasSwbMD::FIELDS_MD],
    ];
}
