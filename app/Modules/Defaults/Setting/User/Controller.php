<?php

declare(strict_types=1);

namespace App\Modules\Defaults\Setting\User;

use Core\Facades\Response;
use Core\Facades\Request;
use Core\Paginator\DataTables\DataTable;
use App\Libraries\Log;
use App\Modules\Defaults\BaseController;
use Core\Facades\Security;

/**
 * @routeGroup('/setting/user')
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
        $search_satker_id = escape_xss($this->request->getPost('search_satker_id'));
        $search_nama      = escape_xss($this->request->getPost('search_nama'));
        $search_username  = escape_xss($this->request->getPost('search_username'));
        $search_role_id   = escape_xss($this->request->getPost('search_role_id'));
        $search_status    = escape_xss($this->request->getPost('search_status'));

        $sql = "
            SELECT
                su.id, 
                su.satker_id, 
                su.username, 
                su.nip, 
                su.nama, 
                su.password, 
                su.pass_enc, 
                su.id_role, 
                sr.role, 
                sr.status, 
                su.state, 
                ms.kode_satker, 
                ms.nama_satker
            FROM
                system_user AS su
            INNER JOIN
                system_role AS sr
                ON 
                    su.id_role = sr.id
                LEFT JOIN
                master_satker AS ms
                ON 
                    su.satker_id = ms.kode_satker 
            WHERE 1 = 1
        ";

        // SQL Server (PDO SQLSRV) does not support named parameters with colons in raw SQL.
        // We'll build the WHERE clause with properly escaped values.

        if (!empty($search_satker_id)) {
            $satker_id_escaped = addslashes($search_satker_id);
            $sql .= " AND su.satker_id = '{$satker_id_escaped}'";
        }
        if (!empty($search_nama)) {
            $nama_escaped = addslashes($search_nama);
            $sql .= " AND su.nama LIKE '%{$nama_escaped}%'";
        }
        if (!empty($search_username)) {
            $username_escaped = addslashes($search_username);
            $sql .= " AND su.username LIKE '%{$username_escaped}%'";
        }
        if (!empty($search_role_id)) {
            $role_id_escaped = addslashes($search_role_id);
            $sql .= " AND su.id_role = '{$role_id_escaped}'";
        }
        if ($search_status !== null && $search_status !== '') {
            $state_escaped = addslashes($search_status);
            $sql .= " AND su.state = '{$state_escaped}'";
        }

        $stmt = $this->db->query($sql);
        $stmt->setFetchMode(\Phalcon\Db\Enum::FETCH_ASSOC);
        $rows = $stmt->fetchAll();

        $dataTables = new DataTable();
        $dataTables->fromArray($rows)->sendResponse();
    }


    /**
     * @routePost('/datatable_old')
     * @routeGet('/datatable_old')
     */
    public function datatable_oldAction()
    {
        $search_installasi = escape_xss($this->request->getPost('search_installasi'));
        $search_jenis_user = escape_xss($this->request->getPost('search_jenis_user'));
        $search_nama = escape_xss($this->request->getPost('search_nama'));
        $search_username = escape_xss($this->request->getPost('search_username'));
        $search_role_id = escape_xss($this->request->getPost('search_role_id'));
        $search_status = escape_xss($this->request->getPost('search_status'));

        // $pdam_id = $this->session->user['pdam_id'];

        $builder = $this->modelsManager->createBuilder()
            ->columns('*')
            ->from(UserViewModel::class)
            ->where("1=1");

        if (!empty($search_installasi) && isset($search_installasi)) {
            $builder->andWhere("id_installasi = '$search_installasi'");
        }

        if (!empty($search_jenis_user) && isset($search_jenis_user)) {
            $builder->andWhere("jenis_user = '$search_jenis_user'");
        }

        if (!empty($search_nama) && isset($search_nama)) {
            $builder->andWhere("nama LIKE '%$search_nama%'");
        }

        if (!empty($search_username) && isset($search_username)) {
            $builder->andWhere("username LIKE '%$search_username%'");
        }

        if (!empty($search_role_id) && isset($search_role_id)) {
            $builder->andWhere("role_id = '$search_role_id'");
        }


        if (isset($search_status)) {
            if ($search_status == 0) {
                $builder->andWhere("state != 1");
            } else if ($search_status != 0) {
                $builder->andWhere("state != 0");
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
            // if ($this->security->checkToken(null, null, false)) {

            $id_delete = escape_xss($this->request->getPost('id_delete'));

            $findForDelete = UserModel::findFirst([
                'conditions' => "id = :id:",
                'bind' => [
                    'id' => $id_delete
                ]
            ]);
            $resultDelete = $findForDelete->delete();

            // Log::write("Melakukan penghapus master data user", Request::getPost(), $resultDelete, "Setting/User/Controller", "DELETE");

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

    /**
     * @routePost('/updateData')
     */
    public function updateDataAction()
    {
        if ($this->request->isPost()) {
            $this->db->begin(); // Begin transaction

            $id_edit = escape_xss($this->request->getPost('id_edit'));

            $satker_id = escape_xss($this->request->getPost('satker_id'));
            $nama = escape_xss($this->request->getPost('nama'));
            $username = escape_xss($this->request->getPost('username'));
            $password = escape_xss($this->request->getPost('password'));
            $role_id = escape_xss($this->request->getPost('role_id'));
            $state = escape_xss($this->request->getPost('state'));

            $existingUser = UserModel::findFirst([
                "conditions" => "username = :username: AND id != :id:",
                "bind" => [
                    "username" => $username,
                    "id" => $id_edit
                ]
            ]);

            if ($existingUser) {
                $this->db->rollback(); // Rollback transaction
                Response::setStatusCode(400);
                return Response::setJsonContent([
                    'error' => 1,
                    'message' => "Username '$username' sudah digunakan oleh pengguna lain. Silahkan gunakan username yang berbeda."
                ]);
            }

            $updateData = [
                'satker_id' => $satker_id,
                'username' => $username,
                'nip' => null,
                'nama' => $nama,
                'id_role' => $role_id,
                'state' => $state
            ];

            if (!empty($password)) {
                $updateData['password'] = Security::hash($password);
                $updateData['pass_enc'] = encrypt($password, $this->config->appKey);
            }

            $update = UserModel::findFirst([
                'conditions' => "id = :id:",
                'bind' => [
                    'id' => $id_edit
                ]
            ]);
            $update->assign($updateData);
            $result_update = $update->save();

            // Log::write("Melakukan perubahan master data user", $updateData, $result_update, "Setting/User/Controller", "UPDATE");

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

            $satker_id = escape_xss($this->request->getPost('satker_id'));
            $nama = escape_xss($this->request->getPost('nama'));
            $username = escape_xss($this->request->getPost('username'));
            $password = escape_xss($this->request->getPost('password'));
            $role_id = escape_xss($this->request->getPost('role_id'));
            $state = escape_xss($this->request->getPost('state'));

            $insertData = [
                'satker_id' => $satker_id,
                'username' => $username,
                'nip' => null,
                'nama' => $nama,
                'password' => Security::hash($password),
                'pass_enc' => encrypt($password, $this->config->appKey),
                'id_role' => $role_id,
                'state' => $state
            ];

            $this->modelsMetadata->reset();

            $insert = new UserModel();
            $insert->assign($insertData);
            $result_insert = $insert->save();

            // Log::write("Melakukan penambahan master data user", $insertData, $result_insert, "Setting/User/Controller", "INSERT");

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
                    'meesage_data' => $insert->getMessages(),
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
     * @routeGet('/getSatker')
     */
    public function getSatkerAction()
    {
        $satker = $this->db->query("SELECT kode_satker, nama_satker FROM master_satker ORDER BY nama_satker ASC");
        $satker->setFetchMode(\Phalcon\Db\Enum::FETCH_ASSOC);
        $data = $satker->fetchAll();

        return $this->response->setJsonContent([
            'error' => 0,
            'data' => $data
        ]);
    }

    /**
     * @routeGet('/getRole')
     */
    public function getRoleAction()
    {
        $role = $this->db->query("SELECT id, role FROM system_role WHERE status = 1 ORDER BY role ASC");
        $role->setFetchMode(\Phalcon\Db\Enum::FETCH_ASSOC);
        $data = $role->fetchAll();

        return $this->response->setJsonContent([
            'error' => 0,
            'data' => $data
        ]);
    }
}
