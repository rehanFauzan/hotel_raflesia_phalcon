<?php

/**
 * PHP Library for Calling Stored Procedures
 * Supports MySQL and SQL Server (MSSQL) databases
 * 
 * @package     DatabaseSP
 * @version     1.0.0
 * @author      Auto
 * @license     MIT
 */

namespace App\Libraries\DatabaseLibNew;

use PDO;
use Phalcon\Db\Adapter\AdapterInterface;

/**
 * Simple Helper Class for Easy Stored Procedure Calls
 * 
 * This class provides a simple interface to call stored procedures
 * that works with both MySQL and SQL Server databases.
 * 
 * Usage Examples:
 * 
 * // With PDO connection
 * $pdo = new PDO(...);
 * $sp = new SPHelper($pdo, 'sqlsrv'); // or 'mysql'
 * $result = $sp->call('sp_proses_periode', ['vm' => 12, 'vy' => 2024], 'akuntansi')->fetchOne();
 * 
 * // With Phalcon DB adapter
 * $sp = SPHelper::fromPhalcon($this->db);
 * $result = $sp->call('sp_proses_periode', ['vm' => 12, 'vy' => 2024], 'akuntansi')->fetchOne();
 * 
 * // For MySQL
 * $sp = SPHelper::fromPhalcon($this->db, 'mysql');
 * $data = $sp->call('sp_proses_periode', ['m_periode' => 12, 'y_periode' => 2024])->fetchAll();
 */
class SPHelper
{
    /** @var DatabaseSP */
    private $db;
    
    /** @var string */
    private $driver;

    /**
     * Create SPHelper from PDO connection
     * 
     * @param PDO $pdo PDO connection instance
     * @param string $driver Database driver ('mysql' or 'sqlsrv')
     * @param string|null $schema Default schema name (optional)
     */
    public function __construct(PDO $pdo, string $driver = 'mysql', ?string $schema = null)
    {
        $this->driver = $driver;
        
        // Extract connection info from PDO (minimal config, we won't use it to connect)
        $config = $this->extractConfigFromPdo($pdo, $driver, $schema);
        
        $this->db = new DatabaseSP($config);
        
        // Directly set the existing PDO connection without calling connect()
        // This avoids creating a new connection and authentication issues
        $this->db->setConnection($pdo);
    }

    /**
     * Create SPHelper from Phalcon DB adapter
     * 
     * @param AdapterInterface $adapter Phalcon database adapter
     * @param string|null $driver Database driver (auto-detect if null)
     * @param string|null $schema Default schema name (optional)
     * @return self
     */
    public static function fromPhalcon(AdapterInterface $adapter, ?string $driver = null, ?string $schema = null): self
    {
        // Get PDO connection from Phalcon adapter
        // Phalcon DB adapter has getInternalHandler() method that returns PDO
        if (method_exists($adapter, 'getInternalHandler')) {
            $pdo = $adapter->getInternalHandler();
        } else {
            // Fallback: try to get connection via reflection
            $reflection = new \ReflectionClass($adapter);
            if ($reflection->hasProperty('_pdo')) {
                $property = $reflection->getProperty('_pdo');
                $property->setAccessible(true);
                $pdo = $property->getValue($adapter);
            } elseif ($reflection->hasProperty('connection')) {
                $property = $reflection->getProperty('connection');
                $property->setAccessible(true);
                $pdo = $property->getValue($adapter);
            } else {
                throw new \RuntimeException('Cannot extract PDO connection from Phalcon adapter');
            }
        }
        
        // Auto-detect driver if not provided
        if ($driver === null) {
            $driver = self::detectDriverFromPhalcon($adapter);
        }
        
        return new self($pdo, $driver, $schema);
    }

