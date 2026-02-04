<?php

namespace Core\Db\Adapter;
use Phalcon\Db\Adapter\PdoFactory as Factory; 

class PdoFactory extends Factory
{
    
    /**
     * {@inheritdoc}
     */
    protected function getAdapters(): array
    {
        return [
            "mysql"      => "Phalcon\\Db\\Adapter\\Pdo\\Mysql",
            "postgresql" => "Phalcon\\Db\\Adapter\\Pdo\\Postgresql",
            "sqlite"     => "Phalcon\\Db\\Adapter\\Pdo\\Sqlite",
            "sqlsrv"     => "Core\\Db\\Adapter\\Pdo\\Sqlsrv",
            "dblib"     => "Core\\Db\\Adapter\\Pdo\\Dblib",
        ];
    }
}