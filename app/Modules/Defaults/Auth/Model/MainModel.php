<?php

declare(strict_types=1);

namespace App\Modules\Defaults\Auth\Model;

use Core\Facades\DB;
use Phalcon\Mvc\Model;
use PDO;

class MainModel extends Model
{
    public function initialize()
    {
        $this->setSource('system_pdam');
        
    }
}
