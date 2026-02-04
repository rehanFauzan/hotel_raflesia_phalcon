<?php

namespace App\Modules\Hotel\Transaksi\Pemesanan;

use App\Modules\Defaults\BaseModel;
use App\Traits\LoggableModelTrait;

class Model extends BaseModel
{
    public function initialize()
    {
        $this->setSource('hotel_pemesanan');
        
        // Relasi dengan tamu
        $this->belongsTo(
            'tamu_id',
            'App\Modules\Hotel\Transaksi\Pemesanan\TamuModel',
            'id',
            ['alias' => 'Tamu']
        );
        
        // Relasi dengan ruangan
        $this->belongsTo(
            'ruangan_id',
            'App\Modules\Hotel\Master\Kamar\Model',
            'id',
            ['alias' => 'Ruangan']
        );
        
        // Relasi dengan user
        $this->belongsTo(
            'user_id',
            'App\Modules\Defaults\Setting\User\UserModel',
            'id',
            ['alias' => 'User']
        );
    }

    public function getSource()
    {
        return 'hotel_pemesanan';
    }
}