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
 * Fetch Mode Constants
 */
class FetchMode
{
    public const ASSOC = PDO::FETCH_ASSOC;
    public const OBJ = PDO::FETCH_OBJ;
    public const NUM = PDO::FETCH_NUM;
    public const BOTH = PDO::FETCH_BOTH;
    public const CLASS_TYPE = PDO::FETCH_CLASS;
    public const COLUMN = PDO::FETCH_COLUMN;
}
