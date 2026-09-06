<?php

namespace App\Models\Traits;

use DateTime;
use PDO;
use App\Core\AuthSession;
use App\Core\Config;

trait CrudOperationsTrait
{
    // O schema usado em consultas de metadados acompanha o storage da conexão do model.
    /**
     * Nota: Este trait assume que a classe que o usa terá as seguintes propriedades/constantes:
     * - Propriedades: $cnx, $tbl, $att, $selectedFields, $queryOrder.
     * - Constantes: FIELDS, FIELDS_PK, FIELDS_AUDIT, FIELDS_MD, TABLE_IDX, LOGICAL_EXCLUSION. Obtém da classe extendida de metadados.
     * - Métodos: newid(), checkDuplicateKey().
     * - Métodos específicos mantidos na classe principal: defaultValuesForNotNulls(), deleteReferencial().
     */

    /**
     * CRUD (create, reader, update, delete)
     */
    public function createRegister()
    {
        $pkField = static::FIELDS_PK[count(static::FIELDS_PK) - 1];
        if (empty($this->att[$pkField])) {
            $this->newid();
        } else {
            if ($this->checkDuplicateKey()) {
                return FALSE;
            }
        }

        /**
         * Mandatory on mode insert
         */
        $dtnow = new DateTime('now');
        $dtnow_str = $dtnow->format('Y-m-d H:i:s');
        $this->att[static::FIELDS_AUDIT[0]] = $dtnow_str; // AudIns
        $this->att[static::FIELDS_AUDIT[1]] = $dtnow_str; // AudUpd
        $this->att[static::FIELDS_AUDIT[2]] = null;      // AudDel
        $this->att[static::FIELDS_AUDIT[3]] = AuthSession::get()['USR_ID'] ?? null; // AudUsr

        /**
         * Default values for not nulls
         */
        $this->defaultValuesForNotNulls($dtnow_str);
        $this->applyDefaultGroupValue();

        /**
         * Query insert
         */
        $parameters = [];
        $queryFields = implode(', ', static::FIELDS);
        $placeholders = ':' . implode(', :', static::FIELDS);

        $qry  = "INSERT INTO {$this->tbl} ({$queryFields}) VALUES ({$placeholders});";

        /**
         * Query parameters
         */
        foreach (static::FIELDS as $field) {
            $parameters[$field] = $this->att[$field];
        }

        /**
         * Run query
         */
        $stmt = $this->cnx->executeQuery(static::class, $qry, $parameters);
        $rows = $stmt->rowCount();

        return boolval($rows);
    }

    public function readRegister()
    {
        /**
         * Query read
         */
        $selectedFields = [];
        foreach ($this->selectedFields as $key => $value) {
            if (in_array($value, static::FIELDS)) {
                $selectedFields[$key] = $value;
            }
        }

        $fields = implode(', ', $selectedFields);

        $parameters = [];
        $qry  = "SELECT {$fields} FROM {$this->tbl}";

        foreach (static::FIELDS_PK as $key => $pkField) {
            if ($key === 0) {
                $qry .= " WHERE ";
            } else {
                $qry .= " AND ";
            }
            $qry .= "{$pkField} = :{$pkField}";
        }

        if (!empty($this->queryOrder)) {
            $qry .= " ORDER BY {$this->queryOrder}";
        }

        $qry .= " LIMIT 1;";

        /**
         * Query parameters
         */
        foreach (static::FIELDS_PK as $key => $pkField) {
            $parameters[$pkField] = $this->att[$pkField];
        }

        /**
         * Run query
         */
        $stmt = $this->cnx->executeQuery(static::class, $qry, $parameters);
        $rows = $stmt->rowCount();

        if ($rows) {
            $this->dataRows[0] = $stmt->fetch(PDO::FETCH_ASSOC);

            foreach ($selectedFields as $field) {
                if (array_key_exists($field, $this->dataRows[0])) {
                    $this->att[$field] = $this->dataRows[0][$field];
                }
            }
        } else {
            $this->dataRows = [];
        }

        return boolval($rows);
    }

    public function readRegisterJoin(array $tablesForeign = [])
    {
        return $this->readAllLinesJoin($tablesForeign);
    }

