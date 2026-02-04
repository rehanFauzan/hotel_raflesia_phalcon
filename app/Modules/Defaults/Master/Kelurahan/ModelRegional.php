<?php

namespace App\Modules\Defaults\Master\Kelurahan;

use Phalcon\Mvc\Model as BaseModel;
use Core\Models\Behavior\SoftDelete;
use Phalcon\Mvc\Model\Behavior\Timestampable;

class ModelRegional extends BaseModel
{
    public function initialize()
    {
        $this->setSource('regional');
        // $this->hasOne('id_kec', KecamatanModel::class, 'id', ['alias' => 'kecamatan', 'reusable' => true]);
    }
}
