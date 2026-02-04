<?php

declare(strict_types=1);

namespace App\Modules\Hotel\Master\Tamu;

use Core\Facades\Response;
use Core\Facades\Request;
use Core\Paginator\DataTables\DataTable;
use App\Libraries\Log;
use App\Modules\Defaults\BaseController;
use Core\Facades\Security;
use Exception;

/**
 * @routeGroup('/hotel/master/tamu')
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
            ->columns('*')
            ->from(Model::class)
            ->where("1=1")
            ->orderBy("id DESC");

        // Filter berdasarkan nama
        $searchNama = $this->request->getPost('search_nama');
        if (!empty($searchNama)) {
            $builder->andWhere("nama_lengkap LIKE :nama:", ['nama' => '%' . $searchNama . '%']);
        }

        // Filter berdasarkan jenis identitas
        $searchJenisIdentitas = $this->request->getPost('search_jenis_identitas');
        if (!empty($searchJenisIdentitas)) {
            $builder->andWhere("jenis_identitas = :jenis_identitas:", ['jenis_identitas' => $searchJenisIdentitas]);
        }

        // Filter berdasarkan jenis kelamin
        $searchJenisKelamin = $this->request->getPost('search_jenis_kelamin');
        if (!empty($searchJenisKelamin)) {
            $builder->andWhere("jenis_kelamin = :jenis_kelamin:", ['jenis_kelamin' => $searchJenisKelamin]);
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
        $nama_lengkap = escape_xss($this->request->getPost('nama_lengkap'));
        $no_identitas = escape_xss($this->request->getPost('no_identitas'));
        $jenis_identitas = escape_xss($this->request->getPost('jenis_identitas'));
        $jenis_kelamin = escape_xss($this->request->getPost('jenis_kelamin'));
        $tanggal_lahir = escape_xss($this->request->getPost('tanggal_lahir'));
        $kebangsaan = escape_xss($this->request->getPost('kebangsaan'));
        $pekerjaan = escape_xss($this->request->getPost('pekerjaan'));
        $email = escape_xss($this->request->getPost('email'));
        $no_telepon = escape_xss($this->request->getPost('no_telepon'));
        $alamat = escape_xss($this->request->getPost('alamat'));
        $kota = escape_xss($this->request->getPost('kota'));
        $catatan = escape_xss($this->request->getPost('catatan'));
        $dateUpdate = date('Y-m-d H:i:s');

        // Validasi: cek apakah no identitas sudah ada (kecuali untuk data yang sedang diedit)
        $cekIdentitas = Model::findFirst([
            'conditions' => 'no_identitas = :no_identitas: AND jenis_identitas = :jenis_identitas: AND id != :id:',
            'bind' => [
                'no_identitas' => $no_identitas,
                'jenis_identitas' => $jenis_identitas,
                'id' => $id_edit
            ]
        ]);
        if ($cekIdentitas) {
            Response::setStatusCode(200, "Error");
            return Response::setJsonContent([
                'error' => 1,
                'message' => "Nomor identitas sudah terdaftar, silakan gunakan nomor lain."
            ]);
        }

        $dataTransact = [
            'nama_lengkap' => $nama_lengkap,
            'no_identitas' => $no_identitas,
            'jenis_identitas' => $jenis_identitas,
            'jenis_kelamin' => $jenis_kelamin,
            'tanggal_lahir' => $tanggal_lahir ?: null,
            'kebangsaan' => $kebangsaan,
            'pekerjaan' => $pekerjaan,
            'email' => $email,
            'no_telepon' => $no_telepon,
            'alamat' => $alamat,
            'kota' => $kota,
            'catatan' => $catatan,
            'updated_at' => $dateUpdate
        ];

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
        $nama_lengkap = escape_xss($this->request->getPost('nama_lengkap'));
        $no_identitas = escape_xss($this->request->getPost('no_identitas'));
        $jenis_identitas = escape_xss($this->request->getPost('jenis_identitas'));
        $jenis_kelamin = escape_xss($this->request->getPost('jenis_kelamin'));
        $tanggal_lahir = escape_xss($this->request->getPost('tanggal_lahir'));
        $kebangsaan = escape_xss($this->request->getPost('kebangsaan'));
        $pekerjaan = escape_xss($this->request->getPost('pekerjaan'));
        $email = escape_xss($this->request->getPost('email'));
        $no_telepon = escape_xss($this->request->getPost('no_telepon'));
        $alamat = escape_xss($this->request->getPost('alamat'));
        $kota = escape_xss($this->request->getPost('kota'));
        $catatan = escape_xss($this->request->getPost('catatan'));
        $dateCreate = date('Y-m-d H:i:s');

        // Validasi: cek apakah no identitas sudah ada
        $cekIdentitas = Model::findFirst([
            'conditions' => 'no_identitas = :no_identitas: AND jenis_identitas = :jenis_identitas:',
            'bind' => [
                'no_identitas' => $no_identitas,
                'jenis_identitas' => $jenis_identitas
            ]
        ]);
        if ($cekIdentitas) {
            Response::setStatusCode(200, "Error");
            return Response::setJsonContent([
                'error' => 1,
                'message' => "Nomor identitas sudah terdaftar, silakan gunakan nomor lain."
            ]);
        }

        $dataTransact = [
            'nama_lengkap' => $nama_lengkap,
            'no_identitas' => $no_identitas,
            'jenis_identitas' => $jenis_identitas,
            'jenis_kelamin' => $jenis_kelamin,
            'tanggal_lahir' => $tanggal_lahir ?: null,
            'kebangsaan' => $kebangsaan,
            'pekerjaan' => $pekerjaan,
            'email' => $email,
            'no_telepon' => $no_telepon,
            'alamat' => $alamat,
            'kota' => $kota,
            'catatan' => $catatan,
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