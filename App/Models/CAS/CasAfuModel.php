<?php

namespace App\Models\CAS;

use App\Core\Database;
use App\Metadata\CAS\CasAfuMD;
use App\Models\Traits\CrudOperationsTrait;
use PDO;

class CasAfuModel extends CasAfuMD
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
    private $tbl = 'CasAfu';
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
            $logData = array('program' => __FILE__, 'line' => __LINE__, 'message'=> $msg_err);
            Database::setLog(json_encode($logData), 'trigger_error', 'Runtime');
            // end LogErros
        }
    }

    /**
     * Others
     */
    private function defaultValuesForNotNulls($dtnow_str) {}

    public function existsProfileAutorized(): bool
    {
        /**
         * Query read
         */
        $parameters = [];
        $qry = "
        select
            exists(
                select
                CasAfu.CasRpsCod,
                CasAfu.CasPfiCod,
                CasAfu.CasPrgCod,
                CasAfu.CasFunCod
                from CasAfu
                where CasAfu.CasRpsCod = :CasRpsCod
                and CasAfu.CasPfiCod = :CasPfiCod
                and CasAfu.CasPrgCod = :CasPrgCod
                and CasAfu.CasFunCod = :CasFunCod
                limit 1
            ) as Autorized;
        ";
        
        /**
         * Query parameters
         */
        $parameters['CasRpsCod'] = $this->att['CasRpsCod'];
        $parameters['CasPfiCod'] = $this->att['CasPfiCod'];
        $parameters['CasPrgCod'] = $this->att['CasPrgCod'];
        $parameters['CasFunCod'] = $this->att['CasFunCod'];

        /**
         * Run query
         */
        $stmt = $this->cnx->executeQuery(static::class, $qry, $parameters);
        $this->dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return boolval($this->dataRows[0]['Autorized']);
    }

    public function deleteAllUserAuthorized()
    {
        /**
         * Query read
         */
        $parameters = [];
        $qry = "
        delete from CasAfu
        where (CasRpsCod = :CasRpsCod 
          and CasPfiCod = :CasPfiCod
          and :CasUsrCod = ''
          and CasPrgCod = :CasPrgCod
          and CasFunCod = :CasFunCod)
           or (CasRpsCod = :CasRpsCod 
          and :CasPfiCod = ''
          and :CasUsrCod = ''
          and CasPrgCod = :CasPrgCod
          and CasFunCod = :CasFunCod)
           or (CasRpsCod = :CasRpsCod 
          and CasPfiCod = :CasPfiCod
          and :CasUsrCod = ''
          and CasPrgCod = :CasPrgCod
          and :CasFunCod = '' )
           or (CasRpsCod = :CasRpsCod 
          and CasPfiCod = :CasPfiCod
          and CasUsrCod = :CasUsrCod
          and :CasPrgCod = ''
          and :CasFunCod = '' )
        ;
        ";

        /**
         * Query parameters
         */
        $parameters['CasRpsCod'] = $this->att['CasRpsCod'];
        $parameters['CasPfiCod'] = $this->att['CasPfiCod'];
        $parameters['CasPrgCod'] = $this->att['CasPrgCod'];
        $parameters['CasFunCod'] = $this->att['CasFunCod'];
        $parameters['CasUsrCod'] = $this->att['CasUsrCod'];

        /**
         * Run query
         */
        $stmt = $this->cnx->executeQuery(static::class, $qry, $parameters);
        $this->dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insertAllUserAuthorized(): void
    {
        /**
         * Query read
         */
        $parameters = [];
        $qry = "
        insert into CasAfu (
            CasAfu.CasRpsCod,
            CasAfu.CasPfiCod,
            CasAfu.CasUsrCod,
            CasAfu.CasPrgCod,
            CasAfu.CasFunCod,
            CasAfu.CasAfuAudIns
        )
        select 
            CasApf.CasRpsCod,
            CasApf.CasPfiCod,
            CasApf.CasUsrCod,
            CasApf.CasPrgCod,
            :CasFunCod,
            NOW()
        from CasApf
        where CasApf.CasRpsCod = :CasRpsCod
            and CasApf.CasPfiCod = :CasPfiCod
            and CasApf.CasPrgCod = :CasPrgCod
            and not exists (
        select 1
            from CasAfu existing_afu
            where existing_afu.CasRpsCod = :CasRpsCod
                and existing_afu.CasPfiCod = :CasPfiCod
                and existing_afu.CasPrgCod = :CasPrgCod
                and existing_afu.CasUsrCod = CasApf.CasUsrCod
                and existing_afu.CasFunCod = :CasFunCod
        );";

        /**
         * Query parameters
         */
        $parameters['CasRpsCod'] = $this->att['CasRpsCod'];
        $parameters['CasPfiCod'] = $this->att['CasPfiCod'];
        $parameters['CasPrgCod'] = $this->att['CasPrgCod'];
        $parameters['CasFunCod'] = $this->att['CasFunCod'];

        /**
         * Run query
         */
        $stmt = $this->cnx->executeQuery(static::class, $qry, $parameters);
        $this->dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
