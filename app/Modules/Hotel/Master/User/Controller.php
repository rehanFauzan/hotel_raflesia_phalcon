<?php

declare(strict_types=1);

namespace App\Modules\Hotel\Master\User;

use Core\Facades\Response;
use Core\Facades\Request;
use Core\Paginator\DataTables\DataTable;
use App\Libraries\Log;
use App\Modules\Defaults\BaseController;
use Core\Facades\Security;
use Exception;

/**
 * @routeGroup('/hotel/master/user')
 * @middleware('RequireUser')
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
        $builder = $this->modelsManager->createBuilder()
            ->columns('id, username, nama, state')
            ->from(Model::class)
            ->where("1=1")
            ->orderBy("id ASC");

        // Filter berdasarkan username
        $searchUsername = $this->request->getPost('search_username');
        if (!empty($searchUsername)) {
            $builder->andWhere("username LIKE :username:", ['username' => '%' . $searchUsername . '%']);
        }

        // Filter berdasarkan nama
        $searchNama = $this->request->getPost('search_nama');
        if (!empty($searchNama)) {
            $builder->andWhere("nama LIKE :nama:", ['nama' => '%' . $searchNama . '%']);
        }

        // Filter berdasarkan status
        $searchStatus = $this->request->getPost('search_status');
        if (!empty($searchStatus)) {
            if ($searchStatus == 'active') {
                $builder->andWhere("state = 1");
            } elseif ($searchStatus == 'inactive') {
                $builder->andWhere("state = 0");
            }
        }

        $dataTables = new DataTable();
        $dataTables->fromBuilder($builder)->sendResponse();
    }

    /**
     * @routePost('/deleteData')
     */
    public function deleteDataAction()
    {
        if ($this->request->isPost()) {
            $id_delete = $this->request->getPost('id_delete');

            $findForDelete = Model::findFirst([
                'conditions' => "id = :id:",
                'bind' => [
                    'id' => $id_delete
                ]
            ]);

            if (!$findForDelete) {
                Response::setStatusCode(404, "NOT FOUND");
                return Response::setJsonContent([
                    'error' => 1,
                    'message' => "Data tidak ditemukan",
                    'lastId' => null
                ]);
            }

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
        } else {
            Response::setStatusCode(601, "FAILED");
            return Response::setJsonContent([
                'error' => 1,
                'message' => "Method not allowed"
            ]);
        }
    }

    /**
     * @routePost('/updateData')
     */
    public function updateDataAction()
    {
        if (!$this->request->isPost()) {
            Response::setStatusCode(200, "OK");
            return Response::setJsonContent([
                'error' => 1,
                'message' => "Method not allowed"
            ]);
        }

        // Ambil input
        $id_edit = escape_xss($this->request->getPost('id_edit'));
        $username = escape_xss($this->request->getPost('username'));
        $nama_lengkap = escape_xss($this->request->getPost('nama_lengkap'));
        $role = escape_xss($this->request->getPost('role'));
        $email = escape_xss($this->request->getPost('email'));
        $no_telepon = escape_xss($this->request->getPost('no_telepon'));
        $status = escape_xss($this->request->getPost('status'));
        $password = escape_xss($this->request->getPost('password'));
        $dateUpdate = date('Y-m-d H:i:s');

        // Validasi: cek apakah username sudah ada (kecuali untuk data yang sedang diedit)
        $cekUsername = Model::findFirst([
            'conditions' => 'username = :username: AND id != :id:',
            'bind' => [
                'username' => $username,
                'id' => $id_edit
            ]
        ]);
        if ($cekUsername) {
            Response::setStatusCode(200, "Error");
            return Response::setJsonContent([
                'error' => 1,
                'message' => "Username sudah terdaftar, silakan gunakan username lain."
            ]);
        }

        $dataTransact = [
            'username' => $username,
            'nama_lengkap' => $nama_lengkap,
            'role' => $role,
            'email' => $email,
            'no_telepon' => $no_telepon,
            'status' => $status,
            'updated_at' => $dateUpdate
        ];

        // Jika password diisi, update password
        if (!empty($password)) {
            $dataTransact['password'] = md5($password);
        }

        // Transaksi dimulai
        try {
            $this->db->begin();

            $actTransactData = Model::findFirst([
                'conditions' => "id = :id:",
                'bind' => [
                    'id' => $id_edit
                ]
            ]);

            if (!$actTransactData) {
                throw new Exception("Data tidak ditemukan");
            }

            $actTransactData->assign($dataTransact);

            if (!$actTransactData->update()) {
                $errorMessage = implode(', ', array_map(function ($m) {
                    return $m->getMessage();
                }, $actTransactData->getMessages()));
                throw new Exception($errorMessage);
            }

            $lastInsertedId = $actTransactData->id;
            $this->db->commit();

            return $this->response->setJsonContent([
                'error' => 0,
                'message' => 'Update Data Berhasil',
                'lastId' => $lastInsertedId
            ]);
        } catch (Exception $e) {
            if ($this->db->isUnderTransaction()) {
                $this->db->rollback();
            }

            return $this->response->setJsonContent([
                'error' => 1,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * @routePost('/saveData')
     */
    public function saveDataAction()
    {
        if (!$this->request->isPost()) {
            Response::setStatusCode(200, "OK");
            return Response::setJsonContent([
                'error' => 1,
                'message' => "Method not allowed"
            ]);
        }

        // Ambil input
        $username = escape_xss($this->request->getPost('username'));
        $nama_lengkap = escape_xss($this->request->getPost('nama_lengkap'));
        $role = escape_xss($this->request->getPost('role'));
        $email = escape_xss($this->request->getPost('email'));
        $no_telepon = escape_xss($this->request->getPost('no_telepon'));
        $status = escape_xss($this->request->getPost('status'));
        $password = escape_xss($this->request->getPost('password'));
        $dateCreate = date('Y-m-d H:i:s');

        // Validasi: cek apakah username sudah ada
        $cekUsername = Model::findFirst([
            'conditions' => 'username = :username:',
            'bind' => [
                'username' => $username
            ]
        ]);
        if ($cekUsername) {
            Response::setStatusCode(200, "Error");
            return Response::setJsonContent([
                'error' => 1,
                'message' => "Username sudah terdaftar, silakan gunakan username lain."
            ]);
        }

        $dataTransact = [
            'username' => $username,
            'password' => md5($password),
            'nama_lengkap' => $nama_lengkap,
            'role' => $role,
            'email' => $email,
            'no_telepon' => $no_telepon,
            'status' => $status,
            'created_at' => $dateCreate,
            'updated_at' => $dateCreate
        ];

        // Transaksi dimulai
        try {
            $this->db->begin();

            $actTransactData = new Model();
            $actTransactData->assign($dataTransact);

            if (!$actTransactData->save()) {
                $errorMessage = implode(', ', array_map(function ($m) {
                    return $m->getMessage();
                }, $actTransactData->getMessages()));
                throw new Exception($errorMessage);
            }

            $lastInsertedId = $actTransactData->id;
            $this->db->commit();

            return $this->response->setJsonContent([
                'error' => 0,
                'message' => 'Simpan Data Berhasil',
                'lastId' => $lastInsertedId
            ]);
        } catch (Exception $e) {
            if ($this->db->isUnderTransaction()) {
                $this->db->rollback();
            }

            return $this->response->setJsonContent([
                'error' => 1,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
}