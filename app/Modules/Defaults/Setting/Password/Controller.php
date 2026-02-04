<?php

declare(strict_types=1);

namespace App\Modules\Defaults\Setting\Password;

use Core\Facades\Response;
use Core\Facades\Request;
use Core\Paginator\DataTables\DataTable;
use App\Libraries\Log;
use App\Modules\Defaults\BaseController;
use Core\Facades\Security;
use App\Modules\Defaults\Setting\User\UserModel;

/**
 * @routeGroup('/setting/ganti-password')
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
     * @routePost('/saveData')
     */
    public function saveDataAction()
    {
        if (!$this->request->isPost()) {
            Response::setStatusCode(405, "Method Not Allowed");
            return Response::setJsonContent([
                'error' => 1,
                'message' => "Method not allowed"
            ]);
        }

        $id_user       = $this->session->user['id']; 
        $pw_lama       = escape_xss($this->request->getPost('pw_lama'));
        $pw_baru       = escape_xss($this->request->getPost('pw_baru'));
        $verifikasi_pw = escape_xss($this->request->getPost('verifikasi_pw'));

        try {
            $this->db->begin();

            if (!$id_user) {
                $this->db->rollback();
                return $this->response->setJsonContent([
                    'error' => 1,
                    'message' => "User tidak ditemukan"
                ]);
            }

            $user = UserModel::findFirst([
                'conditions' => "id = :id:",
                'bind' => [
                    'id' => $id_user
                ]
            ]);

            if (!$user) {
                $this->db->rollback();
                return $this->response->setJsonContent([
                    'error' => 1,
                    'message' => "Data user tidak ditemukan"
                ]);
            }

            if (!Security::checkHash($pw_lama, $user->password)) {
                $this->db->rollback();
                return $this->response->setJsonContent([
                    'error' => 1,
                    'message' => "Password lama salah!"
                ]);
            }

            if ($pw_baru !== $verifikasi_pw) {
                $this->db->rollback();
                return $this->response->setJsonContent([
                    'error' => 1,
                    'message' => "Password baru dan verifikasi tidak sama!"
                ]);
            }

            // Update password
            $user->password = Security::hash($pw_baru);
            $user->pass_enc = encrypt($pw_baru, $this->config->appKey);

            if (!$user->save()) {
                $errorMessage = implode(', ', array_map(function ($m) {
                    return $m->getMessage();
                }, $user->getMessages()));
                throw new \Exception($errorMessage);
            }

            $this->db->commit();

            return $this->response->setJsonContent([
                'error' => 0,
                'message' => "Password berhasil diganti."
            ]);
        } catch (\Exception $e) {
            if ($this->db->isUnderTransaction()) {
                $this->db->rollback();
            }
            Response::setStatusCode(400, "FAILED");
            return $this->response->setJsonContent([
                'error' => 1,
                'message' => "Terjadi kesalahan: " . $e->getMessage()
            ]);
        }
    }
}
