<?php

namespace App\Metadata\CTR;

class CTRConcurrencyGroupMD
{
    public const LOGICAL_EXCLUSION = false;
    public const FIELDS_PK = ['Id'];
    public const FIELDS_FK = [
        ['FK_CTRConcurrencyGroup_Repository' => ['FieldsKey' => ['RepositoryId'], 'References' => 'CasRps', 'Fields' => ['CasRpsCod']]],
    ];
    public const TABLE_IDX = [
        ['IX_CTRConcurrencyGroup_Repository' => ['RepositoryId']],
    ];
    public const TABLE_CHECK = [];
    public const FIELDS_REQUIRED = ['RepositoryId', 'Name', 'AllowMultipleActive'];
    public const FIELDS = [
        'Id',
        'RepositoryId',
        'Name',
        'AllowMultipleActive',
    ];
    public const FIELDS_MD = [
        'Id' => ['Type' => 'int', 'Length' => 10, 'Required' => true, 'Default' => null, 'AutoIncrement' => true, 'LongLabel' => 'Código', 'ShortLabel' => 'Código', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'RepositoryId' => ['Type' => 'string', 'Length' => 65, 'Required' => true, 'Default' => null, 'LongLabel' => 'Repositório', 'ShortLabel' => 'Rep.', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'Name' => ['Type' => 'string', 'Length' => 100, 'Required' => true, 'Default' => null, 'LongLabel' => 'Grupo de Concorrência', 'ShortLabel' => 'Grupo', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'AllowMultipleActive' => ['Type' => 'tinyint', 'Length' => 1, 'Required' => true, 'Default' => '0', 'LongLabel' => 'Permite múltiplos ativos', 'ShortLabel' => 'Múltiplos', 'TextPlaceholder' => '', 'TextHelp' => ''],
    ];
    public const FIELDS_FOREIGN = ['CasRps' => ['FIELDS' => CasRpsMD::FIELDS]];
    public const FIELDS_MD_FOREIGN = ['CasRps' => ['FIELDS_MD' => CasRpsMD::FIELDS_MD]];
}
