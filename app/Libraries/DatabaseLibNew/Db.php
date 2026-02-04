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
 * Static Factory Helper for quick database access
 */
class DB
{
    private static array $instances = [];
    private static string $defaultConnection = 'default';

    /**
     * Register a database connection configuration
     */
    public static function addConnection(array $config, string $name = 'default'): void
    {
        self::$instances[$name] = new DatabaseSP($config);
    }

    /**
     * Get a database connection instance
     */
    public static function connection(string $name = 'default'): DatabaseSP
    {
        if (!isset(self::$instances[$name])) {
            throw new InvalidArgumentException("Database connection '{$name}' not found");
        }
        return self::$instances[$name];
    }

    /**
     * Set the default connection name
     */
    public static function setDefaultConnection(string $name): void
    {
        self::$defaultConnection = $name;
    }

    /**
     * Call a stored procedure on the default connection
     */
    public static function call(string $procedureName, array $params = [], ?string $schema = null): DatabaseSP
    {
        return self::connection(self::$defaultConnection)->call($procedureName, $params, $schema);
    }

    /**
     * Execute a raw query on the default connection
     */
    public static function query(string $sql, array $params = []): DatabaseSP
    {
        return self::connection(self::$defaultConnection)->query($sql, $params);
    }
}
