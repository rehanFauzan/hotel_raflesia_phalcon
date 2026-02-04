<?php

namespace App\Modules\Hotel\Transaksi\Pemesanan;

use App\Modules\Defaults\BaseModel;
use App\Traits\LoggableModelTrait;

class TamuModel extends BaseModel
{
    public function initialize()
    {
        $this->setSource('hotel_tamu');
    }

    public function getSource()
    {
        return 'hotel_tamu';
    }
}