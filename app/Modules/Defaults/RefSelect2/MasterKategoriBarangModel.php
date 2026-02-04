<?php

declare(strict_types=1);

namespace App\Modules\Defaults\RefSelect2;

use App\Modules\Defaults\BaseModel;
use App\Traits\LoggableModelTrait;
// use Phalcon\Mvc\Model as BaseModel;
use Core\Models\Behavior\SoftDelete;
use Phalcon\Mvc\Model\Behavior\Timestampable;

class MasterKategoriBarangModel extends BaseModel
{
    public function initialize()
    {
        $this->setSource('master_kategori');
        $this->setSchema('inventori');
    }
}
