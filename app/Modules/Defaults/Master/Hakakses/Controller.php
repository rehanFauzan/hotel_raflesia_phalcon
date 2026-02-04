<?php

declare(strict_types=1);

namespace App\Modules\Defaults\Master\Hakakses;

use Phalcon\Mvc\Controller as BaseController;

use Core\Facades\Request;
use Core\Facades\Response;
use Core\Paginator\DataTables\DataTable;
use App\Modules\Defaults\Master\Hakakses\Model as RolesModel;
use App\Modules\Defaults\Master\User\Model as UsersModel;
use App\Exceptions\NotFoundException;
use App\Modules\Defaults\Middleware\Controller as MiddlewareHardController;

/**
 * @routeGroup('/Master/Hakakses')
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
        $this->view->setVar('module', $id);
    }

    /**
     * @routeGet('/datatable')
     * @routePost('/datatable')
     */
    public function datatableAction()
    {
        $pdam_id       = $this->session->user['pdam_id'];
        $search_nama = Request::getPost('search_nama');

        $builder =  $this->modelsManager->createBuilder()
            ->columns('*')
            ->from(RolesModel::class)
            ->where("1=1")
            ->andWhere("pdam_id = {$pdam_id}");

        if ($search_nama) {
            $builder->andWhere("hak LIKE '%$search_nama%'");
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
        $newRole = new RolesModel([
            'pdam_id' => $pdam_id,
            'hak' => Request::getPost('name'),
        ]);

        $newRole->save();
        return Response::setJsonContent([
            'message' => 'Success',
        ]);
    }

    /**
     * @routePost('/edit')
     */
    public function editAction()
    {
        $userId = Request::getPost('id');

        $user = RolesModel::findFirstById_hak($userId);
        if (empty($user)) {
            throw new NotFoundException('User not found');
        }
        $form = (object) Request::getPost();

        $user->assign([
            'hak'         => $form->name,
        ]);
        $result = $user->save();

        return Response::setJsonContent([
            'message' => 'Success',
        ]);
    }

    /**
     * @routePost('/delete/{userId:\d+}')
     */
    public function deleteAction($panel, $userId)
    {
        $user = RolesModel::findFirstById_hak($userId);
        if (empty($user)) {
            throw new NotFoundException('User not found');
        }

        $result = $user->delete();
        return Response::setJsonContent([
            'message' => 'Success',
        ]);
    }
}