    /**
     * Call a stored procedure
     * 
     * @param string $procedureName Name of the stored procedure
     * @param array $params Parameters as key-value pairs
     * @param string|null $schema Optional schema name (overrides default)
     * @return DatabaseSP Returns DatabaseSP instance for method chaining
     * 
     * @example
     * // SQL Server
     * $sp->call('sp_proses_periode', ['vm' => 12, 'vy' => 2024], 'akuntansi')->fetchOne();
     * 
     * @example
     * // MySQL
     * $sp->call('sp_proses_periode', ['m_periode' => 12, 'y_periode' => 2024])->fetchAll();
     */
    public function call(string $procedureName, array $params = [], ?string $schema = null): DatabaseSP
    {
        return $this->db->call($procedureName, $params, $schema);
    }

    /**
     * Execute a stored procedure and return all results
     * 
     * @param string $procedureName Name of the stored procedure
     * @param array $params Parameters as key-value pairs
     * @param string|null $schema Optional schema name
     * @param int|null $fetchMode PDO fetch mode
     * @return array
     */
    public function fetchAll(string $procedureName, array $params = [], ?string $schema = null, ?int $fetchMode = null): array
    {
        return $this->call($procedureName, $params, $schema)->fetchAll($fetchMode);
    }

    /**
     * Execute a stored procedure and return single result
     * 
     * @param string $procedureName Name of the stored procedure
     * @param array $params Parameters as key-value pairs
     * @param string|null $schema Optional schema name
     * @param int|null $fetchMode PDO fetch mode
     * @return mixed
     */
    public function fetchOne(string $procedureName, array $params = [], ?string $schema = null, ?int $fetchMode = null)
    {
        return $this->call($procedureName, $params, $schema)->fetchOne($fetchMode);
    }

    /**
     * Execute a stored procedure and return single column value
     * 
     * @param string $procedureName Name of the stored procedure
     * @param array $params Parameters as key-value pairs
     * @param string|null $schema Optional schema name
     * @param int $columnIndex Column index (0-based)
     * @return mixed
     */
    public function fetchColumn(string $procedureName, array $params = [], ?string $schema = null, int $columnIndex = 0)
    {
        return $this->call($procedureName, $params, $schema)->fetchColumn($columnIndex);
    }

    /**
     * Execute a stored procedure (for INSERT/UPDATE/DELETE operations)
     * 
     * @param string $procedureName Name of the stored procedure
     * @param array $params Parameters as key-value pairs
     * @param string|null $schema Optional schema name
     * @return int Number of affected rows
     */
    public function execute(string $procedureName, array $params = [], ?string $schema = null): int
    {
        return $this->db->execute($procedureName, $params, $schema);
    }

    /**
     * Get the underlying DatabaseSP instance
     */
    public function getDatabaseSP(): DatabaseSP
    {
        return $this->db;
    }

    /**
     * Extract configuration from PDO connection
     */
    private function extractConfigFromPdo(PDO $pdo, string $driver, ?string $schema): array
    {
        // Minimal config needed - we'll use the existing PDO connection
        $config = [
            'driver' => $driver,
            'host' => 'localhost',
            'port' => $driver === Driver::MYSQL ? 3306 : 1433,
            'database' => '',
            'username' => '',
            'password' => '',
        ];

        if ($schema !== null) {
            $config['schema'] = $schema;
        }

        return $config;
    }

    /**
     * Detect database driver from Phalcon adapter
     */
    private static function detectDriverFromPhalcon(AdapterInterface $adapter): string
    {
        $adapterType = get_class($adapter);
        
        // Check adapter type
        if (stripos($adapterType, 'mysql') !== false) {
            return Driver::MYSQL;
        } elseif (stripos($adapterType, 'sqlsrv') !== false || stripos($adapterType, 'mssql') !== false) {
            return Driver::SQLSERVER;
        }
        
        // Try to get from connection descriptor
        try {
            $descriptor = $adapter->getDescriptor();
            if (isset($descriptor['driver'])) {
                return $descriptor['driver'];
            }
        } catch (\Exception $e) {
            // Ignore
        }
        
        // Default to MySQL
        return Driver::MYSQL;
    }
}

