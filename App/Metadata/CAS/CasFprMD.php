<?php

namespace App\Metadata\CAS;

use App\Metadata\AudMD;

class CasFprMD
{
    public const LOGICAL_EXCLUSION = false;
    public const FIELDS_PK = ['CasRpsCod', 'CasFunCod', 'CasPrgCod'];
    public const FIELDS_FK = array(
        array('FKCasFpr01' => array('FieldsKey'=>['CasRpsCod', 'CasFunCod'], 'References' => 'CasFun', 'Fields' => ['CasRpsCod', 'CasFunCod'])),
        array('FKCasFpr02' => array('FieldsKey'=>['CasRpsCod', 'CasPrgCod'], 'References' => 'CasPrg', 'Fields' => ['CasRpsCod', 'CasPrgCod'])),
    );
    public const TABLE_IDX = array(
        array('IXCasFpr01' => array(['CasRpsCod', 'CasFunCod'])),
        array('IXCasFpr02' => array(['CasRpsCod', 'CasPrgCod'])),
    );
    public const FIELDS_REQUIRED = [];
    public const FIELDS_AUDIT = [
        'CasFprAudIns',
        'CasFprAudUpd',
        'CasFprAudDlt',
        'CasFprAudUsr',
    ];
    public const FIELDS = [
        // primary key //
        'CasRpsCod',
        'CasFunCod',
        'CasPrgCod',
        // fields //
        // audit fields //
        'CasFprAudIns',
        'CasFprAudUpd',
        'CasFprAudDlt',
        'CasFprAudUsr',
    ];
    public const FIELDS_MD = [
        // primary key //
        'CasRpsCod' => CasRpsMD::FIELDS_MD['CasRpsCod'],
        'CasFunCod' => CasFunMD::FIELDS_MD['CasFunCod'],
        'CasPrgCod' => CasPrgMD::FIELDS_MD['CasPrgCod'],
        // fields //
        // audit fields //
        'CasFprAudIns' => AudMD::Audit_MD['AudIns'],
        'CasFprAudUpd' => AudMD::Audit_MD['AudUpd'],
        'CasFprAudDlt' => AudMD::Audit_MD['AudDlt'],
        'CasFprAudUsr' => AudMD::Audit_MD['AudUsr'],
    ];
    // Foreign //
    public const FIELDS_FOREIGN = [
        'CasFun' => ['FIELDS' => CasFunMD::FIELDS],
        'CasPrg' => ['FIELDS' => CasPrgMD::FIELDS],
    ];
    public const FIELDS_MD_FOREIGN = [
        'CasFun' => ['FIELDS_MD' => CasFunMD::FIELDS_MD],
        'CasPrg' => ['FIELDS_MD' => CasPrgMD::FIELDS_MD],
    ];
}
