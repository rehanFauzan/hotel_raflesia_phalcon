<?php

declare(strict_types=1);

namespace App\Modules\Defaults\ModelStandAlone;

use Phalcon\Mvc\Model as BaseModel;

class MasterBagianModel extends BaseModel
{
    public function initialize()
    {
        $this->setSource('master_bagian');
    }
}
