<?php

declare(strict_types=1);

namespace App\Modules\Defaults\ReferensiData\Golongan;

use Core\Facades\Response;
use Core\Facades\Request;
use Core\Paginator\DataTables\DataTable;
use App\Libraries\Log;
use App\Modules\Defaults\BaseController;
use Core\Facades\Security;
use Exception;

/**
 * @routeGroup('/refdata/golongan')
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
        // Untuk mengurutkan acc_code (varchar) secara numerik di SQL Server,
        // kita bisa gunakan ORDER BY CAST(acc_code AS INT) ASC
        $builder = $this->modelsManager->createBuilder()
            ->columns('*')
            ->from(Model::class)
            ->where("1=1");
            // ->orderBy("CAST(acc_code AS INT) ASC");

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
                'conditions' => "acc_code = :id:",
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
        $acc_name = escape_xss($this->request->getPost('acc_name'));
        $dateUpdate = date('Y-m-d H:i:s');
        $username =  $this->session->user['username'];

        // Validasi: cek apakah kode_satker sudah ada
        // $cekSatker = Model::findFirst([
        //     'conditions' => 'kode_satker = :kode_satker:',
        //     'bind' => [
        //         'kode_satker' => $kode_satker
        //     ]
        // ]);
        // if ($cekSatker) {
        //     Response::setStatusCode(200, "Error");
        //     return Response::setJsonContent([
        //         'error' => 1,
        //         'message' => "Kode Satuan Kerja sudah terdaftar, silakan gunakan kode lain."
        //     ]);
        // }

        $dataTransact = [
            'acc_code' => $acc_code,
            'acc_name' => $acc_name,
            'updated_dt' => $dateUpdate,
            'user_input' => $username
        ];

        // Transaksi dimulai
        try {
            $this->db->begin();

            $actTransactData = Model::findFirst([
                'conditions' => "acc_code = :kode:",
                'bind' => [
                    'kode' => $id_edit
                ]
            ]);

            $metaData = $actTransactData->getModelsMetaData();
            $attributes = $metaData->getAttributes($actTransactData);
            $dataTypes = $metaData->getDataTypes($actTransactData);
            $notNull = $metaData->getNotNullAttributes($actTransactData);

            // echo "<pre>";
            // // print_r($metaData);
            // // print_r($attributes);
            // // print_r($dataTypes);
            // print_r($notNull);
            // echo "</pre>";
            // die();

            $actTransactData->assign($dataTransact);

            if (!$actTransactData->update()) {
                $errorMessage = implode(', ', array_map(function ($m) {
                    return $m->getMessage();
                }, $actTransactData->getMessages()));
                throw new Exception($errorMessage);
            }

            $lastInsertedId = $actTransactData->acc_code;
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
        $acc_name = escape_xss($this->request->getPost('acc_name'));
        $dateCreate = date('Y-m-d H:i:s');
        $username =  $this->session->user['username'];

        // Validasi: cek apakah kode_satker sudah ada
        $cekSatker = Model::findFirst([
            'conditions' => 'acc_code = :kode:',
            'bind' => [
                'kode' => $acc_code
            ]
        ]);
        if ($cekSatker) {
            Response::setStatusCode(200, "Error");
            return Response::setJsonContent([
                'error' => 1,
                'message' => "Kode sudah terdaftar, silakan gunakan kode lain."
            ]);
        }

        $dataTransact = [
            'acc_code' => $acc_code,
            'acc_name' => $acc_name,
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

            $lastInsertedId = $actTransactData->acc_code;
            $this->db->commit();

            return $this->response->setJsonContent([
                'error' => 0,
                'message' => 'Simpan Data Berhasil',
                'lastId' => $lastInsertedId,
                'message' => $actTransactData->getMessages()
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
