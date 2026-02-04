<?php

declare(strict_types=1);

namespace App\Modules\Defaults\Auth\Model;

use Phalcon\Mvc\Model;
use Core\Models\Behavior\SoftDelete;
use Phalcon\Mvc\Model\Behavior\Timestampable;
use App\Modules\Defaults\Auth\Model\RolesModel;
use App\Modules\Defaults\ModelStandAlone\CabangModel;
use App\Modules\Defaults\ModelStandAlone\EmployeeViewModel;

class UsersModel extends Model
{
    public function initialize()
    {
        // $this->setConnectionService('db.main');
        $this->setSource('system_user');
        
        
        // $this->addBehavior(new Timestampable([
        //     'beforeCreate' => ['field' => 'created_at', 'format' => 'Y-m-d H:i:s'],
        //     'beforeUpdate' => ['field' => 'updated_at', 'format' => 'Y-m-d H:i:s'],
        // ]));

        $this->hasOne('id_role', RolesModel::class, 'id', ['alias' => 'role', 'reusable' => true]);
        // $this->hasOne('id_cabang', CabangModel::class, 'of_id', ['alias' => 'office', 'reusable' => true]);
        // $this->hasOne('pgw_id', EmployeeViewModel::class, 'pgw_id', ['alias' => 'emp', 'reusable' => true]);
    }
}
