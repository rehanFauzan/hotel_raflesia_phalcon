<?php

namespace App\Modules\Hotel\Master\Kamar;

use App\Modules\Defaults\BaseModel;
use App\Modules\Hotel\Master\TipeKamar\Model as TipeKamarModel;

class Model extends BaseModel
{
    public function initialize()
    {
        $this->setSource('hotel_ruangan');
        
        $this->belongsTo('tipe_ruangan_id', TipeKamarModel::class, 'id', [
            'alias' => 'tipeKamar',
            'reusable' => true
        ]);
    }
}