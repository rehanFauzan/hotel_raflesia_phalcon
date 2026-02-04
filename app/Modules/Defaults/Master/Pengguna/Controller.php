<?php

declare(strict_types=1);

namespace App\Modules\Defaults\Master\Pengguna;

use Phalcon\Mvc\Controller as BaseController;

use Core\Facades\Request;
use Core\Facades\Response;
use Core\Facades\Security;
use Core\Paginator\DataTables\DataTable;

use App\Libraries\Log;
use App\Exceptions\NotFoundException;
use App\Modules\Defaults\Master\Pengguna\Model as DefaultModel;
use App\Modules\Defaults\Master\Pengguna\ModelView as ViewModel;
use App\Modules\Defaults\Auth\Model\RolesModel;
use App\Modules\Defaults\Master\Kecamatan\Model as CabangModel;
use App\Modules\Defaults\Middleware\Controller as MiddlewareHardController;

/**
 * @routeGroup('/Master/Pengguna')
 * @middleware('RequireRole', 'Admin')
 */
class Controller extends MiddlewareHardController
{
    public function initialize()
    {
        // parent::setUsingRole('yes');
        // parent::setArrayRole(['Kasir', 'Kapala Unit', 'Hublang Unit']);
        parent::initialize();
    }
    /**
     * @routeGet('/')
     */
    public function indexAction($id)
    {
        $pdam_id       = $this->session->user['pdam_id'];

        $this->view->setVar('module', $id);
        $this->view->setVar('pdamid', $pdam_id);
    }

    /**
     * @routeGet('/datatable')
     * @routePost('/datatable')
     */
    public function datatableAction()
    {
        $pdam_id       = $this->session->user['pdam_id'];
        $search_nama = Request::getPost('search_nama');
        $search_username = Request::getPost('search_username');
        $search_id_cabang = Request::getPost('search_id_cabang');
        $search_id_hak = Request::getPost('search_id_hak');

        $builder =  $this->modelsManager->createBuilder()
            ->columns('*')
            ->from(ViewModel::class)
            ->where("1=1")
            ->andWhere("pdam_id = {$pdam_id}");

        if ($search_nama) {
            $builder->andWhere("nama LIKE '%$search_nama%'");
        }

        if ($search_username) {
            $builder->andWhere("username LIKE '%$search_username%'");
        }

        if ($search_id_cabang) {
            $builder->andWhere("id_cabang = '$search_id_cabang'");
        }

        if ($search_id_hak) {
            $builder->andWhere("hak_akses = '$search_id_hak'");
        }


        $dataTables = new DataTable();
        $dataTables->fromBuilder($builder)->sendResponse();
    }

    /**
     * @routePost('/create')
     */
    public function createAction()
    {
        $pdam_id       = $this->session->user['pdam_id'];
        $form = (object) Request::getPost();
        $checkUsername = DefaultModel::query()->where("username = '{$form->username}'")->execute()->toArray();

        if ($pdam_id == 6) {
            if (count($checkUsername) == 0) {
                $newUser = new DefaultModel([
                    'hak_akses'         => $form->id_hak,
                    'id_cabang'         => $form->id_cabang,
                    'username'          => $form->username,
                    'password'          => $form->password,
                    'nama'              => $form->nama,
                    'alamat'            => $form->alamat,
                    'no_hp'             => $form->no_hp,
                    'pdam_id'           => $pdam_id,
                    'is_aktif'           => 1
                ]);
                $result = $newUser->save();
                Log::write("Melakukan penambahan master data user", Request::getPost(), $result, "UserController", "INSERT");

                return Response::setStatusCode(201)->setJsonContent([
                    'message' => 'Success',
                ]);
            } else {
                return Response::setStatusCode(403)->setJsonContent([
                    'message' => 'Username telah digunakan',
                ]);
            }
        } else {
            if (count($checkUsername) == 0) {

                $user_d2d = RolesModel::find(
                    "id_hak = $form->id_hak"
                );
                $user_d2d = $user_d2d->toArray();

                $newUser = new DefaultModel([
                    'hak_akses'         => $form->id_hak,
                    'id_cabang'         => $form->id_cabang,
                    'username'          => $form->username,
                    'password'          => $form->password,
                    'nama'              => $form->nama,
                    'alamat'            => $form->alamat,
                    'no_hp'             => $form->no_hp,
                    'pdam_id'           => $pdam_id,
                    'is_d2d'           => $user_d2d[0]['is_d2d']
                ]);
                $result = $newUser->save();
                $userId = $newUser->id_user;
                Log::write("Melakukan penambahan master data user", Request::getPost(), $result, "UserController", "INSERT");


                // ACTION KE D2D

                if ($user_d2d[0]['is_d2d'] == 1) {
                    $cabang_d2d = CabangModel::find(
                        "of_id = $form->id_cabang"
                    );
                    $cabang_d2d = $cabang_d2d->toArray();
                    $cabang_d2d = $cabang_d2d[0]['of_name'];

                    $options = [
                        'cost' => 10
                    ];
                    $password_hash = password_hash("$form->password", PASSWORD_BCRYPT, $options) . "\n";

                    $sql_d2d = "CALL sp_billing_master_user_insert($pdam_id, '$form->id_cabang', '$cabang_d2d', '$userId', '$form->nama', '$form->username', '$password_hash', '$form->no_hp')";

                    $result_d2d = $this->db_d2d->fetchOne($sql_d2d);
                    Log::write("Melakukan perubahan master data user global_d2d", Request::getPost(), $result_d2d, "UserController", "UPDATE");

                    return Response::setJsonContent([
                        'stts_d2d' => $result_d2d,
                        'sql_d2d' => $sql_d2d,
                    ]);
                }

                return Response::setStatusCode(201)->setJsonContent([
                    'message' => 'Success',
                ]);
            } else {
                return Response::setStatusCode(403)->setJsonContent([
                    'message' => 'Username telah digunakan',
                ]);
            }
        }
    }

