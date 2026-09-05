<?php

namespace App\Models\CAS;

use App\Core\Database;
use App\Metadata\CAS\CasRpuMD;
use App\Models\Traits\CrudOperationsTrait;
use PDO;

class CasRpuModel extends CasRpuMD
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
    private $tbl = 'CasRpu';
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
        if (empty($this->att['CasRpuBlq'])) {
            $this->att['CasRpuBlq'] = 'N';
        }
        if ($this->att['CasRpuBlq'] == 'N') {
            $this->att['CasRpuBlqDtt'] = null;
        }
        if ($this->att['CasRpuBlq'] == 'S' && empty($this->att['CasRpuBlqDtt'])) {
            $this->att['CasRpuBlqDtt'] = $dtnow_str;
        }
    }

    public function getAllRepositories()
    {
        /**
         * Query read
         */
        $qry = "
        SELECT
            CasRps.CasRpsCod as CasRpsCod,
            CasRps.CasRpsDsc as CasRpsDsc,
            CasRps.CasRpsGrp as CasRpsGrp,
            CasRps.CasRpsObs as CasRpsObs,
            CasRps.CasRpsBlq as CasRpsBlq,
            CasRps.CasRpsBlqDtt as CasRpsBlqDtt,
            CasRpu.CasUsrCod as CasUsrCod,
            CasTus.CasTusCod as CasTusCod,
            CasTus.CasTusDsc as CasTusDsc,
            CasTus.CasTusLnk as CasTusLnk,
            CasRpu.CasRpuDsc as CasRpuDsc,
            CasRpu.CasRpuBlq as CasRpuBlq,
            CasRpu.CasRpuBlqDtt as CasRpuBlqDtt
        FROM CasRpu 
        INNER JOIN CasRps 
             ON CasRps.CasRpsCod = CasRpu.CasRpsCod
        INNER JOIN CasTus 
             ON CasTus.CasRpsCod = CasRpu.CasRpsCod
            AND CasTus.CasTusCod = CasRpu.CasTusCod
        WHERE
            CasRpu.CasUsrCod = :CasUsrCod
        ;
        ";

        /**
         * Query parameters
         */
        $parameters['CasUsrCod'] = $this->att['CasUsrCod'];

        /**
         * Run query
         */
        $stmt = $this->cnx->executeQuery(__FILE__, $qry, $parameters);
        $this->dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $rows = count($this->dataRows);

        return boolval($rows);
    }
}
