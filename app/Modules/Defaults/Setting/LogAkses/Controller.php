<?php

declare(strict_types=1);

namespace App\Modules\Defaults\Setting\LogAkses;

use Phalcon\Mvc\Controller as BaseController;
use Core\Facades\Response;
use Core\Facades\Request;
use Core\Paginator\DataTables\DataTable;
use App\Libraries\Log;
use Core\Facades\Security;


/**
 * @routeGroup('/setting/log_akses')
 * @middleware('RequireUser')
 */
class Controller extends BaseController
{

    /**
     * @routeGet('/')
     */
    public function indexAction() {}


    /**
     * @routeGet("/datatable")
     * @routePost("/datatable")
     */
    public function datatableAction()
    {
        $tanggal_awal   = Request::getPost('filter_tanggal_awal_temp');
        $tanggal_akhir  = Request::getPost('filter_tanggal_akhir_temp');
        $keterangan     = Request::getPost('filter_keterangan_temp');
        $user_id        = Request::getPost('id_user');

        $builder = $this->modelsManager->createBuilder()
            ->columns('*')
            ->from(LogAksesModel::class)
            ->where("id_user = :user_id:", ['user_id' => $user_id]);

        // filter tanggal awal & akhir
        if (!empty($tanggal_awal) && !empty($tanggal_akhir)) {
            $builder->andWhere("CAST(times AS DATE) BETWEEN :awal: AND :akhir:", [
                'awal'  => $tanggal_awal,
                'akhir' => $tanggal_akhir
            ]);
        } else if (!empty($tanggal_awal)) {
            $builder->andWhere("CAST(times AS DATE) >= :awal:", [
                'awal' => $tanggal_awal
            ]);
        } else if (!empty($tanggal_akhir)) {
            $builder->andWhere("CAST(times AS DATE) <= :akhir:", [
                'akhir' => $tanggal_akhir
            ]);
        }

        // filter keterangan
        if (!empty($keterangan)) {
            $builder->andWhere("message LIKE :ket:", [
                'ket' => "%$keterangan%"
            ]);
        }

        $dataTables = new DataTable();
        $dataTables->fromBuilder($builder)->sendResponse();
    }

    /**
     * @routeGet("/getUserdata")
     * @routePost("/getUserdata")
     */
    public function getUserdataAction()
    {
        header('Content-Type: application/json');

        // $pdam_id = $this->session->user['pdam_id'];
        $data = LogAksesViewModel::find();

        return $this->response->setContent(json_encode($data));
    }
}
