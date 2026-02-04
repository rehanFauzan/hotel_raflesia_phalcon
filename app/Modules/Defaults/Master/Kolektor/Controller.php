<?php

namespace App\Modules\Defaults\Master\Kolektor;

use Phalcon\Mvc\Controller as BaseController;

use Core\Facades\Response;
use Core\Facades\Request;
use Core\Paginator\DataTables\DataTable;

use App\Libraries\Log;
use App\Exceptions\NotFoundException;

use App\Modules\Defaults\Master\Kelurahan\Model as KelurahanModel;
use App\Modules\Defaults\Master\Kecamatan\Model as KecamatanModel;
use App\Modules\Defaults\Master\Kelurahan\ModelView as KelurahanviewModel;
use App\Modules\Defaults\Master\Kolektor\Model as KolektorModel;
use App\Modules\Defaults\ModelStandAlone\CabangModel;
use App\Modules\Defaults\Middleware\Controller as MiddlewareHardController;

/**
 * @routeGroup('/Master/Kolektor')
 * @middleware('RequireRole', 'Admin')
 */
class Controller extends MiddlewareHardController
{
    public function initialize()
    {
        // parent::setUsingRole('yes');
        // parent::setArrayRole(['Kasir', 'Kapala Unit', 'Hublang Unit']);
        parent::initialize();
    }
    /**
     * @routeGet('/')
     */
    public function indexAction($id)
    {
        $pdamid = $this->session->user['pdam_id'];

        $this->view->setVar('module', $id);
        $this->view->setVar('pdamid', $pdamid);
    }

    /**
     * @routePost('/add/{id:\d+}')
     * @routeGet('/add/{id:\d+}')
     */
    public function addAction($moduleParams, $id)
    {
        $sql = "CALL sp_billing_master_kolektor_getdata($id)";
        $data = $this->db->fetchAll($sql);
        $a = $data;

        foreach ($a as $row) {
            $nama = $row['nama_kolektor'];
            $cabang = "(" . $row['of_code'] . ") " . $row['of_name'];
        }

        $this->view->nama = $nama;
        $this->view->cabang = $cabang;
        $this->view->id = $id;
        $this->view->setVar('module', $moduleParams);
    }

