<?php

declare(strict_types=1);

namespace App\Modules\Hotel\Master\HargaKamar;

use Core\Facades\Response;
use Core\Paginator\DataTables\DataTable;
use App\Modules\Defaults\BaseController;
use Exception;

/**
 * @routeGroup('/hotel/master/harga-kamar')
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
            ->columns('id, nama, harga_per_malam')
            ->from(Model::class)
            ->where("1=1")
            ->orderBy("id ASC");

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

        $dataTables = new DataTable();
        $dataTables->fromBuilder($builder)->sendResponse();
    }

    /**
     * @routePost('/updateData')
     */
    public function updateDataAction()
    {
        if (!$this->request->isPost()) {
            return Response::setJsonContent([
                'error' => 1,
                'message' => "Method not allowed"
            ]);
        }

        $id_edit = escape_xss($this->request->getPost('id_edit'));
        $harga_per_malam = (float)$this->request->getPost('harga_per_malam');

        try {
            $this->db->begin();

            $actTransactData = Model::findFirst([
                'conditions' => "id = :id:",
                'bind' => ['id' => $id_edit]
            ]);

            if (!$actTransactData) {
                throw new Exception("Data tidak ditemukan");
            }

            $actTransactData->harga_per_malam = $harga_per_malam;

            if (!$actTransactData->update()) {
                throw new Exception("Gagal update data");
            }

            $this->db->commit();

            return $this->response->setJsonContent([
                'error' => 0,
                'message' => 'Update Harga Berhasil'
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