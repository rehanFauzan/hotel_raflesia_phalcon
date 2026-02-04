<?php

namespace App\Modules\Hotel\Master\TipeKamar;

use App\Modules\Defaults\BaseModel;

class Model extends BaseModel
{
    public function initialize()
    {
        $this->setSource('hotel_tipe_ruangan');
    }
}