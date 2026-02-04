<?php

namespace App\Modules\Defaults;

use Phalcon\Mvc\Controller;
use Phalcon\Mvc\Router;
use Phalcon\Mvc\Dispatcher;

class ControllerBase extends Controller
{
	public $sess_nama;
	public $sess_id;
	public $sess_username;
	public $sess_ofid;
	public $sess_hak;
	public $sess_password;
	public $sess_pdamid;
	public $sess_ispusat;

	public function initialize()
	{
		// print_r("x");exit;
		// CEK JIKA TIDA ADA SESSION
		if (!$this->session->has("auth")) {
			return $this->dispatcher->forward(array(
				'controller' => 'Redirect'
			));
		}

		if ($this->session->has("auth")) {
			$auth = $this->session->get("auth");
			$this->sess_nama = $auth['nama'];
			$this->sess_id = $auth['id'];
			$this->sess_username = $auth['username'];
			$this->sess_ofid = $auth['ofid'];
			$this->sess_hak = $auth['hak'];
			$this->sess_password = $auth['pw'];
			$this->sess_pdamid = $auth['pdam_id'];
			$this->sess_ispusat = $auth['is_pusat'];
		}

		$session = $this->session->get("auth");

		$create = new LogBilling();
		$create->assign(array(
			"times" => date("Y-m-d H:i:s"),
			"id_user" => $session['id'],
			"menu" =>  $this->router->getControllerName(),
			"ip" => $this->request->getClientAddress(),
			"error" => "",
			"request_post" => json_encode($this->request->getPost()),
			"request_get" => json_encode($this->request->get()),
		));

		$result = $create->save();


		// CEK AJAX DAN GET MENU
		if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
		} else {
			if ($this->cekMenu() == 0) {
				$this->response->redirect('Redirect');
			}
			$hasilcount = 4;

			$hak = $this->sess_hak;
			$pdamid = $this->sess_pdamid;
			$sql = "CALL sp_billing_menu_getdata(0,$hak,$pdamid)";
			$data = new Phalcon\Mvc\Model\Resultset\Simple(null, null, $this->db->query($sql));
			$mainmenu = $data->toArray();
			unset($data);
			// print_r($a);exit;

			$menu = array();
			foreach ($mainmenu as $data) {

				$sql2 = "CALL sp_billing_menu_getdata($data[id_menu],$hak,$pdamid)";
				$data2 = new Phalcon\Mvc\Model\Resultset\Simple(null, null, $this->db->query($sql2));
				$data_submenu = $data2->toArray();
				unset($data2);
				$tmp = array(
					'title' => $data['nama_menu'],
					'link' => $data['link_menu'],
					'icon' => $data['icon']
				);

				// if($data['id_menu']==7)
				// {
				// $tmp['notif']=$hasilcount;
				// }

				if (isset($data_submenu)) {
					$submenu = array();
					foreach ($data_submenu as $data2) {
						$tmpsub = array(
							'title' => $data2['nama_menu'],
							'link' => $data2['link_menu'],
							'icon' => $data['icon']
						);
						// if($data2['id_menu']==7)
						// {
						// $tmpsub['notif']=$hasilcount;
						// }
						$submenu[] = $tmpsub;
					}



					//$tmp['link'] = '#';
					$tmp['sub'] = $submenu;
				}
				$menu[] = $tmp;
			}

			$this->view->menu = $menu;
			$this->view->username = $this->sess_username;
			$this->view->pdam_id = $this->sess_pdamid;
			$this->view->hak = $this->sess_hak;
		}
	}
	function cekMenu()
	{

		$array_sess_men = array();
		$jml = 0;
		foreach ($_SESSION['basket_menu_billing'] as $datas) {
			$get_menu = explode('/', $datas['link_menu']);
			$array_sess_men[$jml] = $get_menu[0];
			$jml++;
		}
		$status = 0;
		for ($i = 0; $i < $jml; $i++) {
			if ((strpos($array_sess_men[$i], $this->router->getControllerName()) !== false) || $this->router->getControllerName() == "Dashboard") {
				$status = 1;
				break;
			}
		}
		return $status;
	}
	public function beforeExecuteRoute(Dispatcher $dispatcher)
	{
		$controllerName = $this->router->getControllerName();

		if (!$this->session->has('auth') || (!$this->session->has('auth') && $controllerName != "Infolang")) {
			$dispatcher->forward(['controller' => 'Redirect', 'action' => 'index']);
			return false;
		}
	}
	function insertLog($vid_user, $vjenis, $vketerangan, $vparam1, $vparam2)
	{
		$sql = "CALL sp_billing_settinglog_insert($vid_user,'$vjenis','$vketerangan','$vparam1','$vparam2')";
		$data = new Phalcon\Mvc\Model\Resultset\Simple(null, null, $this->db->query($sql));
	}
	function insertLogkasir($vidloket, $vid_user, $vnopel, $vjumbulan, $vipaddr, $vketerangan, $vtglbayar, $vkodebalikan, $vstatus)
	{
		$sql = "CALL sp_billing_settinglog_insertkasir($vidloket,'$vid_user','$vnopel',$vjumbulan,'$vipaddr','$vketerangan','$vtglbayar','$vkodebalikan','$vstatus')";
		$data = new Phalcon\Mvc\Model\Resultset\Simple(null, null, $this->db->query($sql));
	}

	function log()
	{
		$session = $this->session->get("auth");

		$create = new LogBilling();
		$create->assign(array(
			"times" => date("Y-m-d H:i:s"),
			"id_user" => $session['id'],
			"menu" =>  $this->router->fetch_method(),
			"ip" => $this->request->getClientAddress(),
			"error" => "",
			"request_post" => json_encode($this->request->getPost()),
			"request_get" => json_encode($this->request->get()),
		));

		$result = $create->save();
	}

	function DateUSA($tanggal)
	{
		$date = date_create($tanggal);
		return date_format($date, 'Y-m-d');
	}

	function dateINA($tanggal)
	{
		$date = date_create($tanggal);
		return date_format($date, 'd-m-Y');
	}
}