    public function updateRegister()
    {
        /**
         * Mandatory on mode update
         */
        $dtnow = new DateTime('now');
        $dtnow_str = $dtnow->format('Y-m-d H:i:s');
        $this->att[static::FIELDS_AUDIT[1]] = $dtnow_str; // AudUpd
        $this->att[static::FIELDS_AUDIT[3]] = AuthSession::get()['USR_ID'] ?? null; // AudUsr

        // Campos que NUNCA devem ser atualizados diretamente via updateRegister
        // (PK é usado no WHERE, AudIns/AudDel são definidos em outros momentos)
        $fieldsNotUpdate = array_merge(static::FIELDS_PK, [static::FIELDS_AUDIT[0], static::FIELDS_AUDIT[2]]);

        /**
         * Default values for not nulls
         */
        $this->defaultValuesForNotNulls($dtnow_str);
        $this->applyDefaultGroupValue();

        /**
         * Query update
         */
        $selectedFields = [];
        foreach ($this->selectedFields as $key => $value) {
            if (in_array($value, static::FIELDS)) {
                $selectedFields[$key] = $value;
            }
        }
        // Garante que os campos de auditoria necessários estejam na lista
        $fieldsToConsider = array_unique(array_merge($selectedFields, [static::FIELDS_AUDIT[1], static::FIELDS_AUDIT[3]]));

        $setParts = [];
        $parameters = [];

        foreach ($fieldsToConsider as $field) {
            // Só adiciona ao SET se não for um campo proibido E se existir nos atributos
            if (!in_array($field, $fieldsNotUpdate) && array_key_exists($field, $this->att)) {
                // Adiciona ao SET: field = :field
                $setParts[] = "{$field} = :{$field}";
                // Adiciona ao array de parâmetros: [':field' => value]
                $parameters[":{$field}"] = $this->att[$field];
            }
        }

        // Verifica se há algo para atualizar
        if (empty($setParts)) {
            // Nada para atualizar (talvez logar ou retornar FALSE)
            return FALSE;
        }

        $qry  = "UPDATE {$this->tbl} SET ";
        $qry .= implode(', ', $setParts);

        foreach (static::FIELDS_PK as $key => $pkField) {
            if ($key === 0) {
                $qry .= " WHERE ";
            } else {
                $qry .= " AND ";
            }
            $qry .= " {$pkField} = :{$pkField}";
        }

        // Adiciona o parâmetro da PK
        foreach (static::FIELDS_PK as $key => $pkField) {
            $parameters[$pkField] = $this->att[$pkField];
        }

        /**
         * Run query
         */
        $stmt = $this->cnx->executeQuery(static::class, $qry, $parameters);
        $rows = $stmt->rowCount();

        // rowCount em UPDATE pode retornar 0 se nenhum dado foi alterado,
        // mas a query foi bem-sucedida. Você pode querer retornar true
        // se a query executou sem erros, ou verificar $stmt->errorInfo().
        // Retornar boolval($rows) significa que só retorna true se linhas foram *efetivamente* modificadas.
        return boolval($rows);
    }

    public function deleteRegister()
    {
        if (static::LOGICAL_EXCLUSION) {
            /**
             * Mandatory on mode logical delete
             */
            $dtnow = new DateTime('now');
            $dtnow_str = $dtnow->format('Y-m-d H:i:s');
            $this->att[static::FIELDS_AUDIT[2]] = $dtnow_str; // AudDel
            $this->att[static::FIELDS_AUDIT[3]] = AuthSession::get()['USR_ID'] ?? null; // AudUsr

            /**
             * Query update (logical delete)
             */
            // Define os campos que precisam ser atualizados para a exclusão lógica
            $fieldsToUpdate = [static::FIELDS_AUDIT[2], static::FIELDS_AUDIT[3]];

            $setParts = [];
            $parameters = [];

            foreach ($fieldsToUpdate as $field) {
                $setParts[] = "{$field} = :{$field}";
            }

            $qry  = "UPDATE {$this->tbl} SET ";
            $qry .= implode(', ', $setParts);

            foreach (static::FIELDS_PK as $key => $pkField) {
                if ($key === 0) {
                    $qry .= " WHERE ";
                } else {
                    $qry .= " AND ";
                }
                $qry .= "{$pkField} = :{$pkField}";
            }

            $qry .= ';';

            /**
             * Query parameters
             */
            foreach (static::FIELDS_PK as $pkField) {
                $parameters[$pkField] = $this->att[$pkField];
            }

            /**
             * Run query
             */
            $stmt = $this->cnx->executeQuery(static::class, $qry, $parameters);
            $rows = $stmt->rowCount();
        } else {
            if (! $this->deleteReferencial()) {
                return false; // Não conseguiu deletar as referências, aborta a exclusão
            }

            /**
             * Query delete
             */
            $parameters = [];
            $qry = "DELETE FROM {$this->tbl}";

            foreach (static::FIELDS_PK as $key => $pkField) {
                if ($key === 0) {
                    $qry .= " WHERE ";
                } else {
                    $qry .= " AND ";
                }
                $qry .= "{$pkField} = :{$pkField}";
            }

            $qry .= ';';

            /**
             * Query parameters
             */
            foreach (static::FIELDS_PK as $pkField) {
                $parameters[$pkField] = $this->att[$pkField];
            }

            /**
             * Run query
             */
            $stmt = $this->cnx->executeQuery(static::class, $qry, $parameters);
            $rows = $stmt->rowCount();
        }

        return boolval($rows);
    }

