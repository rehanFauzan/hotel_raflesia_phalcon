<?php

namespace App\Modules\Defaults\Master\Direktorat;

use App\Traits\LoggableModelTrait;
use Phalcon\Mvc\Model as BaseModel;
use Core\Models\Behavior\SoftDelete;
use Phalcon\Mvc\Model\Behavior\Timestampable;

class DirektoratViewModel extends BaseModel
{
    use LoggableModelTrait;

    public function initialize()
    {
        $this->setSource('master_vw_datatable_direktorat');
    }
}