    /**
     * @routePost('/edit')
     */
    public function editAction()
    {
        $userId = Request::getPost('id');
        $pdam_id = $this->session->user['pdam_id'];

        $user = DefaultModel::findFirstById_user($userId);
        if (empty($user)) {
            throw new NotFoundException('User not found');
        }
        $form = (object) Request::getPost();

        $user->assign([
            'hak_akses'         => $form->id_hak,
            'id_cabang'         => $form->id_cabang,
            'username'          => $form->username,
            'password'          => $form->password,
            'nama'              => $form->nama,
            'alamat'            => $form->alamat,
            'no_hp'             => $form->no_hp
        ]);
        $result = $user->save();
        Log::write("Melakukan perubahan master data user", Request::getPost(), $result, "UserController", "UPDATE");




        // UPDATE KE D2D
        $user_d2d = $user->toArray();
        if ($pdam_id == 7 && $user_d2d['is_d2d'] == 1) {

            $options = [
                'cost' => 10
            ];
            $password_hash = password_hash("$form->password", PASSWORD_BCRYPT, $options) . "\n";

            $sql = "CALL sp_billing_master_user_update($userId, '$form->nama', '$form->username', '$password_hash', '$form->no_hp')";

            $result = $this->db_d2d->fetchOne($sql);
            Log::write("Melakukan perubahan master data user global_d2d", Request::getPost(), $result, "UserController", "UPDATE");

            return Response::setJsonContent([
                'stts' => $result,
            ]);
        }

        return Response::setJsonContent([
            'message' => 'Success',
        ]);
    }

    /**
     * @routePost('/delete/{userId:\d+}')
     */
    public function deleteAction($panel, $userId)
    {
        $user = DefaultModel::findFirst($userId);
        if (empty($user)) {
            throw new NotFoundException('User not found');
        }

        $result = $user->delete();
        Log::write("Melakukan penghapusan master data user", array('id_user' => $userId), $result, "UserController", "DELETE");

        return Response::setJsonContent([
            'message' => 'Success',
        ]);
    }

    /**
     * @routePost('/nonaktif/{userId:\d+}')
     */
    public function nonaktifAction($panel, $userId)
    {
        $pdam_id       = $this->session->user['pdam_id'];
        $user = DefaultModel::findFirstById_user($userId);
        if (empty($user)) {
            throw new NotFoundException('User not found');
        }
        $form = (object) Request::getPost();

        $sql = "CALL sp_billing_master_user_nonaktif(                
            '$userId',
            '$pdam_id'
        )";

        $result = $this->db->fetchOne($sql);

        Log::write("Melakukan nonaktif master data user", Request::getPost(), $result, "UserController", "UPDATE");

        return Response::setJsonContent([
            'message' => 'Success',
        ]);
    }

    /**
     * @routePost('/aktif/{userId:\d+}')
     */
    public function aktifAction($panel, $userId)
    {
        $pdam_id       = $this->session->user['pdam_id'];
        $user = DefaultModel::findFirstById_user($userId);
        if (empty($user)) {
            throw new NotFoundException('User not found');
        }
        $form = (object) Request::getPost();

        $sql = "CALL sp_billing_master_user_aktif(                
            '$userId',
            '$pdam_id'
        )";

        $result = $this->db->fetchOne($sql);

        Log::write("Melakukan aktif master data user", Request::getPost(), $result, "UserController", "UPDATE");

        return Response::setJsonContent([
            'message' => 'Success',
        ]);
    }
}
