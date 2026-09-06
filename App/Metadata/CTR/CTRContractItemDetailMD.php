<?php

namespace App\Metadata\CTR;

class CTRContractItemDetailMD
{
    public const LOGICAL_EXCLUSION = false;
    public const FIELDS_PK = ['ContractItemId'];
    public const FIELDS_FK = [
        ['FK_CTRItemDetail_ContractItem' => ['FieldsKey' => ['ContractItemId'], 'References' => 'CTRContractItem', 'Fields' => ['Id'], 'OnDelete' => 'CASCADE']],
    ];
    public const TABLE_IDX = [];
    public const TABLE_CHECK = [];
    public const FIELDS_REQUIRED = ['ContractItemId'];
    public const FIELDS = ['ContractItemId', 'ItemAttributes', 'CustomNotes'];
    public const FIELDS_MD = [
        'ContractItemId' => ['Type' => 'int', 'Length' => 10, 'Required' => true, 'Default' => null, 'LongLabel' => 'Item', 'ShortLabel' => 'Item', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'ItemAttributes' => ['Type' => 'json', 'Length' => 0, 'Required' => false, 'Default' => null, 'LongLabel' => 'Atributos', 'ShortLabel' => 'Atributos', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'CustomNotes' => ['Type' => 'text', 'Length' => 0, 'Required' => false, 'Default' => null, 'LongLabel' => 'Observações', 'ShortLabel' => 'Obs.', 'TextPlaceholder' => '', 'TextHelp' => ''],
    ];
    public const FIELDS_FOREIGN = ['CTRContractItem' => ['FIELDS' => CTRContractItemMD::FIELDS]];
    public const FIELDS_MD_FOREIGN = ['CTRContractItem' => ['FIELDS_MD' => CTRContractItemMD::FIELDS_MD]];
}
