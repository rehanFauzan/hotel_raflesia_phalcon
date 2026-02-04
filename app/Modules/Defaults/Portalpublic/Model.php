<?php

namespace App\Modules\Defaults\Pelanggan\Daftar;

use Phalcon\Mvc\Model as BaseModel;
use Core\Models\Behavior\SoftDelete;
use Phalcon\Mvc\Model\Behavior\Timestampable;

class Model extends BaseModel
{
    public function initialize()
    {
        $this->setSource('hublang_pelangganbaru');
    }
}
