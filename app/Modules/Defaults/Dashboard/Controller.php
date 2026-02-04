<?php

declare(strict_types=1);

namespace App\Modules\Defaults\Dashboard;

use Phalcon\Mvc\Controller as BaseController;
use App\Modules\Defaults\Master\Hakakses\Model as RoleModel;
use Core\Facades\Response;
use App\Modules\Defaults\Middleware\Controller as MiddlewareHardController;

/**
 * @routeGroup("/dashboard")
 * @middleware('RequireUser')
 */
class Controller extends BaseController
{
    // Set This Pdam
    public $arr_pdam_id = ["8", "12", "11"];

    /**
     * @routeGet("/")
     */
    public function indexAction()
    {


        // $this->view->setVar('module', $id);

        // $this->view->pdam_id = $this->session->user['pdam_id'];

        // $this->view->m_periode = $this->session->m_periode;
        // $this->view->y_peridoe = $this->session->y_periode;
        // $this->view->tahun_berjalan = $this->session->tahun;

        $arrSendData = [
            'm_periode' => $this->session->m_periode,
            'y_periode' => $this->session->y_periode,
            'tahun_berjalan' => $this->session->tahun,
            'nama_hak' => $this->session->user['role_nama']
        ];

        $hak_dashboard = $this->session->user['dir_dashboard'];

        if (isset($this->session->user['dir_dashboard']) && !empty($this->session->user['dir_dashboard'])) {

            $this->setDashboardView($hak_dashboard, $arrSendData);
        } else {
            $this->view->setMainView('Dashboard/index');
        }
    }

    private function setDashboardView($dir, $param = [])
    {
        $this->view->setVars($param);

        // Check view if not exist return default view
        if ($this->view->exists('Defaults/Dashboard/' . $dir . '/index_dashboard')) {
            $this->view->setMainView('Defaults/Dashboard/' . $dir . '/index_dashboard');
        } else {
            $this->view->setMainView('Defaults/Dashboard/index');
        }
    }

    /**
     * @routeGet("/oldindex")
     */
    public function oldindexAction($id)
    {
        $this->view->setVar('module', $id);
    }


    /**
     * @routePost('/getDataVoucher')
     */
    public function getDataVoucherAction()
    {
        if ($this->request->isPost() && $this->request->isAjax()) {

            $tahun = $this->request->getPost('tahun');
            $bulan = $this->request->getPost('bulan');

            $sql = "SET NOCOUNT ON; EXEC [akuntansi].sp_dashboard_info_voucher 
                        @bulan  = " . intval($bulan)   . ",
                        @tahun  = " . intval($tahun) . ";
            ";
            $resultData = $this->db->fetchOne($sql);


            if (count($resultData) > 0) {
                return $this->response->setJsonContent([
                    'error' => 0,
                    'message' => "Berhasil mengambil data voucher",
                    'dataFetch' => $resultData
                ]);
            } else {
                return $this->response->setJsonContent([
                    'error' => 1,
                    'message' => "Gagal mengambil data voucher",
                    'dataFetch' => []
                ]);
            }
        } else {
            return $this->response->setJsonContent([
                'error' => 1,
                'message' => 'Method not allowed'
            ]);
        }
    }



    /**
     * @routePost('/getDataLabaRugi')
     */
    public function getDataLabaRugiAction()
    {
        if ($this->request->isPost() && $this->request->isAjax()) {

            $tahun = $this->request->getPost('tahun');
            $bulan = $this->request->getPost('bulan');

            $sql = "SET NOCOUNT ON; EXEC [akuntansi].sp_dashboard_info_laba_rugi 
                        @bulan  = " . intval($bulan)   . ",
                        @tahun  = " . intval($tahun) . ";
            ";
            $resultData = $this->db->fetchAll($sql);

            if (count($resultData) > 0) {
                return $this->response->setJsonContent([
                    'error' => 0,
                    'message' => "Berhasil mengambil data laba rugi",
                    'dataFetch' => $resultData
                ]);
            } else {
                return $this->response->setJsonContent([
                    'error' => 1,
                    'message' => "Gagal mengambil data laba rugi",
                    'dataFetch' => []
                ]);
            }
        } else {
            return $this->response->setJsonContent([
                'error' => 1,
                'message' => 'Method not allowed'
            ]);
        }
    }