    public function readAllLines($fillPK = [])
    {
        /**
         * Query read
         */
        foreach ($this->selectedFields as $key => $value) {
            $this->selectedFields[$key] = "{$value} as `{$value}`";
        }

        $fields = implode(', ', $this->selectedFields);

        $parameters = [];
        $qry  = "SELECT {$fields} FROM {$this->tbl}";

        if (count($fillPK) > 0) {
            foreach ($fillPK as $key => $pkField) {
                if ($key === 0) {
                    $qry .= " WHERE ";
                } else {
                    $qry .= " AND ";
                }
                $qry .= "{$pkField} = :{$pkField}";
            }
        }

        if (!empty($this->queryOrder)) {
            $qry .= " ORDER BY {$this->queryOrder}";
        }

        $qry .= " ;";

        /**
         * Query parameters
         */
        if ($fillPK) {
            foreach ($fillPK as $key => $pkField) {
                $parameters[$pkField] = $this->att[$pkField];
            }
        }

        /**
         * Run query
         */
        $stmt = $this->cnx->executeQuery(static::class, $qry, $parameters);
        $this->dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $rows = count($this->dataRows);

        return boolval($rows);
    }

    public function readAllLinesJoin(array $tablesForeign = [])
    {
        /**
         * Query read
         */
        foreach ($this->selectedFields as $key => $field) {
            if (in_array($field, static::FIELDS)) {
                $this->selectedFields[$key] = "{$this->tbl}.{$field}";
            } else {
                // Foreign Tables //
                if (count(static::FIELDS_FOREIGN)) {
                    foreach (static::FIELDS_FOREIGN as $tableForeign => $valueFieldsForeign) {
                        if (in_array($field, $valueFieldsForeign['FIELDS'])) {
                            $this->selectedFields[$key] = "{$tableForeign}.{$field}";
                        }
                    }
                }
            }
        }

        $fields = implode(', ', $this->selectedFields);

        $parameters = [];
        $qry  = "SELECT {$fields} FROM {$this->tbl}";

        /**
         * INNER JOIN
         */
        if (count(static::FIELDS_FK)) {
            foreach (static::FIELDS_FK as $fields_fk) {
                foreach ($fields_fk as $fields) {
                    $qry .= " INNER JOIN {$fields['References']}";
                    for ($i = 0; $i < count($fields['FieldsKey']); $i++) {
                        if ($i === 0) {
                            $qry .= " ON {$this->tbl}.{$fields['FieldsKey'][$i]} = {$fields['References']}.{$fields['Fields'][$i]}";
                        } else {
                            $qry .= " AND {$this->tbl}.{$fields['FieldsKey'][$i]} = {$fields['References']}.{$fields['Fields'][$i]}";
                        }
                    }
                }
            }
        }

        /**
         * CONDITIONS FOREIGN KEY
         */
        if (count(static::FIELDS_FK)) {
            $qryWhere = '';
            foreach (static::FIELDS_FK as $fields_fk) {
                foreach ($fields_fk as $fields) {
                    if (in_array($fields['References'], $tablesForeign) || empty($tablesForeign)) {
                        $qryWhere .= (empty($qryWhere) ? ' WHERE ' : ' AND ');
                        for ($i = 0; $i < count($fields['FieldsKey']); $i++) {
                            if ($i === 0) {
                                $qryWhere .= "( {$this->tbl}.{$fields['FieldsKey'][$i]} = :{$fields['Fields'][$i]}";
                                $parameters[$fields['Fields'][$i]] = $this->att[$fields['FieldsKey'][$i]];
                            } else {
                                $qryWhere .= " AND {$this->tbl}.{$fields['FieldsKey'][$i]} = :{$fields['Fields'][$i]}";
                                $parameters[$fields['Fields'][$i]] = $this->att[$fields['FieldsKey'][$i]];
                            }
                        }
                        $qryWhere .= ' )';
                    }
                }
            }
            $qry .= $qryWhere;
        }

        /**
         * CONDITIONS PRIMARY KEY
         */
        if (count(static::FIELDS_PK)) {
            $qryWhere = '';
            foreach (static::FIELDS_PK as $fields) {
                if (in_array($fields, $tablesForeign)) {
                    if (stripos($qry, 'WHERE ') !== false) {
                        $qryWhere .= (empty($qryWhere) ? ' AND ( ' : ' AND ');
                    } else {
                        $qryWhere .= ' WHERE ( ';
                    }
                    $qryWhere .= "{$this->tbl}.{$fields} = :{$fields}";
                    $parameters[$fields] = $this->att[$fields];
                }
            }
            $qryWhere .= (!empty($qryWhere) ? ' )' : '');
            $qry .= $qryWhere;
        }

        if (!empty($this->queryOrder)) {
            $qry .= " ORDER BY {$this->queryOrder}";
        }

        $qry .= " ;";

        /**
         * Run query
         */
        $stmt = $this->cnx->executeQuery(static::class, $qry, $parameters);
        $this->dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $rows = count($this->dataRows);

        return boolval($rows);
    }

