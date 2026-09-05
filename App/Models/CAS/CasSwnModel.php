<?php

namespace App\Models\CAS;

use App\Core\Database;
use App\Metadata\CAS\CasSwnMD;
use App\Models\Traits\CrudOperationsTrait;
use App\Traits\ReferencedTables;
use PDO;

class CasSwnModel extends CasSwnMD
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
    private $tbl = 'CasSwn';
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
        // none //
    }

    public function getLastId()
    {
        /**
         * Query read
         */
        $qry  = "SELECT (1 + CasSwnCod) as CasSwnCod FROM CasSwn";
        $qry .= " WHERE CasRpsCod = :CasRpsCod AND CasSwbCod = :CasSwbCod ";
        $qry .= " ORDER BY CasSwnCod DESC LIMIT 1";

        $this->att['CasSwnCod'] = 1; // default initial value //

        /**
         * Query parameters
         */
        $parameters['CasRpsCod'] = $this->att['CasRpsCod'];
        $parameters['CasSwbCod'] = $this->att['CasSwbCod'];

        /**
         * Run query
         */
        $stmt = $this->cnx->executeQuery(static::class, $qry, $parameters);
        $rows = $stmt->rowCount();

        if ($rows) {
            $this->dataRows[0] = $stmt->fetch(PDO::FETCH_ASSOC);

            $this->att['CasSwnCod'] = $this->dataRows[0]['CasSwnCod'];
        }

        return $this->att['CasSwnCod'];
    }
}
