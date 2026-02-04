<?php

namespace App\Modules\Defaults\RefSelect2;

use App\Modules\Defaults\BaseModel;
use App\Traits\LoggableModelTrait;
// use Phalcon\Mvc\Model as BaseModel;
use Core\Models\Behavior\SoftDelete;
use Phalcon\Mvc\Model\Behavior\Timestampable;



class VwTransUangMukaPjModel extends BaseModel
{
    // use LoggableModelTrait;
    public function initialize()
    {
        $this->setSource('trans_uangmuka_pj_view');
        
    }
}
