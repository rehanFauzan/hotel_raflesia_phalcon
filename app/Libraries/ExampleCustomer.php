<?php


use App\Libraries\DatabaseSP\DatabaseSP;
use App\Libraries\DatabaseSP\Driver;
use App\Libraries\DatabaseSP\FetchMode;
use App\Libraries\DatabaseSp\DB;
use App\Libraries\DatabaseSP\DatabaseSPException;

/**
 * Example 6: Fetch as Custom Class
 */
class ExampleCustomer
{
    public int $id;
    public string $name;
    public string $email;
    public ?string $phone;
}
