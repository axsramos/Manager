<?php

namespace App\Metadata\CAS;

use App\Metadata\AudMD;

class CasAfuMD
{
    public const LOGICAL_EXCLUSION = false;
    public const FIELDS_PK = ['CasRpsCod', 'CasPfiCod', 'CasUsrCod', 'CasPrgCod', 'CasFunCod'];
    public const FIELDS_FK = array(
        array('FKCasAfu01' => array('FieldsKey'=>['CasRpsCod', 'CasPfiCod', 'CasUsrCod', 'CasPrgCod'], 'References' => 'CasApf', 'Fields' => ['CasRpsCod', 'CasPfiCod', 'CasUsrCod', 'CasPrgCod'])),
        array('FKCasAfu02' => array('FieldsKey'=>['CasRpsCod', 'CasFunCod', 'CasPrgCod'], 'References' => 'CasFpr', 'Fields' => ['CasRpsCod', 'CasFunCod', 'CasPrgCod'])),
    );
    public const TABLE_IDX = array(
        array('IXCasAfu01' => array(['CasRpsCod', 'CasPfiCod', 'CasUsrCod', 'CasPrgCod'])),
        array('IXCasAfu02' => array(['CasRpsCod', 'CasFunCod', 'CasPrgCod'])),
    );
    public const FIELDS_REQUIRED = [];
    public const FIELDS_AUDIT = [
        'CasAfuAudIns',
        'CasAfuAudUpd',
        'CasAfuAudDlt',
        'CasAfuAudUsr',
    ];
    public const FIELDS = [
        // primary key //
        'CasRpsCod',
        'CasPfiCod',
        'CasUsrCod',
        'CasPrgCod',
        'CasFunCod',
        // fields //
        // audit fields //
        'CasAfuAudIns',
        'CasAfuAudUpd',
        'CasAfuAudDlt',
        'CasAfuAudUsr',
    ];
    public const FIELDS_MD = [
        // primary key //
        'CasRpsCod' => CasRpsMD::FIELDS_MD['CasRpsCod'],
        'CasPfiCod' => CasPfiMD::FIELDS_MD['CasPfiCod'],
        'CasUsrCod' => CasUsrMD::FIELDS_MD['CasUsrCod'],
        'CasPrgCod' => CasPrgMD::FIELDS_MD['CasPrgCod'],
        'CasFunCod' => CasFunMD::FIELDS_MD['CasFunCod'],
        // fields //
        // audit fields //
        'CasAfuAudIns' => AudMD::Audit_MD['AudIns'],
        'CasAfuAudUpd' => AudMD::Audit_MD['AudUpd'],
        'CasAfuAudDlt' => AudMD::Audit_MD['AudDlt'],
        'CasAfuAudUsr' => AudMD::Audit_MD['AudUsr'],
    ];
    // Foreign //
    public const FIELDS_FOREIGN = [
        'CasApf' => ['FIELDS' => CasApfMD::FIELDS],
        'CasFpr' => ['FIELDS' => CasFprMD::FIELDS],
    ];
    public const FIELDS_MD_FOREIGN = [
        'CasApf' => ['FIELDS_MD' => CasApfMD::FIELDS_MD],
        'CasFpr' => ['FIELDS_MD' => CasFprMD::FIELDS_MD],
    ];
}
