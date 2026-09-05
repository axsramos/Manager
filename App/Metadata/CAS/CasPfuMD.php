<?php

namespace App\Metadata\CAS;

use App\Metadata\AudMD;

class CasPfuMD
{
    public const LOGICAL_EXCLUSION = false;
    public const FIELDS_PK = ['CasRpsCod', 'CasPfiCod', 'CasUsrCod'];
    public const FIELDS_FK = array(
        array('FKCasPfu01' => array('FieldsKey'=>['CasRpsCod', 'CasPfiCod'], 'References' => 'CasPfi', 'Fields' => ['CasRpsCod', 'CasPfiCod'])),
        array('FKCasPfu02' => array('FieldsKey'=>['CasRpsCod', 'CasUsrCod'], 'References' => 'CasRpu', 'Fields' => ['CasRpsCod', 'CasUsrCod'])),
    );
    public const TABLE_IDX = array(
        array('IXCasPfu01' => array(['CasRpsCod', 'CasPfiCod'])),
        array('IXCasPfu02' => array(['CasRpsCod', 'CasUsrCod'])),
    );
    public const FIELDS_REQUIRED = [];
    public const FIELDS_AUDIT = [
        'CasPfuAudIns',
        'CasPfuAudUpd',
        'CasPfuAudDlt',
        'CasPfuAudUsr',
    ];
    public const FIELDS = [
        // primary key //
        'CasRpsCod',
        'CasPfiCod',
        'CasUsrCod',
        // fields //
        // audit fields //
        'CasPfuAudIns',
        'CasPfuAudUpd',
        'CasPfuAudDlt',
        'CasPfuAudUsr',
    ];
    public const FIELDS_MD = [
        // primary key //
        'CasRpsCod' => CasRpsMD::FIELDS_MD['CasRpsCod'],
        'CasPfiCod' => CasPfiMD::FIELDS_MD['CasPfiCod'],
        'CasUsrCod' => CasUsrMD::FIELDS_MD['CasUsrCod'],
        // fields //
        // audit fields //
        'CasPfuAudIns' => AudMD::Audit_MD['AudIns'],
        'CasPfuAudUpd' => AudMD::Audit_MD['AudUpd'],
        'CasPfuAudDlt' => AudMD::Audit_MD['AudDlt'],
        'CasPfuAudUsr' => AudMD::Audit_MD['AudUsr'],
    ];
    // Foreign //
    public const FIELDS_FOREIGN = [
        'CasPfi' => ['FIELDS' => CasPfiMD::FIELDS],
        'CasRpu' => ['FIELDS' => CasRpuMD::FIELDS],
    ];
    public const FIELDS_MD_FOREIGN = [
        'CasPfi' => ['FIELDS_MD' => CasPfiMD::FIELDS_MD],
        'CasRpu' => ['FIELDS_MD' => CasRpuMD::FIELDS_MD],
    ];
}
