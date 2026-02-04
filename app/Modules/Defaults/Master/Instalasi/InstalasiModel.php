<?php

namespace App\Modules\Defaults\Master\Instalasi;

use App\Traits\LoggableModelTrait;
use Phalcon\Mvc\Model as BaseModel;
use Core\Models\Behavior\SoftDelete;
use Phalcon\Mvc\Model\Behavior\Timestampable;

class InstalasiModel extends BaseModel
{
    use LoggableModelTrait;

    public function initialize()
    {
        $this->setSource('ref_installasi');
    }
}
