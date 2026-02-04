<?php

/**
 * PHP Library for Calling Stored Procedures
 * Supports MySQL and SQL Server (MSSQL) databases
 * 
 * @package     DatabaseSP
 * @version     1.0.0
 * @author      Merlin AI Assistant
 * @license     MIT
 */

namespace App\Libraries\DatabaseLibNew;

use PDO;
use PDOException;
use PDOStatement;
use InvalidArgumentException;
use RuntimeException;


/**
 * Main Database Stored Procedure Class
 * 
 * Provides a unified API for calling stored procedures on MySQL and SQL Server databases
 */
class DatabaseSP
{
    /** @var PDO|null */
    private $connection = null;
    
    /** @var ConnectionConfig */
    private $config;
    
    /** @var PDOStatement|null */
    private $lastStatement = null;
    
    /** @var int */
    private $defaultFetchMode;
    
    /** @var array */
    private $queryLog = [];
    
    /** @var bool */
    private $enableQueryLog = false;

    /**
     * Create a new DatabaseSP instance
     * 
     * @param array $config Database configuration
     * @param int $fetchMode Default fetch mode
     */
    public function __construct(array $config, int $fetchMode = FetchMode::ASSOC)
    {
        $this->config = new ConnectionConfig($config);
        $this->defaultFetchMode = $fetchMode;
    }

    /**
     * Establish database connection
     * 
     * @throws DatabaseSPException
     */
    public function connect(): self
    {
        if ($this->connection !== null) {
            return $this;
        }

        try {
            $options = $this->config->getOptions();
            
            // For MySQL, enable buffered query to avoid "unbuffered queries" error
            if ($this->config->getDriver() === Driver::MYSQL) {
                $options[PDO::MYSQL_ATTR_USE_BUFFERED_QUERY] = true;
            }
            
            $this->connection = new PDO(
                $this->config->buildDsn(),
                $this->config->getUsername(),
                $this->config->getPassword(),
                $options
            );
        } catch (PDOException $e) {
            throw new DatabaseSPException(
                "Failed to connect to database: " . $e->getMessage(),
                (int) $e->getCode(),
                $e,
                $e->errorInfo ?? []
            );
        }

        return $this;
    }

    /**
     * Set an existing PDO connection (useful when reusing connections)
     * 
     * @param PDO $connection Existing PDO connection
     * @return self
     */
    public function setConnection(PDO $connection): self
    {
        $this->connection = $connection;
        
        // For MySQL, enable buffered query to avoid "unbuffered queries" error
        // This allows multiple queries to run without closing previous result sets
        if ($this->config->getDriver() === Driver::MYSQL) {
            try {
                $connection->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
            } catch (\Exception $e) {
                // Ignore if attribute cannot be set (e.g., not MySQL)
            }
        }
        
        return $this;
    }

    /**
     * Get the PDO connection instance
     */
    public function getConnection(): PDO
    {
        $this->connect();
        return $this->connection;
    }

    /**
     * Close the database connection
     */
    public function disconnect()
    {
        $this->connection = null;
        $this->lastStatement = null;
    }

    /**
     * Get the current database driver
     */
    public function getDriver(): string
    {
        return $this->config->getDriver();
    }

    /**
     * Create a new query builder instance
     */
    public function builder(): SPQueryBuilder
    {
        // For MySQL, don't use default schema from config
        // MySQL stored procedures are in the current database, not in a separate schema
        $schema = null;
        if ($this->config->getDriver() !== Driver::MYSQL) {
            $schema = $this->config->getSchema();
        }
        
        return new SPQueryBuilder(
            $this->config->getDriver(),
            $schema
        );
    }

    /**
     * Call a stored procedure using the query builder
     * 
     * @param string $procedureName Name of the stored procedure
     * @param array $params Parameters as key-value pairs
     * @param string|null $schema Optional schema name (for MySQL, only use if explicitly needed)
     * @return self
     */
    public function call(string $procedureName, array $params = [], ?string $schema = null): self
    {
        // For MySQL, don't use default schema from config unless explicitly specified
        // MySQL stored procedures are in the current database, not in a separate schema
        if ($this->config->getDriver() === Driver::MYSQL) {
            // For MySQL, only use schema if explicitly provided in the call
            // If schema is null, explicitly set it to null to override any default
            if ($schema === null) {
                $schema = null; // Force null for MySQL
            }
        }
        
        $builder = $this->builder();
        
        // For MySQL, explicitly reset schema to null if not provided
        if ($this->config->getDriver() === Driver::MYSQL && $schema === null) {
            $builder->procedure($procedureName, null);
        } else {
            $builder->procedure($procedureName, $schema);
        }
        
        $builder->params($params);

        $query = $builder->build();
        
        // Debug: Log the SQL query for troubleshooting
        // Remove this in production if not needed
        if ($this->config->getDriver() === Driver::MYSQL) {
            // Ensure no schema is used in MySQL queries
            if (stripos($query['sql'], '`akuntansi`') !== false || stripos($query['sql'], '.`') !== false) {
                // If schema found in MySQL query, rebuild without schema
                $builder = $this->builder();
                $builder->procedure($procedureName, null); // Force null schema
                $builder->params($params);
                $query = $builder->build();
            }
        }
        
        $this->executeQuery($query['sql'], $query['bindings'], $query['named']);

        return $this;
    }

