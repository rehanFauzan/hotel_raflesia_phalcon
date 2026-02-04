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
 * Stored Procedure Parameter
 */
class SPParameter
{
    /** @var string */
    public $name;
    
    /** @var mixed */
    public $value;
    
    /** @var int */
    public $type;
    
    /** @var bool */
    public $isOutput;
    
    public function __construct(
        string $name,
        $value,
        int $type = PDO::PARAM_STR,
        bool $isOutput = false
    ) {
        $this->name = $name;
        $this->value = $value;
        $this->type = $type;
        $this->isOutput = $isOutput;
    }

    /**
     * Automatically detect PDO parameter type from value
     * 
     * @param mixed $value
     * @return int
     */
    public static function detectType($value): int
    {
        if (is_null($value)) {
            return PDO::PARAM_NULL;
        } elseif (is_bool($value)) {
            return PDO::PARAM_BOOL;
        } elseif (is_int($value)) {
            return PDO::PARAM_INT;
        } else {
            return PDO::PARAM_STR;
        }
    }
}