    /**
     * Others
     */
    private function newid()
    {
        $pkField = static::FIELDS_PK[count(static::FIELDS_PK) - 1];

        // Gera um ID único e verifica a duplicação
        do {
            // Considerar usar UUIDs mais robustos se necessário (ex: ramsey/uuid)
            $this->att[$pkField] = uniqid(); // uniqid($prefix, true); // Adiciona prefixo e mais entropia
        } while ($this->checkDuplicateKey());
    }

    /**
     * Fields ending in Grp inherit the non-foreign primary-key component.
     * When every component of a composite key is also a foreign key, a
     * stable random value is generated at insert time.
     */
    private function applyDefaultGroupValue(): void
    {
        $groupFields = array_values(array_filter(
            static::FIELDS,
            static fn (string $field): bool => str_ends_with($field, 'Grp')
        ));

        if ($groupFields === []) {
            return;
        }

        $foreignKeyFields = [];
        foreach (static::FIELDS_FK as $foreignKey) {
            if (is_string($foreignKey)) {
                $foreignKeyFields[] = $foreignKey;
                continue;
            }

            foreach ($foreignKey as $definition) {
                foreach (($definition['FieldsKey'] ?? []) as $field) {
                    $foreignKeyFields[] = $field;
                }
            }
        }

        $ownPrimaryKeys = array_values(array_diff(static::FIELDS_PK, array_unique($foreignKeyFields)));

        foreach ($groupFields as $groupField) {
            if (! empty($this->att[$groupField])) {
                continue;
            }

            $sourceField = count(static::FIELDS_PK) === 1
                ? static::FIELDS_PK[0]
                : ($ownPrimaryKeys[count($ownPrimaryKeys) - 1] ?? null);

            if ($sourceField !== null && ! empty($this->att[$sourceField])) {
                $this->att[$groupField] = $this->att[$sourceField];
                continue;
            }

            $length = (int) (static::FIELDS_MD[$groupField]['Length'] ?? 32);
            $this->att[$groupField] = substr(bin2hex(random_bytes(16)), 0, max(1, $length));
        }
    }

