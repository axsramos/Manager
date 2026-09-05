<?php

namespace App\Metadata\CAS;

use App\Metadata\AudMD;

class CasUsrMD
{
    public const LOGICAL_EXCLUSION = false;
    public const FIELDS_PK = ['CasUsrCod'];
    public const FIELDS_FK = [];
    public const TABLE_IDX = array(
        array('IXCasUsr01' => array('CasUsrAudIns')),
        array('IXCasUsr02' => array('CasUsrDmn', 'CasUsrLgn')),
        array('IXCasUsr03' => array('CasUsrBlq', 'CasUsrDsc')),
    );
    public const FIELDS_REQUIRED = ['CasUsrNme', 'CasUsrDsc', 'CasUsrDmn', 'CasUsrLgn', 'CasUsrBlq'];
    public const FIELDS_AUDIT = [
        'CasUsrAudIns',
        'CasUsrAudUpd',
        'CasUsrAudDlt',
        'CasUsrAudUsr',
    ];
    public const FIELDS = [
        // primary key //
        'CasUsrCod',
        // fields //
        'CasUsrNme',
        'CasUsrSnm',
        'CasUsrNck',
        'CasUsrDsc',
        'CasUsrDmn',
        'CasUsrLgn',
        'CasUsrPwd',
        'CasUsrActDtt',
        'CasUsrBlq',
        'CasUsrBlqDtt',
        // audit fields //
        'CasUsrAudIns',
        'CasUsrAudUpd',
        'CasUsrAudDlt',
        'CasUsrAudUsr',
    ];
    public const FIELDS_MD = [
        // primary key //
        'CasUsrCod' => [
            'Type' => 'string',
            'Length' => 65,
            'Required' => true,
            'Default' => null,
            'LongLabel' => 'Usuário',
            'ShortLabel' => 'Usuário',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        // fields //
        'CasUsrNme' => [
            'Type' => 'string',
            'Length' => 65,
            'Required' => true,
            'Default' => null,
            'LongLabel' => 'Primeiro Nome',
            'ShortLabel' => 'Nome',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasUsrSnm' => [
            'Type' => 'string',
            'Length' => 65,
            'Required' => false,
            'Default' => null,
            'LongLabel' => 'Sobrenome',
            'ShortLabel' => 'Sobrenome',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasUsrNck' => [
            'Type' => 'string',
            'Length' => 65,
            'Required' => true,
            'Default' => null,
            'LongLabel' => 'Apelido',
            'ShortLabel' => 'Apelido',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasUsrDsc' => [
            'Type' => 'string',
            'Length' => 255,
            'Required' => true,
            'Default' => null,
            'LongLabel' => 'Nome Completo',
            'ShortLabel' => 'Nome',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasUsrDmn' => [
            'Type' => 'string',
            'Length' => 65,
            'Required' => true,
            'Default' => null,
            'LongLabel' => 'Domínio',
            'ShortLabel' => 'Domínio',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasUsrLgn' => [
            'Type' => 'string',
            'Length' => 65,
            'Required' => true,
            'Default' => null,
            'LongLabel' => 'Login',
            'ShortLabel' => 'Login',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasUsrPwd' => [
            'Type' => 'string',
            'Length' => 255,
            'Required' => true,
            'Default' => null,
            'LongLabel' => 'Senha',
            'ShortLabel' => 'Senha',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasUsrActDtt' => [
            'Type' => 'datetime',
            'Length' => 0,
            'Required' => false,
            'Default' => null,
            'LongLabel' => 'Data de Ativacao',
            'ShortLabel' => 'Ativacao',
            'TextPlaceholder' => '',
            'TextHelp' => 'Nulo enquanto o e-mail da conta nao estiver confirmado.',
        ],
        'CasUsrBlq' => [
            'Type' => 'boolean',
            'Length' => 1,
            'Required' => true,
            'Default' => 'N',
            'LongLabel' => 'Bloqueado',
            'ShortLabel' => 'Bloqueado',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasUsrBlqDtt' => [
            'Type' => 'datetime',
            'Length' => 0,
            'Required' => false,
            'Default' => null,
            'LongLabel' => 'Data de Bloqueio',
            'ShortLabel' => 'Bloqueio',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        // audit fields //
        'CasUsrAudIns' => AudMD::Audit_MD['AudIns'],
        'CasUsrAudUpd' => AudMD::Audit_MD['AudUpd'],
        'CasUsrAudDlt' => AudMD::Audit_MD['AudDlt'],
        'CasUsrAudUsr' => AudMD::Audit_MD['AudUsr'],
    ];
}
