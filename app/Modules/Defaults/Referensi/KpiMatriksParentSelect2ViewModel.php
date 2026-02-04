<?php

declare(strict_types=1);

namespace App\Modules\Defaults\Referensi;

use Phalcon\Mvc\Model as BaseModel;
use Core\Models\Behavior\SoftDelete;
use Phalcon\Mvc\Model\Behavior\Timestampable;

class KpiMatriksParentSelect2ViewModel extends BaseModel
{
    public function initialize()
    {
        $this->setSource('kpi_vw_select2_kpi_matriks_parents');
    }
}
