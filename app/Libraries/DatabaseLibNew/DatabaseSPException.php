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
 * Custom Exception for Database SP Operations
 */
class DatabaseSPException extends RuntimeException
{
    /** @var array */
    protected $errorInfo = [];
    
    /** @var string */
    protected $sqlState = '';

    public function __construct(
        string $message = '',
        int $code = 0,
        ?PDOException $previous = null,
        array $errorInfo = []
    ) {
        parent::__construct($message, $code, $previous);
        $this->errorInfo = $errorInfo;
        if (!empty($errorInfo[0])) {
            $this->sqlState = $errorInfo[0];
        }
    }

    public function getErrorInfo(): array
    {
        return $this->errorInfo;
    }

    public function getSqlState(): string
    {
        return $this->sqlState;
    }
}
