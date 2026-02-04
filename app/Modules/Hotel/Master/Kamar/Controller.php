<?php

declare(strict_types=1);

namespace App\Modules\Hotel\Master\Kamar;

use Core\Facades\Response;
use Core\Facades\Request;
use Core\Paginator\DataTables\DataTable;
use App\Libraries\Log;
use App\Modules\Defaults\BaseController;
use Core\Facades\Security;
use Exception;

/**
 * @routeGroup('/hotel/master/kamar')
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
     * @routeGet('/getTipeKamar')
     */
    public function getTipeKamarAction()
    {
        $tipeKamar = \App\Modules\Hotel\Master\TipeKamar\Model::find([
            'order' => 'nama ASC'
        ]);
        
        return $this->response->setJsonContent([
            'error' => 0,
            'data' => $tipeKamar->toArray()
        ]);
    }

    /**
     * @routePost('/datatable')
     * @routeGet('/datatable')
     */
    public function datatableAction()
    {
        $builder = $this->modelsManager->createBuilder()
            ->columns('k.id, k.nomor_kamar, k.lantai, k.status, k.keterangan, k.tipe_ruangan_id, t.nama as tipe_kamar_nama')
            ->from(['k' => Model::class])
            ->leftJoin('\App\Modules\Hotel\Master\TipeKamar\Model', 'k.tipe_ruangan_id = t.id', 't')
            ->where("1=1")
            ->orderBy("k.nomor_kamar ASC");

        // Filter berdasarkan nomor kamar
        $searchNomorKamar = $this->request->getPost('search_nomor_kamar');
        if (!empty($searchNomorKamar)) {
            $builder->andWhere("k.nomor_kamar LIKE :nomor_kamar:", ['nomor_kamar' => '%' . $searchNomorKamar . '%']);
        }

        // Filter berdasarkan status
        $searchStatus = $this->request->getPost('search_status');
        if (!empty($searchStatus)) {
            $builder->andWhere("k.status = :status:", ['status' => $searchStatus]);
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
        $nomor_kamar = escape_xss($this->request->getPost('nomor_kamar'));
        $tipe_ruangan_id = (int)$this->request->getPost('tipe_ruangan_id');
        $status = escape_xss($this->request->getPost('status'));
        $lantai = (int)$this->request->getPost('lantai');
        $keterangan = escape_xss($this->request->getPost('keterangan'));

        // Validasi: cek apakah nomor kamar sudah ada (kecuali untuk data yang sedang diedit)
        $cekNomor = Model::findFirst([
            'conditions' => 'nomor_kamar = :nomor: AND id != :id:',
            'bind' => [
                'nomor' => $nomor_kamar,
                'id' => $id_edit
            ]
        ]);
        if ($cekNomor) {
            Response::setStatusCode(200, "Error");
            return Response::setJsonContent([
                'error' => 1,
                'message' => "Nomor kamar sudah terdaftar, silakan gunakan nomor lain."
            ]);
        }

        $dataTransact = [
            'nomor_kamar' => $nomor_kamar,
            'tipe_ruangan_id' => $tipe_ruangan_id,
            'status' => $status,
            'lantai' => $lantai,
            'keterangan' => $keterangan
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
        $nomor_kamar = escape_xss($this->request->getPost('nomor_kamar'));
        $tipe_ruangan_id = (int)$this->request->getPost('tipe_ruangan_id');
        $status = escape_xss($this->request->getPost('status'));
        $lantai = (int)$this->request->getPost('lantai');
        $keterangan = escape_xss($this->request->getPost('keterangan'));
        $dateCreate = date('Y-m-d H:i:s');

        // Validasi: cek apakah nomor kamar sudah ada
        $cekNomor = Model::findFirst([
            'conditions' => 'nomor_kamar = :nomor:',
            'bind' => [
                'nomor' => $nomor_kamar
            ]
        ]);
        if ($cekNomor) {
            Response::setStatusCode(200, "Error");
            return Response::setJsonContent([
                'error' => 1,
                'message' => "Nomor kamar sudah terdaftar, silakan gunakan nomor lain."
            ]);
        }

        $dataTransact = [
            'nomor_kamar' => $nomor_kamar,
            'tipe_ruangan_id' => $tipe_ruangan_id,
            'status' => $status,
            'lantai' => $lantai,
            'keterangan' => $keterangan,
            'created_at' => $dateCreate
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