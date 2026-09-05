<?php

namespace App\Traits;

use App\Core\Config;
use App\Core\Database;
use PDO;

trait ReferencedTables
{
    public static function getReferencedTables(string $tableName, string $fieldName): array
    {
        $database = Config::$DB_STORAGE['Default']['DB_DATABASE'];
        $tables = [];
        $parameters = [];

        $cnx = new Database();

        /**
         * Query read
         */
        $qry  = "SELECT TABLE_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE";
        $qry .= " WHERE REFERENCED_TABLE_SCHEMA = :DBName";
        $qry .= " AND REFERENCED_TABLE_NAME = :TBName";
        $qry .= " AND TABLE_SCHEMA = :DBName";
        $qry .= " AND CONSTRAINT_NAME != :PKName";

        /**
         * Query parameters
         */
        $parameters[':DBName'] = $database;
        $parameters[':TBName'] = $tableName;
        $parameters[':PKName'] = $fieldName;

        /**
         * Run query
         */
        $stmt = $cnx->executeQuery($qry, $parameters);
        $dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($dataRows) {
            foreach ($dataRows as $row) {
                $tables[] = $row['TABLE_NAME'];
            }
        }

        return $tables;
    }

    public static function isEmptyAllReferencialTables(string $tableName, string $fieldName, array $fieldsKeys, array $fieldsValues): bool
    {

        $referencedTables = self::getReferencedTables($tableName, $fieldName);

        $cnx = new Database();
        $rows = 0;

        foreach ($referencedTables as $refTable) {
            /**
             * Query read
             */
            $parameters = [];

            $qry  = "SELECT COUNT(*) FROM ";
            $qry .= $refTable;
            $qry .= " WHERE (";
            foreach ($fieldsKeys as $key => $fieldKey) {
                if ($key > 0) {
                    $qry .= " AND ";
                }
                $field = "{$refTable}.{$fieldKey}";
                $qry .= " {$field} = :{$fieldKey}";
                $parameters[":{$fieldKey}"] = $fieldsValues[$fieldKey];
            }
            $qry .= " );";

            /**
             * Run query
             */
            $stmt = $cnx->executeQuery($qry, $parameters);
            $rows = $stmt->fetchColumn();
            if ($rows > 0) {
                break;
            }
        }

        // rows é zero quando não há uso do registro em outras tabelas //
        return ($rows == 0 ? true : false);
    }
}
