<?php

namespace App\Metadata\CAS;

use App\Metadata\AudMD;

class CasSwbMD
{
    public const LOGICAL_EXCLUSION = false;
    public const FIELDS_PK = ['CasRpsCod', 'CasSwbCod'];
    public const FIELDS_FK = ['CasRpsCod'];
    public const TABLE_IDX = array(
        array('IXCasSwb01' => array('CasRpsCod')),
        array('IXCasSwb02' => array('CasRpsCod', 'CasSwbAudIns')),
        array('IXCasSwb03' => array('CasRpsCod', 'CasSwbUsrCod')),
        array('IXCasSwb04' => array('CasRpsCod', 'CasSwbWksCod')),
    );
    public const FIELDS_REQUIRED = ['CasSwbBlq'];
    public const FIELDS_AUDIT = [
        'CasSwbAudIns',
        'CasSwbAudUpd',
        'CasSwbAudDlt',
        'CasSwbAudUsr',
    ];
    public const FIELDS = [
        // primary key //
        'CasRpsCod',
        'CasSwbCod',
        // fields //
        'CasSwbBlq',
        'CasSwbBlqDtt',
        'CasSwbWks',
        'CasSwbUsu',
        'CasSwbBrw',
        'CasSwbIni',
        'CasSwbFin',
        'CasSwbUsrCod',
        'CasSwbWksCod',
        // audit fields //
        'CasSwbAudIns',
        'CasSwbAudUpd',
        'CasSwbAudDlt',
        'CasSwbAudUsr',
    ];
    public const FIELDS_MD = [
        // primary key //
        'CasRpsCod' => CasRpsMD::FIELDS_MD['CasRpsCod'],
        'CasSwbCod' => [
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
        'CasSwbWks' => [
            'Type' => 'string',
            'Length' => 65,
            'Required' => false,
            'Default' => null,
            'LongLabel' => 'Estação de Trabalho (Workstation)',
            'ShortLabel' => 'Workstation',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasSwbUsu' => [
            'Type' => 'string',
            'Length' => 65,
            'Required' => false,
            'Default' => null,
            'LongLabel' => 'Usuario remoto',
            'ShortLabel' => 'Usuario',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasSwbBrw' => [
            'Type' => 'string',
            'Length' => 65,
            'Required' => false,
            'Default' => null,
            'LongLabel' => 'Browser',
            'ShortLabel' => 'Browser',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasSwbIni' => [
            'Type' => 'datetime',
            'Length' => 0,
            'Required' => false,
            'Default' => null,
            'LongLabel' => 'Data início',
            'ShortLabel' => 'Início',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasSwbFin' => [
            'Type' => 'datetime',
            'Length' => 0,
            'Required' => false,
            'Default' => null,
            'LongLabel' => 'Data final',
            'ShortLabel' => 'Final',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasSwbBlq' => [
            'Type' => 'boolean',
            'Length' => 1,
            'Required' => true,
            'Default' => 'N',
            'LongLabel' => 'Bloqueado',
            'ShortLabel' => 'Bloqueado',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasSwbBlqDtt' => [
            'Type' => 'datetime',
            'Length' => 0,
            'Required' => false,
            'Default' => null,
            'LongLabel' => 'Data de Bloqueio',
            'ShortLabel' => 'Bloqueio',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasSwbUsrCod' => [
            'Type' => 'string',
            'Length' => 36,
            'Required' => false,
            'Default' => null,
            'LongLabel' => 'Código do Usuário',
            'ShortLabel' => 'Cód.Usuário',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasSwbWksCod' => [
            'Type' => 'string',
            'Length' => 36,
            'Required' => false,
            'Default' => null,
            'LongLabel' => 'Código da Workstation',
            'ShortLabel' => 'Cód.Workstation',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        // audit fields //
        'CasSwbAudIns' => AudMD::Audit_MD['AudIns'],
        'CasSwbAudUpd' => AudMD::Audit_MD['AudUpd'],
        'CasSwbAudDlt' => AudMD::Audit_MD['AudDlt'],
        'CasSwbAudUsr' => AudMD::Audit_MD['AudUsr'],
    ];
    public const FIELDS_FOREIGN = [
        'CasRps' => ['FIELDS' => CasRpsMD::FIELDS],
    ];
    public const FIELDS_MD_FOREIGN = [
        'CasRps' => ['FIELDS_MD' => CasRpsMD::FIELDS_MD],
    ];
}
