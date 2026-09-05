<?php

namespace App\Metadata\CAS;

use App\Metadata\AudMD;

class CasRpaMD
{
    public const LOGICAL_EXCLUSION = false;
    public const FIELDS_PK = ['CasRpsCod', 'CasAppCod'];
    public const FIELDS_FK = array(
        array('FKCasRpa01' => array('FieldsKey'=>['CasRpsCod'], 'References' => 'CasRps', 'Fields' => ['CasRpsCod'])),
        array('FKCasRpa02' => array('FieldsKey'=>['CasAppCod'], 'References' => 'CasApp', 'Fields' => ['CasAppCod'])),
    );
    public const TABLE_IDX = array(
        array('IXCasRpa01' => array('CasRpsCod')),
        array('IXCasRpa02' => array('CasAppCod')),
        array('IXCasRpa03' => array('CasRpsCod', 'CasRpaGrp', 'CasRpaDsc')),
        array('IXCasRpa04' => array('CasRpsCod', 'CasRpaBlq', 'CasRpaDsc')),
    );
    public const FIELDS_REQUIRED = ['CasRpaDsc', 'CasRpaBlq', 'CasRpaGrp'];
    public const FIELDS_AUDIT = [
        'CasRpaAudIns',
        'CasRpaAudUpd',
        'CasRpaAudDlt',
        'CasRpaAudUsr',
    ];
    public const FIELDS = [
        // primary key //
        'CasRpsCod',
        'CasAppCod',
        // fields //
        'CasRpaDsc',
        'CasRpaObs',
        'CasRpaBlq',
        'CasRpaBlqDtt',
        'CasRpaGrp',
        // audit fields //
        'CasRpaAudIns',
        'CasRpaAudUpd',
        'CasRpaAudDlt',
        'CasRpaAudUsr',
    ];
    public const FIELDS_MD = [
        // primary key //
        'CasRpsCod' => CasRpsMD::FIELDS_MD['CasRpsCod'],
        'CasAppCod' => CasAppMD::FIELDS_MD['CasAppCod'],
        // fields //
        'CasRpaDsc' => [
            'Type' => 'string',
            'Length' => 255,
            'Required' => true,
            'Default' => null,
            'LongLabel' => 'Repositório & Aplicativo',
            'ShortLabel' => 'Rps&App',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasRpaObs' => [
            'Type' => 'string',
            'Length' => 999,
            'Required' => false,
            'Default' => null,
            'LongLabel' => 'Observações',
            'ShortLabel' => 'Observações',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasRpaBlq' => [
            'Type' => 'boolean',
            'Length' => 1,
            'Required' => true,
            'Default' => 'N',
            'LongLabel' => 'Bloqueado',
            'ShortLabel' => 'Bloqueado',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasRpaBlqDtt' => [
            'Type' => 'datetime',
            'Length' => 0,
            'Required' => false,
            'Default' => null,
            'LongLabel' => 'Data de Bloqueio',
            'ShortLabel' => 'Bloqueio',
            'TextPlaceholder' => '',
            'TextHelp' => '',
        ],
        'CasRpaGrp' => [
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
        'CasRpaAudIns' => AudMD::Audit_MD['AudIns'],
        'CasRpaAudUpd' => AudMD::Audit_MD['AudUpd'],
        'CasRpaAudDlt' => AudMD::Audit_MD['AudDlt'],
        'CasRpaAudUsr' => AudMD::Audit_MD['AudUsr'],
    ];
    // Foreign //
    public const FIELDS_FOREIGN = [
        'CasRps' => ['FIELDS' => CasRpsMD::FIELDS],
        'CasApp' => ['FIELDS' => CasAppMD::FIELDS],
    ];
    public const FIELDS_MD_FOREIGN = [
        'CasRps' => ['FIELDS_MD' => CasRpsMD::FIELDS_MD],
        'CasApp' => ['FIELDS_MD' => CasAppMD::FIELDS_MD],
    ];
}
