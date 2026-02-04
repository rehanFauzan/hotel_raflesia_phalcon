<?php

declare(strict_types=1);

namespace App\Modules\Defaults\Middleware;

use CURLFile;
use Exception;
use Core\Facades\View;
use App\Models\MenuModel;
use Phalcon\Filter\Sanitize\StringVal;
use WpOrg\Requests\Hooks;
use Phalcon\Http\Response;
use Phalcon\Mvc\Dispatcher;
use WpOrg\Requests\Requests;
use Phalcon\Mvc\Controller as BaseController;

class Controller extends BaseController
{
    private $myArray = [];
    private $is_usingRole = 'no';

    public function setArrayRole($newArray)
    {
        if (is_array($newArray)) {
            $this->myArray = $newArray;
        } else {
            throw new Exception("Invalid argument, must be an array");
        }
    }

    public function getArrayRole()
    {
        return $this->myArray;
    }

    public function setUsingRole($is_using)
    {
        if (is_string($is_using)) {
            $this->is_usingRole = $is_using;
        } else {
            throw new Exception("Invalid argument, must be a string");
        }
    }

    public function getUsingRole()
    {
        return $this->is_usingRole;
    }

    public function initialize()
    {
        // var_dump($this->cekMenu());die;


        if (!$this->session->has('user')) {
            /** @var array */
            $queries = $this->request->getQuery();
            $intended = isset($queries['_url']) == true ? $queries['_url'] : null;
            unset($queries['_url']);

            $intended .= empty($queries) ? '' : ('?' . http_build_query($queries));

            $this->session->set('intended', $intended);

            return $this->response->redirect('/panel/auth/login');
        } else {

            if ($this->isValidUrl($this->router->getControllerName()) == 0) {
                return $this->response->redirect('/panel/Redirect');
            }

            // echo '<pre>';
            // print_r($this->getUsingRole());
            // echo '</pre>';
            // die();

            if ($this->getUsingRole() == 'yes') {
                if (!in_array($this->session->has('user')['hak_akses'], $this->getArrayRole())) {
                    return $this->response->redirect('/panel/auth/unauthorized');
                }
            }
        }
    }
    
    private function isValidUrl($url)
    {
        $hak = $this->session->user['id_hak'];
        $pdam_id = $this->session->user['pdam_id'];

        if ($pdam_id == 6) {
            $module = 'pdam-tbw';
        } else if ($pdam_id == 7) {
            $module = 'nciho';
        } else if ($pdam_id == 9) {
            $module = 'pdam-manna';
        } else if ($pdam_id == 13) {
            $module = 'natuna';
        }
        
        
        $url = str_replace(['Defaults\\', '\\'], ["", '/'], $url);
        
        $sql = "CALL sp_billing_cek_menu_getperhak($hak, $pdam_id)";
        $result = $this->db->fetchAll($sql);
        
        $validUrls = [];
        foreach ($result as $value) {
            if ($value['link_menu_v2']) {
                $get_menu=explode('/',$value['link_menu_v2']);

                $url = str_replace(["$get_menu[0]/", '/'], ["", '/'], $value['link_menu_v2']);
                $validUrls[] = $url;
            }
        }

        
        // echo "<pre>";
        // print_r($validUrls);die;
        $status = 0;
        if (in_array($url, $validUrls) || $this->router->getControllerName()=="Defaults\Dashboard" || $this->router->getControllerName()=="Tbw\Dashboard") {
            $status = 1;
        }

        // var_dump($this->router->getControllerName());die;
        return $status;
    }
	
	function insertLogkasir($vidloket, $vid_user, $vnopel, $vjumbulan, $vipaddr, $vketerangan, $vtglbayar, $vkodebalikan, $vstatus)
	{
		$sql = "CALL sp_billing_settinglog_insertkasir($vidloket,'$vid_user','$vnopel',$vjumbulan,'$vipaddr','$vketerangan','$vtglbayar','$vkodebalikan','$vstatus')";
		$result = $this->db->fetchOne($sql);
		// $data = new Phalcon\Mvc\Model\Resultset\Simple(null, null, $this->db->query($sql));
	}
}
