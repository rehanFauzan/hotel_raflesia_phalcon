<?php

declare(strict_types=1);

namespace App\Modules\Defaults\Master\Jabatan;

use Core\Facades\Response;
use Core\Facades\Request;
use Core\Paginator\DataTables\DataTable;
use App\Libraries\Log;
use App\Modules\Defaults\BaseController;
use Core\Facades\Security;
use App\Modules\Defaults\Master\Jabatan\JabatanModel;
use App\Modules\Defaults\Master\Jabatan\JabatanViewModel;

/**
 * @routeGroup('/master/master_jabatan')
 * @middleware('RequireUser')
 * 
 */
class Controller extends BaseController
{
    /**
     * @routeGet('/')
     */
    public function indexAction()
    {

        $this->view->is_can_insert = $this->is_hak_input;
        $this->view->is_can_update = $this->is_hak_ubah;
        $this->view->is_can_delete = $this->is_hak_delete;
        $this->view->is_can_print = $this->is_hak_cetak;
        $this->view->is_can_verifikasi = $this->is_hak_verifikasi;
        $this->view->is_can_unverifikasi = $this->is_hak_unverifikasi;
    }


    /**
     * @routePost('/datatable')
     * @routeGet('/datatable')
     */
    public function datatableAction()
    {
        $search = $_POST['search'] ?? '';

        $search_kode = escape_xss($this->request->getPost('search_kode'));
        $search_nama = escape_xss($this->request->getPost('search_nama'));
        $search_level_id = escape_xss($this->request->getPost('search_level_id'));
        $search_bagian_id = escape_xss($this->request->getPost('search_bagian_id'));
        $search_sub_bagian_id = escape_xss($this->request->getPost('search_sub_bagian_id'));

        $pdam_id = escape_xss($this->session->user['pdam_id']);

        $builder = $this->modelsManager->createBuilder()
            ->columns('*')
            ->from(JabatanViewModel::class)
            ->where("1=1")
            ->andWhere("pdam_id = :pdam_id:", ['pdam_id' => $pdam_id]);

        // Then use in your SQL WHERE clause
        if (!empty($search) && isset($search)) {
            $builder->andWhere("kode LIKE '%$search%' OR nama LIKE '%$search%' OR master_level_jabatan_nama LIKE '%$search%' OR master_bagian_nama LIKE '%$search%' OR master_sub_bagian_nama LIKE '%$search%'");
        }

        if (!empty($search_kode) && isset($search_kode)) {
            $builder->andWhere("kode LIKE '%$search_kode%'");
        }

        if (!empty($search_nama) && isset($search_nama)) {
            $builder->andWhere("nama LIKE '%$search_nama%'");
        }

        if (!empty($search_level_id) && isset($search_level_id)) {
            $builder->andWhere("master_level_jabatan_id = '$search_level_id'");
        }

        if (!empty($search_bagian_id) && isset($search_bagian_id)) {
            $builder->andWhere("master_bagian_id = '$search_bagian_id'");
        }

        if (!empty($search_sub_bagian_id) && isset($search_sub_bagian_id)) {
            $builder->andWhere("master_sub_bagian_id = '$search_sub_bagian_id'");
        }

        $dataTables = new DataTable();
        $dataTables->fromBuilder($builder)->sendResponse();
    }


    /**
     * @routePost('/saveData')
     */
    public function saveDataAction()
    {
        if ($this->request->isPost()) {
            $this->db->begin(); // Begin transaction

            $pdam_id = escape_xss($this->session->user['pdam_id']);
            $id_user = escape_xss($this->session->user['id']);

            $kode = escape_xss($this->request->getPost('kode'));
            $nama = escape_xss($this->request->getPost('nama'));
            $level_id = escape_xss($this->request->getPost('level_id'));
            $bagian_id = escape_xss($this->request->getPost('bagian_id'));
            $sub_bagian_id = escape_xss($this->request->getPost('sub_bagian_id'));

            $insertData = [
                'pdam_id' => $pdam_id,
                'kode' => $kode,
                'nama' => $nama,
                'master_level_jabatan_id' => $level_id,
                'master_bagian_id' => $bagian_id,
                'master_sub_bagian_id' => $sub_bagian_id,
                'created_at' => date('Y-m-d H:i:s'),
                'user_create' => $id_user
            ];

            $insert = new JabatanModel();
            $insert->assign($insertData);
            $result_insert = $insert->save();

            if ($result_insert) {
                $this->db->commit(); // Commit transaction
                Response::setStatusCode(200, "OK");
                return Response::setJsonContent([
                    'error' => 0,
                    'message' => "Simpan Data Berhasil",
                    'lastId' => $insert->id
                ]);
            } else {
                $this->db->rollback(); // Rollback transaction
                Response::setStatusCode(404, "FAILED");
                return Response::setJsonContent([
                    'error' => 1,
                    'message' => "Simpan Data Gagal",
                    'lastId' => null
                ]);
            }
        } else {
            Response::setStatusCode(601, "FAILED");
            return Response::setJsonContent([
                'error' => 1,
                'message' => "Methode not allowed"
            ]);
        }
    }
    

