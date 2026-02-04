<?php

declare(strict_types=1);

namespace App\Modules\Defaults\Master\Otoritasmenu;

use Phalcon\Mvc\Controller as BaseController;
use Core\Facades\Response;
use Core\Paginator\DataTables\DataTable;
use App\Modules\Defaults\Master\Hakakses\Model as RolesModel;
use App\Modules\Defaults\Middleware\Controller as MiddlewareHardController;

/**
 * @routeGroup('/Master/Otoritasmenu')
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
        $builder =  $this->modelsManager->createBuilder()
            ->columns('*')
            ->from(RolesModel::class)
            ->where("1=1")
            ->andWhere("pdam_id = {$pdam_id}");

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
        $pdamid = $this->session->user['pdam_id'];

        foreach ($menuids as $key => $valueEach) {
            $sql = "SET NOCOUNT ON; EXEC akuntansi.system_sp_set_menu_hak @roleid = '$roleid', @menuid = '$valueEach', @value = '$value', @pdamid = '$pdamid'";
            $a = $this->db->fetchAll($sql);
            foreach ($a as $datas) {
                $status = $datas['state'];
            }
        }

        // Log::write("Melakukan perubahan data otorisasi menu batch", ['sql' => $sql], TRUE, "Setting/OtorisasiMenu/Controller", "CALL SP");

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
        $pdamid = $this->session->user['pdam_id'];

        $sql = "SET NOCOUNT ON; EXEC akuntansi.system_sp_set_menu_hak @roleid = '$roleid', @menuid = '$menuid', @value = '$value', @pdamid = '$pdamid'";
        $a = $this->db->fetchAll($sql);
        foreach ($a as $datas) {
            $status = $datas['state'];
        }

        // Log::write("Melakukan perubahan data otorisasi menu", ['sql' => $sql], TRUE, "Setting/OtorisasiMenu/Controller", "CALL SP");

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
			SET NOCOUNT ON;
			SELECT 
                0 AS parent_level, 
                m.id_menu as menu_id, 
                m.parent_menu AS parent, 
                m.nama_menu AS nama, 
                m.link_menu AS link, 
                m.icon, 
                ISNULL(r.id, 0) as id 
            FROM akuntansi.system_menu m 
			LEFT JOIN 
				akuntansi.system_menu_otorisasi r 
					ON 
				r.id_menu = m.id_menu AND r.id_role = $id
			WHERE m.parent_menu = 0 AND m.is_aktif = 1 AND m.is_tampil = 1
			ORDER BY m.urutan;";
        
        $data_menu = $this->db->fetchAll($sql);

        $menus = [];
        foreach ($data_menu as $parent) {
            $menus[] = $parent;
            $sql1 = "
				SET NOCOUNT ON;
				SELECT 
					m.id_menu as menu_id
                    ,1 AS parent_level
					,ISNULL(r.id, 0) as id
                    ,m.parent_menu AS parent
					,m.nama_menu AS nama
					,m.link_menu AS link
					,m.icon  
				FROM 
					akuntansi.system_menu m 
				LEFT JOIN 
					akuntansi.system_menu_otorisasi r 
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
				SET NOCOUNT ON;
				SELECT 
					m.id_menu as menu_id
                    ,2 AS parent_level
					,ISNULL(r.id, 0) as id
					,m.parent_menu AS parent
					,m.nama_menu AS nama
					,m.link_menu AS link
					,m.icon  
				FROM 
					akuntansi.system_menu m 
				LEFT JOIN 
					akuntansi.system_menu_otorisasi r 
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
                        SET NOCOUNT ON;
                        SELECT 
                            m.id_menu as menu_id
                            ,3 AS parent_level 
                            ,ISNULL(r.id, 0) as id
                            ,m.parent_menu AS parent
                            ,m.nama_menu AS nama
                            ,m.link_menu AS link
                            ,m.icon  
                        FROM 
                            akuntansi.system_menu m 
                        LEFT JOIN 
                            akuntansi.system_menu_otorisasi r 
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
