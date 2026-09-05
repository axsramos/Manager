<?php

namespace App\Models\CAS;

use App\Core\Database;
use App\Metadata\CAS\CasPfuMD;
use App\Models\Traits\CrudOperationsTrait;
use PDO;

class CasPfuModel extends CasPfuMD
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
    private $tbl = 'CasPfu';
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
        } else {
            return null;
        }
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
        if (empty($this->att['CasPfuBlq'])) {
            $this->att['CasPfuBlq'] = 'N';
        }
        if ($this->att['CasPfuBlq'] == 'N') {
            $this->att['CasPfuBlqDtt'] = null;
        }
        if ($this->att['CasPfuBlq'] == 'S' && empty($this->att['CasPfuBlqDtt'])) {
            $this->att['CasPfuBlqDtt'] = $dtnow_str;
        }
    }

    public function usersWithProfile()
    {
        /**
         * Query read
         */
        $parameters = [];
        $qry = "
        select count(*) as QtdUsers
        from CasPfu
        where CasPfu.CasRpsCod = :CasRpsCod
        and CasPfu.CasPfiCod = :CasPfiCod
        ;
        ";

        /**
         * Query parameters
         */
        $parameters['CasRpsCod'] = $this->att['CasRpsCod'];
        $parameters['CasPfiCod'] = $this->att['CasPfiCod'];

        /**
         * Run query
         */
        $stmt = $this->cnx->executeQuery(static::class, $qry, $parameters);
        $this->dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->dataRows[0]['QtdUsers'];
    }
}
