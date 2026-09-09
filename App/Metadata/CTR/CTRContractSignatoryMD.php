<?php

namespace App\Metadata\CTR;

class CTRContractSignatoryMD
{
    public const LOGICAL_EXCLUSION = false;
    public const FIELDS_PK = ['Id'];
    public const FIELDS_FK = [
        ['FK_CTRSignatory_Repository' => ['FieldsKey' => ['RepositoryId'], 'References' => 'CasRps', 'Fields' => ['CasRpsCod']]],
        ['FK_CTRSignatory_Contract' => ['FieldsKey' => ['ContractId'], 'References' => 'CTRContract', 'Fields' => ['Id'], 'OnDelete' => 'CASCADE']],
    ];
    public const TABLE_IDX = [
        ['IX_CTRSignatory_Contract' => ['ContractId', 'SignedAt']],
    ];
    public const TABLE_CHECK = [];
    public const FIELDS_REQUIRED = ['RepositoryId', 'ContractId', 'UserId', 'Role', 'SignedAt', 'IpAddress'];
    public const FIELDS = [
        'Id',
        'RepositoryId',
        'ContractId',
        'UserId',
        'Role',
        'SignedAt',
        'IpAddress',
        'DeviceFingerprint',
    ];
    public const FIELDS_MD = [
        'Id' => ['Type' => 'int', 'Length' => 10, 'Required' => true, 'Default' => null, 'AutoIncrement' => true, 'LongLabel' => 'Signatário', 'ShortLabel' => 'Sign.', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'RepositoryId' => ['Type' => 'string', 'Length' => 65, 'Required' => true, 'Default' => null, 'LongLabel' => 'Repositório', 'ShortLabel' => 'Rep.', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'ContractId' => ['Type' => 'char', 'Length' => 36, 'Required' => true, 'Default' => null, 'LongLabel' => 'Contrato', 'ShortLabel' => 'Contrato', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'UserId' => ['Type' => 'string', 'Length' => 65, 'Required' => true, 'Default' => null, 'LongLabel' => 'Usuário', 'ShortLabel' => 'Usuário', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'Role' => ['Type' => 'string', 'Length' => 50, 'Required' => true, 'Default' => null, 'LongLabel' => 'Papel', 'ShortLabel' => 'Papel', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'SignedAt' => ['Type' => 'datetime', 'Length' => 0, 'Required' => true, 'DefaultSql' => 'CURRENT_TIMESTAMP', 'LongLabel' => 'Assinado em', 'ShortLabel' => 'Assinado', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'IpAddress' => ['Type' => 'string', 'Length' => 45, 'Required' => true, 'Default' => null, 'LongLabel' => 'IP', 'ShortLabel' => 'IP', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'DeviceFingerprint' => ['Type' => 'string', 'Length' => 255, 'Required' => false, 'Default' => null, 'LongLabel' => 'Dispositivo', 'ShortLabel' => 'Dispositivo', 'TextPlaceholder' => '', 'TextHelp' => ''],
    ];
    public const FIELDS_FOREIGN = ['CasRps' => ['FIELDS' => CasRpsMD::FIELDS], 'CTRContract' => ['FIELDS' => CTRContractMD::FIELDS]];
    public const FIELDS_MD_FOREIGN = ['CasRps' => ['FIELDS_MD' => CasRpsMD::FIELDS_MD], 'CTRContract' => ['FIELDS_MD' => CTRContractMD::FIELDS_MD]];
}
