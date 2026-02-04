<?php

declare(strict_types=1);

namespace App\Modules\Defaults\Setting\OtoritasMenu;

use Core\Facades\Response;
use Core\Facades\Request;
use Core\Paginator\DataTables\DataTable;
use App\Libraries\Log;
use App\Modules\Defaults\BaseController;
use Core\Facades\Security;
use App\Modules\Defaults\Setting\Role\Model as RolesModel;

/**
 * @routeGroup('/setting/otoritas_menu')
 * @middleware('RequireUser')
 */
class Controller extends BaseController
{

    /**
     * @routeGet('/')
     */
    public function indexAction() {}


    /**
     * @routePost('/datatable')
     * @routeGet('/datatable')
     */
    public function datatableAction()
    {
        $builder = $this->modelsManager->createBuilder()
            ->columns('*')
            ->from(RolesModel::class)
            ->where("1=1");

        $dataTables = new DataTable();
        $dataTables->fromBuilder($builder)->sendResponse();
    }


    /**
     * @routeGet('/setAksesBatch')
     * @routePost('/setAksesBatch')
     */
    public function setAksesBatchAction()
    {
        $roleid = $this->request->getPost('roleid');
        $menuids = $this->request->getPost('menuids');
        $value = $this->request->getPost('value');

        foreach ($menuids as $key => $valueEach) {

            // $sql = "EXEC akuntansi.system_sp_set_menu_hak @vRoleID = '$roleid', @vMenuID = '$valueEach', @vValue = '$value'";
            // $a = $this->db->fetchAll($sql);

            try {
                $a = $this->sp->call('system_sp_set_menu_hak', [
                    'vRoleID' => $roleid,
                    'vMenuID' => $valueEach,
                    'vValue' => $value
                ])->fetchAll();

                $queryLog = $this->sp->getDatabaseSP()->getQueryLog();
            } catch (\Exception $e) {
                // Log query untuk debugging
                $queryLog = $this->sp->getDatabaseSP()->getQueryLog();
                error_log("DatabaseSP Query Log: " . print_r($queryLog, true));
                error_log("Error: " . $e->getMessage());
                throw $e;
            }

            foreach ($a as $datas) {
                $status = $datas['state'];
            }
        }

        Log::write("Melakukan perubahan data otorisasi menu batch", ['queryLog' => $queryLog], TRUE, "Setting/OtorisasiMenu/Controller", "CALL SP");

        echo $status;
    }

    /**
     * @routeGet('/setAkses')
     * @routePost('/setAkses')
     */
    public function setAksesAction()
    {
        $roleid = $this->request->getPost('roleid');
        $menuid = $this->request->getPost('menuid');
        $value = $this->request->getPost('value');

        // $sql = "EXEC akuntansi.system_sp_set_menu_hak @vRoleID = '$roleid', @vMenuID = '$menuid', @vValue = '$value'";
        // $a = $this->db->fetchAll($sql);


        try {
            $a = $this->sp->call('system_sp_set_menu_hak', [
                'vRoleID' => $roleid,
                'vMenuID' => $menuid,
                'vValue' => $value
            ])->fetchAll();
            
            $queryLog = $this->sp->getDatabaseSP()->getQueryLog();
        } catch (\Exception $e) {
            // Log query untuk debugging
            $queryLog = $this->sp->getDatabaseSP()->getQueryLog();
            error_log("DatabaseSP Query Log: " . print_r($queryLog, true));
            error_log("Error: " . $e->getMessage());
            throw $e;
        }

        foreach ($a as $datas) {
            $status = $datas['state'];
        }

        Log::write("Melakukan perubahan data otorisasi menu", ['queryLog' => $queryLog], TRUE, "Setting/OtorisasiMenu/Controller", "CALL SP");

        echo $status;
    }

    /**
     * @routePost('/menuAkses')
     * @routeGet('/menuAkses')
     */
    public function menuAksesAction()
    {
        $id_hak = $this->request->get('id');
        $data = RolesModel::findFirstByid($id_hak);

        $hak_nama = $data->toArray()['role'];

        $this->view->id_hak = $id_hak;
        $this->view->hak_nama = $hak_nama;
    }

    /**
     * @routeGet('/loadMenu')
     * @routePost('/loadMenu')
     */
    public function loadMenuAction()
    {
        $id = $this->request->getPost('id');
        $sql = "
			SELECT 
                0 AS parent_level, 
                m.id_menu as menu_id, 
                m.parent_menu AS parent, 
                m.nama_menu AS nama, 
                m.link_menu AS link, 
                m.icon, 
                COALESCE(r.id, 0) as id 
            FROM system_menu m 
			LEFT JOIN 
				system_menu_otorisasi r 
					ON 
				r.id_menu = m.id_menu AND r.id_role = $id
			WHERE m.parent_menu = 0 AND m.is_aktif = 1 AND m.is_tampil = 1
			ORDER BY m.urutan;";

        $data_menu = $this->db->fetchAll($sql);

        $menus = [];
        foreach ($data_menu as $parent) {
            $menus[] = $parent;
            $sql1 = "
				SELECT 
					m.id_menu as menu_id
                    ,1 AS parent_level
					,COALESCE(r.id, 0) as id
                    ,m.parent_menu AS parent
					,m.nama_menu AS nama
					,m.link_menu AS link
					,m.icon  
				FROM 
					system_menu m 
				LEFT JOIN 
					system_menu_otorisasi r 
						ON 
					r.id_menu = m.id_menu 
					AND r.id_role = '$id'
				WHERE 
					m.parent_menu = '$parent[menu_id]' 
					AND m.is_aktif = 1 AND m.is_tampil = 1
				ORDER BY m.urutan";

            $data_menu2 = $this->db->fetchAll($sql1);

            foreach ($data_menu2 as $child) {
                $menus[] = $child;
                $sql2 = "
				SELECT 
					m.id_menu as menu_id
                    ,2 AS parent_level
					,COALESCE(r.id, 0) as id
                    ,m.parent_menu AS parent
					,m.nama_menu AS nama
					,m.link_menu AS link
					,m.icon  
				FROM 
					system_menu m 
				LEFT JOIN 
					system_menu_otorisasi r 
						ON 
					r.id_menu = m.id_menu 
					AND r.id_role = '$id'
				WHERE 
					m.parent_menu = '$child[menu_id]' 
					AND m.is_aktif = 1 AND m.is_tampil = 1
				ORDER BY m.urutan";

                $data_menu3 = $this->db->fetchAll($sql2);
                foreach ($data_menu3 as $key3 => $sub_child) {

                    $menus[] = $sub_child;
                    $sql4 = "
                        SELECT 
                            m.id_menu as menu_id
                            ,3 AS parent_level 
                            ,COALESCE(r.id, 0) as id
                            ,m.parent_menu AS parent
                            ,m.nama_menu AS nama
                            ,m.link_menu AS link
                            ,m.icon  
                        FROM 
                            system_menu m 
                        LEFT JOIN 
                            system_menu_otorisasi r 
                                ON 
                            r.id_menu = m.id_menu 
                            AND r.id_role = '$id'
                        WHERE 
                            m.parent_menu = '$sub_child[menu_id]' 
                            AND m.is_aktif = 1 AND m.is_tampil = 1
                        ORDER BY m.urutan";
                    $data_menu4 = $this->db->fetchAll($sql4);
                    foreach ($data_menu4 as $sub_sub_child) {
                        $menus[] = $sub_sub_child;
                    }
                }
            }
        }
        echo json_encode($menus);
    }
}