    private function checkDuplicateKey(): bool // Retorna true se duplicado, FALSE se não
    {
        $pkField = static::FIELDS_PK[count(static::FIELDS_PK) - 1];

        $pkValue = $this->att[$pkField] ?? null;

        // Não pode verificar duplicidade sem um valor de PK
        if ($pkValue === null) {
            $this->isDuplicated = FALSE; // Não é considerado duplicado se não tem ID ainda
            return FALSE; // Não está duplicado (ainda)
        }

        /**
         * Query search register
         */
        $qry  = "SELECT {$pkField} FROM {$this->tbl}";
        foreach (static::FIELDS_PK as $key => $pkField) {
            if ($key === 0) {
                $qry .= " WHERE ";
            } else {
                $qry .= " AND ";
            }
            $qry .= " {$pkField} = :{$pkField}";
        }
        $qry .= " LIMIT 1;";

        /**
         * Query parameters
         */
        $parameters = array();
        foreach (static::FIELDS_PK as $key => $pkField) {
            $parameters[$pkField] = $this->att[$pkField];
        }

        /**
         * Run query
         */
        $stmt = $this->cnx->executeQuery(static::class, $qry, $parameters);
        $rows = $stmt->rowCount();

        $this->isDuplicated = boolval($rows);

        // Retorna true se encontrou linhas (duplicado), FALSE caso contrário
        return $this->isDuplicated;
    }

    public function setSelectedFields(array | null $fields = null, int | null $index = null): void
    {
        if (is_null($fields)) {
            $this->selectedFields = static::FIELDS;
        } else {
            $mergeFields = static::FIELDS;
            $foreignFields = defined(static::class . '::FIELDS_FOREIGN') ? static::FIELDS_FOREIGN : [];
            if (count($foreignFields)) {
                foreach ($foreignFields as $tableForeign => $valueFieldsForeign) {
                    $mergeFields = array_merge($valueFieldsForeign['FIELDS'], $mergeFields);
                }
            }
            $this->selectedFields = array_intersect($fields, $mergeFields);
            $this->selectedFields = array_values($this->selectedFields);
        }

        $this->setSelectedIndex($index);
    }

    /**
     * Referential Integrity
     */
    public function checkReferencialKey($tableParent, $deep = false): void
    {
        // deep = true: verifica todas as tabelas referenciadas //

        /**
         * Query read
         */
        $constraint = implode(', ', array_map(function ($field) {
            return "'{$field}'";
        }, static::FIELDS_PK));

        $parameters = [];
        $qry = "
        SELECT
            TABLE_NAME
        FROM
            INFORMATION_SCHEMA.KEY_COLUMN_USAGE
        WHERE
            REFERENCED_TABLE_SCHEMA = :TABLE_SCHEMA
            AND REFERENCED_TABLE_NAME = :TABLE_NAME
            AND TABLE_SCHEMA = :TABLE_SCHEMA
            AND CONSTRAINT_NAME NOT IN({$constraint})
        GROUP BY
            TABLE_NAME
        ;
        ";

        /**
         * Query parameters
         */
        $parameters['TABLE_SCHEMA'] = $this->cnx->getDatabaseName();
        $parameters['TABLE_NAME'] = $tableParent;

        /**
         * Run query
         */
        $stmt = $this->cnx->executeQuery(static::class, $qry, $parameters);
        $this->dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($this->dataRows as $key => $value) {
            $this->referencedTables[] = array('Parent' => $tableParent, 'TableName' => $value['TABLE_NAME']);
        }

        if ($deep) {
            foreach ($this->referencedTables as $table) {
                $newReferencedTables = $this->checkReferencialKeyChildren($table['TableName']);
                do {
                    $newReferencedTables = $this->checkReferencialKeyChildren($newReferencedTables[0]);
                } while (count($newReferencedTables) > 0);
            }
            // old version, não funciona com mais de 1 nível de profundidade //
            // foreach ($this->referencedTables as $table) {
            //     echo '<br>--- #1# Children: ' . $table['TableName'];
            //     $newReferencedTables = $this->checkReferencialKeyChildren($table['TableName']);
            //     if ($newReferencedTables) {
            //         foreach ($newReferencedTables as $newTable) {
            //             echo '<br>--- #2# Children: ' . $newTable;
            //             $newReferencedTables = $this->checkReferencialKeyChildren($newTable);
            //         }
            //     }
            // }
        }
    }

