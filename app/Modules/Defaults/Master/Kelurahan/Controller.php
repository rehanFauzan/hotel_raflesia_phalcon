<?php

namespace App\Modules\Defaults\Master\Kelurahan;

use Phalcon\Mvc\Controller as BaseController;
use Core\Facades\Response;
use Core\Facades\Request;
use Core\Paginator\DataTables\DataTable;
use App\Libraries\Log;
use App\Exceptions\NotFoundException;
use App\Modules\Defaults\Master\Kelurahan\Model as KelurahanModel;
use App\Modules\Defaults\Master\Kelurahan\ModelRegional as RegionalModel;
use App\Modules\Defaults\Master\Kelurahan\ModelView as KelurahanviewModel;
use App\Modules\Defaults\Middleware\Controller as MiddlewareHardController;

/**
 * @routeGroup('/Master/Kelurahan')
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
     * @routePost('/datatable')
     * @routeGet('/datatable')
     */
    public function datatableAction()
    {
        $pdam_id       = $this->session->user['pdam_id'];
        $idKecamatan = Request::getPost('search_id_kecamatan');
        $kodeKelurahan = Request::getPost('search_kode_kelurahan');
        $namaKelurahan = Request::getPost('search_nama_kelurahan');

        $builder = $this->modelsManager->createBuilder()
            ->columns('*')
            ->from(KelurahanviewModel::class)
            ->where("1=1")
            ->andWhere("pdam_id = {$pdam_id}")
            ->orderBy("nama_kec ASC");

        if (isset($idKecamatan) && !empty($idKecamatan)) {
            $builder->andWhere("id_kec LIKE '%$idKecamatan%'");
        }

        if (isset($kodeKelurahan) && !empty($kodeKelurahan)) {
            $builder->andWhere("kode_kel LIKE '%$kodeKelurahan%'");
        }

        if (isset($namaKelurahan) && !empty($namaKelurahan)) {
            $builder->andWhere("nama_kel LIKE '%$namaKelurahan%'");
        }

        $dataTables = new DataTable();
        $dataTables->fromBuilder($builder)->sendResponse();
    }

    /**
     * @routePost('/create')
     */
    public function createAction()
    {
        $pdam_id = $this->session->user['pdam_id'];

        $create = new RegionalModel([
            'pdam_id' => $pdam_id,
            'rgn_code' => Request::getPost('kode_kelurahan', null, null),
            'rgn_name' => Request::getPost('nama_kelurahan', null, null),
            'of_id' => Request::getPost('of_id', null, null),
            'of_code' => Request::getPost('of_code', null, null)
        ]);
        $result = $create->save();

        Log::write("Melakukan penambahan master data kelurahan", Request::getPost(), $result, "KelurahanController", "INSERT");

        return Response::setJsonContent([
            'message' => 'Success',
        ]);
    }

    /**
     * @routePost('/edit')
     */
    public function editAction()
    {
        $pdam_id = $this->session->user['pdam_id'];

        $id = Request::getPost('id');

        $update = RegionalModel::findFirstByrgn_id($id);
        if (empty($update)) {
            throw new NotFoundException('Table not found');
        }

        $update->assign([
            'pdam_id' => $pdam_id,
            'rgn_id'  => $id,
            'rgn_code' => Request::getPost('kode_kelurahan', null, null),
            'rgn_name' => Request::getPost('nama_kelurahan', null, null),
            'of_id' => Request::getPost('of_id', null, null),
            'of_code' => Request::getPost('of_code', null, null)
        ]);

        $result = $update->save();
        Log::write("Melakukan perubahan master data Kelurahan", Request::getPost(), $result, "KelurahanController", "UPDATE");

        return Response::setJsonContent([
            'message' => 'Success',
        ]);
    }

    /**
     * @routePost('/delete/{id:\d+}')
     */
    public function deleteAction($moduleParams, $id)
    {
        $delete = RegionalModel::findFirstByrgn_id($id);
        if (empty($delete)) {
            throw new NotFoundException('Table not found');
        }

        $result = $delete->delete();
        Log::write("Melakukan penghapusan master data Kelurahan", Request::getPost(), $result, "KelurahanController", "DELETE");

        return Response::setJsonContent([
            'message' => 'Success',
        ]);
    }
}
