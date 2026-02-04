<?php

declare(strict_types=1);

namespace App\Modules\Defaults\ModelStandAlone;

use Phalcon\Mvc\Model as BaseModel;
use Core\Models\Behavior\SoftDelete;
use Phalcon\Mvc\Model\Behavior\Timestampable;


class PegawaiViewModel extends BaseModel
{
    public function initialize()
    {
        $this->setSource('vw_employee_periode');
    }
}
