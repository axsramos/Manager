<?php

namespace App\Metadata\CAS;

use App\Metadata\AudMD;

class CasSwpMD
{
    public const LOGICAL_EXCLUSION = false;
    public const FIELDS_PK = ['CasRpsCod', 'CasSwbCod', 'CasPrgCod'];
    public const FIELDS_FK = ['CasRpsCod', 'CasSwbCod'];
    public const TABLE_IDX = array(
        array('IXCasSwp01' => array('CasRpsCod', 'CasSwbCod')),
        array('IXCasSwp02' => array('CasRpsCod', 'CasPrgCod')),
    );
    public const FIELDS_REQUIRED = [];
    public const FIELDS_AUDIT = [
        'CasSwpAudIns',
        'CasSwpAudUpd',
        'CasSwpAudDlt',
        'CasSwpAudUsr',
    ];
    public const FIELDS = [
        // primary key //
        'CasRpsCod',
        'CasSwbCod',
        'CasPrgCod',
        // fields //
        'CasSwpTxt',
        // audit fields //
        'CasSwpAudIns',
        'CasSwpAudUpd',
        'CasSwpAudDlt',
        'CasSwpAudUsr',
    ];
    public const FIELDS_MD = [
        // primary key //
        'CasRpsCod' => CasRpsMD::FIELDS_MD['CasRpsCod'],
        'CasSwbCod' => CasSwbMD::FIELDS_MD['CasSwbCod'],
        'CasPrgCod' => CasPrgMD::FIELDS_MD['CasPrgCod'],
        'CasSwpTxt' => [
            'Type' => 'string',
            'Length' => 4000,
            'Required' => false,
            'Default' => null,
            'LongLabel' => 'Parâmetros',
            'ShortLabel' => 'Parâmetros',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        // fields //
        // audit fields //
        'CasSwpAudIns' => AudMD::Audit_MD['AudIns'],
        'CasSwpAudUpd' => AudMD::Audit_MD['AudUpd'],
        'CasSwpAudDlt' => AudMD::Audit_MD['AudDlt'],
        'CasSwpAudUsr' => AudMD::Audit_MD['AudUsr'],
    ];
    public const FIELDS_FOREIGN = [
        'CasRps' => ['FIELDS' => CasRpsMD::FIELDS],
        'CasSwb' => ['FIELDS' => CasSwbMD::FIELDS],
        'CasPrg' => ['FIELDS' => CasPrgMD::FIELDS],
    ];
    public const FIELDS_MD_FOREIGN = [
        'CasRps' => ['FIELDS_MD' => CasRpsMD::FIELDS_MD],
        'CasSwb' => ['FIELDS_MD' => CasSwbMD::FIELDS_MD],
        'CasPrg' => ['FIELDS_MD' => CasPrgMD::FIELDS_MD],
    ];
}