    /**
     * Execute a raw SQL query with parameter binding
     * 
     * @param string $sql SQL query string
     * @param array $params Parameters for binding
     * @return self
     */
    public function query(string $sql, array $params = []): self
    {
        $isNamed = $this->isNamedParams($params);
        $bindings = $this->normalizeBindings($params);
        $this->executeQuery($sql, $bindings, $isNamed);

        return $this;
    }

    /**
     * Execute a stored procedure and return affected rows (for INSERT/UPDATE/DELETE)
     * 
     * @param string $procedureName Name of the stored procedure
     * @param array $params Parameters as key-value pairs
     * @param string|null $schema Optional schema name
     * @return int Number of affected rows
     */
    public function execute(string $procedureName, array $params = [], ?string $schema = null): int
    {
        $this->call($procedureName, $params, $schema);
        
        $rowCount = $this->lastStatement !== null ? $this->lastStatement->rowCount() : 0;
        
        // For MySQL, close cursor after execute to avoid unbuffered query errors
        // This ensures the connection is ready for the next query
        if ($this->config->getDriver() === Driver::MYSQL && $this->lastStatement !== null) {
            $this->lastStatement->closeCursor();
        }
        
        return $rowCount;
    }

    /**
     * Fetch all results from the last executed query
     * 
     * @param int|null $fetchMode PDO fetch mode
     * @param string|null $className Class name for FETCH_CLASS mode
     * @return array
     */
    public function fetchAll(?int $fetchMode = null, ?string $className = null): array
    {
        if ($this->lastStatement === null) {
            return [];
        }

        $mode = $fetchMode ?? $this->defaultFetchMode;

        try {
            $results = [];
            if ($mode === FetchMode::CLASS_TYPE && $className !== null) {
                $results = $this->lastStatement->fetchAll($mode, $className);
            } else {
                $results = $this->lastStatement->fetchAll($mode);
            }
            
            // For MySQL, close cursor after fetchAll to avoid unbuffered query errors
            // This ensures the connection is ready for the next query
            if ($this->config->getDriver() === Driver::MYSQL) {
                $this->lastStatement->closeCursor();
            }
            
            return $results;
        } catch (PDOException $e) {
            throw new DatabaseSPException(
                "Failed to fetch results: " . $e->getMessage(),
                (int) $e->getCode(),
                $e,
                $e->errorInfo ?? []
            );
        }
    }

    /**
     * Fetch a single row from the last executed query
     * 
     * @param int|null $fetchMode PDO fetch mode
     * @param string|null $className Class name for FETCH_CLASS mode
     * @return mixed
     */
    public function fetchOne(?int $fetchMode = null, ?string $className = null)
    {
        if ($this->lastStatement === null) {
            return null;
        }

        $mode = $fetchMode ?? $this->defaultFetchMode;

        try {
            if ($mode === FetchMode::CLASS_TYPE && $className !== null) {
                $this->lastStatement->setFetchMode($mode, $className);
            }
            $result = $this->lastStatement->fetch($mode) ?: null;
            
            // For MySQL, close cursor after fetch to avoid unbuffered query errors
            // This ensures the connection is ready for the next query
            if ($this->config->getDriver() === Driver::MYSQL) {
                $this->lastStatement->closeCursor();
            }
            
            return $result;
        } catch (PDOException $e) {
            throw new DatabaseSPException(
                "Failed to fetch result: " . $e->getMessage(),
                (int) $e->getCode(),
                $e,
                $e->errorInfo ?? []
            );
        }
    }

    /**
     * Fetch a single column value from the first row
     * 
     * @param int $columnIndex Column index (0-based)
     * @return mixed
     */
    public function fetchColumn(int $columnIndex = 0)
    {
        if ($this->lastStatement === null) {
            return null;
        }

        try {
            $result = $this->lastStatement->fetchColumn($columnIndex) ?: null;
            
            // For MySQL, close cursor after fetchColumn to avoid unbuffered query errors
            // This ensures the connection is ready for the next query
            if ($this->config->getDriver() === Driver::MYSQL) {
                $this->lastStatement->closeCursor();
            }
            
            return $result;
        } catch (PDOException $e) {
            throw new DatabaseSPException(
                "Failed to fetch column: " . $e->getMessage(),
                (int) $e->getCode(),
                $e,
                $e->errorInfo ?? []
            );
        }
    }