    /**
     * @routePost('/updateData')
     */
    public function updateDataAction()
    {
        if ($this->request->isPost()) {
            $this->db->begin(); // Begin transaction

            $id_user = escape_xss($this->session->user['id']);

            $id_edit = escape_xss($this->request->getPost('id_edit'));
            $kode = escape_xss($this->request->getPost('kode'));
            $nama = escape_xss($this->request->getPost('nama'));
            $level_id = escape_xss($this->request->getPost('level_id'));
            $bagian_id = escape_xss($this->request->getPost('bagian_id'));
            $sub_bagian_id = escape_xss($this->request->getPost('sub_bagian_id'));

            $existingUser = JabatanModel::findFirst([
                "conditions" => "kode = :kode: AND id != :id:",
                "bind" => [
                    "kode" => $kode,
                    "id" => $id_edit
                ]
            ]);

            if ($existingUser) {
                $this->db->rollback(); // Rollback transaction
                Response::setStatusCode(400);
                return Response::setJsonContent([
                    'error' => 1,
                    'message' => "Nama '$nama' sudah digunakan oleh Jabatan lain. Silahkan gunakan Jabatan yang berbeda."
                ]);
            }

            $updateData = [
                'kode' => $kode,
                'nama' => $nama,
                'master_level_jabatan_id' => $level_id,
                'master_bagian_id' => $bagian_id,
                'master_sub_bagian_id' => $sub_bagian_id,
                'updated_at' => date('Y-m-d H:i:s'),
                'last_update_by' => $id_user
            ];

            $update = JabatanModel::findFirst([
                'conditions' => "id = :id:",
                'bind' => [
                    'id' => $id_edit
                ]
            ]);
            $update->assign($updateData);
            $result_update = $update->save();

            if ($result_update) {
                $this->db->commit(); // Commit transaction
                Response::setStatusCode(200, "OK");
                return Response::setJsonContent([
                    'error' => 0,
                    'message' => "Ubah Data Berhasil",
                    'lastId' => $id_edit
                ]);
            } else {
                $this->db->rollback(); // Rollback transaction
                Response::setStatusCode(404, "FAILED");
                return Response::setJsonContent([
                    'error' => 1,
                    'message' => "Ubah Data Gagal",
                    'lastId' => null
                ]);
            }
        } else {
            Response::setStatusCode(601, "FAILED");
            return Response::setJsonContent([
                'error' => 1,
                'message' => "Methode not allowed"
            ]);
        }
    }


    /**
     * @routePost('/deleteData')
     */
    public function deleteDataAction()
    {
        if ($this->request->isPost()) {
            // if ($this->security->checkToken(null, null, false)) {

            $pdam_id = escape_xss($this->session->user['pdam_id']);

            $id_delete = escape_xss($this->request->getPost('id_delete'));

            $findForDelete = JabatanModel::findFirst([
                'conditions' => "id = :id: AND pdam_id = :pdam_id:",
                'bind' => [
                    'pdam_id' => $pdam_id,
                    'id' => $id_delete
                ]
            ]);
            $resultDelete = $findForDelete->delete();

            if ($resultDelete) {
                Response::setStatusCode(200, "OK");
                return Response::setJsonContent([
                    'error' => 0,
                    'message' => "Hapus Data Berhasil",
                    'lastId' => $id_delete
                ]);
            } else {
                Response::setStatusCode(404, "FAILED");
                return Response::setJsonContent([
                    'error' => 1,
                    'message' => "Hapus Data Gagal",
                    'lastId' => null
                ]);
            }

            // } else {
            //     Response::setStatusCode(602, "FAILED");
            //     return Response::setJsonContent([
            //         'error' => 1,
            //         'message' => "Terjadi kesalahan karena masalah keamanan, silahkan coba refresh ulang"
            //     ]);
            // }

        } else {
            Response::setStatusCode(601, "FAILED");
            return Response::setJsonContent([
                'error' => 1,
                'message' => "Methode not allowed"
            ]);
        }
    }

}
