<?php

namespace App\Metadata\CTR;

class CTRContractDetailMD
{
    public const LOGICAL_EXCLUSION = false;
    public const FIELDS_PK = ['ContractId'];
    public const FIELDS_FK = [
        ['FK_CTRContractDetail_Contract' => ['FieldsKey' => ['ContractId'], 'References' => 'CTRContract', 'Fields' => ['Id'], 'OnDelete' => 'CASCADE']],
    ];
    public const TABLE_IDX = [];
    public const TABLE_CHECK = [];
    public const FIELDS_REQUIRED = ['ContractId'];
    public const FIELDS = ['ContractId', 'TemplatePath', 'TemplateContent', 'CustomTerms'];
    public const FIELDS_MD = [
        'ContractId' => ['Type' => 'char', 'Length' => 36, 'Required' => true, 'Default' => null, 'LongLabel' => 'Contrato', 'ShortLabel' => 'Contrato', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'TemplatePath' => ['Type' => 'string', 'Length' => 255, 'Required' => false, 'Default' => null, 'LongLabel' => 'Template', 'ShortLabel' => 'Template', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'TemplateContent' => ['Type' => 'longtext', 'Length' => 0, 'Required' => false, 'Default' => null, 'LongLabel' => 'Conteúdo', 'ShortLabel' => 'Conteúdo', 'TextPlaceholder' => '', 'TextHelp' => ''],
        'CustomTerms' => ['Type' => 'text', 'Length' => 0, 'Required' => false, 'Default' => null, 'LongLabel' => 'Termos', 'ShortLabel' => 'Termos', 'TextPlaceholder' => '', 'TextHelp' => ''],
    ];
    public const FIELDS_FOREIGN = ['CTRContract' => ['FIELDS' => CTRContractMD::FIELDS]];
    public const FIELDS_MD_FOREIGN = ['CTRContract' => ['FIELDS_MD' => CTRContractMD::FIELDS_MD]];
}
