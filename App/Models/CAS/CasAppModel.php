<?php

namespace App\Models\CAS;

use App\Class\Pattern\FormatData;
use App\Core\Database;
use App\Metadata\CAS\CasAppMD;
use App\Models\Traits\CrudOperationsTrait;
use App\Traits\ReferencedTables;

class CasAppModel extends CasAppMD
{
    use CrudOperationsTrait;
    use ReferencedTables;

    /**
     * Attributes
     */
    private $att;

    /**
     * Controls
     */
    private $cnx;
    private $tbl = 'CasApp';
    private $selectedFields = array();
    private $queryOrder = '';
    private $dataRows = array();
    public $isDuplicated = false;
    public $referencedTables = array();

    public function __construct()
    {
        $this->cnx = new Database();

        $this->att = [];
        foreach (static::FIELDS_MD as $key => $value) {
            $this->att[$key] = $value['Default'] ?? null;
        }

        $this->setSelectedFields();
    }

    /**
     * GET
     */
    public function __get(string $name): mixed
    {
        if (in_array($name, static::FIELDS)) {
            return $this->att[$name] ?? null;
        }
        return null;
    }

    public function getRecords(): array
    {
        return $this->dataRows;
    }

    /**
     * SET
     */
    public function __set(string $name, mixed $value): void
    {
        if (in_array($name, static::FIELDS)) {
            $this->att[$name] = $value;
        }
    }

    public function setSelectedFields(array | null $fields = null, int | null $index = null): void
    {
        if (is_null($fields)) {
            $this->selectedFields = static::FIELDS;
        } else {
            $this->selectedFields = array_intersect($fields, static::FIELDS);
            $this->selectedFields = array_values($this->selectedFields);
        }

        $this->setSelectedIndex($index);
    }

    public function setSelectedIndex(int | null $index = null): void
    {
        $index = $index ?? 0;

        if (isset(static::TABLE_IDX[$index])) {
            $orderFields = static::TABLE_IDX[$index][0] ?? [];
            if (!empty($orderFields)) {
                $this->queryOrder = implode(', ', $orderFields);
            } else {
                $this->queryOrder = '';
            }
        } else {
            $this->queryOrder = '';

            /**
             * LogErros
             */
            $msg_err = "Índice de tabela inválido fornecido: {$index}";
            $logData = array('program' => __FILE__, 'line' => __LINE__, 'message' => $msg_err);
            Database::setLog(json_encode($logData), 'trigger_error', 'Runtime');
            // end LogErros
        }
    }

    /**
     * Others
     */
    private function defaultValuesForNotNulls($dtnow_str)
    {
        if (empty($this->att['CasAppBlq'])) {
            $this->att['CasAppBlq'] = static::FIELDS_MD['CasAppBlq']['Default'];
        }
        if ($this->att['CasAppBlq'] == 'N') {
            $this->att['CasAppBlqDtt'] = null;
        }
        if ($this->att['CasAppBlq'] == 'S' && empty($this->att['CasAppBlqDtt'])) {
            $this->att['CasAppBlqDtt'] = $dtnow_str;
        }
        if (empty($this->att['CasAppTst'])) {
            $this->att['CasAppTst'] = static::FIELDS_MD['CasAppTst']['Default'];
        }
        if ($this->att['CasAppTst'] == 'N') {
            $this->att['CasAppTstDtt'] = null;
        }
        if ($this->att['CasAppTst'] == 'S' && empty($this->att['CasAppTstDtt'])) {
            $this->att['CasAppTstDtt'] = $dtnow_str;
        }
        if (empty($this->att['CasAppVerDtt'])) {
            $this->att['CasAppVerDtt'] = null;
        }
        if (empty($this->att['CasAppKey'])) {
            $this->att['CasAppKey'] = uniqid();
        }
        if (empty($this->att['CasAppKeyExp'])) {
            $this->att['CasAppKeyExp'] = null;
        }
        if (empty($this->att['CasAppGrp'])) {
            $this->att['CasAppGrp'] = $this->att['CasAppCod'];
        }
    }
}
