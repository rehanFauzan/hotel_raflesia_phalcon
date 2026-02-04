<?php

namespace App\Modules\Hotel\Master\HargaKamar;

use App\Modules\Defaults\BaseModel;

class Model extends BaseModel
{
    public function initialize()
    {
        $this->setSource('hotel_tipe_ruangan');
    }
}