<?php

namespace App\Models\CAS;

use App\Core\Database;
use PDO;
use App\Metadata\CAS\CasApfMD;
use App\Models\Traits\CrudOperationsTrait;

class CasApfModel extends CasApfMD
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
    private $tbl = 'CasApf';
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
    private function defaultValuesForNotNulls($dtnow_str) {}

    public function listAccessRulesQuantity(): array
    {
        /**
         * Query read
         */
        $parameters = [];
        $qry = "
        select 
            CasRps.CasRpsCod,
            CasPfi.CasPfiCod,
            CasPfi.CasPfiDsc,
            CasPfi.CasPfiBlq,
            (
                select count(*) as QtdMdl from (
                    select CasMpr.CasMdlCod
                    from CasApf, CasMpr
                    where CasApf.CasRpsCod = CasRps.CasRpsCod
                    and CasApf.CasPfiCod = CasPfi.CasPfiCod
                    and CasMpr.CasRpsCod = CasApf.CasRpsCod
                    and CasMpr.CasPrgCod = CasApf.CasPrgCod
                    group by CasMpr.CasMdlCod
                ) as QtdMdl
            ) as QtdMdl,
            (
                select count(*) as QtdPrg from (
                    select CasApf.CasPrgCod
                    from CasApf 
                    where CasApf.CasRpsCod = CasRps.CasRpsCod
                    and CasApf.CasPfiCod = CasPfi.CasPfiCod
                    group by CasApf.CasPrgCod
                ) as QtdPrg
            ) as QtdPrg,
            (
                select count(*) as QtdUsr from (
                    select CasRpu.CasUsrCod
                    from CasRpu, CasPfu
                    where CasRpu.CasRpsCod = CasRps.CasRpsCod
                    and CasPfu.CasRpsCod = CasRps.CasRpsCod
                    and CasPfu.CasPfiCod = CasPfi.CasPfiCod
                    and CasPfu.CasUsrCod = CasRpu.CasUsrCod
                    group by CasRpu.CasUsrCod
                ) as QtdUsr
            ) as QtdUsr
        from CasRps
        inner join CasPfi
            on CasPfi.CasRpsCod = CasRps.CasRpsCod
        where CasRps.CasRpsCod = :CasRpsCod
        ;";

        /**
         * Query parameters
         */
        $parameters['CasRpsCod'] = $this->att['CasRpsCod'];

        /**
         * Run query
         */
        $stmt = $this->cnx->executeQuery(static::class, $qry, $parameters);
        $this->dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->dataRows;
    }

    public function listModulesQuantityPrograms(): array
    {
        /**
         * Query read
         */
        $parameters = [];
        $qry = "
        select 
            CasRps.CasRpsCod, CasPfi.CasPfiCod,
            CasMdl.CasMdlCod, CasMdl.CasMdlDsc, CasMdl.CasMdlBlq,
            (
                select count(*) as QtdPrg from (
                    select count(CasApf.CasPrgCod) as QtdPrg 
                    from CasApf, CasMpr
                    where CasApf.CasRpsCod = CasRps.CasRpsCod
                      and CasApf.CasPfiCod = CasPfi.CasPfiCod
                      and CasMpr.CasRpsCod = CasRps.CasRpsCod
                      and CasMpr.CasPrgCod = CasApf.CasPrgCod
                      and CasMpr.CasMdlCod = CasMdl.CasMdlCod
                    group by CasApf.CasPrgCod
                ) as QtdPrg
            ) as QtdPrg
        from CasRps, CasPfi, CasMdl
        where CasRps.CasRpsCod = :CasRpsCod
        and CasPfi.CasRpsCod = CasRps.CasRpsCod
        and CasPfi.CasPfiCod = :CasPfiCod
        and CasMdl.CasRpsCod = CasRps.CasRpsCod
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

        return $this->dataRows;
    }

    public function listProgramsInModulesAutorized(string $pfi_id, string $mdl_id): array
    {
        /**
         * Query read
         */
        $parameters = [];
        $qry = "
        select 
        CasRps.CasRpsCod, CasMpr.CasMdlCod,
        CasPrg.CasPrgCod, CasPrg.CasPrgDsc, CasPrg.CasPrgBlq, CasPrg.CasPrgTst,
        (
            select case when count(*) > 0 then 'S' else 'N' end from (
            select CasApf.CasRpsCod, CasApf.CasPfiCod, CasApf.CasPrgCod from CasApf
            where CasApf.CasRpsCod = CasRps.CasRpsCod
            and CasApf.CasPfiCod = :CasPfiCod
            and CasApf.CasPrgCod = CasPrg.CasPrgCod
            group by CasApf.CasRpsCod, CasApf.CasPfiCod, CasApf.CasPrgCod
            ) as GroupCasApf
        ) as AutorizedProgram,
        (
            select count(CasFpr.CasFunCod) from CasFpr 
            where CasFpr.CasRpsCod = CasRps.CasRpsCod
            and CasFpr.CasPrgCod = CasPrg.CasPrgCod
        ) as Features,
        (
            select count(*) as CasFunCod from (
            select
                CasAfu.CasRpsCod, CasAfu.CasPfiCod, CasAfu.CasPrgCod, CasAfu.CasFunCod
            from
                CasAfu,
                CasFun
            where
                CasAfu.CasRpsCod = CasRps.CasRpsCod
                and CasAfu.CasPfiCod = :CasPfiCod
                and CasAfu.CasPrgCod = CasPrg.CasPrgCod
                and CasFun.CasRpsCod = CasRps.CasRpsCod
                and CasFun.CasFunCod = CasAfu.CasFunCod
                and CasFun.CasFunBlq = 'N'
                group by CasAfu.CasRpsCod, CasAfu.CasPfiCod, CasAfu.CasPrgCod, CasAfu.CasFunCod
            ) as AutorizedFeatures
        ) as AutorizedFeatures
        from CasRps, CasMpr, CasPrg
        where CasRps.CasRpsCod = :CasRpsCod
        and CasMpr.CasRpsCod = CasRps.CasRpsCod
        and CasMpr.CasMdlCod = :CasMdlCod
        and CasPrg.CasRpsCod = CasRps.CasRpsCod
        and CasPrg.CasPrgCod = CasMpr.CasPrgCod
        order by CasRps.CasRpsCod asc , CasMpr.CasMdlCod asc, CasPrg.CasPrgDsc asc;";

        /**
         * Query parameters
         */
        $parameters['CasRpsCod'] = $this->att['CasRpsCod'];
        $parameters['CasMdlCod'] = $mdl_id;
        $parameters['CasPfiCod'] = $pfi_id;

        /**
         * Run query
         */
        $stmt = $this->cnx->executeQuery(static::class, $qry, $parameters);
        $this->dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->dataRows;
    }

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
                CasApf.CasRpsCod,
                CasApf.CasPfiCod,
                CasApf.CasPrgCod
                from CasApf
                where CasApf.CasRpsCod = :CasRpsCod
                and CasApf.CasPfiCod = :CasPfiCod
                and CasApf.CasPrgCod = :CasPrgCod
                limit 1
            ) as Autorized;
        ";
        
        /**
         * Query parameters
         */
        $parameters['CasRpsCod'] = $this->att['CasRpsCod'];
        $parameters['CasPfiCod'] = $this->att['CasPfiCod'];
        $parameters['CasPrgCod'] = $this->att['CasPrgCod'];

        /**
         * Run query
         */
        $stmt = $this->cnx->executeQuery(static::class, $qry, $parameters);
        $this->dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return boolval($this->dataRows[0]['Autorized']);
    }

    public function insertAllUserAuthorized(): void
    {
        /**
         * Query read
         */
        $parameters = [];
        $qry = "
        INSERT INTO CasApf (
            CasApf.CasRpsCod,
            CasApf.CasPfiCod,
            CasApf.CasUsrCod,
            CasApf.CasPrgCod,
            CasApf.CasApfAudIns
        )
        SELECT
            CasRpu.CasRpsCod,
            :CasPfiCod,
            CasRpu.CasUsrCod,
            :CasPrgCod,
            NOW()
        FROM CasRpu
        inner join CasPfu on CasPfu.CasRpsCod = CasRpu.CasRpsCod
          and CasPfu.CasPfiCod = :CasPfiCod
          and CasPfu.CasUsrCod = CasRpu.CasUsrCod
        WHERE CasRpu.CasRpsCod = :CasRpsCod
        AND NOT EXISTS (
            SELECT 1
            FROM CasApf existing_apf
            WHERE existing_apf.CasRpsCod = CasRpu.CasRpsCod
            AND existing_apf.CasUsrCod = CasRpu.CasUsrCod
            AND existing_apf.CasPfiCod = :CasPfiCod
            AND existing_apf.CasPrgCod = :CasPrgCod
        );
        ";

        /**
         * Query parameters
         */
        $parameters['CasRpsCod'] = $this->att['CasRpsCod'];
        $parameters['CasPfiCod'] = $this->att['CasPfiCod'];
        $parameters['CasPrgCod'] = $this->att['CasPrgCod'];

        /**
         * Run query
         */
        $stmt = $this->cnx->executeQuery(static::class, $qry, $parameters);
        $this->dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    }

    public function deleteAllUserAuthorized()
    {
        /**
         * Query read
         */
        $parameters = [];
        $qry = "
        delete from CasApf
        where (CasRpsCod = :CasRpsCod 
          and CasPfiCod = :CasPfiCod
          and :CasUsrCod = ''
          and CasPrgCod = :CasPrgCod)
           or (CasRpsCod = :CasRpsCod 
          and :CasPfiCod = ''
          and :CasUsrCod = ''
          and CasPrgCod = :CasPrgCod)
           or (CasRpsCod = :CasRpsCod 
          and CasPfiCod = :CasPfiCod
          and CasUsrCod = :CasUsrCod
          and :CasPrgCod = '')
        ;
        ";

        /**
         * Query parameters
         */
        $parameters['CasRpsCod'] = $this->att['CasRpsCod'];
        $parameters['CasPfiCod'] = $this->att['CasPfiCod'];
        $parameters['CasPrgCod'] = $this->att['CasPrgCod'];
        $parameters['CasUsrCod'] = $this->att['CasUsrCod'];

        /**
         * Run query
         */
        $stmt = $this->cnx->executeQuery(static::class, $qry, $parameters);
        $this->dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
