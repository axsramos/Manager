<?php

namespace App\Metadata\CAS;

use App\Metadata\AudMD;

class CasMprMD
{
    public const LOGICAL_EXCLUSION = false;
    public const FIELDS_PK = ['CasRpsCod', 'CasMdlCod', 'CasPrgCod'];
    public const FIELDS_FK = array(
        array('FKCasMpr01' => array('FieldsKey'=>['CasRpsCod', 'CasMdlCod'], 'References' => 'CasMdl', 'Fields' => ['CasRpsCod', 'CasMdlCod'])),
        array('FKCasMpr02' => array('FieldsKey'=>['CasRpsCod', 'CasPrgCod'], 'References' => 'CasPrg', 'Fields' => ['CasRpsCod', 'CasPrgCod'])),
    );
    public const TABLE_IDX = array(
        array('IXCasMpr01' => array(['CasRpsCod', 'CasMdlCod'])),
        array('IXCasMpr02' => array(['CasRpsCod', 'CasPrgCod'])),
    );
    public const FIELDS_REQUIRED = [];
    public const FIELDS_AUDIT = [
        'CasMprAudIns',
        'CasMprAudUpd',
        'CasMprAudDlt',
        'CasMprAudUsr',
    ];
    public const FIELDS = [
        // primary key //
        'CasRpsCod',
        'CasMdlCod',
        'CasPrgCod',
        // fields //
        // audit fields //
        'CasMprAudIns',
        'CasMprAudUpd',
        'CasMprAudDlt',
        'CasMprAudUsr',
    ];
    public const FIELDS_MD = [
        // primary key //
        'CasRpsCod' => CasRpsMD::FIELDS_MD['CasRpsCod'],
        'CasMdlCod' => CasMdlMD::FIELDS_MD['CasMdlCod'],
        'CasPrgCod' => CasPrgMD::FIELDS_MD['CasPrgCod'],
        // fields //
        // audit fields //
        'CasMprAudIns' => AudMD::Audit_MD['AudIns'],
        'CasMprAudUpd' => AudMD::Audit_MD['AudUpd'],
        'CasMprAudDlt' => AudMD::Audit_MD['AudDlt'],
        'CasMprAudUsr' => AudMD::Audit_MD['AudUsr'],
    ];
    // Foreign //
    public const FIELDS_FOREIGN = [
        'CasMdl' => ['FIELDS' => CasMdlMD::FIELDS],
        'CasPrg' => ['FIELDS' => CasPrgMD::FIELDS],
    ];
    public const FIELDS_MD_FOREIGN = [
        'CasMdl' => ['FIELDS_MD' => CasMdlMD::FIELDS_MD],
        'CasPrg' => ['FIELDS_MD' => CasPrgMD::FIELDS_MD],
    ];
}
