<?php

namespace App\Metadata\CTR;

class CTRPackageCheckpointMD
{
    public const LOGICAL_EXCLUSION = false;
    public const FIELDS_PK = ['RepositoryId', 'ProductKey', 'Version', 'Task'];
    public const FIELDS_FK = [
        ['FK_CTRPackageCheckpoint_Repository' => ['FieldsKey' => ['RepositoryId'], 'References' => 'CasRps', 'Fields' => ['CasRpsCod']]],
    ];
    public const TABLE_IDX = [
        ['IX_CTRPackageCheckpoint_Product' => ['ProductKey', 'Version']],
    ];
    public const TABLE_CHECK = [];
    public const FIELDS_REQUIRED = ['RepositoryId', 'ProductKey', 'Version', 'Task', 'Status', 'ContentHash', 'AppliedAt'];
    public const FIELDS = [
        'RepositoryId',
        'ProductKey',
        'Version',
        'Task',
        'Status',
        'ContentHash',
        'Message',
        'AppliedAt',
    ];
    public const FIELDS_MD = [
        'RepositoryId' => ['Type' => 'string', 'Length' => 65, 'Required' => true, 'Default' => null, 'LongLabel' => 'Repositório', 'ShortLabel' => 'Rep.', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'ProductKey' => ['Type' => 'string', 'Length' => 65, 'Required' => true, 'Default' => null, 'LongLabel' => 'Produto', 'ShortLabel' => 'Produto', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'Version' => ['Type' => 'string', 'Length' => 20, 'Required' => true, 'Default' => null, 'LongLabel' => 'Versão', 'ShortLabel' => 'Versão', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'Task' => ['Type' => 'int', 'Length' => 10, 'Required' => true, 'Default' => null, 'LongLabel' => 'Tarefa', 'ShortLabel' => 'Tarefa', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'Status' => ['Type' => 'string', 'Length' => 20, 'Required' => true, 'Default' => 'applied', 'LongLabel' => 'Status', 'ShortLabel' => 'Status', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'ContentHash' => ['Type' => 'char', 'Length' => 64, 'Required' => true, 'Default' => null, 'LongLabel' => 'Hash', 'ShortLabel' => 'Hash', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'Message' => ['Type' => 'string', 'Length' => 255, 'Required' => false, 'Default' => null, 'LongLabel' => 'Mensagem', 'ShortLabel' => 'Mensagem', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'AppliedAt' => ['Type' => 'datetime', 'Length' => 0, 'Required' => true, 'DefaultSql' => 'CURRENT_TIMESTAMP', 'LongLabel' => 'Aplicado em', 'ShortLabel' => 'Aplicado', 'TextPlaceholder' => '', 'TextHelp' => ''],
    ];
    public const FIELDS_FOREIGN = ['CasRps' => ['FIELDS' => CasRpsMD::FIELDS]];
    public const FIELDS_MD_FOREIGN = ['CasRps' => ['FIELDS_MD' => CasRpsMD::FIELDS_MD]];
}
