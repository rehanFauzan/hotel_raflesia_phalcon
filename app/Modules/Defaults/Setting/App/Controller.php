<?php

declare(strict_types=1);

namespace App\Modules\Defaults\Setting\App;

use Core\Facades\Request;
use Core\Facades\Response;
use Phalcon\Mvc\Controller as BaseController;
use App\Libraries\Log;
use App\Exceptions\NotFoundException;
use DateTime;
use App\Modules\Defaults\Middleware\Controller as MiddlewareHardController;

/**
 * @routeGroup('/Setting/App')
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
        
        // $pdamId = $this->session->user['pdam_id'];
        $this->view->setVar('pdam_id', 1);
    }

    /**
     * @routeGet('/getData')
     * @routePost('/getData')
     */
    public function getDataAction()
    {
        $jenis = $this->request->getPost('jenis');
        $jenisLaporan = $this->request->getPost('jenisLaporan');
        $pdamId = $this->session->user['pdam_id'];
        $ofId = $this->session->user['of_id'];

        if ($jenis == "HEADER-LAPORAN") {
            $sql = "CALL sp_billing_setting_app_getdata_header_lap($pdamId)";
            $data = $this->db->fetchOne($sql);
            return Response::setJsonContent($data);
        } elseif ($jenis == "HEADER-STRUK") {

            $sql = "CALL sp_billing_setting_app_getdata_struk($pdamId)";
            $data = $this->db->fetchOne($sql);
            return Response::setJsonContent($data);
        } elseif ($jenis == "FOOTER-STRUK") {

            $sql = "CALL sp_billing_setting_app_getdata_struk($pdamId)";
            $data = $this->db->fetchOne($sql);
            return Response::setJsonContent($data);
        } elseif ($jenis == "TTD-LAPORAN") {
            if ($jenisLaporan == "LAP-LPP") {

                $sql = "CALL sp_billing_setting_app_getdata_ttd($pdamId)";
                $data = $this->db->fetchAll($sql);

                $arrayFiltered = array_filter($data, function ($value, $index) {
                    return $value['type'] == 'LPP';
                }, ARRAY_FILTER_USE_BOTH);
                $arrRed = array_reduce($arrayFiltered, 'array_merge', array());
                return Response::setJsonContent($arrRed);
            } elseif ($jenisLaporan == "LAP-DRD") {

                $sql = "CALL sp_billing_setting_app_getdata_ttd($pdamId)";
                $data = $this->db->fetchAll($sql);

                $arrayFiltered = array_filter($data, function ($value, $index) {
                    return $value['type'] == 'DRD';
                }, ARRAY_FILTER_USE_BOTH);
                $arrRed = array_reduce($arrayFiltered, 'array_merge', array());
                return Response::setJsonContent($arrRed);
            } elseif ($jenisLaporan == "LAP-DSR") {

                $sql = "CALL sp_billing_setting_app_getdata_ttd($pdamId)";
                $data = $this->db->fetchAll($sql);
                $arrayFiltered = array_filter($data, function ($value, $index) {
                    return $value['type'] == 'DSR';
                }, ARRAY_FILTER_USE_BOTH);

                $arrRed = array_reduce($arrayFiltered, 'array_merge', array());
                return Response::setJsonContent($arrRed);
            } elseif ($jenisLaporan == "LAP-Efektifitas") {

                $sql = "CALL sp_billing_setting_app_getdata_ttd($pdamId)";
                $data = $this->db->fetchAll($sql);
                $arrayFiltered = array_filter($data, function ($value, $index) {
                    return $value['type'] == 'Efektifitas';
                }, ARRAY_FILTER_USE_BOTH);

                $arrRed = array_reduce($arrayFiltered, 'array_merge', array());
                return Response::setJsonContent($arrRed);
            }
        } elseif ($jenis == "TTD-SPT-INFOLANG") {

            $sql = "CALL sp_billing_ttd_nciho('spt_infolang',  $pdamId, $ofId)";
            $data = $this->db->fetchAll($sql);

            $arrayFiltered = array_filter($data, function ($value, $index) {
                return $value['type'] == 'spt_infolang';
            }, ARRAY_FILTER_USE_BOTH);
            $arrRed = array_reduce($arrayFiltered, 'array_merge', array());
            return Response::setJsonContent($arrRed);

        }
    }

    /**
     * @routeGet('/saveHeaderLap')
     * @routePost('/saveHeaderLap')
     */
    public function saveHeaderLapAction()
    {
        // Array
        // (
        //     [_type] => create
        //     [id] => 
        //     [nama_perusahaan] => -
        //     [nama_pdam] => PMgS
        //     [kota_kab_pdam] => Bandung Barat
        //     [alamat_pdam] => JLN. KOMPLEK PU PROSIDA BENDUNG PINTU 10, MEKARSARI. TELP : (021) 5587234
        //     [no_telp_pdam] => +62 21 5538865
        // )

        $filenameFoto1 = $this->request->getPost('filenameFoto1');
        $txt_namaperusahaan = $this->request->getPost('nama_perusahaan');
        $txt_namapdam = $this->request->getPost('nama_pdam');
        $txt_kotapdam = $this->request->getPost('kota_kab_pdam');
        $txt_alamatpdam = $this->request->getPost('alamat_pdam');
        $txt_telppdam = $this->request->getPost('no_telp_pdam');
        $pdam_id = $this->session->user['pdam_id'];

        $sql = "CALL sp_billing_setting_app_update_header_lap('$txt_namaperusahaan', '$txt_namapdam', '$txt_kotapdam', '$txt_alamatpdam', '$txt_telppdam', $pdam_id, '$filenameFoto1')";
        $data = $this->db->fetchOne($sql);

        if ($data['stts'] == 1 || $data['stts'] == 0) {
            return Response::setJsonContent([
                'error' => 0,
                'message' => 'Success'
            ]);
        } else {
            return Response::setJsonContent([
                'error' => 0,
                'message' => 'Success'
            ]);
        }
    }

    /**
     * @routeGet('/saveHeaderRekening')
     * @routePost('/saveHeaderRekening')
     */
    public function saveHeaderRekeningAction()
    {

        $id = $this->request->getPost('id');
        $namaPdam = $this->request->getPost('Rekening_nama_pdam');
        $alamatPdam = $this->request->getPost('Rekening_alamat_pdam');
        $header3 = "";

        $sql = "CALL sp_billing_setting_app_update_struk($id, '$namaPdam', '$alamatPdam', '$header3')";
        $data = $this->db->fetchOne($sql);

        if ($data['stts'] == 1 || $data['stts'] == 0) {
            return Response::setJsonContent([
                'error' => 0,
                'message' => 'Success'
            ]);
        } else {
            return Response::setJsonContent([
                'error' => 0,
                'message' => 'Success'
            ]);
        }
    }

    /**
     * @routeGet('/saveFooterRekening')
     * @routePost('/saveFooterRekening')
     */
    public function saveFooterRekeningAction()
    {

        $id = $this->request->getPost('id');
        $Footer_1 = $this->request->getPost('Footer_baris1');
        $Footer_2 = $this->request->getPost('Footer_baris2');
        $Footer_3 = $this->request->getPost('Footer_baris3');

        $sql = "CALL sp_billing_setting_app_update_struk_footer($id, '$Footer_1', '$Footer_2', '$Footer_3')";
        $data = $this->db->fetchOne($sql);

        if ($data['stts'] == 1 || $data['stts'] == 0) {
            return Response::setJsonContent([
                'error' => 0,
                'message' => 'Success'
            ]);
        } else {
            return Response::setJsonContent([
                'error' => 0,
                'message' => 'Success'
            ]);
        }
    }

    /**
     * @routeGet('/saveTTDLPP')
     * @routePost('/saveTTDLPP')
     */
    public function saveTTDLPPAction()
    {
        // Array
        // (
        //     [_type] => create
        //     [id] => 1
        //     [LPP_judul_kiri] => Mengetahui, lpp
        //     [LPP_judul_tengah] => Yang Membuat, lpp
        //     [LPP_judul_kanan] => Tangerang, lpp
        //     [LPP_jabatan_kiri] => Keuangan lpp
        //     [LPP_jabatan_tengah] => Hublang lpp
        //     [LPP_jabatan_kanan] => lpp
        //     [LPP_nama_kiri] => John LPP
        //     [LPP_nama_tengah] => Daniel LPP
        //     [LPP_nama_kanan] => Andrew LPP
        //     [LPP_nik_kiri] => 123
        //     [LPP_nik_tengah] => 456
        //     [LPP_nik_kanan] => 789
        // )

        $id = $this->request->getPost('id');
        $LPP_judul_kiri         = $this->request->getPost('LPP_judul_kiri');
        $LPP_judul_tengah       = $this->request->getPost('LPP_judul_tengah');
        $LPP_judul_kanan        = $this->request->getPost('LPP_judul_kanan');
        $LPP_jabatan_kiri       = $this->request->getPost('LPP_jabatan_kiri');
        $LPP_jabatan_tengah     = $this->request->getPost('LPP_jabatan_tengah');
        $LPP_jabatan_kanan      = $this->request->getPost('LPP_jabatan_kanan');
        $LPP_nama_kiri          = $this->request->getPost('LPP_nama_kiri');
        $LPP_nama_tengah        = $this->request->getPost('LPP_nama_tengah');
        $LPP_nama_kanan         = $this->request->getPost('LPP_nama_kanan');
        $LPP_nik_kiri           = $this->request->getPost('LPP_nik_kiri');
        $LPP_nik_tengah         = $this->request->getPost('LPP_nik_tengah');
        $LPP_nik_kanan          = $this->request->getPost('LPP_nik_kanan');

        $sql = "CALL sp_billing_setting_app_update_ttd(
                        $id, 
                        '$LPP_judul_kiri', 
                        '$LPP_judul_tengah', 
                        '$LPP_judul_kanan', 
                        '', 
                        '', 
                        '$LPP_jabatan_kiri', 
                        '$LPP_jabatan_tengah', 
                        '$LPP_jabatan_kanan', 
                        '', 
                        '',
                        '$LPP_nama_kiri', 
                        '$LPP_nama_tengah', 
                        '$LPP_nama_kanan', 
                        '', 
                        '', 
                        '$LPP_nik_kiri', 
                        '$LPP_nik_tengah',  
                        '$LPP_nik_kanan', 
                        '', 
                        ''
                    )";
        $data = $this->db->fetchOne($sql);
        if ($data['stts'] == 1 || $data['stts'] == 0) {
            return Response::setJsonContent([
                'error' => 0,
                'message' => 'Success'
            ]);
        } else {
            return Response::setJsonContent([
                'error' => 0,
                'message' => 'Success'
            ]);
        }
    }

    /**
     * @routeGet('/saveTTDDRD')
     * @routePost('/saveTTDDRD')
     */
    public function saveTTDDRDAction()
    {
        $id = $this->request->getPost('id');
        $DRD_judul_kiri         = $this->request->getPost('DRD_judul_kiri');
        $DRD_judul_tengah       = $this->request->getPost('DRD_judul_tengah');
        $DRD_judul_kanan        = $this->request->getPost('DRD_judul_kanan');

        $DRD_judul_bawah_kiri         = $this->request->getPost('DRD_judul_bawah_kiri');
        $DRD_judul_bawah_tengah       = $this->request->getPost('DRD_judul_bawah_tengah');

        $DRD_jabatan_kiri       = $this->request->getPost('DRD_jabatan_kiri');
        $DRD_jabatan_tengah     = $this->request->getPost('DRD_jabatan_tengah');
        $DRD_jabatan_kanan      = $this->request->getPost('DRD_jabatan_kanan');

        $DRD_jabatan_bawah_kiri       = $this->request->getPost('DRD_jabatan_bawah_kiri');
        $DRD_jabatan_bawah_tengah     = $this->request->getPost('DRD_jabatan_bawah_tengah');

        $DRD_nama_kiri          = $this->request->getPost('DRD_nama_kiri');
        $DRD_nama_tengah        = $this->request->getPost('DRD_nama_tengah');
        $DRD_nama_kanan         = $this->request->getPost('DRD_nama_kanan');

        $DRD_nama_bawah_kiri          = $this->request->getPost('DRD_nama_bawah_kiri');
        $DRD_nama_bawah_tengah        = $this->request->getPost('DRD_nama_bawah_tengah');

        $DRD_nik_kiri           = $this->request->getPost('DRD_nik_kiri');
        $DRD_nik_tengah         = $this->request->getPost('DRD_nik_tengah');
        $DRD_nik_kanan          = $this->request->getPost('DRD_nik_kanan');

        $DRD_nik_bawah_kiri           = $this->request->getPost('DRD_nik_bawah_kiri');
        $DRD_nik_bawah_tengah         = $this->request->getPost('DRD_nik_bawah_tengah');

        $sql = "CALL sp_billing_setting_app_update_ttd(
                        $id, 
                        '$DRD_judul_kiri', 
                        '$DRD_judul_tengah', 
                        '$DRD_judul_kanan', 
                        '$DRD_judul_bawah_kiri', 
                        '$DRD_judul_bawah_tengah', 
                        '$DRD_jabatan_kiri', 
                        '$DRD_jabatan_tengah', 
                        '$DRD_jabatan_kanan', 
                        '$DRD_jabatan_bawah_kiri', 
                        '$DRD_jabatan_bawah_tengah',
                        '$DRD_nama_kiri', 
                        '$DRD_nama_tengah', 
                        '$DRD_nama_kanan', 
                        '$DRD_nama_bawah_kiri', 
                        '$DRD_nama_bawah_tengah', 
                        '$DRD_nik_kiri', 
                        '$DRD_nik_tengah',  
                        '$DRD_nik_kanan', 
                        '$DRD_nik_bawah_kiri', 
                        '$DRD_nik_bawah_tengah'
                    )";

        $data = $this->db->fetchOne($sql);
        if ($data['stts'] == 1 || $data['stts'] == 0) {
            return Response::setJsonContent([
                'error' => 0,
                'message' => 'Success'
            ]);
        } else {
            return Response::setJsonContent([
                'error' => 0,
                'message' => 'Success'
            ]);
        }
    }

    /**
     * @routeGet('/saveTTDDSR')
     * @routePost('/saveTTDDSR')
     */
    public function saveTTDDSRAction()
    {
        // Array
        // (
        //     [_type] => create
        //     [id] => 1
        //     [DSR_judul_kiri] => Mengetahui, lpp
        //     [DSR_judul_tengah] => Yang Membuat, lpp
        //     [DSR_judul_kanan] => Tangerang, lpp
        //     [DSR_jabatan_kiri] => Keuangan lpp
        //     [DSR_jabatan_tengah] => Hublang lpp
        //     [DSR_jabatan_kanan] => lpp
        //     [DSR_nama_kiri] => John LPP
        //     [DSR_nama_tengah] => Daniel LPP
        //     [DSR_nama_kanan] => Andrew LPP
        //     [DSR_nik_kiri] => 123
        //     [DSR_nik_tengah] => 456
        //     [DSR_nik_kanan] => 789
        // )

        $id = $this->request->getPost('id');
        $DSR_judul_kiri         = $this->request->getPost('DSR_judul_kiri');
        $DSR_judul_tengah       = $this->request->getPost('DSR_judul_tengah');
        $DSR_judul_kanan        = $this->request->getPost('DSR_judul_kanan');
        $DSR_jabatan_kiri       = $this->request->getPost('DSR_jabatan_kiri');
        $DSR_jabatan_tengah     = $this->request->getPost('DSR_jabatan_tengah');
        $DSR_jabatan_kanan      = $this->request->getPost('DSR_jabatan_kanan');
        $DSR_nama_kiri          = $this->request->getPost('DSR_nama_kiri');
        $DSR_nama_tengah        = $this->request->getPost('DSR_nama_tengah');
        $DSR_nama_kanan         = $this->request->getPost('DSR_nama_kanan');
        $DSR_nik_kiri           = $this->request->getPost('DSR_nik_kiri');
        $DSR_nik_tengah         = $this->request->getPost('DSR_nik_tengah');
        $DSR_nik_kanan          = $this->request->getPost('DSR_nik_kanan');

        $sql = "CALL sp_billing_setting_app_update_ttd(
                    $id, 
                    '$DSR_judul_kiri', 
                    '$DSR_judul_tengah', 
                    '$DSR_judul_kanan', 
                    '', 
                    '', 
                    '$DSR_jabatan_kiri', 
                    '$DSR_jabatan_tengah', 
                    '$DSR_jabatan_kanan', 
                    '', 
                    '',
                    '$DSR_nama_kiri', 
                    '$DSR_nama_tengah', 
                    '$DSR_nama_kanan', 
                    '', 
                    '', 
                    '$DSR_nik_kiri', 
                    '$DSR_nik_tengah',  
                    '$DSR_nik_kanan', 
                    '', 
                    ''
                )";
        $data = $this->db->fetchOne($sql);
        if ($data['stts'] == 1 || $data['stts'] == 0) {
            return Response::setJsonContent([
                'error' => 0,
                'message' => 'Success'
            ]);
        } else {
            return Response::setJsonContent([
                'error' => 0,
                'message' => 'Success'
            ]);
        }
    }

    /**
     * @routeGet('/uploadFile')
     * @routePost('/uploadFile')
     */
    public function uploadFileAction()
    {
        $jenis = $this->request->getPost('jenisFile');
        $date  = date('s');
        $kode = generateRandomstring(4);
        if ($this->request->hasFiles(true) == true) {
            $upload_dir = 'uploadDokumen/';
            $namaFile = 'logo_bumiwibawa_' . $kode;

            foreach ($this->request->getUploadedFiles() as $file) {
                if ($file->isUploadedFile()) {
                    $exploded = explode(".", $file->getName());
                    $extension = end($exploded);
                    $filename = $namaFile . '.' . $extension;
                    $file->moveTo($upload_dir . $filename);

                    $jason['status'] = 1;
                    $jason['filename'] = $filename;
                    echo json_encode($jason);
                } else {
                    $jason['status'] = 0;
                    echo json_encode($jason);
                }
            }
        }
    }

    /**
     * @routeGet('/saveTTDSPTINFOLANG')
     * @routePost('/saveTTDSPTINFOLANG')
     */
    public function saveTTDSPTINFOLANGAction()
    {

        $id = $this->request->getPost('id');
        $ofId = $this->session->user['of_id'];
        $INFOLANG_judul_kiri         = $this->request->getPost('INFOLANG_judul_kiri');
        $INFOLANG_judul_tengah       = $this->request->getPost('INFOLANG_judul_tengah');
        $INFOLANG_judul_kanan        = $this->request->getPost('INFOLANG_judul_kanan');
        $INFOLANG_jabatan_kiri       = $this->request->getPost('INFOLANG_jabatan_kiri');
        $INFOLANG_jabatan_tengah     = $this->request->getPost('INFOLANG_jabatan_tengah');
        $INFOLANG_jabatan_kanan      = $this->request->getPost('INFOLANG_jabatan_kanan');
        $INFOLANG_nama_kiri          = $this->request->getPost('INFOLANG_nama_kiri');
        $INFOLANG_nama_tengah        = $this->request->getPost('INFOLANG_nama_tengah');
        $INFOLANG_nama_kanan         = $this->request->getPost('INFOLANG_nama_kanan');
        $INFOLANG_nik_kiri           = $this->request->getPost('INFOLANG_nik_kiri');
        $INFOLANG_nik_tengah         = $this->request->getPost('INFOLANG_nik_tengah');
        $INFOLANG_nik_kanan          = $this->request->getPost('INFOLANG_nik_kanan');

        $sql = "CALL sp_billing_setting_app_update_ttd_nciho(
                        '$id', 
                        '$INFOLANG_judul_kiri', 
                        '$INFOLANG_judul_tengah', 
                        '$INFOLANG_judul_kanan', 
                        '', 
                        '', 
                        '$INFOLANG_jabatan_kiri', 
                        '$INFOLANG_jabatan_tengah', 
                        '$INFOLANG_jabatan_kanan', 
                        '', 
                        '',
                        '$INFOLANG_nama_kiri', 
                        '$INFOLANG_nama_tengah', 
                        '$INFOLANG_nama_kanan', 
                        '', 
                        '', 
                        '$INFOLANG_nik_kiri', 
                        '$INFOLANG_nik_tengah',  
                        '$INFOLANG_nik_kanan', 
                        '', 
                        '',
                        '$ofId'
                    )";
        $data = $this->db->fetchOne($sql);
        if ($data['stts'] == 1 || $data['stts'] == 0) {
            return Response::setJsonContent([
                'error' => 0,
                'message' => 'Success'
            ]);
        } else {
            return Response::setJsonContent([
                'error' => 0,
                'message' => 'Success'
            ]);
        }
    }
}
