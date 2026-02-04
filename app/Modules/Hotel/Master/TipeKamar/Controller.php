<?php

declare(strict_types=1);

namespace App\Modules\Hotel\Master\TipeKamar;

use Core\Facades\Response;
use Core\Facades\Request;
use Core\Paginator\DataTables\DataTable;
use App\Libraries\Log;
use App\Modules\Defaults\BaseController;
use Core\Facades\Security;
use Exception;

/**
 * @routeGroup('/hotel/master/tipe-kamar')
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
            ->columns('tk.id, tk.nama, tk.deskripsi, tk.harga_per_malam, tk.kapasitas, tk.fasilitas, tk.status, tk.created_at, tk.updated_at, 
                (SELECT COUNT(*) FROM App\\Modules\\Hotel\\Master\\Kamar\\Model k WHERE k.tipe_ruangan_id = tk.id AND k.status != "ditempati") as jumlah_ruangan_tersedia,
                (SELECT COUNT(*) FROM App\\Modules\\Hotel\\Master\\Kamar\\Model k WHERE k.tipe_ruangan_id = tk.id AND k.status = "ditempati") as jumlah_ruangan_terpakai')
            ->from(['tk' => Model::class])
            ->where("1=1")
            ->orderBy("tk.id ASC");

        // Filter berdasarkan nama tipe kamar
        $searchNama = $this->request->getPost('search_nama');
        if (!empty($searchNama)) {
            $builder->andWhere("id = :search_nama:", ['search_nama' => $searchNama]);
        }

        // Filter berdasarkan harga minimum
        $searchHargaMin = $this->request->getPost('search_harga_min');
        if (!empty($searchHargaMin)) {
            $builder->andWhere("harga_per_malam >= :harga_min:", ['harga_min' => $searchHargaMin]);
        }

        // Filter berdasarkan harga maksimum
        $searchHargaMax = $this->request->getPost('search_harga_max');
        if (!empty($searchHargaMax)) {
            $builder->andWhere("harga_per_malam <= :harga_max:", ['harga_max' => $searchHargaMax]);
        }

        // Filter berdasarkan kapasitas
        $searchKapasitas = $this->request->getPost('search_kapasitas');
        if (!empty($searchKapasitas)) {
            if ($searchKapasitas == '5') {
                $builder->andWhere("kapasitas >= 5");
            } else {
                $builder->andWhere("kapasitas = :kapasitas:", ['kapasitas' => $searchKapasitas]);
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

        // Ambil input & session
        $id_edit = escape_xss($this->request->getPost('id_edit'));
        $nama = escape_xss($this->request->getPost('nama'));
        $deskripsi = escape_xss($this->request->getPost('deskripsi'));
        $harga_per_malam = (float)$this->request->getPost('harga_per_malam');
        $kapasitas = (int)$this->request->getPost('kapasitas');
        $fasilitas = escape_xss($this->request->getPost('fasilitas'));
        $status = escape_xss($this->request->getPost('status'));
        $dateUpdate = date('Y-m-d H:i:s');

        $dataTransact = [
            'nama' => $nama,
            'deskripsi' => $deskripsi,
            'harga_per_malam' => $harga_per_malam,
            'kapasitas' => $kapasitas,
            'fasilitas' => $fasilitas,
            'status' => $status,
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

        // Ambil input & session
        $nama = escape_xss($this->request->getPost('nama'));
        $deskripsi = escape_xss($this->request->getPost('deskripsi'));
        $harga_per_malam = (float)$this->request->getPost('harga_per_malam');
        $kapasitas = (int)$this->request->getPost('kapasitas');
        $fasilitas = escape_xss($this->request->getPost('fasilitas'));
        $status = escape_xss($this->request->getPost('status'));
        $dateCreate = date('Y-m-d H:i:s');

        // Validasi: cek apakah nama sudah ada
        $cekNama = Model::findFirst([
            'conditions' => 'nama = :nama:',
            'bind' => [
                'nama' => $nama
            ]
        ]);
        if ($cekNama) {
            Response::setStatusCode(200, "Error");
            return Response::setJsonContent([
                'error' => 1,
                'message' => "Nama tipe kamar sudah terdaftar, silakan gunakan nama lain."
            ]);
        }

        $dataTransact = [
            'nama' => $nama,
            'deskripsi' => $deskripsi,
            'harga_per_malam' => $harga_per_malam,
            'kapasitas' => $kapasitas,
            'fasilitas' => $fasilitas,
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

    /**
     * @routePost('/getTipeKamarOptions')
     * @routeGet('/getTipeKamarOptions')
     */
    public function getTipeKamarOptionsAction()
    {
        $search = $this->request->get('q', 'string', '');
        
        $builder = $this->modelsManager->createBuilder()
            ->columns('id, nama')
            ->from(Model::class)
            ->where("status = 'active'");
            
        if (!empty($search)) {
            $builder->andWhere("nama LIKE :search:", ['search' => '%' . $search . '%']);
        }
        
        $builder->orderBy('nama ASC');
        $results = $builder->getQuery()->execute();
        
        $data = [];
        foreach ($results as $result) {
            $data[] = [
                'id' => $result->id,
                'nama' => $result->nama
            ];
        }
        
        return $this->response->setJsonContent($data);
    }
}