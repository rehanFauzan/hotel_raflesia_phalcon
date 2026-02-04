<?php

namespace App\Modules\Defaults\Referensi;

use Phalcon\Mvc\Model as BaseModel;
use Core\Models\Behavior\SoftDelete;
use Phalcon\Mvc\Model\Behavior\Timestampable;

class TransHarianModel extends BaseModel
{
    public function initialize()
    {
        $this->setSource('trans_harian');
    }
}
