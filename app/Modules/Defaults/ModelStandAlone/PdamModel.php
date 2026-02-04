<?php

namespace App\Modules\Defaults\ModelStandAlone;

use Phalcon\Mvc\Model as BaseModel;
use Core\Models\Behavior\SoftDelete;
use Phalcon\Mvc\Model\Behavior\Timestampable;

class PdamModel extends BaseModel
{
    public function initialize()
    {
        $this->setSource('pdam');
        // $this->hasOne('id_kec', KecamatanModel::class, 'id', ['alias' => 'kecamatan', 'reusable' => true]);
    }
}