<?php

namespace App\Modules\Defaults\Master\Bagian;

use App\Traits\LoggableModelTrait;
use Phalcon\Mvc\Model as BaseModel;
use Core\Models\Behavior\SoftDelete;
use Phalcon\Mvc\Model\Behavior\Timestampable;

class BagianModel extends BaseModel
{
    use LoggableModelTrait;

    public function initialize()
    {
        $this->setSource('master_bagian');
    }
}
