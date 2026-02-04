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
 * Database Driver Constants
 */
class Driver
{
    public const MYSQL = 'mysql';
    public const SQLSERVER = 'sqlsrv';
    public const MSSQL_DBLIB = 'dblib';
}
