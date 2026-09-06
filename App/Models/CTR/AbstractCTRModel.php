<?php

namespace App\Models\CTR;

use App\Core\Database;
use PDO;
use RuntimeException;

abstract class AbstractCTRModel
{
    protected array $att = [];
    protected Database $cnx;
    protected string $tbl;
    protected array $selectedFields = [];
    protected string $queryOrder = '';
    protected array $dataRows = [];
    public bool $isDuplicated = false;

    public function __construct(string $storage = 'SAAS')
    {
        $this->cnx = new Database($storage);
        $this->tbl = $this->resolveTableName();

        foreach (static::FIELDS_MD as $field => $metadata) {
            $this->att[$field] = $metadata['Default'] ?? null;
        }

        $this->setSelectedFields();
    }

    public function __get(string $name): mixed
    {
        return in_array($name, static::FIELDS, true) ? ($this->att[$name] ?? null) : null;
    }

    public function __set(string $name, mixed $value): void
    {
        if (in_array($name, static::FIELDS, true)) {
            $this->att[$name] = $value;
        }
    }

    public function getRecords(): array
    {
        return $this->dataRows;
    }

    public function setSelectedFields(array | null $fields = null, int | null $index = null): void
    {
        $this->selectedFields = $fields === null
            ? static::FIELDS
            : array_values(array_intersect($fields, static::FIELDS));

        $this->setSelectedIndex($index);
    }

    public function setSelectedIndex(int | null $index = null): void
    {
        $index = $index ?? 0;
        $this->queryOrder = '';

        if (isset(static::TABLE_IDX[$index])) {
            $orderFields = array_values(static::TABLE_IDX[$index])[0] ?? [];
            $this->queryOrder = implode(', ', array_map(
                static fn (string|int $field, string|int $direction): string => is_int($field) ? (string) $direction : $field . ' ' . $direction,
                array_keys($orderFields),
                $orderFields
            ));
        }
    }

    public function createRegister(): bool
    {
        if ($this->checkDuplicateKey()) {
            return false;
        }

        $fields = [];
        $parameters = [];
        foreach (static::FIELDS as $field) {
            $metadata = static::FIELDS_MD[$field];
            if (($metadata['Generated'] ?? null) !== null) {
                continue;
            }
            if (($metadata['AutoIncrement'] ?? false) === true && empty($this->att[$field])) {
                continue;
            }

            $fields[] = $field;
            $parameters[':' . $field] = $this->prepareValue($this->att[$field] ?? null, $metadata);
        }

        $query = 'INSERT INTO ' . $this->tbl
            . ' (' . implode(', ', $fields) . ')'
            . ' VALUES (:' . implode(', :', $fields) . ')';

        $stmt = $this->cnx->executeQuery(static::class, $query, $parameters);
        $created = $stmt->rowCount() > 0;

        if ($created) {
            $pkField = static::FIELDS_PK[count(static::FIELDS_PK) - 1];
            if (($this->att[$pkField] ?? null) === null && (static::FIELDS_MD[$pkField]['AutoIncrement'] ?? false) === true) {
                $this->att[$pkField] = (int) $this->cnx->getPdo()->lastInsertId();
            }
        }

        return $created;
    }

    public function readRegister(): bool
    {
        $fields = $this->selectedFields ?: static::FIELDS;
        $conditions = [];
        $parameters = [];

        foreach (static::FIELDS_PK as $field) {
            $conditions[] = $field . ' = :' . $field;
            $parameters[':' . $field] = $this->att[$field] ?? null;
        }

        $query = 'SELECT ' . implode(', ', $fields)
            . ' FROM ' . $this->tbl
            . ' WHERE ' . implode(' AND ', $conditions);

        if ($this->queryOrder !== '') {
            $query .= ' ORDER BY ' . $this->queryOrder;
        }

        $query .= ' LIMIT 1';
        $stmt = $this->cnx->executeQuery(static::class, $query, $parameters);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            $this->dataRows = [];
            return false;
        }

        $this->dataRows = [$row];
        foreach ($row as $field => $value) {
            $this->att[$field] = $value;
        }

        return true;
    }

    public function readAllLines(array $criteria = []): bool
    {
        $fields = $this->selectedFields ?: static::FIELDS;
        $parameters = [];
        $query = 'SELECT ' . implode(', ', $fields) . ' FROM ' . $this->tbl;

        if ($criteria !== []) {
            $conditions = [];
            foreach ($criteria as $field => $value) {
                if (!in_array($field, static::FIELDS, true)) {
                    throw new RuntimeException("Campo inválido para {$this->tbl}: {$field}");
                }
                $conditions[] = $field . ' = :' . $field;
                $parameters[':' . $field] = $value;
            }
            $query .= ' WHERE ' . implode(' AND ', $conditions);
        }

        if ($this->queryOrder !== '') {
            $query .= ' ORDER BY ' . $this->queryOrder;
        }

        $stmt = $this->cnx->executeQuery(static::class, $query, $parameters);
        $this->dataRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $this->dataRows !== [];
    }

    public function updateRegister(): bool
    {
        $set = [];
        $conditions = [];
        $parameters = [];

        foreach (static::FIELDS as $field) {
            if (in_array($field, static::FIELDS_PK, true) || (static::FIELDS_MD[$field]['Generated'] ?? null) !== null) {
                continue;
            }
            if (!array_key_exists($field, $this->att)) {
                continue;
            }
            $set[] = $field . ' = :' . $field;
            $parameters[':' . $field] = $this->prepareValue($this->att[$field], static::FIELDS_MD[$field]);
        }

        foreach (static::FIELDS_PK as $field) {
            $conditions[] = $field . ' = :pk_' . $field;
            $parameters[':pk_' . $field] = $this->att[$field] ?? null;
        }

        if ($set === []) {
            return false;
        }

        $query = 'UPDATE ' . $this->tbl . ' SET ' . implode(', ', $set) . ' WHERE ' . implode(' AND ', $conditions);
        $stmt = $this->cnx->executeQuery(static::class, $query, $parameters);

        return $stmt->rowCount() >= 0;
    }

    private function checkDuplicateKey(): bool
    {
        foreach (static::FIELDS_PK as $field) {
            if (($this->att[$field] ?? null) === null || $this->att[$field] === '') {
                $this->isDuplicated = false;
                return false;
            }
        }

        $previousFields = $this->selectedFields;
        $this->selectedFields = static::FIELDS_PK;
        $this->isDuplicated = $this->readRegister();
        $this->selectedFields = $previousFields;

        return $this->isDuplicated;
    }

    private function prepareValue(mixed $value, array $metadata): mixed
    {
        if ($value === null) {
            return null;
        }

        if (($metadata['Type'] ?? null) === 'json' && is_array($value)) {
            return json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        return $value;
    }

    private function resolveTableName(): string
    {
        $base = substr(strrchr(static::class, '\\'), 1);
        return str_ends_with($base, 'Model') ? substr($base, 0, -5) : $base;
    }
}
