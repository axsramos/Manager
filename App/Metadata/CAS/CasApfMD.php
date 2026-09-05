<?php

namespace App\Metadata\CAS;

use App\Metadata\AudMD;

class CasApfMD
{
    public const LOGICAL_EXCLUSION = false;
    public const FIELDS_PK = ['CasRpsCod', 'CasPfiCod', 'CasUsrCod', 'CasPrgCod'];
    public const FIELDS_FK = array(
        array('FKCasApf01' => array('FieldsKey' => ['CasRpsCod', 'CasPfiCod', 'CasUsrCod'], 'References' => 'CasPfu', 'Fields' => ['CasRpsCod', 'CasPfiCod', 'CasUsrCod'])),
        array('FKCasApf02' => array('FieldsKey' => ['CasRpsCod', 'CasPrgCod'], 'References' => 'CasPrg', 'Fields' => ['CasRpsCod', 'CasPrgCod'])),
    );
    public const TABLE_IDX = array(
        array('IXCasApf01' => array(['CasRpsCod', 'CasPfiCod', 'CasUsrCod'])),
        array('IXCasApf02' => array(['CasRpsCod', 'CasPrgCod'])),
    );
    public const FIELDS_REQUIRED = [];
    public const FIELDS_AUDIT = [
        'CasApfAudIns',
        'CasApfAudUpd',
        'CasApfAudDlt',
        'CasApfAudUsr',
    ];
    public const FIELDS = [
        // primary key //
        'CasRpsCod',
        'CasPfiCod',
        'CasUsrCod',
        'CasPrgCod',
        // fields //
        // audit fields //
        'CasApfAudIns',
        'CasApfAudUpd',
        'CasApfAudDlt',
        'CasApfAudUsr',
    ];
    public const FIELDS_MD = [
        // primary key //
        'CasRpsCod' => CasRpsMD::FIELDS_MD['CasRpsCod'],
        'CasPfiCod' => CasPfiMD::FIELDS_MD['CasPfiCod'],
        'CasUsrCod' => CasUsrMD::FIELDS_MD['CasUsrCod'],
        'CasPrgCod' => CasPrgMD::FIELDS_MD['CasPrgCod'],
        // fields //
        // audit fields //
        'CasApfAudIns' => AudMD::Audit_MD['AudIns'],
        'CasApfAudUpd' => AudMD::Audit_MD['AudUpd'],
        'CasApfAudDlt' => AudMD::Audit_MD['AudDlt'],
        'CasApfAudUsr' => AudMD::Audit_MD['AudUsr'],
    ];
    // Foreign //
    public const FIELDS_FOREIGN = [
        'CasPfu' => ['FIELDS' => CasPfuMD::FIELDS],
        'CasPrg' => ['FIELDS' => CasPrgMD::FIELDS],
    ];
    public const FIELDS_MD_FOREIGN = [
        'CasPfu' => ['FIELDS_MD' => CasPfuMD::FIELDS_MD],
        'CasPrg' => ['FIELDS_MD' => CasPrgMD::FIELDS_MD],
    ];
}
