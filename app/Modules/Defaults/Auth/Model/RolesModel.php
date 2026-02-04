<?php

declare(strict_types=1);

namespace App\Modules\Defaults\Auth\Model;

use Phalcon\Mvc\Model;

class RolesModel extends Model
{
    public function initialize()
    {
        $this->setSource('system_role');
        
    }
}