    /**
     * @routePost('/getDataCashFlow')
     */
    public function getDataCashFlowAction()
    {
        if ($this->request->isPost() && $this->request->isAjax()) {

            $tahun = $this->request->getPost('tahun');
            $bulan = $this->request->getPost('bulan');

            $sql = "SET NOCOUNT ON; EXEC [akuntansi].sp_dashboard_info_aruskas 
                        @tahun  = " . intval($tahun) . ",
                        @bulan  = " . intval($bulan)   . ";
            ";
            $resultData = $this->db->fetchAll($sql);

            if (count($resultData) > 0) {
                return $this->response->setJsonContent([
                    'error' => 0,
                    'message' => "Berhasil mengambil data cash flow",
                    'dataFetch' => $resultData
                ]);
            } else {
                return $this->response->setJsonContent([
                    'error' => 1,
                    'message' => "Gagal mengambil data cash flow",
                    'dataFetch' => []
                ]);
            }
        } else {
            return $this->response->setJsonContent([
                'error' => 1,
                'message' => 'Method not allowed'
            ]);
        }
    }



    /**
     * @routePost('/getDataRealisasiAnggaranPendapatanDanBiaya')
     */
    public function getDataRealisasiAnggaranPendapatanDanBiayaAction()
    {
        if ($this->request->isPost() && $this->request->isAjax()) {

            $tahun = $this->request->getPost('tahun');
            $bulan = $this->request->getPost('bulan');

            // Pendapatan
            $sql1 = "SET NOCOUNT ON; EXEC [akuntansi].sp_dashboard_info_realisasi_anggaran 
                        @tahun  = " . intval($tahun) . ",
                        @bulan  = " . intval($bulan)   . ",
                        @jenis  = 1;
            ";
            $resultData1 = $this->db->fetchAll($sql1);

            // Biaya
            $sql2 = "SET NOCOUNT ON; EXEC [akuntansi].sp_dashboard_info_realisasi_anggaran 
                        @tahun  = " . intval($tahun) . ",
                        @bulan  = " . intval($bulan)   . ",
                        @jenis  = 2;
            ";
            $resultData2 = $this->db->fetchAll($sql2);

            if (count($resultData1) > 0 || count($resultData2) > 0) {
                return $this->response->setJsonContent([
                    'error' => 0,
                    'message' => "Berhasil mengambil data info realisasi",
                    'dataFetch1' => $resultData1,
                    'dataFetch2' => $resultData2
                ]);
            } else {
                return $this->response->setJsonContent([
                    'error' => 1,
                    'message' => "Gagal mengambil data info realisasi",
                    'dataFetch1' => [],
                    'dataFetch2' => []
                ]);
            }
        } else {
            return $this->response->setJsonContent([
                'error' => 1,
                'message' => 'Method not allowed'
            ]);
        }
    }


    /**
     * @routePost('/getDataGrafikLrDanRealisasiBiayaPendInv')
     */
    public function getDataGrafikLrDanRealisasiBiayaPendInvAction()
    {
        if ($this->request->isPost() && $this->request->isAjax()) {

            $tahun = $this->request->getPost('tahun');
            $bulan = $this->request->getPost('bulan');

            // Pendapatan
            $sql = "SET NOCOUNT ON; EXEC [akuntansi].sp_dashboard_info_grafik_laba_rugi 
                        @tahun  = " . intval($tahun) . ";
            ";
            $resultData = $this->db->fetchAll($sql);

            if (count($resultData) > 0) {
                return $this->response->setJsonContent([
                    'error' => 0,
                    'message' => "Berhasil mengambil data grafik lr",
                    'dataFetch' => $resultData
                ]);
            } else {
                return $this->response->setJsonContent([
                    'error' => 1,
                    'message' => "Gagal mengambil data grafik lr",
                    'dataFetch' => []
                ]);
            }
        } else {
            return $this->response->setJsonContent([
                'error' => 1,
                'message' => 'Method not allowed'
            ]);
        }
    }
}
