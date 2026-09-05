<?php

namespace App\Models\CAS;

use App\Core\Database;
use App\Metadata\CAS\CasRpaMD;
use App\Models\Traits\CrudOperationsTrait;

class CasRpaModel extends CasRpaMD
{
    use CrudOperationsTrait;

    /**
     * Attributes
     */
    private $att;

    /**
     * Controls
     */
    private $cnx;
    private $tbl = 'CasRpa';
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
        if (empty($this->att['CasRpaBlq'])) {
            $this->att['CasRpaBlq'] = 'N';
        }
        if ($this->att['CasRpaBlq'] == 'N') {
            $this->att['CasRpaBlqDtt'] = null;
        }
        if ($this->att['CasRpaBlq'] == 'S') {
            $this->att['CasRpaBlqDtt'] = $dtnow_str;
        }
        if (empty($this->att['CasRpaKey'])) {
            $this->att['CasRpaKey'] = uniqid();
        }
        if (empty($this->att['CasRpaKeyExp'])) {
            $this->att['CasRpaKeyExp'] = null;
        }
        // CasRpaGrp is filled by CrudOperationsTrait because both PK fields
        // are foreign keys and therefore require a random group value.
    }
}
