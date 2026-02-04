<?php

namespace App\Modules\Defaults\Master\Kelurahan;

use Phalcon\Mvc\Model as BaseModel;
use Core\Models\Behavior\SoftDelete;
use Phalcon\Mvc\Model\Behavior\Timestampable;

class Model extends BaseModel
{
    public function initialize()
    {
        $this->setSource('kelurahan');
        // $this->hasOne('id_kec', KecamatanModel::class, 'id', ['alias' => 'kecamatan', 'reusable' => true]);
    }
}