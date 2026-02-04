<?php

namespace App\Modules\Defaults\Setting\User;

use Phalcon\Mvc\Model as BaseModel;
use Core\Models\Behavior\SoftDelete;
use Phalcon\Mvc\Model\Behavior\Timestampable;
class UserViewModel extends BaseModel
{
    public function initialize()
    {
        $this->setSource('system_vw_datatable_master_user');
        
    }
}
