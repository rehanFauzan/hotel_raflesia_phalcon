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
 * Stored Procedure Query Builder
 */
class SPQueryBuilder
{
    /** @var string */
    private $driver;
    
    /** @var string */
    private $procedureName;
    
    /** @var string|null */
    private $schema;
    
    /** @var array */
    private $parameters = [];
    
    /** @var bool */
    private $setNoCount = true;

    public function __construct(string $driver, ?string $schema = null)
    {
        $this->driver = $driver;
        $this->schema = $schema;
    }

    /**
     * Set the stored procedure name
     */
    public function procedure(string $name, ?string $schema = null): self
    {
        $this->procedureName = $name;
        // Always set schema if provided (even if null)
        // This allows overriding default schema from config
        // For MySQL, we want to explicitly set to null to avoid using schema
        if (func_num_args() >= 2) {
            $this->schema = $schema;
        } else {
            // If schema is not provided as argument, check if we should keep existing
            // But for MySQL driver, always reset to null if not explicitly provided
            if ($this->driver === Driver::MYSQL) {
                $this->schema = null;
            }
        }
        return $this;
    }

    /**
     * Add a parameter
     * 
     * @param string $name
     * @param mixed $value
     * @param int|null $type
     * @return self
     */
    public function param(string $name, $value, ?int $type = null): self
    {
        $this->parameters[] = new SPParameter(
            $name,
            $value,
            $type ?? SPParameter::detectType($value)
        );
        return $this;
    }

    /**
     * Add multiple parameters
     */
    public function params(array $params): self
    {
        foreach ($params as $name => $value) {
            $this->param($name, $value);
        }
        return $this;
    }

    /**
     * Set whether to include SET NOCOUNT ON (SQL Server only)
     */
    public function noCount(bool $enabled = true): self
    {
        $this->setNoCount = $enabled;
        return $this;
    }

    /**
     * Build the SQL query for execution
     */
    public function build(): array
    {
        // For MySQL, always reset schema to null before building query
        if ($this->driver === Driver::MYSQL) {
            $this->schema = null;
        }
        
        switch ($this->driver) {
            case Driver::MYSQL:
                return $this->buildMySqlQuery();
            case Driver::SQLSERVER:
            case Driver::MSSQL_DBLIB:
                return $this->buildSqlServerQuery();
            default:
                throw new InvalidArgumentException("Unsupported driver: {$this->driver}");
        }
    }

    /**
     * Build MySQL CALL statement
     * 
     * Note: For MySQL, schema is usually not needed as stored procedures
     * are in the current database. Schema is only used if explicitly specified.
     * 
     * IMPORTANT: For MySQL, we NEVER use schema to avoid "Unknown database" errors.
     * MySQL stored procedures are always in the current database context.
     */
    private function buildMySqlQuery(): array
    {
        $placeholders = array_fill(0, count($this->parameters), '?');
        
        // For MySQL, NEVER use schema - always use procedure name only
        // This avoids "Unknown database" errors when schema name doesn't match current database
        // Force schema to null/empty to ensure no schema is used
        $this->schema = null; // Explicitly reset schema for MySQL
        
        $procedureName = "`{$this->procedureName}`";

        $sql = sprintf(
            'CALL %s(%s)',
            $procedureName,
            implode(', ', $placeholders)
        );

        $bindings = [];
        foreach ($this->parameters as $param) {
            $bindings[] = [
                'value' => $param->value,
                'type' => $param->type,
            ];
        }

        return ['sql' => $sql, 'bindings' => $bindings, 'named' => false];
    }

    /**
     * Build SQL Server EXEC statement
     */
    private function buildSqlServerQuery(): array
    {
        $procedureName = $this->schema
            ? "[{$this->schema}].[{$this->procedureName}]"
            : "[{$this->procedureName}]";

        $paramStrings = [];
        $bindings = [];

        foreach ($this->parameters as $index => $param) {
            $placeholder = ":param{$index}";
            $paramStrings[] = "@{$param->name} = {$placeholder}";
            $bindings[$placeholder] = [
                'value' => $param->value,
                'type' => $param->type,
            ];
        }

        $sql = '';
        if ($this->setNoCount) {
            $sql .= 'SET NOCOUNT ON; ';
        }

        $sql .= sprintf(
            'EXEC %s %s',
            $procedureName,
            implode(', ', $paramStrings)
        );

        return ['sql' => $sql, 'bindings' => $bindings, 'named' => true];
    }

    /**
     * Reset the builder state
     */
    public function reset(): self
    {
        $this->procedureName = '';
        $this->parameters = [];
        $this->setNoCount = true;
        return $this;
    }
}
