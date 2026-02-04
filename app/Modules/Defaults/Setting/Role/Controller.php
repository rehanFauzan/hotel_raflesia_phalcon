<?php

declare(strict_types=1);

namespace App\Modules\Defaults\Setting\Role;

use Core\Facades\Response;
use Core\Facades\Request;
use Core\Paginator\DataTables\DataTable;
use App\Libraries\Log;
use App\Modules\Defaults\BaseController;
use Core\Facades\Security;

/**
 * @routeGroup('/setting/role')
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
        // $pdam_id = $this->session->user['pdam_id'];

        $builder = $this->modelsManager->createBuilder()
            ->columns('*')
            ->from(RoleViewModel::class)
            ->where("1=1");

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

            // Log::write("Melakukan penghapus master data role", ['id' => $id_delete], $resultDelete, "Setting/Role/Controller", "INSERT");

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
        if ($this->request->isPost()) {

            $this->db->begin(); // Begin transaction

            $id_edit = escape_xss($this->request->getPost('id_edit'));
            $satuan_kerja_id = escape_xss($this->request->getPost('satuan_kerja_id'));
            $role = escape_xss($this->request->getPost('role'));
            $status = escape_xss($this->request->getPost('status'));

            $arrayUpdateData = [
                'satker_id' => $satuan_kerja_id ?? 0,
                'role' => $role,
                'status' => $status
            ];

            $update = Model::findFirst([
                'conditions' => "id = :id:",
                'bind' => [
                    'id' => $id_edit
                ]
            ]);
            $update->assign($arrayUpdateData);
            $result_update = $update->save();

            // Log::write("Melakukan perubahan master data role", $arrayUpdateData, $result_update, "Setting/Role/Controller", "UPDATE");

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
     * @routePost('/saveData')
     */
    public function saveDataAction()
    {
        if ($this->request->isPost()) {

            $this->db->begin(); // Begin transaction

            $satuan_kerja_id = escape_xss($this->request->getPost('satuan_kerja_id'));
            $role = escape_xss($this->request->getPost('role'));
            $status = escape_xss($this->request->getPost('status'));
            
            $arrayInsertData = [
                'satker_id' => $satuan_kerja_id ?? 0,
                'role' => $role,
                'status' => $status
            ];

            $insertData = new Model();
            $insertData->assign($arrayInsertData);
            $resultInsert = $insertData->save();

            // Log::write("Melakukan penambahan master data role", $arrayInsertData, $resultInsert, "Setting/Role/Controller", "INSERT");

            if ($resultInsert) {
                $this->db->commit(); // Commit transaction
                Response::setStatusCode(200, "OK");
                return Response::setJsonContent([
                    'error' => 0,
                    'message' => "Simpan Data Berhasil",
                    'lastId' => $insertData->id
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
}
