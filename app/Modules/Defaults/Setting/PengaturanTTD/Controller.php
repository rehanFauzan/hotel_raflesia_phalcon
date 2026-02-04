<?php

declare(strict_types=1);

namespace App\Modules\Defaults\Setting\PengaturanTTD;

use Core\Facades\Response;
use Core\Facades\Request;
use Core\Paginator\DataTables\DataTable;
use App\Libraries\Log;
use App\Modules\Defaults\BaseController;
use Core\Facades\Security;
use Exception;
use App\Modules\Defaults\Setting\PengaturanTTD\Model;

/**
 * @routeGroup('/setting/pengaturan-ttd')
 * @middleware('RequireUser')
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
        if ($this->request->isPost()) {
            $this->db->begin();

            try {
                $dokumen     = $this->request->getPost('dokumen');
                $ttd_list    = $this->request->getPost('ttd');

                $this->modelsMetadata->reset();

                $dataTTD = Model::find([
                    "conditions" => "jenis_laporan = :jenis_laporan:",
                    "bind" => [
                        "jenis_laporan" => $dokumen,
                    ]
                ]);

                if ($dataTTD->count() > 0) {
                    foreach ($dataTTD as $row) {
                        $row->delete();
                    }
                }

                $lastIds = [];

                if (is_array($ttd_list) && count($ttd_list) > 0) {
                    foreach ($ttd_list as $i => $row) {
                        $insertData = [
                            'jenis_laporan' => $dokumen,
                            'urutan_ke'     => $i,
                            'keterangan'    => ($row['keterangan'] ?? null),
                            'nama'          => ($row['nama'] ?? null),
                            'jabatan'       => ($row['jabatan'] ?? null),
                            'nup'           => ($row['nup'] ?? null),
                            'create_dt'     => date('Y-m-d H:i:s'),
                            'update_dt'     => date('Y-m-d H:i:s')
                        ];

                        $insert = new Model();
                        $insert->assign($insertData);
                        $result_insert = $insert->save();

                        if (!$result_insert) {
                            $this->db->rollback();
                            Response::setStatusCode(400, "FAILED");
                            return Response::setJsonContent([
                                'error' => 1,
                                'message' => "Simpan Data Gagal di baris ke-" . ($i + 1),
                                'message_data' => $insert->getMessages(),
                                'lastId' => null
                            ]);
                        }

                        $lastIds[] = $insert->id;
                    }
                }

                $this->db->commit();
                Response::setStatusCode(200, "OK");
                return Response::setJsonContent([
                    'error' => 0,
                    'message' => "Simpan Data Berhasil",
                    'lastId' => $lastIds
                ]);

            } catch (\Exception $e) {
                $this->db->rollback();
                Response::setStatusCode(500, "FAILED");
                return Response::setJsonContent([
                    'error' => 1,
                    'message' => "Terjadi kesalahan: " . $e->getMessage()
                ]);
            }

        } else {
            Response::setStatusCode(405, "FAILED");
            return Response::setJsonContent([
                'error' => 1,
                'message' => "Method not allowed"
            ]);
        }
    }

    /**
     * @routePost('/getDataTTD')
     */
    public function getDataTTDAction()
    {
        if ($this->request->isPost()) {
            try {
                $dokumen = $this->request->getPost('dokumen');

                $data = Model::find([
                    "conditions" => "jenis_laporan = :jenis_laporan:",
                    "bind" => [
                        "jenis_laporan" => $dokumen
                    ],
                    "order" => "urutan_ke ASC"
                ]);

                $result = [];
                foreach ($data as $row) {
                    $result[] = [
                        'keterangan' => $row->keterangan,
                        'nama'       => $row->nama,
                        'jabatan'    => $row->jabatan,
                        'nup'        => $row->nup
                    ];
                }

                Response::setStatusCode(200, "OK");
                return Response::setJsonContent([
                    'error' => 0,
                    'data'  => $result
                ]);
            } catch (\Exception $e) {
                Response::setStatusCode(500, "FAILED");
                return Response::setJsonContent([
                    'error' => 1,
                    'message' => $e->getMessage()
                ]);
            }
        }
    }

}
