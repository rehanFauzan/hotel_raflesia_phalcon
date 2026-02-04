<?php

namespace App\Modules\Defaults\ReferensiData\Ajuan;

use App\Modules\Defaults\BaseModel;
use App\Traits\LoggableModelTrait;
// use Phalcon\Mvc\Model as BaseModel;
use Core\Models\Behavior\SoftDelete;
use Phalcon\Mvc\Model\Behavior\Timestampable;

class Model extends BaseModel
{
    use LoggableModelTrait;

    public function initialize()
    {
        $this->setSource('master_ajuan');
        
    }
}
