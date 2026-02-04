<?php

namespace App\Modules\Defaults;

use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Controller;
use App\Libraries\DatabaseLibNew\SPHelper;

class BaseController extends Controller
{
	public $is_hak_input;
	public $is_hak_ubah;
	public $is_hak_delete;
	public $is_hak_cetak;
	public $is_hak_verifikasi;
	public $is_hak_unverifikasi;
	public $sp;

	public function initialize()
	{
		// pakai DI yang sudah ada, jangan new FactoryDefault()
		$di       = $this->getDI();
		$request  = $this->request;

		// rakit path saat ini → buang query string
		$uri      = strtok($request->getURI(), '?');
		$segments = explode('/', trim($uri, '/'));

		// role & link menu dari URL
		$idRole    = $this->session->user['id_role'] ?? null;

		$this->sp = SPHelper::fromPhalcon($this->db, 'mysql');
		// Enable query log untuk debugging
		$this->sp->getDatabaseSP()->enableQueryLog();


		$linkMenu  = implode('/', array_slice($segments, 1)); // buang segmen PDAM
		$linkMenu  = str_replace('global-spi/', '', $linkMenu);

		// jaga-jaga kalau kosong
		if (!$idRole) {
			$idRole = 0;
		}
		if ($linkMenu === null) {
			$linkMenu = '';
		}

		// --- SQL Server style: TOP 1, LEN(), ORDER BY
		//   • cocok untuk "cari menu terpanjang yang menjadi prefix dari URL sekarang"
		//   • gunakan binding :role dan :link
		$sql = "
			SELECT
				m.id_menu, m.nama_menu, m.jenis, m.parent_menu, m.link_menu,
				m.icon, m.urutan, m.is_aktif, m.is_tampil, m.keterangan,
				o.hak_input, o.hak_ubah, o.hak_hapus, o.hak_cetak, o.hak_verifikasi, o.hak_unverifikasi
			FROM system_menu AS m
			INNER JOIN system_menu_otorisasi AS o
				ON o.id_menu = m.id_menu
			AND o.id_role = :role
			WHERE m.is_aktif = 1
				AND m.is_tampil = 1
				AND :link LIKE CONCAT(m.link_menu, '%')
			ORDER BY CHAR_LENGTH(m.link_menu) DESC, m.urutan
			LIMIT 1;
		";

		// eksekusi dengan binding
		$stmt = $this->db->query($sql, [
			'role' => $idRole,
			'link' => $linkMenu,
		]);
		$row = $stmt->fetch(\PDO::FETCH_ASSOC) ?: [
			'hak_input'       => 1,
			'hak_ubah'        => 1,
			'hak_hapus'       => 1,
			'hak_cetak'       => 1,
			'hak_verifikasi'  => 1,
			'hak_unverifikasi' => 1,
		];

		// set ke properti instance
		$this->is_hak_input       = (int)($row['hak_input']       ?? 1);
		$this->is_hak_ubah        = (int)($row['hak_ubah']        ?? 1);
		$this->is_hak_delete      = (int)($row['hak_hapus']       ?? 1);
		$this->is_hak_cetak       = (int)($row['hak_cetak']       ?? 1);
		$this->is_hak_verifikasi  = (int)($row['hak_verifikasi']  ?? 1);
		$this->is_hak_unverifikasi = (int)($row['hak_unverifikasi'] ?? 1);

		// $this->is_hak_input       = 1;
		// $this->is_hak_ubah        = 1;
		// $this->is_hak_delete      = 1;
		// $this->is_hak_cetak       = 1;
		// $this->is_hak_verifikasi  = 1;
		// $this->is_hak_unverifikasi = 1;

		$this->view->hak = $idRole;
	}
}
