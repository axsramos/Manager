<?php

namespace App\Metadata\CAS;

use App\Metadata\AudMD;

class CasAppMD
{
    public const LOGICAL_EXCLUSION = false;
    public const FIELDS_PK = ['CasAppCod'];
    public const FIELDS_FK = [];
    public const TABLE_IDX = array(
        array('IXCasApp01' => array('CasAppAudIns')),
        array('IXCasApp02' => array('CasAppGrp', 'CasAppDsc')),
        array('IXCasApp03' => array('CasAppBlq', 'CasAppDsc')),
        array('IXCasApp04' => array('CasAppKey')),
    );
    public const FIELDS_REQUIRED = ['CasAppDsc', 'CasAppBlq', 'CasAppTst', 'CasAppKey', 'CasAppGrp'];
    public const FIELDS_AUDIT = [
        'CasAppAudIns',
        'CasAppAudUpd',
        'CasAppAudDlt',
        'CasAppAudUsr',
    ];
    public const FIELDS = [
        // primary key //
        'CasAppCod',
        // fields //
        'CasAppDsc',
        'CasAppObs',
        'CasAppBlq',
        'CasAppBlqDtt',
        'CasAppTst',
        'CasAppTstDtt',
        'CasAppVer',
        'CasAppVerDtt',
        'CasAppVerLnk',
        'CasAppKey',
        'CasAppKeyExp',
        'CasAppGrp',
        // audit fields //
        'CasAppAudIns',
        'CasAppAudUpd',
        'CasAppAudDlt',
        'CasAppAudUsr',
    ];
    public const FIELDS_MD = [
        // primary key //
        'CasAppCod' => [
            'Type' => 'string',
            'Length' => 65,
            'Required' => true,
            'Default' => null,
            'LongLabel' => 'Código do Aplicativo',
            'ShortLabel' => 'Cód.App',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        // fields //
        'CasAppDsc' => [
            'Type' => 'string',
            'Length' => 255,
            'Required' => true,
            'Default' => null,
            'LongLabel' => 'Descrição do Aplicativo',
            'ShortLabel' => 'Aplicativo',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasAppObs' => [
            'Type' => 'string',
            'Length' => 999,
            'Required' => false,
            'Default' => null,
            'LongLabel' => 'Observações',
            'ShortLabel' => 'Observações',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasAppBlq' => [
            'Type' => 'boolean',
            'Length' => 1,
            'Required' => true,
            'Default' => 'N',
            'LongLabel' => 'Bloqueado',
            'ShortLabel' => 'Bloqueado',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasAppBlqDtt' => [
            'Type' => 'datetime',
            'Length' => 0,
            'Required' => false,
            'Default' => null,
            'LongLabel' => 'Data de Bloqueio',
            'ShortLabel' => 'Bloqueio',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasAppTst' => [
            'Type' => 'boolean',
            'Length' => 1,
            'Required' => true,
            'Default' => 'S',
            'LongLabel' => 'Em Teste',
            'ShortLabel' => 'Teste',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasAppTstDtt' => [
            'Type' => 'datetime',
            'Length' => 0,
            'Required' => false,
            'Default' => null,
            'LongLabel' => 'Data em Teste',
            'ShortLabel' => 'Data Teste',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasAppVer' => [
            'Type' => 'string',
            'Length' => 10,
            'Required' => false,
            'Default' => null,
            'LongLabel' => 'Versão',
            'ShortLabel' => 'Versão',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasAppVerDtt' => [
            'Type' => 'datetime',
            'Length' => 0,
            'Required' => false,
            'Default' => null,
            'LongLabel' => 'Data da Versão',
            'ShortLabel' => 'Data Versão',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasAppVerLnk' => [
            'Type' => 'string',
            'Length' => 999,
            'Required' => false,
            'Default' => null,
            'LongLabel' => 'Link',
            'ShortLabel' => 'Link',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasAppKey' => [
            'Type' => 'string',
            'Length' => 65,
            'Required' => true,
            'Default' => null,
            'LongLabel' => 'Chave',
            'ShortLabel' => 'Chave',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasAppKeyExp' => [
            'Type' => 'datetime',
            'Length' => 0,
            'Required' => false,
            'Default' => null,
            'LongLabel' => 'Data da Expiração',
            'ShortLabel' => 'Expiração',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasAppGrp' => [
            'Type' => 'string',
            'Length' => 65,
            'Required' => true,
            'Default' => null,
            'LongLabel' => 'Grupo',
            'ShortLabel' => 'Grupo',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        // audit fields //
        'CasAppAudIns' => AudMD::Audit_MD['AudIns'],
        'CasAppAudUpd' => AudMD::Audit_MD['AudUpd'],
        'CasAppAudDlt' => AudMD::Audit_MD['AudDlt'],
        'CasAppAudUsr' => AudMD::Audit_MD['AudUsr'],
    ];
}
