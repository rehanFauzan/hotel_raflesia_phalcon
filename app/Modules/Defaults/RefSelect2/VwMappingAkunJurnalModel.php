<?php

namespace App\Modules\Defaults\RefSelect2;

use App\Modules\Defaults\BaseModel;
use App\Traits\LoggableModelTrait;
// use Phalcon\Mvc\Model as BaseModel;
use Core\Models\Behavior\SoftDelete;
use Phalcon\Mvc\Model\Behavior\Timestampable;

class VwMappingAkunJurnalModel extends BaseModel
{
    use LoggableModelTrait;

    public function initialize()
    {
        $this->setSource('vw_account_status');
        
    }
}
