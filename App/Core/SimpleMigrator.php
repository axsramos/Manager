<?php

namespace App\Core;

use PDO;
use RuntimeException;

class SimpleMigrator
{
    private PDO $pdo;
    private string $database;
    private bool $dryRun;
    private array $sqlLog = [];
    private array $metadataClasses = [];

    public function __construct(string $storage = 'Default', bool $dryRun = false)
    {
        Config::getInstance();

        if (!isset(Config::$DB_STORAGE[$storage])) {
            throw new RuntimeException("Storage de banco de dados invalido: {$storage}");
        }

        $config = Config::$DB_STORAGE[$storage];
        $this->database = $config['DB_DATABASE'];
        $this->dryRun = $dryRun;

        $dsn = "mysql:host={$config['DB_HOST']};dbname={$config['DB_DATABASE']};port={$config['DB_PORT']};charset={$config['DB_CHARSET']}";
        $this->pdo = new PDO($dsn, $config['DB_USERNAME'], $config['DB_PASSWORD']);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function runCasMetadata(bool $force = false): array
    {
        $this->metadataClasses = $this->discoverMetadataClasses();
        $checksum = $this->checksum($this->metadataClasses);
        $migration = 'cas_metadata_schema';

        $this->ensureMigrationTable();

        if (!$force && $this->migrationWasApplied($migration, $checksum)) {
            return [
                'status' => 'skipped',
                'checksum' => $checksum,
                'sql' => $this->sqlLog,
                'message' => 'Nenhuma alteracao nos metadados CAS.',
            ];
        }

        $this->applyTables();
        $this->applyAccountActivationBackfill();
        $this->applyIndexes();
        $this->applyForeignKeys();
        $this->registerMigration($migration, $checksum);

        return [
            'status' => $this->dryRun ? 'dry-run' : 'applied',
            'checksum' => $checksum,
            'sql' => $this->sqlLog,
            'message' => $this->dryRun ? 'SQL gerado sem executar.' : 'Migration CAS aplicada.',
        ];
    }

    private function discoverMetadataClasses(): array
    {
        $path = Config::$DIR_BASE . '/App/Metadata/CAS';
        $files = glob($path . '/*MD.php') ?: [];
        sort($files);

        $classes = [];
        foreach ($files as $file) {
            $classBase = basename($file, '.php');
            $class = "App\\Metadata\\CAS\\{$classBase}";

            if (class_exists($class) && defined("{$class}::FIELDS") && defined("{$class}::FIELDS_MD")) {
                $classes[] = $class;
            }
        }

        $this->validateMetadata($classes);

        return $classes;
    }

    private function validateMetadata(array $classes): void
    {
        $errors = [];

        foreach ($classes as $class) {
            foreach ($class::FIELDS as $field) {
                if (!isset($class::FIELDS_MD[$field]) || !is_array($class::FIELDS_MD[$field])) {
                    $errors[] = $this->tableName($class) . "::FIELDS_MD nao define o campo {$field}.";
                }
            }
        }

        if (!empty($errors)) {
            throw new RuntimeException("Metadados CAS invalidos:\n- " . implode("\n- ", $errors));
        }
    }

    private function checksum(array $classes): string
    {
        $data = [];
        foreach ($classes as $class) {
            $data[$this->tableName($class)] = [
                'fields' => $class::FIELDS,
                'fields_md' => $class::FIELDS_MD,
                'fields_pk' => $class::FIELDS_PK,
                'fields_fk' => $class::FIELDS_FK,
                'table_idx' => $class::TABLE_IDX,
            ];
        }

        return hash('sha256', json_encode($data));
    }

    private function ensureMigrationTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS `schema_migrations` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `migration` VARCHAR(150) NOT NULL,
            `checksum` CHAR(64) NOT NULL,
            `applied_at` DATETIME NOT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `UXSchemaMigrations01` (`migration`, `checksum`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        $this->execute($sql);
    }

    private function migrationWasApplied(string $migration, string $checksum): bool
    {
        if ($this->dryRun) {
            return false;
        }

        $stmt = $this->pdo->prepare('SELECT 1 FROM `schema_migrations` WHERE `migration` = :migration AND `checksum` = :checksum LIMIT 1');
        $stmt->execute([
            ':migration' => $migration,
            ':checksum' => $checksum,
        ]);

        return (bool) $stmt->fetchColumn();
    }

    private function applyTables(): void
    {
        foreach ($this->metadataClasses as $class) {
            $table = $this->tableName($class);

            if (!$this->tableExists($table)) {
                $this->execute($this->createTableSql($table, $class));
                continue;
            }

            foreach ($class::FIELDS as $field) {
                if (!$this->columnExists($table, $field)) {
                    $this->execute('ALTER TABLE ' . $this->id($table) . ' ADD COLUMN ' . $this->columnSql($field, $class::FIELDS_MD[$field], in_array($field, $class::FIELDS_PK, true)));
                }
            }

            if (!empty($class::FIELDS_PK) && !$this->hasPrimaryKey($table)) {
                $this->execute('ALTER TABLE ' . $this->id($table) . ' ADD PRIMARY KEY (' . $this->fieldList($class::FIELDS_PK) . ')');
            }
        }
    }

    private function applyIndexes(): void
    {
        foreach ($this->metadataClasses as $class) {
            $table = $this->tableName($class);

            foreach ($class::TABLE_IDX as $indexData) {
                foreach ($indexData as $indexName => $fields) {
                    $fields = $this->normalizeFields($fields);
                    if (empty($fields) || $this->indexExists($table, $indexName)) {
                        continue;
                    }

                    $this->execute('CREATE INDEX ' . $this->id($indexName) . ' ON ' . $this->id($table) . ' (' . $this->fieldList($fields) . ')');
                }
            }

            $simpleFkFields = array_filter($class::FIELDS_FK, 'is_string');
            if (!empty($simpleFkFields)) {
                $indexName = 'IX' . $table . 'FK';
                if (!$this->indexExists($table, $indexName)) {
                    $this->execute('CREATE INDEX ' . $this->id($indexName) . ' ON ' . $this->id($table) . ' (' . $this->fieldList($simpleFkFields) . ')');
                }
            }
        }
    }

    private function applyAccountActivationBackfill(): void
    {
        $migration = 'cas_usr_activation_backfill_v1';

        if (!$this->dryRun && $this->migrationWasApplied($migration, 'v1')) {
            return;
        }

        $this->execute('UPDATE `CasUsr` SET `CasUsrActDtt` = COALESCE(`CasUsrAudIns`, NOW()) WHERE `CasUsrActDtt` IS NULL');
        $this->registerMigration($migration, 'v1');
    }

    private function applyForeignKeys(): void
    {
        foreach ($this->metadataClasses as $class) {
            $table = $this->tableName($class);

            foreach ($class::FIELDS_FK as $fkData) {
                if (!is_array($fkData)) {
                    continue;
                }

                foreach ($fkData as $fkName => $definition) {
                    if ($this->foreignKeyExists($table, $fkName)) {
                        continue;
                    }

                    $fieldsKey = $definition['FieldsKey'] ?? [];
                    $references = $definition['References'] ?? '';
                    $fields = $definition['Fields'] ?? [];

                    if (empty($fieldsKey) || empty($references) || empty($fields)) {
                        continue;
                    }

                    if (!$this->dryRun && !$this->tableExists($references)) {
                        continue;
                    }

                    $sql = 'ALTER TABLE ' . $this->id($table)
                        . ' ADD CONSTRAINT ' . $this->id($fkName)
                        . ' FOREIGN KEY (' . $this->fieldList($fieldsKey) . ')'
                        . ' REFERENCES ' . $this->id($references) . ' (' . $this->fieldList($fields) . ')';

                    $this->execute($sql);
                }
            }
        }
    }

    private function registerMigration(string $migration, string $checksum): void
    {
        $sql = "INSERT IGNORE INTO `schema_migrations` (`migration`, `checksum`, `applied_at`)
            VALUES (" . $this->quote($migration) . ', ' . $this->quote($checksum) . ', NOW())';

        $this->execute($sql);
    }

    private function createTableSql(string $table, string $class): string
    {
        $columns = [];
        foreach ($class::FIELDS as $field) {
            $columns[] = $this->columnSql($field, $class::FIELDS_MD[$field], in_array($field, $class::FIELDS_PK, true));
        }

        if (!empty($class::FIELDS_PK)) {
            $columns[] = 'PRIMARY KEY (' . $this->fieldList($class::FIELDS_PK) . ')';
        }

        return 'CREATE TABLE ' . $this->id($table) . " (\n    " . implode(",\n    ", $columns) . "\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    }

    private function columnSql(string $field, array $metadata, bool $primaryKey = false): string
    {
        $type = $this->columnType($metadata);
        $required = $primaryKey || ($metadata['Required'] ?? false);
        $sql = $this->id($field) . ' ' . $type . ($required ? ' NOT NULL' : ' NULL');

        if (array_key_exists('Default', $metadata) && $metadata['Default'] !== null && !$this->isTextType($type)) {
            $sql .= ' DEFAULT ' . $this->quote((string) $metadata['Default']);
        }

        return $sql;
    }

    private function columnType(array $metadata): string
    {
        $type = strtolower((string) ($metadata['Type'] ?? 'string'));
        $length = (int) ($metadata['Length'] ?? 0);

        return match ($type) {
            'int', 'integer' => $length > 10 ? 'BIGINT' : 'INT',
            'boolean', 'bool' => 'CHAR(1)',
            'datetime' => 'DATETIME',
            'date' => 'DATE',
            'text' => 'TEXT',
            default => $length > 0 && $length <= 255 ? "VARCHAR({$length})" : 'TEXT',
        };
    }

    private function tableExists(string $table): bool
    {
        if ($this->dryRun) {
            return false;
        }

        $stmt = $this->pdo->prepare('SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = :schema AND TABLE_NAME = :table LIMIT 1');
        $stmt->execute([':schema' => $this->database, ':table' => $table]);

        return (bool) $stmt->fetchColumn();
    }

    private function columnExists(string $table, string $column): bool
    {
        if ($this->dryRun) {
            return false;
        }

        $stmt = $this->pdo->prepare('SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = :schema AND TABLE_NAME = :table AND COLUMN_NAME = :column LIMIT 1');
        $stmt->execute([':schema' => $this->database, ':table' => $table, ':column' => $column]);

        return (bool) $stmt->fetchColumn();
    }

    private function hasPrimaryKey(string $table): bool
    {
        if ($this->dryRun) {
            return false;
        }

        $stmt = $this->pdo->prepare("SELECT 1 FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = :schema AND TABLE_NAME = :table AND CONSTRAINT_TYPE = 'PRIMARY KEY' LIMIT 1");
        $stmt->execute([':schema' => $this->database, ':table' => $table]);

        return (bool) $stmt->fetchColumn();
    }

    private function indexExists(string $table, string $index): bool
    {
        if ($this->dryRun) {
            return false;
        }

        $stmt = $this->pdo->prepare('SELECT 1 FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = :schema AND TABLE_NAME = :table AND INDEX_NAME = :index LIMIT 1');
        $stmt->execute([':schema' => $this->database, ':table' => $table, ':index' => $index]);

        return (bool) $stmt->fetchColumn();
    }

    private function foreignKeyExists(string $table, string $constraint): bool
    {
        if ($this->dryRun) {
            return false;
        }

        $stmt = $this->pdo->prepare("SELECT 1 FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = :schema AND TABLE_NAME = :table AND CONSTRAINT_NAME = :constraint AND CONSTRAINT_TYPE = 'FOREIGN KEY' LIMIT 1");
        $stmt->execute([':schema' => $this->database, ':table' => $table, ':constraint' => $constraint]);

        return (bool) $stmt->fetchColumn();
    }

    private function execute(string $sql): void
    {
        $this->sqlLog[] = $sql;

        if (!$this->dryRun) {
            $this->pdo->exec($sql);
        }
    }

    private function tableName(string $class): string
    {
        return substr(strrchr($class, '\\'), 1, -2);
    }

    private function normalizeFields(array $fields): array
    {
        if (count($fields) === 1 && isset($fields[0]) && is_array($fields[0])) {
            return $fields[0];
        }

        return $fields;
    }

    private function fieldList(array $fields): string
    {
        return implode(', ', array_map(fn ($field) => $this->id($field), $fields));
    }

    private function id(string $identifier): string
    {
        if (!preg_match('/^[A-Za-z0-9_]+$/', $identifier)) {
            throw new RuntimeException("Identificador SQL invalido: {$identifier}");
        }

        return "`{$identifier}`";
    }

    private function quote(string $value): string
    {
        return $this->pdo->quote($value);
    }

    private function isTextType(string $type): bool
    {
        return in_array(strtoupper($type), ['TEXT', 'MEDIUMTEXT', 'LONGTEXT'], true);
    }
}
