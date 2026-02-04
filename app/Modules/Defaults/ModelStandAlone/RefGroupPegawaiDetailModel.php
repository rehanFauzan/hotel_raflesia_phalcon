<?php

namespace App\Modules\Defaults\ModelStandAlone;

use App\Traits\LoggableModelTrait;
use Phalcon\Mvc\Model as BaseModel;
use Core\Models\Behavior\SoftDelete;
use Phalcon\Mvc\Model\Behavior\Timestampable;

class RefGroupPegawaiDetailModel extends BaseModel
{
    use LoggableModelTrait;

    public function initialize()
    {
        $this->setSource('ref_group_pegawai_detail');
    }
}
