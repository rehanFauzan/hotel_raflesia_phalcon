<?php

declare(strict_types=1);

namespace App\Modules\Defaults\ReferensiData\Mapping;

use Core\Facades\Response;
use Core\Facades\Request;
use Core\Paginator\DataTables\DataTable;
use App\Libraries\Log;
use App\Modules\Defaults\BaseController;
use Core\Facades\Security;
use Exception;
use App\Modules\Defaults\ReferensiData\Golongan\Model as GolonganModel;
use App\Modules\Defaults\ReferensiData\Kelompok\Model as KelompokModel;
use App\Modules\Defaults\ReferensiData\Perkiraan\Model as PerkiraanModel;
use App\Modules\Defaults\RefSelect2\ReffJurnalModel;

/**
 * @routeGroup('/refdata/mapping')
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
        $search_acc_code = escape_xss($this->request->getPost('search_acc_code'));
        $search_jt_id = escape_xss($this->request->getPost('search_jt_id'));
        $search_is_debet = escape_xss($this->request->getPost('search_is_debet'));

        $builder = $this->modelsManager->createBuilder()
            ->columns([
                'a.id', // kolom dari tabel utama
                'a.acc_code',
                'a.jt_id',
                'jt.jt_id       AS reff_jurnal_id',
                'jt.nama_jurnal AS reff_jurnal_nama ',
                'a.user_input',
                'a.is_debet',
                'p.acc_code AS perk_code',
                'p.acc_name AS perk_name',
                'k.acc_code AS kel_code',
                'k.acc_name AS kel_name',
                'g.acc_code AS gol_code',
                'g.acc_name AS gol_name' // kolom dari tabel yang di-join, bisa di-alias
            ])
            ->from(['a' => Model::class])
            ->leftJoin(
                ReffJurnalModel::class,
                'a.jt_id = jt.jt_id',
                'jt'
            )
            ->leftJoin(
                PerkiraanModel::class,
                'a.acc_code = p.acc_code',
                'p'
            )->leftJoin(
                KelompokModel::class,
                'p.acc_parent_kelompok = k.acc_code',
                'k'
            )
            ->leftJoin(
                GolonganModel::class,
                'k.acc_parent_gol = g.acc_code',
                'g'
            )
            ->where("1=1")
            // Ganti urutan ke string, karena CAST tidak didukung di QueryBuilder Phalcon
            ->orderBy("k.acc_code ASC");


        if (!empty($search_acc_code) && isset($search_acc_code)) {
            $builder->andWhere("a.acc_code LIKE '%$search_acc_code%'");
        }

        if (!empty($search_jt_id) && isset($search_jt_id)) {
            $builder->andWhere("a.jt_id = '$search_jt_id'");
        }

        if (!empty($search_is_debet) && isset($search_is_debet)) {
            $builder->andWhere("a.is_debet = '$search_is_debet'");
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
            $resultDelete = $findForDelete->delete();
            // Log::write("Melakukan penghapus master data satuan kerja", ['id' => $id_delete], $resultDelete, "Setting/Role/Controller", "INSERT");

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
                'message' => "Methode not allowed"
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
        $acc_code = escape_xss($this->request->getPost('acc_code'));
        $jt_id = escape_xss($this->request->getPost('jt_id'));
        $is_debet = escape_xss($this->request->getPost('is_debet'));

        $dateUpdate = date('Y-m-d H:i:s');
        $username =  $this->session->user['username'];

        $dataTransact = [
            'acc_code' => $acc_code,
            'jt_id' => $jt_id,
            'is_debet' => $is_debet,
            'updated_dt' => $dateUpdate,
            'user_input' => $username
        ];

        // Transaksi dimulai
        try {
            $this->db->begin();

            $actTransactData = Model::findFirst([
                'conditions' => "id = :id_edit:",
                'bind' => [
                    'id_edit' => $id_edit
                ]
            ]);

            // $metaData = $actTransactData->getModelsMetaData();
            // $attributes = $metaData->getAttributes($actTransactData);
            // $dataTypes = $metaData->getDataTypes($actTransactData);
            // $notNull = $metaData->getNotNullAttributes($actTransactData);

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
        $acc_code = escape_xss($this->request->getPost('acc_code'));
        $jt_id = escape_xss($this->request->getPost('jt_id'));
        $is_debet = escape_xss($this->request->getPost('is_debet'));

        $dateCreate = date('Y-m-d H:i:s');
        $username =  $this->session->user['username'];

        // Validasi: cek apakah kode_satker sudah ada
        $cekExisting = Model::findFirst([
            'conditions' => 'acc_code = :acc_code: AND jt_id = :jt_id: AND is_debet = :is_debet:',
            'bind' => [
                'acc_code' => $acc_code,
                'jt_id' => $jt_id,
                'is_debet' => $is_debet
            ]
        ]);
        if ($cekExisting) {
            Response::setStatusCode(200, "Error");
            return Response::setJsonContent([
                'error' => 1,
                'message' => "Data yang akan diinput sudah pernah diinputkan sebelumnya"
            ]);
        }

        $dataTransact = [
            'acc_code' => $acc_code,
            'jt_id' => $jt_id,
            'is_debet' => $is_debet,
            'create_dt' => $dateCreate,
            'user_input' => $username
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
                'lastId' => $lastInsertedId,
                'error_message' => $actTransactData->getMessages()
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
