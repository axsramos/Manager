<?php

namespace App\Models\CAS;

use App\Core\Database;
use App\Metadata\CAS\CasUsrMD;
use App\Models\Traits\CrudOperationsTrait;
use App\Traits\ReferencedTables;
use PDO;

class CasUsrModel extends CasUsrMD
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
    private $tbl = 'CasUsr';
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

    public function __isset(string $name): bool
    {
        return in_array($name, static::FIELDS, true)
            && isset($this->att[$name]);
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
        if (empty($this->att['CasUsrBlq'])) {
            $this->att['CasUsrBlq'] = 'N';
        }
        if ($this->att['CasUsrBlq'] == 'N') {
            $this->att['CasUsrBlqDtt'] = null;
        }
        if ($this->att['CasUsrBlq'] == 'S' && empty($this->att['CasUsrBlqDtt'])) {
            $this->att['CasUsrBlqDtt'] = $dtnow_str;
        }
    }

    public function existsMailAccount(): bool
    {
        $qry = "SELECT CasUsrCod FROM CasUsr";
        $qry .= " WHERE CasUsrDmn = :CasUsrDmn AND CasUsrLgn = :CasUsrLgn";
        $qry .= ' ORDER BY CasUsrDmn, CasUsrLgn';
        $qry .= ' LIMIT 1;';
        
        /**
         * Query parameters
         */
        $parameters[':CasUsrDmn'] = $this->att['CasUsrDmn'];
        $parameters[':CasUsrLgn'] = $this->att['CasUsrLgn'];

        /**
         * Run query
         */
        $stmt = $this->cnx->executeQuery(__FILE__, $qry, $parameters);
        $rows = $stmt->rowCount();

        if ($rows) {
            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);

            $this->att['CasUsrCod'] = $dataRow['CasUsrCod'];
        }

        return boolval($rows);
    }

    public function checkLogin(): bool
    {
        $selectedFields = array('CasUsrCod', 'CasUsrNme', 'CasUsrSnm', 'CasUsrNck', 'CasUsrDsc', 'CasUsrDmn', 'CasUsrLgn', 'CasUsrActDtt', 'CasUsrBlq', 'CasUsrAudIns');

        $qry = "SELECT ";
        foreach ($selectedFields as $key => $field) {
            if ($key == 0) {
                $qry .= $field;
                continue;
            }
            $qry .= ', ' . $field;
        }
        $qry .= " FROM CasUsr";
        $qry .= " WHERE CasUsrDmn = :CasUsrDmn AND CasUsrLgn = :CasUsrLgn AND CasUsrPwd = :CasUsrPwd";
        $qry .= ' ORDER BY CasUsrDmn, CasUsrLgn';
        $qry .= ' LIMIT 1;';
        
        /**
         * Query parameters
         */
        $parameters[':CasUsrDmn'] = $this->att['CasUsrDmn'];
        $parameters[':CasUsrLgn'] = $this->att['CasUsrLgn'];
        $parameters[':CasUsrPwd'] = $this->att['CasUsrPwd'];

        /**
         * Run query
         */
        $stmt = $this->cnx->executeQuery(__FILE__, $qry, $parameters);
        $rows = $stmt->rowCount();
        if ($rows) {
            $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);
            foreach ($selectedFields as $field) {
                if (array_key_exists($field, $dataRow)) {
                    $this->att[$field] = $dataRow[$field];
                }
            }
        }
        
        return boolval($rows);
    }
}
