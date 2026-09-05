<?php

namespace App\Metadata\CAS;

use App\Metadata\AudMD;

class CasTknMD
{
    public const LOGICAL_EXCLUSION = false;
    public const FIELDS_PK = ['CasTknCod'];
    public const FIELDS_FK = [];
    public const TABLE_IDX = array(
        array('IXCasTkn01' => array('CasTknAudIns')),
        array('IXCasTkn02' => array('CasTknBlq', 'CasTknDsc')),
        array('IXCasTkn03' => array('CasTknKey')),
    );
    public const FIELDS_REQUIRED = ['CasTknDsc', 'CasTknBlq', 'CasTknKey'];
    public const FIELDS_AUDIT = [
        'CasTknAudIns',
        'CasTknAudUpd',
        'CasTknAudDlt',
        'CasTknAudUsr',
    ];
    public const FIELDS = [
        // primary key //
        'CasTknCod',
        // fields //
        'CasTknDsc',
        'CasTknBlq',
        'CasTknBlqDtt',
        'CasTknKey',
        'CasTknKeyExp',
        // audit fields //
        'CasTknAudIns',
        'CasTknAudUpd',
        'CasTknAudDlt',
        'CasTknAudUsr',
    ];
    public const FIELDS_MD = [
        // primary key //
        'CasTknCod' => [
            'Type' => 'string',
            'Length' => 65,
            'Required' => true,
            'Default' => null,
            'LongLabel' => 'Token',
            'ShortLabel' => 'Token',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        // fields //
        'CasTknDsc' => [
            'Type' => 'string',
            'Length' => 255,
            'Required' => true,
            'Default' => null,
            'LongLabel' => 'Descrição do Token',
            'ShortLabel' => 'Descrição',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasTknBlq' => [
            'Type' => 'boolean',
            'Length' => 1,
            'Required' => true,
            'Default' => 'N',
            'LongLabel' => 'Bloqueado',
            'ShortLabel' => 'Bloqueado',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasTknBlqDtt' => [
            'Type' => 'datetime',
            'Length' => 0,
            'Required' => false,
            'Default' => null,
            'LongLabel' => 'Data de Bloqueio',
            'ShortLabel' => 'Bloqueio',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasTknKey' => [
            'Type' => 'string',
            'Length' => 65,
            'Required' => true,
            'Default' => false,
            'LongLabel' => 'Chave',
            'ShortLabel' => 'Chave',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasTknKeyExp' => [
            'Type' => 'datetime',
            'Length' => 0,
            'Required' => false,
            'Default' => null,
            'LongLabel' => 'Data da Expiração',
            'ShortLabel' => 'Expiração',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        // audit fields //
        'CasTknAudIns' => AudMD::Audit_MD['AudIns'],
        'CasTknAudUpd' => AudMD::Audit_MD['AudUpd'],
        'CasTknAudDlt' => AudMD::Audit_MD['AudDlt'],
        'CasTknAudUsr' => AudMD::Audit_MD['AudUsr'],
    ];
}
