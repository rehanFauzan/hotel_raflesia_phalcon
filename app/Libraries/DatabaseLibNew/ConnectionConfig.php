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
 * Database Connection Configuration
 */
class ConnectionConfig
{
    /** @var string */
    private $driver;
    
    /** @var string */
    private $host;
    
    /** @var int */
    private $port;
    
    /** @var string */
    private $database;
    
    /** @var string */
    private $username;
    
    /** @var string */
    private $password;
    
    /** @var string */
    private $charset;
    
    /** @var array */
    private $options;
    
    /** @var string|null */
    private $schema;

    public function __construct(array $config)
    {
        $this->driver = $config['driver'] ?? Driver::MYSQL;
        $this->host = $config['host'] ?? 'localhost';
        $this->port = $config['port'] ?? $this->getDefaultPort();
        $this->database = $config['database'] ?? '';
        $this->username = $config['username'] ?? '';
        $this->password = $config['password'] ?? '';
        $this->charset = $config['charset'] ?? $this->getDefaultCharset();
        $this->options = $config['options'] ?? [];
        $this->schema = $config['schema'] ?? null;
    }

    private function getDefaultPort(): int
    {
        switch ($this->driver) {
            case Driver::MYSQL:
                return 3306;
            case Driver::SQLSERVER:
            case Driver::MSSQL_DBLIB:
                return 1433;
            default:
                return 3306;
        }
    }

    private function getDefaultCharset(): string
    {
        switch ($this->driver) {
            case Driver::MYSQL:
                return 'utf8mb4';
            case Driver::SQLSERVER:
            case Driver::MSSQL_DBLIB:
                return 'UTF-8';
            default:
                return 'utf8mb4';
        }
    }

    /**
     * Build PDO DSN string based on driver
     */
    public function buildDsn(): string
    {
        switch ($this->driver) {
            case Driver::MYSQL:
                return sprintf(
                    'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                    $this->host,
                    $this->port,
                    $this->database,
                    $this->charset
                );
            case Driver::SQLSERVER:
                return sprintf(
                    'sqlsrv:Server=%s,%d;Database=%s',
                    $this->host,
                    $this->port,
                    $this->database
                );
            case Driver::MSSQL_DBLIB:
                return sprintf(
                    'dblib:host=%s:%d;dbname=%s;charset=%s',
                    $this->host,
                    $this->port,
                    $this->database,
                    $this->charset
                );
            default:
                throw new InvalidArgumentException("Unsupported driver: {$this->driver}");
        }
    }

    /**
     * Get default PDO options
     */
    public function getOptions(): array
    {
        $defaultOptions = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        if ($this->driver === Driver::MYSQL) {
            $defaultOptions[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES {$this->charset}";
            $defaultOptions[PDO::MYSQL_ATTR_FOUND_ROWS] = true;
        }

        return array_replace($defaultOptions, $this->options);
    }

    public function getDriver(): string
    {
        return $this->driver;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getSchema(): ?string
    {
        return $this->schema;
    }

    public function getDatabase(): string
    {
        return $this->database;
    }
}
