<?php

namespace App\Metadata\CTR;

class CTRContractMD
{
    public const LOGICAL_EXCLUSION = false;
    public const FIELDS_PK = ['Id'];
    public const FIELDS_FK = [
        ['FK_CTRContract_Repository' => ['FieldsKey' => ['RepositoryId'], 'References' => 'CasRps', 'Fields' => ['CasRpsCod']]],
        ['FK_CTRContract_Parent' => ['FieldsKey' => ['ParentContractId'], 'References' => 'CTRContract', 'Fields' => ['Id']]],
        ['FK_CTRContract_Group' => ['FieldsKey' => ['ConcurrencyGroupId'], 'References' => 'CTRConcurrencyGroup', 'Fields' => ['Id']]],
    ];
    public const TABLE_IDX = [
        ['IX_CTRContract_User_Status_Group' => ['RepositoryId', 'UserId', 'Status', 'ConcurrencyGroupId']],
        ['IX_CTRContract_Parent' => ['ParentContractId']],
    ];
    public const TABLE_CHECK = [];
    public const FIELDS_REQUIRED = ['Id', 'RepositoryId', 'UserId', 'ConcurrencyGroupId', 'Status'];
    public const FIELDS = [
        'Id',
        'RepositoryId',
        'UserId',
        'ParentContractId',
        'ConcurrencyGroupId',
        'Status',
        'BillingCycle',
        'StartDate',
        'EndDate',
        'ContractHash',
        'CreatedAt',
    ];
    public const FIELDS_MD = [
        'Id' => ['Type' => 'char', 'Length' => 36, 'Required' => true, 'Default' => null, 'LongLabel' => 'Contrato', 'ShortLabel' => 'Contrato', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'RepositoryId' => ['Type' => 'string', 'Length' => 65, 'Required' => true, 'Default' => null, 'LongLabel' => 'Repositório', 'ShortLabel' => 'Rep.', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'UserId' => ['Type' => 'int', 'Length' => 10, 'Required' => true, 'Default' => null, 'LongLabel' => 'Usuário', 'ShortLabel' => 'Usuário', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'ParentContractId' => ['Type' => 'char', 'Length' => 36, 'Required' => false, 'Default' => null, 'LongLabel' => 'Contrato anterior', 'ShortLabel' => 'Anterior', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'ConcurrencyGroupId' => ['Type' => 'int', 'Length' => 10, 'Required' => true, 'Default' => null, 'LongLabel' => 'Grupo de Concorrência', 'ShortLabel' => 'Grupo', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'Status' => ['Type' => 'string', 'Length' => 20, 'Required' => true, 'Default' => 'Draft', 'LongLabel' => 'Status', 'ShortLabel' => 'Status', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'BillingCycle' => ['Type' => 'string', 'Length' => 20, 'Required' => false, 'Default' => null, 'LongLabel' => 'Ciclo de Cobrança', 'ShortLabel' => 'Ciclo', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'StartDate' => ['Type' => 'datetime', 'Length' => 0, 'Required' => false, 'Default' => null, 'LongLabel' => 'Início', 'ShortLabel' => 'Início', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'EndDate' => ['Type' => 'datetime', 'Length' => 0, 'Required' => false, 'Default' => null, 'LongLabel' => 'Término', 'ShortLabel' => 'Término', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'ContractHash' => ['Type' => 'varchar', 'Length' => 256, 'Required' => false, 'Default' => null, 'LongLabel' => 'Hash do Contrato', 'ShortLabel' => 'Hash', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'CreatedAt' => ['Type' => 'datetime', 'Length' => 0, 'Required' => false, 'DefaultSql' => 'CURRENT_TIMESTAMP', 'LongLabel' => 'Criação', 'ShortLabel' => 'Criação', 'TextPlaceholder' => '', 'TextHelp' => ''],
    ];
    public const FIELDS_FOREIGN = ['CasRps' => ['FIELDS' => CasRpsMD::FIELDS], 'CTRConcurrencyGroup' => ['FIELDS' => CTRConcurrencyGroupMD::FIELDS]];
    public const FIELDS_MD_FOREIGN = ['CasRps' => ['FIELDS_MD' => CasRpsMD::FIELDS_MD], 'CTRConcurrencyGroup' => ['FIELDS_MD' => CTRConcurrencyGroupMD::FIELDS_MD]];
}
