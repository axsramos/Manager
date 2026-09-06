<?php

namespace App\Metadata\CTR;

class CTRContractItemMD
{
    public const LOGICAL_EXCLUSION = false;
    public const FIELDS_PK = ['Id'];
    public const FIELDS_FK = [
        ['FK_CTRContractItem_Repository' => ['FieldsKey' => ['RepositoryId'], 'References' => 'CasRps', 'Fields' => ['CasRpsCod']]],
        ['FK_CTRContractItem_Contract' => ['FieldsKey' => ['ContractId'], 'References' => 'CTRContract', 'Fields' => ['Id'], 'OnDelete' => 'CASCADE']],
    ];
    public const TABLE_IDX = [
        ['IX_CTRContractItem_Search' => ['RepositoryId', 'ContractId', 'ServiceCode', 'CollaboratorReference']],
        ['IX_CTRContractItem_Collaborator' => ['RepositoryId', 'CollaboratorReference', 'ContractId']],
    ];
    public const TABLE_CHECK = [];
    public const FIELDS_REQUIRED = ['RepositoryId', 'ContractId', 'Description', 'IsIncluded', 'DisplayOrder', 'ItemType', 'IsBillable'];
    public const FIELDS = [
        'Id',
        'RepositoryId',
        'ContractId',
        'ServiceCode',
        'Description',
        'CollaboratorReference',
        'IsIncluded',
        'DisplayOrder',
        'ItemType',
        'ValidUntil',
        'IsBillable',
        'Quantity',
        'UnitPrice',
        'DiscountValue',
        'ReplacementValue',
        'UsageLimit',
        'LineTotal',
    ];
    public const FIELDS_MD = [
        'Id' => ['Type' => 'int', 'Length' => 10, 'Required' => true, 'Default' => null, 'AutoIncrement' => true, 'LongLabel' => 'Item', 'ShortLabel' => 'Item', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'RepositoryId' => ['Type' => 'string', 'Length' => 65, 'Required' => true, 'Default' => null, 'LongLabel' => 'Repositório', 'ShortLabel' => 'Rep.', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'ContractId' => ['Type' => 'char', 'Length' => 36, 'Required' => true, 'Default' => null, 'LongLabel' => 'Contrato', 'ShortLabel' => 'Contrato', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'ServiceCode' => ['Type' => 'string', 'Length' => 50, 'Required' => false, 'Default' => null, 'LongLabel' => 'Serviço', 'ShortLabel' => 'Serviço', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'Description' => ['Type' => 'string', 'Length' => 255, 'Required' => true, 'Default' => null, 'LongLabel' => 'Descrição', 'ShortLabel' => 'Descrição', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'CollaboratorReference' => ['Type' => 'string', 'Length' => 100, 'Required' => false, 'Default' => null, 'LongLabel' => 'Colaborador', 'ShortLabel' => 'Colab.', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'IsIncluded' => ['Type' => 'tinyint', 'Length' => 1, 'Required' => true, 'Default' => '1', 'LongLabel' => 'Incluído', 'ShortLabel' => 'Incl.', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'DisplayOrder' => ['Type' => 'int', 'Length' => 10, 'Required' => true, 'Default' => '0', 'LongLabel' => 'Ordem', 'ShortLabel' => 'Ordem', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'ItemType' => ['Type' => 'string', 'Length' => 20, 'Required' => true, 'Default' => 'Base', 'LongLabel' => 'Tipo', 'ShortLabel' => 'Tipo', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'ValidUntil' => ['Type' => 'datetime', 'Length' => 0, 'Required' => false, 'Default' => null, 'LongLabel' => 'Válido até', 'ShortLabel' => 'Validade', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'IsBillable' => ['Type' => 'tinyint', 'Length' => 1, 'Required' => true, 'Default' => '0', 'LongLabel' => 'Cobravel', 'ShortLabel' => 'Cobravel', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'Quantity' => ['Type' => 'int', 'Length' => 10, 'Required' => false, 'Default' => null, 'LongLabel' => 'Quantidade', 'ShortLabel' => 'Qtd.', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'UnitPrice' => ['Type' => 'bigint', 'Length' => 20, 'Required' => false, 'Default' => null, 'LongLabel' => 'Preço unitário', 'ShortLabel' => 'Preço', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'DiscountValue' => ['Type' => 'bigint', 'Length' => 20, 'Required' => false, 'Default' => null, 'LongLabel' => 'Desconto', 'ShortLabel' => 'Desconto', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'ReplacementValue' => ['Type' => 'bigint', 'Length' => 20, 'Required' => false, 'Default' => '0', 'LongLabel' => 'Reposição', 'ShortLabel' => 'Reposição', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'UsageLimit' => ['Type' => 'int', 'Length' => 10, 'Required' => false, 'Default' => null, 'LongLabel' => 'Franquia', 'ShortLabel' => 'Franquia', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'LineTotal' => ['Type' => 'bigint', 'Length' => 20, 'Required' => false, 'Default' => null, 'Generated' => 'CASE WHEN IsBillable = 1 THEN (IFNULL(Quantity, 1) * IFNULL(UnitPrice, 0)) - IFNULL(DiscountValue, 0) ELSE 0 END', 'Stored' => true, 'LongLabel' => 'Total', 'ShortLabel' => 'Total', 'TextPlaceholder' => '', 'TextHelp' => ''],
    ];
    public const FIELDS_FOREIGN = ['CasRps' => ['FIELDS' => CasRpsMD::FIELDS], 'CTRContract' => ['FIELDS' => CTRContractMD::FIELDS]];
    public const FIELDS_MD_FOREIGN = ['CasRps' => ['FIELDS_MD' => CasRpsMD::FIELDS_MD], 'CTRContract' => ['FIELDS_MD' => CTRContractMD::FIELDS_MD]];
}