    private function checkReferencialKeyChildren($tableParent): array
    {
        /**
         * Query read
         */
        $constraint = implode(', ', array_map(function ($field) {
            return "'{$field}'";
        }, static::FIELDS_PK));

        $parameters = [];
        $qry = "
        SELECT
            TABLE_NAME
        FROM
            INFORMATION_SCHEMA.KEY_COLUMN_USAGE
        WHERE
            REFERENCED_TABLE_SCHEMA = :TABLE_SCHEMA
            AND REFERENCED_TABLE_NAME = :TABLE_NAME
            AND TABLE_SCHEMA = :TABLE_SCHEMA
            AND CONSTRAINT_NAME NOT IN({$constraint})
        GROUP BY
            TABLE_NAME
        ;
        ";

        /**
         * Query parameters
         */
        $parameters['TABLE_SCHEMA'] = $this->cnx->getDatabaseName();
        $parameters['TABLE_NAME'] = $tableParent;

        /**
         * Run query
         */
        $stmt = $this->cnx->executeQuery(static::class, $qry, $parameters);
        $this->dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $newReferencedTables = [];
        foreach ($this->dataRows as $key => $value) {
            if (in_array($value['TABLE_NAME'], array_column($this->referencedTables, 'TableName'))) {
                continue; // Já está na lista, não adiciona novamente
            }
            $this->referencedTables[] = array('Parent' => $tableParent, 'TableName' => $value['TABLE_NAME']);
            $newReferencedTables[] = $value['TABLE_NAME'];
        }

        return $newReferencedTables;
    }

    public function isEmptyAllReferencedTables()
    {
        $this->checkReferencialKey($this->tbl, false);

        if ($this->referencedTables) {
            foreach ($this->referencedTables as $table) {
                $qry = "SELECT COUNT(*) as count FROM {$table['TableName']}";
                foreach (static::FIELDS_PK as $key => $pkField) {
                    if ($key === 0) {
                        $qry .= " WHERE ";
                    } else {
                        $qry .= " AND ";
                    }
                    $qry .= "{$pkField} = :{$pkField}";
                }
    
                /**
                 * Query parameters
                 */
                foreach (static::FIELDS_PK as $pkField) {
                    $parameters[$pkField] = $this->att[$pkField];
                }
    
                // Run query
                $stmt = $this->cnx->executeQuery(static::class, $qry, $parameters);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
                if ($result['count'] > 0) {
                    return false; // Se qualquer tabela referenciada não estiver vazia, retorna false
                }
            }
        }

        return true;
    }

    private function deleteReferencial(): bool
    {
        $this->referencedTables = array();

        $this->checkReferencialKey($this->tbl, true);

        $this->referencedTables = array_reverse($this->referencedTables);

        $loopReferencedTables = count($this->referencedTables);

        // remove records in child tables //
        for ($i=0; $i < $loopReferencedTables; $i++) { 
            $table = $this->referencedTables[$i]['TableName'];
            $qry = "DELETE FROM {$table} ";
            foreach (static::FIELDS_PK as $key => $pkField) {
                if ($key === 0) {
                    $qry .= " WHERE ";
                } else {
                    $qry .= " AND ";
                }
                $qry .= "{$pkField} = :{$pkField}";
            }

            /**
             * Query parameters
             */
            foreach (static::FIELDS_PK as $pkField) {
                $parameters[$pkField] = $this->att[$pkField];
            }

            // Run query
            $stmt = $this->cnx->executeQuery(static::class, $qry, $parameters);
        }

        // rotina já implementada no chamador: deleteRegister() //
        // remove records in parent tables //
        // for ($i=0; $i < $loopReferencedTables; $i++) { 
        //     $table = $this->referencedTables[$i]['Parent'];
        //     $qry = "DELETE FROM {$table} ";
        //     foreach (static::FIELDS_PK as $key => $pkField) {
        //         if ($key === 0) {
        //             $qry .= " WHERE ";
        //         } else {
        //             $qry .= " AND ";
        //         }
        //         $qry .= "{$pkField} = :{$pkField}";
        //     }

        //     /**
        //      * Query parameters
        //      */
        //     foreach (static::FIELDS_PK as $pkField) {
        //         $parameters[$pkField] = $this->att[$pkField];
        //     }

        //     // Run query
        //     echo '<br>Run query - remove records in parent tables:' . $table;
        //     $stmt = $this->cnx->executeQuery(static::class, $qry, $parameters);
        // }

        // Verifica se todas as tabelas referenciadas estão vazias
        return $this->isEmptyAllReferencedTables();
    }
}