    /**
     * Get multiple result sets (for stored procedures that return multiple result sets)
     * 
     * @param int|null $fetchMode PDO fetch mode
     * @return array Array of result sets
     */
    public function fetchAllResultSets(?int $fetchMode = null): array
    {
        if ($this->lastStatement === null) {
            return [];
        }

        $mode = $fetchMode ?? $this->defaultFetchMode;
        $resultSets = [];

        try {
            do {
                $resultSets[] = $this->lastStatement->fetchAll($mode);
            } while ($this->lastStatement->nextRowset());

            return $resultSets;
        } catch (PDOException $e) {
            throw new DatabaseSPException(
                "Failed to fetch result sets: " . $e->getMessage(),
                (int) $e->getCode(),
                $e,
                $e->errorInfo ?? []
            );
        }
    }

    /**
     * Get the number of rows affected by the last statement
     */
    public function rowCount(): int
    {
        return $this->lastStatement !== null ? $this->lastStatement->rowCount() : 0;
    }

    /**
     * Get the last inserted ID
     * 
     * @param string|null $sequenceName Sequence name for PostgreSQL
     * @return string|false
     */
    public function lastInsertId(?string $sequenceName = null)
    {
        $this->connect();
        return $this->connection->lastInsertId($sequenceName);
    }

    /**
     * Begin a database transaction
     */
    public function beginTransaction(): bool
    {
        $this->connect();
        return $this->connection->beginTransaction();
    }

    /**
     * Commit the current transaction
     */
    public function commit(): bool
    {
        $this->connect();
        return $this->connection->commit();
    }

    /**
     * Rollback the current transaction
     */
    public function rollback(): bool
    {
        $this->connect();
        return $this->connection->rollBack();
    }

    /**
     * Execute within a transaction
     * 
     * @param callable $callback Function to execute within transaction
     * @return mixed Result of the callback
     */
    public function transaction(callable $callback)
    {
        $this->beginTransaction();

        try {
            $result = $callback($this);
            $this->commit();
            return $result;
        } catch (\Throwable $e) {
            $this->rollback();
            throw $e;
        }
    }

    /**
     * Enable query logging
     */
    public function enableQueryLog(): self
    {
        $this->enableQueryLog = true;
        return $this;
    }

    /**
     * Disable query logging
     */
    public function disableQueryLog(): self
    {
        $this->enableQueryLog = false;
        return $this;
    }

    /**
     * Get the query log
     */
    public function getQueryLog(): array
    {
        return $this->queryLog;
    }

    /**
     * Clear the query log
     */
    public function clearQueryLog(): self
    {
        $this->queryLog = [];
        return $this;
    }

    /**
     * Execute the prepared query with bindings
     */
    private function executeQuery(string $sql, array $bindings, bool $isNamed)
    {
        $this->connect();

        $startTime = microtime(true);

        try {
            $this->lastStatement = $this->connection->prepare($sql);

            if ($isNamed) {
                foreach ($bindings as $key => $binding) {
                    $this->lastStatement->bindValue(
                        $key,
                        $binding['value'],
                        $binding['type']
                    );
                }
            } else {
                foreach ($bindings as $index => $binding) {
                    $this->lastStatement->bindValue(
                        $index + 1,
                        $binding['value'],
                        $binding['type']
                    );
                }
            }

            $this->lastStatement->execute();

            if ($this->enableQueryLog) {
                $this->queryLog[] = [
                    'sql' => $sql,
                    'bindings' => $bindings,
                    'time' => microtime(true) - $startTime,
                ];
            }
        } catch (PDOException $e) {
            // Include SQL query in error message for debugging
            $errorMessage = "Query execution failed: " . $e->getMessage();
            $errorMessage .= "\nSQL Query: " . $sql;
            if (!empty($bindings)) {
                $errorMessage .= "\nBindings: " . json_encode($bindings, JSON_UNESCAPED_UNICODE);
            }
            
            throw new DatabaseSPException(
                $errorMessage,
                (int) $e->getCode(),
                $e,
                $e->errorInfo ?? []
            );
        }
    }

    /**
     * Check if parameters are named (associative array)
     */
    private function isNamedParams(array $params): bool
    {
        if (empty($params)) {
            return false;
        }
        return array_keys($params) !== range(0, count($params) - 1);
    }

    /**
     * Normalize bindings to include type information
     */
    private function normalizeBindings(array $params): array
    {
        $bindings = [];
        foreach ($params as $key => $value) {
            $bindings[$key] = [
                'value' => $value,
                'type' => SPParameter::detectType($value),
            ];
        }
        return $bindings;
    }

    /**
     * Quote a value for safe SQL usage
     * 
     * @param mixed $value
     * @param int $type
     * @return string
     */
    public function quote($value, int $type = PDO::PARAM_STR): string
    {
        $this->connect();
        return $this->connection->quote((string) $value, $type);
    }

    /**
     * Quote an identifier (table name, column name)
     */
    public function quoteIdentifier(string $identifier): string
    {
        $driver = $this->config->getDriver();
        
        switch ($driver) {
            case Driver::MYSQL:
                return "`{$identifier}`";
            case Driver::SQLSERVER:
            case Driver::MSSQL_DBLIB:
                return "[{$identifier}]";
            default:
                return "\"{$identifier}\"";
        }
    }
}
