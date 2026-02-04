<?php

declare(strict_types=1);

namespace App\Modules\Defaults\Auth\Model;

use App\Modules\Defaults\BaseModel;
use App\Traits\LoggableModelTrait;
// use Phalcon\Mvc\Model as BaseModel;
use Core\Models\Behavior\SoftDelete;
use Phalcon\Mvc\Model\Behavior\Timestampable;

class PeriodeAktifModel extends BaseModel
{
    // use LoggableModelTrait
    public function initialize()
    {
        $this->setSource('periodeaktif');
        
    }
}
