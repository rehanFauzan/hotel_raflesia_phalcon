<?php

namespace App\Modules\Hotel\Master\User;

use App\Modules\Defaults\BaseModel;

class Model extends BaseModel
{
    public function initialize()
    {
        $this->setSource('system_user');
    }
}