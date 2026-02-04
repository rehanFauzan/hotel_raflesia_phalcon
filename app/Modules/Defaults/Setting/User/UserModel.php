<?php

namespace App\Modules\Defaults\Setting\User;

use App\Traits\LoggableModelTrait;
// use App\Modules\Defaults\BaseModel;
use Phalcon\Mvc\Model as BaseModel;
use Core\Models\Behavior\SoftDelete;
use Phalcon\Mvc\Model\Behavior\Timestampable;

class UserModel extends BaseModel
{
    use LoggableModelTrait;

    public function initialize()
    {
        $this->setSource('system_user');
        
        $this->getModelsMetaData()->reset();
    }
}
