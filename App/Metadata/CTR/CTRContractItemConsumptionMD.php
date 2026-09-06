<?php

namespace App\Metadata\CTR;

class CTRContractItemConsumptionMD
{
    public const LOGICAL_EXCLUSION = false;
    public const FIELDS_PK = ['Id'];
    public const FIELDS_FK = [
        ['FK_CTRConsumption_Repository' => ['FieldsKey' => ['RepositoryId'], 'References' => 'CasRps', 'Fields' => ['CasRpsCod']]],
        ['FK_CTRConsumption_ContractItem' => ['FieldsKey' => ['ContractItemId'], 'References' => 'CTRContractItem', 'Fields' => ['Id']]],
    ];
    public const TABLE_IDX = [
        ['IX_CTRConsumption_History' => ['ContractItemId', 'ConsumedAt' => 'DESC']],
    ];
    public const TABLE_CHECK = [
        'CHK_CTRBalance_Not_Negative' => 'CurrentBalance >= 0',
    ];
    public const FIELDS_REQUIRED = ['RepositoryId', 'ContractItemId', 'PreviousBalance', 'ConsumedQuantity', 'CurrentBalance', 'ConsumedAt'];
    public const FIELDS = [
        'Id',
        'RepositoryId',
        'ContractItemId',
        'PreviousBalance',
        'ConsumedQuantity',
        'CurrentBalance',
        'ExternalReference',
        'Description',
        'ConsumedAt',
    ];
    public const FIELDS_MD = [
        'Id' => ['Type' => 'bigint', 'Length' => 20, 'Required' => true, 'Default' => null, 'AutoIncrement' => true, 'LongLabel' => 'Consumo', 'ShortLabel' => 'Consumo', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'RepositoryId' => ['Type' => 'string', 'Length' => 65, 'Required' => true, 'Default' => null, 'LongLabel' => 'Repositório', 'ShortLabel' => 'Rep.', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'ContractItemId' => ['Type' => 'int', 'Length' => 10, 'Required' => true, 'Default' => null, 'LongLabel' => 'Item', 'ShortLabel' => 'Item', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'PreviousBalance' => ['Type' => 'int', 'Length' => 10, 'Required' => true, 'Default' => null, 'LongLabel' => 'Saldo anterior', 'ShortLabel' => 'Anterior', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'ConsumedQuantity' => ['Type' => 'int', 'Length' => 10, 'Required' => true, 'Default' => null, 'LongLabel' => 'Consumo', 'ShortLabel' => 'Consumo', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'CurrentBalance' => ['Type' => 'int', 'Length' => 10, 'Required' => true, 'Default' => null, 'LongLabel' => 'Saldo atual', 'ShortLabel' => 'Atual', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'ExternalReference' => ['Type' => 'string', 'Length' => 100, 'Required' => false, 'Default' => null, 'LongLabel' => 'Referência externa', 'ShortLabel' => 'Ref.', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'Description' => ['Type' => 'string', 'Length' => 255, 'Required' => false, 'Default' => null, 'LongLabel' => 'Descrição', 'ShortLabel' => 'Descrição', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'ConsumedAt' => ['Type' => 'datetime', 'Length' => 0, 'Required' => true, 'DefaultSql' => 'CURRENT_TIMESTAMP', 'LongLabel' => 'Data do Consumo', 'ShortLabel' => 'Consumo', 'TextPlaceholder' => '', 'TextHelp' => ''],
    ];
    public const FIELDS_FOREIGN = ['CasRps' => ['FIELDS' => CasRpsMD::FIELDS], 'CTRContractItem' => ['FIELDS' => CTRContractItemMD::FIELDS]];
    public const FIELDS_MD_FOREIGN = ['CasRps' => ['FIELDS_MD' => CasRpsMD::FIELDS_MD], 'CTRContractItem' => ['FIELDS_MD' => CTRContractItemMD::FIELDS_MD]];
}
