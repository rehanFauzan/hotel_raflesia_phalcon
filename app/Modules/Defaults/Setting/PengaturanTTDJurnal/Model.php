<?php

namespace App\Modules\Defaults\Setting\PengaturanTTDJurnal;

use Phalcon\Mvc\Model as BaseModel;
use Core\Models\Behavior\SoftDelete;
use Phalcon\Mvc\Model\Behavior\Timestampable;

class Model extends BaseModel
{
    public function initialize()
    {
        $this->setSource('ttd_ref_pengesahan');
        
    }
}