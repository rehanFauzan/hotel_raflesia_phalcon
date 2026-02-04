<?php

namespace App\Modules\Hotel\Master\Tamu;

use App\Modules\Defaults\BaseModel;

class Model extends BaseModel
{
    public function initialize()
    {
        $this->setSource('hotel_tamu');
    }
}