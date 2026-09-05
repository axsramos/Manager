<?php

namespace App\Models\CAS;

use App\Core\Database;
use App\Metadata\CAS\CasTusMD;
use App\Models\Traits\CrudOperationsTrait;
use App\Traits\ReferencedTables;
use PDO;

class CasTusModel extends CasTusMD
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
    private $tbl = 'CasTus';
    private $selectedFields = array();
    private static $LOGICAL_EXCLUSION = false;
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
        if (empty($this->att['CasTusBlq'])) {
            $this->att['CasTusBlq'] = 'N';
        }
        if ($this->att['CasTusBlq'] == 'N') {
            $this->att['CasTusBlqDtt'] = null;
        }
        if ($this->att['CasTusBlq'] == 'S' && empty($this->att['CasTusBlqDtt'])) {
            $this->att['CasTusBlqDtt'] = $dtnow_str;
        }
    }

    // descontinuado //
    // public function existsDescription(): bool
    // {
    //     $qry = "SELECT CasRpsCod, CasTusCod, CasTusDsc FROM CasTus";
    //     $qry .= " WHERE CasRpsCod =:CasRpsCod ";
    //     $qry .= "   AND CasTusDsc =:CasTusDsc ";
    //     $qry .= ' ORDER BY CasRpsCod, CasTusBlq, CasTusDsc';
    //     $qry .= ' LIMIT 1;';
        
    //     /**
    //      * Query parameters
    //      */
    //     $parameters[':CasRpsCod'] = $this->att['CasRpsCod'];
    //     $parameters[':CasTusDsc'] = $this->att['CasTusDsc'];

    //     /**
    //      * Run query
    //      */
    //     $stmt = $this->cnx->executeQuery(__FILE__, $qry, $parameters);
    //     $rows = $stmt->rowCount();

    //     if ($rows) {
    //         $dataRow = $stmt->fetch(PDO::FETCH_ASSOC);

    //         $this->att['CasRpsCod'] = $dataRow['CasRpsCod'];
    //         $this->att['CasTusCod'] = $dataRow['CasTusCod'];
    //         $this->att['CasTusDsc'] = $dataRow['CasTusDsc'];
    //     }

    //     return boolval($rows);
    // }
}