    /**
     * @routePost('/loadData')
     * @routeGet('/loadData')
     */
    public function loadDataAction()
    {
        //bawaan datatable
        // $search = $this->request->getPost('search')['value'];
        $search = $this->request->getPost('search_kolektor_nama');
        $is_search = (empty($search) ? 0 : 1);

        $limit = $this->request->getPost('length');
        $start = $this->request->getPost('start');
        $pdamid = $this->session->user['pdam_id'];

        $sql = "CALL sp_billing_master_kolektor_count($is_search, '$search', $limit, $start, '$pdamid')";
        $sql2 = $sql;
        $data = $this->db->fetchAll($sql);
        $a = $data;
        $totalData = $a[0]['jml'];
        unset($data);

        $sql = "CALL sp_billing_master_kolektor($is_search, '$search',$limit,$start,'$pdamid')";
        $data = $this->db->fetchAll($sql);
        $a = $data;

        $json_data = array(
            "draw" => intval($this->request->getPost('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalData),
            "data" => $a,
            "query" => $sql2,
        );
        echo json_encode($json_data);
    }

    /**
     * @routePost('/saveData')
     * @routeGet('/saveData')
     */
    public function saveDataAction()
    {
        $nama = $this->request->getPost('nama_kolektor');
        $kec_id = $this->request->getPost('id_kecamatan');

        $checkDataKolektor = KolektorModel::findFirst([
            "conditions" => "nama_kolektor = '$nama'"
        ]);
        if ($checkDataKolektor) {
            $cNs = $checkDataKolektor->toArray();
        } else {
            $cNs = 0;
        }

        if ($cNs != 0) {
            echo 3;
        } else {
            $getDataKec = CabangModel::findFirstByof_id($kec_id)->toArray();
            $kec_name = $getDataKec['of_name'];
            $pdamid = $this->session->user['pdam_id'];

            if ($pdamid == 13) {
                $sql = "CALL sp_billing_master_kolektor_insert_natuna('$nama','$kec_id','$kec_name','$pdamid')";
            } else if ($pdamid == 7) {
                $sql = "CALL sp_billing_master_kolektor_insert_nciho('$nama','$kec_id','$kec_name','$pdamid')";
            } else {
                $sql = "CALL sp_billing_master_kolektor_insert('$nama','$kec_id','$kec_name','$pdamid')";
            }

            $data = $this->db->fetchAll($sql);
            $a = $data;

            foreach ($a as $datas) {
                $status = $datas['stts'];
            }

            echo $status;
        }
    }

    /**
     * @routePost('/updateData')
     * @routeGet('/updateData')
     */
    public function updateDataAction()
    {
        $id = $this->request->getPost('id');
        $nama = $this->request->getPost('nama_kolektor');
        // $cabang = explode("||", $this->request->getPost('cmb_cabang'));
        $kec_id = $this->request->getPost('id_kecamatan');
        $getDataKec = CabangModel::findFirstByof_id($kec_id)->toArray();
        $kec_name = $getDataKec['of_name'];

        $sql = "CALL sp_billing_master_kolektor_update($id,'$nama','$kec_id','$kec_name')";
        $data = $this->db->fetchAll($sql);
        $a = $data;

        foreach ($a as $datas) {
            $status = $datas['stts'];
        }

        echo $status;
    }

    /**
     * @routePost('/deleteData/{id:\d+}')
     * @routeGet('/deleteData/{id:\d+}')
     */
    public function deleteDataAction($moduleParams, $id)
    {
        $idDel = $id;

        $sql = "CALL sp_billing_master_kolektor_delete($idDel)";
        $data = $this->db->fetchAll($sql);
        $a = $data;

        foreach ($a as $datas) {
            $status = $datas['stts'];
        }

        echo $status;
    }

    /**
     * @routePost('/loadDatakolektor')
     * @routeGet('/loadDatakolektor')
     */
    public function loadDatakolektorAction()
    {
        //bawaan datatable
        $search = $this->request->getPost('search')['value'];
        $is_search = (empty($search) ? 0 : 1);
        $id = $this->request->getPost('id');
        $limit = $this->request->getPost('length');
        $start = $this->request->getPost('start');

        $sql = "CALL sp_billing_master_kolektor_datapelanggan_count($id,$is_search, '$search',$limit,$start)";
        $sql2 = $sql;
        $data = $this->db->fetchAll($sql);
        $a = $data;
        $totalData = $a[0]['jml'];
        unset($data);

        $sql = "CALL sp_billing_master_kolektor_datapelanggan($id,$is_search, '$search',$limit,$start)";
        $data = $this->db->fetchAll($sql);
        $a = $data;

        $json_data = array(
            "draw" => intval($this->request->getPost('draw')),
            "recordsTotal" => intval($totalData),
            "recordsFiltered" => intval($totalData),
            "data" => $a,
            "query" => $sql2,
        );

        echo json_encode($json_data);
    }

    /**
     * @routePost('/saveDatakolektor')
     * @routeGet('/saveDatakolektor')
     */
    public function saveDatakolektorAction()
    {
        $id = $this->request->getPost('id');
        $pelanggan = $this->request->getPost('cust_code123');

        $sql = "CALL sp_billing_master_kolektor_datapelanggan_insert($id,'$pelanggan')";
        $data = $this->db->fetchAll($sql);
        $a = $data;

        foreach ($a as $datas) {
            $status = $datas['stts'];
        }
        echo $status;
    }

    /**
     * @routePost('/deleteDatakolektor')
     * @routeGet('/deleteDatakolektor')
     */
    public function deleteDatakolektorAction()
    {
        $id = $this->request->getPost('cust_id');

        $sql = "CALL sp_billing_master_kolektor_datapelanggan_delete('$id')";
        $data = $this->db->fetchAll($sql);
        $a = $data;

        foreach ($a as $datas) {
            $status = $datas['stts'];
        }
        echo $status;
    }
}
