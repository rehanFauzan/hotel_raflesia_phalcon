<?php

namespace App\Modules\Hotel\ReferensiData\Pemesanan;

use App\Modules\Defaults\BaseModel;

class Model extends BaseModel
{
    public function initialize()
    {
        $this->setSource('hotel_pemesanan');
    }
}