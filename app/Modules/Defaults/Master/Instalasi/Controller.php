<?php

declare(strict_types=1);

namespace App\Modules\Defaults\Master\Instalasi;

use Core\Facades\Response;
use Core\Facades\Request;
use Core\Paginator\DataTables\DataTable;
use App\Libraries\Log;
use App\Modules\Defaults\BaseController;
use Core\Facades\Security;
use Exception;
use App\Modules\Defaults\Master\Instalasi\InstalasiModel;
use App\Modules\Defaults\Master\AuditorEksternal\AuditorEksternalViewModel;

/**
 * @routeGroup('/master/instalasi')
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

        $pdam_id = escape_xss($this->session->user['pdam_id']);

        $builder = $this->modelsManager->createBuilder()
            ->columns('*')
            ->from(InstalasiModel::class)
            ->where("1=1");

        // Then use in your SQL WHERE clause
        if (!empty($search) && isset($search)) {
            $builder->andWhere("kode_installasi LIKE '%$search%' OR nama_installasi LIKE '%$search%'");
        }

        if (!empty($search_kode) && isset($search_kode)) {
            $builder->andWhere("kode_installasi LIKE '%$search_kode%'");
        }

        if (!empty($search_nama) && isset($search_nama)) {
            $builder->andWhere("nama_installasi LIKE '%$search_nama%'");
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

            $id_user = escape_xss($this->session->user['id']);
            $pdam_id = escape_xss($this->session->user['pdam_id']);

            $namaInstalasi = escape_xss($this->request->getPost('nama_instalasi'));
            $kodeInstalasi = escape_xss($this->request->getPost('kode_instalasi'));

            $insertData = [
                'kode_installasi' => $kodeInstalasi,
                'nama_installasi' => $namaInstalasi,
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => $id_user
            ];

            try {

                $actTransactData = new InstalasiModel();
                $actTransactData->assign($insertData);
                $resultTransactData = $actTransactData->save();

                if (!$resultTransactData) {
                    $errorMessage = implode(', ', array_map(function ($m) {
                        return $m->getMessage();
                    }, $actTransactData->getMessages()));
                    throw new Exception($errorMessage);
                }

                $this->db->commit();

                return $this->response->setJsonContent([
                    'error' => 0,
                    'message' => 'Simpan Data Berhasil',
                    'lastId' => $actTransactData->id
                ]);
            } catch (Exception $e) {
                // Rollback transaction if any operation fails
                if ($this->db->isUnderTransaction()) {
                    $this->db->rollback();
                }

                return $this->response->setJsonContent([
                    'error' => 1,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
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
            $namaInstalasi = escape_xss($this->request->getPost('nama_instalasi'));
            $kodeInstalasi = escape_xss($this->request->getPost('kode_instalasi'));

            // $existingUser = InstalasiModel::findFirst([
            //     "conditions" => "kode_installasi = :kode_installasi: AND id != :id:",
            //     "bind" => [
            //         "kode_installasi" => $kodeInstalasi,
            //         "id" => $id_edit
            //     ]
            // ]);

            // if ($existingUser) {
            //     $this->db->rollback(); // Rollback transaction
            //     Response::setStatusCode(400);
            //     return Response::setJsonContent([
            //         'error' => 1,
            //         'message' => "Nama '$nama' sudah digunakan oleh Auditor Eksternal lain. Silahkan gunakan Auditor Eksternal yang berbeda."
            //     ]);
            // }

            $updateData = [
                'kode_installasi' => $kodeInstalasi,
                'nama_installasi' => $namaInstalasi,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $id_user

            ];

            $update = InstalasiModel::findFirst([
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

            $findForDelete = InstalasiModel::findFirst([
                'conditions' => "id = :id:",
                'bind' => [
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
