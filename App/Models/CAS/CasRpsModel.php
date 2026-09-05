<?php

namespace App\Models\CAS;

use App\Core\Database;
use App\Metadata\CAS\CasRpsMD;
use App\Models\Traits\CrudOperationsTrait;
use App\Traits\ReferencedTables;

class CasRpsModel extends CasRpsMD
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
    private $tbl = 'CasRps';
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

    // public function setSelectedFields(array | null $fields = null, int | null $index = null): void
    // {
    //     if (is_null($fields)) {
    //         $this->selectedFields = static::FIELDS;
    //     } else {
    //         $this->selectedFields = array_intersect($fields, static::FIELDS);
    //         $this->selectedFields = array_values($this->selectedFields);
    //     }

    //     $this->setSelectedIndex($index);
    // }

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
        if (empty($this->att['CasRpsBlq'])) {
            $this->att['CasRpsBlq'] = 'N';
        }
        if ($this->att['CasRpsBlq'] == 'N') {
            $this->att['CasRpsBlqDtt'] = null;
        }
        if ($this->att['CasRpsBlq'] == 'S' && empty($this->att['CasRpsBlqDtt'])) {
            $this->att['CasRpsBlqDtt'] = $dtnow_str;
        }
        if (empty($this->att['CasRpsKey'])) {
            $this->att['CasRpsKey'] = uniqid();
        }
        if (empty($this->att['CasRpsKeyExp'])) {
            $this->att['CasRpsKeyExp'] = null;
        }
        if (empty($this->att['CasRpsGrp'])) {
            $this->att['CasRpsGrp'] = $this->att['CasRpsCod'];
        }
    }
}
