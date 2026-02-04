<?php

namespace App\Modules\Defaults\Setting\LogAkses;

use Phalcon\Mvc\Model as BaseModel;
use Core\Models\Behavior\SoftDelete;
use Phalcon\Mvc\Model\Behavior\Timestampable;

class LogAksesModel extends BaseModel
{
    public function initialize()
    {
        $this->setSource('system_log_akses');
        
    }
}
