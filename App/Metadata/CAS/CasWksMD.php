<?php

namespace App\Metadata\CAS;

use App\Metadata\AudMD;

class CasWksMD
{
    public const LOGICAL_EXCLUSION = false;
    public const FIELDS_PK = ['CasRpsCod', 'CasWksCod'];
    public const FIELDS_FK = ['CasRpsCod'];
    public const TABLE_IDX = array(
        array('IXCasWks01' => array('CasRpsCod', 'CasWksGrp', 'CasWksDsc')),
        array('IXCasWks02' => array('CasRpsCod', 'CasWksBlq', 'CasWksDsc')),
        array('IXCasWks03' => array('CasRpsCod', 'CasWksChv')),
    );
    public const FIELDS_REQUIRED = ['CasWksDsc', 'CasWksBlq', 'CasWksChv'];
    public const FIELDS_AUDIT = [
        'CasWksAudIns',
        'CasWksAudUpd',
        'CasWksAudDlt',
        'CasWksAudUsr',
    ];
    public const FIELDS = [
        // primary key //
        'CasRpsCod',
        'CasWksCod',
        // fields //
        'CasWksDsc',
        'CasWksObs',
        'CasWksBlq',
        'CasWksBlqDtt',
        'CasWksMac',
        'CasWksEip',
        'CasWksChv',
        'CasWksGrp',
        // audit fields //
        'CasWksAudIns',
        'CasWksAudUpd',
        'CasWksAudDlt',
        'CasWksAudUsr',
    ];
    public const FIELDS_MD = [
        // primary key //
        'CasRpsCod' => CasRpsMD::FIELDS_MD['CasRpsCod'],
        'CasWksCod' => [
            'Type' => 'string',
            'Length' => 65,
            'Required' => true,
            'Default' => null,
            'LongLabel' => 'Sessão Web',
            'ShortLabel' => 'Cód.Sessão',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        // fields //
        'CasWksDsc' => [
            'Type' => 'string',
            'Length' => 255,
            'Required' => true,
            'Default' => null,
            'LongLabel' => 'Estação de Trabalho (Workstation)',
            'ShortLabel' => 'Workstation',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasWksObs' => [
            'Type' => 'string',
            'Length' => 999,
            'Required' => false,
            'Default' => null,
            'LongLabel' => 'Observações',
            'ShortLabel' => 'Observações',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasWksBlq' => [
            'Type' => 'boolean',
            'Length' => 1,
            'Required' => true,
            'Default' => 'N',
            'LongLabel' => 'Bloqueado',
            'ShortLabel' => 'Bloqueado',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasWksBlqDtt' => [
            'Type' => 'datetime',
            'Length' => 0,
            'Required' => false,
            'Default' => null,
            'LongLabel' => 'Data de Bloqueio',
            'ShortLabel' => 'Bloqueio',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasWksMac' => [
            'Type' => 'string',
            'Length' => 17,
            'Required' => false,
            'Default' => null,
            'LongLabel' => 'MAC Address',
            'ShortLabel' => 'MAC.Address',
            'TextPlaceholder' => '00:00:00:00:00:00',
            'TextHelp' => 'Endereço físico do equipamento.',
        ],
        'CasWksEip' => [
            'Type' => 'string',
            'Length' => 45,
            'Required' => false,
            'Default' => null,
            'LongLabel' => 'Endereço IP',
            'ShortLabel' => 'Endereço IP',
            'TextPlaceholder' => '0.0.0.0',
            'TextHelp' => '',
        ],
        'CasWksChv' => [
            'Type' => 'string',
            'Length' => 36,
            'Required' => true,
            'Default' => null,
            'LongLabel' => 'Chave de Licença',
            'ShortLabel' => 'Chave de Licença',
            'TextPlaceholder' => '0.0.0.0',
            'TextHelp' => '',
        ],
        'CasWksGrp' => [
            'Type' => 'string',
            'Length' => 65,
            'Required' => false,
            'Default' => null,
            'LongLabel' => 'Grupo',
            'ShortLabel' => 'Grupo',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        // audit fields //
        'CasWksAudIns' => AudMD::Audit_MD['AudIns'],
        'CasWksAudUpd' => AudMD::Audit_MD['AudUpd'],
        'CasWksAudDlt' => AudMD::Audit_MD['AudDlt'],
        'CasWksAudUsr' => AudMD::Audit_MD['AudUsr'],
    ];
    public const FIELDS_FOREIGN = [
        'CasRps' => ['FIELDS' => CasRpsMD::FIELDS],
    ];
    public const FIELDS_MD_FOREIGN = [
        'CasRps' => ['FIELDS_MD' => CasRpsMD::FIELDS_MD],
    ];
}
