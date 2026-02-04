<?php

declare(strict_types=1);

namespace App\Modules\Defaults\Portalpublic;

use Core\Facades\Request;
use Core\Facades\Response;
use Phalcon\Mvc\Controller as BaseController;
use App\Libraries\Log;
use App\Exceptions\NotFoundException;
use App\Modules\Defaults\Master\Kelurahan\ModelRegional;
use DateTime;
use App\Modules\Defaults\Middleware\Controller as MiddlewareHardController;
use App\Modules\Defaults\ModelStandAlone\TariftipeModel;
use App\Modules\Defaults\Pelanggan\Survey\Model;
use App\Modules\Defaults\ModelStandAlone\CabangModel as CabangModel;
use App\Modules\Defaults\ModelStandAlone\KecamatanModel as KecamatanModelV2;
use App\Modules\Defaults\Master\Kelurahan\Model as KelurahanModel;
use App\Modules\Defaults\Master\Blokjalan\ModelView as BlokjalanviewModel;
use App\Modules\Defaults\ModelStandAlone\PaketBarangModel;
use App\Modules\Defaults\ModelStandAlone\WatermetersizeModel;
use App\Modules\Defaults\Pelanggan\Daftar\Model as DefaultModel;
use App\Modules\Defaults\Pelanggan\TtdPelanggan\Model as TTDModel;

/**
 * @routeGroup('/Portalpublic')
 */
class Controller extends BaseController
{
    /**
     * @routeGet('/')
     */
    public function indexAction()
    {
        $this->view->setVar('module', 'pdam-tbw');
    }

    /**
     * @routePost('/loadData')
     * @routeGet('/loadData')
     */
    public function loadDataAction()
    {
        // Default Datatable 
        $search = $this->request->getPost('search')['value'];
        $limit = $this->request->getPost('length');
        $start = $this->request->getPost('start');

        // Get Session
        $sessOfId = $this->session->user['of_id'];
        $sessIsPusat = $this->session->user['is_pusat'];
        // $sessPdamId  = 6;
        $sessPdamId = 6;

        if ($sessIsPusat == 1) {
            $isOfId = 0;
            $ofId = $sessOfId;
        } else {
            $isOfId = 1;
            $ofId = $sessOfId;
        }

        // Default Val Tanggal Awal Dan Akir
        $tanggal1 = date("Y-m-d");
        $tanggal2 = date("Y-m-d");

        // Filter Tanggal Awal Dan Akhir
        $getTanggalAwal = $this->request->getPost('filter_tanggal_awal');
        $valTanggalAwal = (empty($getTanggalAwal) == true ? $tanggal1 : $getTanggalAwal);

        $getTanggalAkhir = $this->request->getPost('filter_tanggal_akhir');
        $valTanggalAkhir = (empty($getTanggalAkhir) == true ? $tanggal2 : $getTanggalAkhir);

        if (empty($getTanggalAwal) && empty($getTanggalAkhir)) {
            $isTanggal = 0;
        } else {
            $isTanggal = 1;
        }

        $custCode123 = $this->request->getPost('filter_nomor_pelanggan');
        $isCustCode123 = (empty($custCode123) == true ? 0 : 1);

        $periode = $this->request->getPost('filter_periode');
        $isPeriode = (empty($periode) == true ? 0 : 1);

        $tahunRaw = $this->request->getPost('filter_tahun');
        if ($tahunRaw == null ||$tahunRaw == '') {
            $tahun = date('Y');
        } else {
            $tahun = $tahunRaw;
        }
        
        $isTahun = 1;

        $valCabang = $this->request->getPost('filter_cabang');
        $isCabang = (empty($valCabang) == true ? 0 : 1);

        $valGolTarif = $this->request->getPost('filter_gol_tarif');
        $isGolTarif = (empty($valGolTarif) == true ? 0 : 1);

        $valJenisPel = $this->request->getPost('filter_jenis_pel');
        $isJenisPel = (empty($valJenisPel) == true ? 0 : 1);

        $valNomorReg = $this->request->getPost('filter_noreg');
        $isNomorReg = (empty($valNomorReg) == true ? 0 : 1);

        $valNamaPel = $this->request->getPost('filter_nama');
        $isNamaPel = (empty($valNamaPel) == true ? 0 : 1);

        $valSttsInput = $this->request->getPost('filter_stts_input', null, '');
        $isSttsInput = (empty($valSttsInput) == true ? 0 : 1);

        $sql_Counting = "CALL sp_billing_trans_pelangganbaru_count($isTanggal, '$valTanggalAwal', '$valTanggalAkhir', $isCustCode123, '$custCode123', $isPeriode, '$periode', $isCabang, '$valCabang', $isGolTarif, '$valGolTarif', $isJenisPel, '$valJenisPel', $isNomorReg, '$valNomorReg', $isNamaPel, '$valNamaPel', $isSttsInput, '$valSttsInput', $isTahun, '$tahun', '$sessPdamId')";

        $data = $this->db->fetchAll($sql_Counting);
        $totalData_Counting = $data[0]['jml_baris'];



        $sql_Data = "CALL sp_billing_trans_pelangganbaru($isTanggal, '$valTanggalAwal', '$valTanggalAkhir', $isCustCode123, '$custCode123', $isPeriode, '$periode', $isCabang, '$valCabang', $isGolTarif, '$valGolTarif', $isJenisPel, '$valJenisPel', $isNomorReg, '$valNomorReg', $isNamaPel, '$valNamaPel', $isSttsInput, '$valSttsInput', $isTahun, '$tahun', $limit, $start, $sessPdamId)";

        $resultData = $this->db->fetchAll($sql_Data);


        $json_data = array(
            "draw" => intval($this->request->getPost('draw')),
            "recordsTotal" => intval($totalData_Counting),
            "recordsFiltered" => intval($totalData_Counting),
            "data" => $resultData,
            "param" => Request::getPost(),
            "query" => $sql_Data,
        );

        return Response::setJsonContent($json_data);
    }

    /**
     * @routeGet('/add')
     */
    public function addAction()
    {
        $this->view->setVar('module', 'pdam-tbw');
        $pdamId = 6;
        $bulan_berjalan = date('m');
        $tahun_berjalan = date('Y');

        $sql = "CALL sp_billing_generate_noreg_pelbaru($pdamId, '$bulan_berjalan', '$tahun_berjalan')";
        $exec_query = $this->db->fetchAll($sql);
        $sess_data = $exec_query;

        if (!empty($sess_data)) {
            $numberUrut = $sess_data[0]['nomor_urut'];
            $periode = $sess_data[0]['bulan_no'] . $sess_data[0]['tahun_no'];
        } else {
            $numberUrut = "000001";
            $periode = date('m') . "" . date('Y');
        }

        $noreg = $numberUrut . $periode;
        $this->view->pdam_id = $pdamId;
        $this->view->nomorPengaduan = $noreg;
    }

    /**
     * @routeGet('/edit')
     */
    public function editAction()
    {
        $this->view->setVar('module', 'pdam-tbw');
        $pdamId = 6;
        $bulan_berjalan = date('m');
        $tahun_berjalan = date('Y');

        $id_edit = $this->request->get('id');
        $this->view->id_edit = $id_edit;
    }

    /**
     * @routeGet('/getDataPelangganBaruEdit')
     * @routePost('/getDataPelangganBaruEdit')
     */
    public function getDataPelangganBaruEditAction()
    {
        $id_pel_baru = $this->request->get('id');

        // Get Data Head
        $findData = Model::findFirst([
            'conditions' => "id = '$id_pel_baru'"
        ])->toArray();

        
        // Get Data Kecamatan
        $getDataKecamatan = KecamatanModelV2::findFirst([
            'conditions' => "id = '" . $findData['kecamatan'] . "'"
        ]);
        $findDataKecamatan = $getDataKecamatan->toArray();
        $findDataKecamatan['id'] = $findDataKecamatan['id'];
        $findDataKecamatan['text'] = $findDataKecamatan['nama_kec'];
        $findDataKecamatan['selected'] = true;
        
        // Get Data Kelurahan
        $getDataKelurahan = KelurahanModel::findFirst([
            'conditions' => "id = '" . $findData['kelurahan'] . "'"
        ]);
        $findDataKelurahan = $getDataKelurahan->toArray();
        $findDataKelurahan['id'] = $findDataKelurahan['id'];
        $findDataKelurahan['text'] = $findDataKelurahan['nama_kel'];
        $findDataKelurahan['selected'] = true;
        
        // echo "<pre>";
        // print_r($findDataKecamatan);
        // echo "</pre>";
        // die;

        //Get Data Cabang
        $findDataCabang = CabangModel::findFirst([
            'conditions' => "of_id = '" . $findData['of_id'] . "'"
        ])->toArray();
        $findDataCabang['id'] = $findDataCabang['of_id'];
        $findDataCabang['text'] = $findDataCabang['of_name'];
        $findDataCabang['selected'] = true;



        // Get Data Watermetersize
        $findDataWaterMeterSize = WatermetersizeModel::findFirst([
            'conditions' => "wmz_id = '" . $findData['cust_wmsizeid'] . "'"
        ])->toArray();
        $findDataWaterMeterSize['id'] = $findDataWaterMeterSize['wmz_id'];
        $findDataWaterMeterSize['text'] = doubleval($findDataWaterMeterSize['wmz_size']);
        $findDataWaterMeterSize['selected'] = true;

        //Get Paket Barang
        $getDataPaketBarang = PaketBarangModel::findFirst([
            'conditions' => "id_paket = '" . $findData['svy_paketbarang'] . "'"
        ]);
        if ($getDataPaketBarang) {
            $findDataPaketBarang = $getDataPaketBarang->toArray();
            $findDataPaketBarang['id'] = $findDataPaketBarang['id_paket'];
            $findDataPaketBarang['text'] = $findDataPaketBarang['nama_paket'];
            $findDataPaketBarang['selected'] = true;
        } else {
            $findDataPaketBarang = [];
        }

        return $this->response->setJsonContent([
            'hublang_pel_baru' => $findData,
            'kecamatan' => $findDataKecamatan,
            'kelurahan' => $findDataKelurahan,
            'office' => $findDataCabang,
            'paket_barang' => $findDataPaketBarang,
            'watermeter_size' => $findDataWaterMeterSize
        ]);
    }

    /**
     * @routePost('/deleteData')
     * @routeGet('/deleteData')
     */
    public function deleteDataAction()
    {
        $idPelanggan = $this->request->getPost('idPelanggan');
        $pdamId = 6;
        $custCode = $this->request->getPost('custCode');
        $custCode123 = $this->request->getPost('custCode123');

        $sql = "CALL sp_billing_trans_hublangpelangganbaru_delete($idPelanggan)";
        $resultAction = $this->db->fetchOne($sql);

        // $sql_del_cont = "CALL sp_billing_transv2_hublangpelangganbaru_delete_cont($pdamId, '$custCode123')";
        // $resultActionCont = $this->db->fetchOne($sql_del_cont);

        if ($resultAction['status'] == 1) {
            return Response::setJsonContent([
                'error' => 0,
                'message' => 'Success',
                'data' => $resultAction,
                'data_cont' => $resultAction
            ]);
        } else {
            return Response::setJsonContent([
                'error' => 1,
                'message' => 'Failed',
                'data' => $resultAction,
                'data_cont' => $resultAction
            ]);
        }
    }

     /**
     * @routePost('/updateData')
     * @routeGet('/updateData')
     */
    public function updateDataAction()
    {


        $pdamId = 6;

        $dateNow = date('Y-m-d');
        $tanggalInput = $dateNow;

        $inputby = $this->session->user['nama'];
        $inputbyof = $this->session->user['of_id'];
        $user_id = $this->session->user['id'];

        $id_edit = $this->request->getPost('id');
        $nomorBA = $this->request->getPost('no_registrasi');
        $tglDaftar = $this->request->getPost('tgl_daftar');
        $custName = htmlspecialchars($this->request->getPost('nama_pemohon'), ENT_QUOTES);
        $custTelpon = $this->request->getPost('nomor_hp');
        $email = $this->request->getPost('email');
        $noKTP = $this->request->getPost('nomor_ktp');
        $alamatKTP = htmlspecialchars($this->request->getPost('alamat_ktp'), ENT_QUOTES);
        $fotoKTP = $this->request->getPost('foto_ktp_text');
        $cabang = $this->request->getPost('cabang');
        $alamatPasang = htmlspecialchars($this->request->getPost('alamat_pasang'), ENT_QUOTES);
        $kecamatan = $this->request->getPost('kecamatan');
        $kelurahan = $this->request->getPost('kelurahan');
        $pekerjaan_pemohon = $this->request->getPost('pekerjaan_pemohon');
        $nomor_kk = $this->request->getPost('nomor_kk');
        $fotoKK = $this->request->getPost('foto_kk_text');


        $input_ByOfId = $inputbyof;
        $input_By = $inputby;

        $sql_InsertData = "CALL sp_billing_trans_hublangpelangganbaru_update_sukabumi(                
                '$id_edit',
                '$pdamId',
                '$nomorBA', 
                '$tglDaftar', 
                '$custName', 
                '$custTelpon', 
                '$email', 
                '$noKTP', 
                '$alamatKTP', 
                '$fotoKTP', 
                '$cabang', 
                '$alamatPasang', 
                '$kecamatan', 
                '$kelurahan', 
                '$pekerjaan_pemohon', 
                '$nomor_kk', 
                '$fotoKK',
                '$input_By',
                '$user_id'
            )";
            
        // echo '<pre>';
        // print_r($sql_InsertData);
        // echo '</pre>';
        // die();

        $data_InsertData = $this->db->fetchOne($sql_InsertData);

        return Response::setJsonContent([
            'error' => 0,
            'message' => 'Simpan data pelanggan baru sukses!',
            'data' => $data_InsertData,
            'sql' => $sql_InsertData,
        ]);
    }

    /**
     * @routePost('/saveData')
     * @routeGet('/saveData')
     */
    public function saveDataAction()
    {


        $pdamId = 6;

        $dateNow = date('Y-m-d');
        $tanggalInput = $dateNow;

        $inputby = $this->session->user['nama'];
        $inputbyof = $this->session->user['of_id'];
        $user_id = $this->session->user['id'];

        $nomorBA = $this->request->getPost('no_registrasi');
        $tglDaftar = $this->request->getPost('tgl_daftar');
        $custName = htmlspecialchars($this->request->getPost('nama_pemohon'), ENT_QUOTES);
        $custTelpon = $this->request->getPost('nomor_hp');
        $email = $this->request->getPost('email');
        $noKTP = $this->request->getPost('nomor_ktp');
        $alamatKTP = htmlspecialchars($this->request->getPost('alamat_ktp'), ENT_QUOTES);
        $fotoKTP = $this->request->getPost('foto_ktp_text');
        $cabang = $this->request->getPost('cabang');
        $alamatPasang = $this->request->getPost('addressSearch');
        $kecamatan = $this->request->getPost('kecamatan');
        $kelurahan = $this->request->getPost('kelurahan');
        $pekerjaan_pemohon = $this->request->getPost('pekerjaan_pemohon');
        $nomor_kk = $this->request->getPost('nomor_kk');
        $fotoKK = $this->request->getPost('foto_kk_text');


        $input_ByOfId = $inputbyof;
        $input_By = $inputby;

        $sql_InsertData = "CALL sp_billing_hublangpelangganbaru_insert_tbw_public(
                '$pdamId',
                '$nomorBA', 
                '$tglDaftar', 
                '$custName', 
                '$custTelpon', 
                '$email', 
                '$noKTP', 
                '$alamatKTP', 
                '$fotoKTP', 
                '$cabang', 
                '$alamatPasang', 
                '$kecamatan', 
                '$kelurahan', 
                '$pekerjaan_pemohon', 
                '$nomor_kk', 
                '$fotoKK'
            )";

        // echo '<pre>';
        // print_r($sql_InsertData);
        // echo '</pre>';
        // die();

        $data_InsertData = $this->db->fetchOne($sql_InsertData);

        return Response::setJsonContent([
            'error' => 0,
            'message' => 'Simpan data pelanggan baru sukses!',
            'data' => $data_InsertData,
            'sql' => $sql_InsertData,
        ]);
    }


    /**
     * @routePost('/printStruk')
     * @routeGet('/printStruk')
     */
    public function printStrukAction()
    {
        $pdamid = 6;

        $sql = "CALL sp_billing_profilesetting('$pdamid')";
        $data = $this->db->fetchAll($sql);
        $profile = $data;
        unset($data);



        $arr_billIdAir = $this->request->get('bill_id');
        $sql = "CALL sp_new_v2_kasir_payment_nonair_struke_custcode123('$arr_billIdAir')";
        //echo $sql;exit;
        $data = $this->db->fetchAll($sql);

        $this->view->data_struke = $data;
    }

    /**
     * @routePost('/uploadFotoKTP')
     */
    public function uploadFotoKTPAction()
    {
        $tanggal = date("Y_m_d_H_i_s");
        $folder = BASEPATH . '/public/tbw/ktp/';

        $file_type = array('PNG', 'JPG', 'jpg', 'png', 'Jpg', 'Png', 'JPEG', 'jpeg', 'Jpeg');
        $max_size = 10000000;

        $file_name = $_FILES['userfile']['name'];
        $file_size = $_FILES['userfile']['size'];
        $element_index = $this->request->getPost('element_index');

        $pra_extensi = (explode(".", $file_name));

        $id_user = $this->session->get('user')['id'];
        $nama_user = $this->session->get('user')['nama'];

        $pdam_id = 6;
        $bulan = date('m');
        $tahun = date('Y');

        $sql = "CALL sp_billing_generate_noreg_pelbaru($pdam_id, '$bulan', '$tahun')";
        $exec_query = $this->db->fetchAll($sql);
        $sess_data = $exec_query;

        $numberUrut = $sess_data[0]['nomor_urut'];
        $periode = $sess_data[0]['bulan_no'] . $sess_data[0]['tahun_no'];
        $noreg = $numberUrut . $periode;


        $extensi = end($pra_extensi);
        $filename = "identitas_{$noreg}.{$extensi}";

        if (!in_array($extensi, $file_type)) {

            $result = array(
                "error" => 1,
                "message" => "Extensi File Tidak Sesuai !",
                "data" => array()
            );

            $this->response->setContent(json_encode($result));
            return $this->response;
        } else if ($file_size > $max_size) {

            $result = array(
                "error" => 1,
                "message" => "Size File Melebihi Ketentuan !",
                "data" => array()
            );

            $this->response->setContent(json_encode($result));
            return $this->response;
        } else {
            if (file_exists($folder . $filename)) {
                unlink($folder . $filename);
            }

            if (move_uploaded_file($_FILES['userfile']['tmp_name'], $folder . $filename)) {

                $result = array(
                    "error" => 0,
                    "message" => "Upload File Sukses !",
                    "data" => array(
                        'filename' => $filename,
                        'element_index' => $element_index
                    )
                );
                $this->response->setContent(json_encode($result));
                return $this->response;
            } else {

                $result = array(
                    "error" => 1,
                    "message" => "Upload File gagal !",
                    "data" => array()
                );
                $this->response->setContent(json_encode($result));
                return $this->response;
            }
        }
    }

    /**
     * @routePost('/uploadFotoKK')
     */
    public function uploadFotoKKAction()
    {
        $tanggal = date("Y_m_d_H_i_s");
        $folder = BASEPATH . '/public/tbw/kk/';

        $file_type = array('PNG', 'JPG', 'jpg', 'png', 'Jpg', 'Png', 'JPEG', 'jpeg', 'Jpeg');
        $max_size = 10000000;

        $file_name = $_FILES['userfile']['name'];
        $file_size = $_FILES['userfile']['size'];
        $element_index = $this->request->getPost('element_index');
        $noreg = $this->request->getPost('noreg');

        $pra_extensi = (explode(".", $file_name));

        $id_user = $this->session->get('user')['id'];
        $nama_user = $this->session->get('user')['nama'];

        // $sql = "CALL sp_billing_generate_noreg_pelbaru()";
        // $exec_query = $this->db->fetchAll($sql);
        // $sess_data = $exec_query;

        // $numberUrut = $sess_data[0]['nomor_urut_berjalan'];
        // $periode = $sess_data[0]['bulan_no'] . $sess_data[0]['tahun_no'];
        // $noreg = $numberUrut . $periode;


        $extensi = end($pra_extensi);
        $filename = "kk_{$noreg}.{$extensi}";

        if (!in_array($extensi, $file_type)) {

            $result = array(
                "error" => 1,
                "message" => "Extensi File Tidak Sesuai !",
                "data" => array()
            );

            $this->response->setContent(json_encode($result));
            return $this->response;
        } else if ($file_size > $max_size) {

            $result = array(
                "error" => 1,
                "message" => "Size File Melebihi Ketentuan !",
                "data" => array()
            );

            $this->response->setContent(json_encode($result));
            return $this->response;
        } else {
            if (move_uploaded_file($_FILES['userfile']['tmp_name'], $folder . $filename)) {

                $result = array(
                    "error" => 0,
                    "message" => "Upload File Sukses !",
                    "data" => array(
                        'filename' => $filename,
                        'element_index' => $element_index
                    )
                );
                $this->response->setContent(json_encode($result));
                return $this->response;
            } else {

                $result = array(
                    "error" => 1,
                    "message" => "Upload File gagal !",
                    "data" => array()
                );
                $this->response->setContent(json_encode($result));
                return $this->response;
            }
        }
    }

    /**
     * @routeGet('/buktiDaftar')
     */
    public function buktiDaftarAction()
    {
        $this->view->setVar('module', 'pdam-tbw');
        $pdam_id = 6;
        $of_id = $this->session->user['of_id'];
        $idPelangganbaru = $this->request->get('id');

        $sql = "CALL sp_cater_profilesetting($pdam_id)";
        $data = $this->db->fetchAll($sql);
        $profile = $data;
        unset($data);

        $sql = "CALL sp_billing_hublangpelangganbaru_detail_sukabumi('$idPelangganbaru', '$pdam_id')";
        $data = $this->db->fetchAll($sql);

        $ttd_data1 = TTDModel::find([
            "of_id = '$of_id' AND jenis = '1.1' "
        ]);
        $this->view->ttd_data1 = $ttd_data1->toArray();
        

        $ttd_data2 = TTDModel::find([
            "of_id = '$of_id' AND jenis = '1.2' "
        ]);
        $this->view->ttd_data2 = $ttd_data2->toArray();

        $this->view->pdamid = $pdam_id;
        $this->view->data_profile = $profile;
        $this->view->data_pelanggan = $data[0];
        $this->view->gambar_kop = "kopsurat_harmony.jpg";
    }

    /**
     * @routeGet('/detail')
     * @routePost('/detail')
     */
    public function detailAction($id)
    {
        $this->view->setVar('module', $id);
        $pdamId = 6;
        $idPelangganbaru = $this->request->get('id');
        $noreg = $this->request->get('noreg');

        $sql = "CALL sp_billing_hublangpelangganbaru_detail_sukabumi('$idPelangganbaru', '$pdamId')";
        $data = $this->db->fetchAll($sql);


        $this->view->pdam_id = $pdamId;
        $this->view->nomorPengaduan = $noreg;
        $this->view->data_pelanggan = $data[0];
    }

    /**
     * @routePost('/createPdfLaporanHasilSurvey')
     * @routeGet('/createPdfLaporanHasilSurvey')
     */
    public function createPdfLaporanHasilSurveyAction()
    {
        $pdamid = 6;
        $idPelangganbaru = $this->request->get('id');
        $of_id = $this->session->user['of_id'];

        $sql = "CALL sp_billing_profilesetting('$pdamid')";
        $data = $this->db->fetchAll($sql);
        $profile = $data;
        unset($data);

        $sql = "CALL sp_billing_hublangpelangganbaru_detail_sukabumi('$idPelangganbaru', '$pdamid')";
        $data = $this->db->fetchAll($sql);

        $idPaketBarang = $data[0]['svy_paketbarang'];
        $sql2 = "CALL sp_billing_hublang_paketBarang('$idPaketBarang')";
        $data2 = $this->db->fetchAll($sql2);

        $ttd_data1 = TTDModel::find([
            "of_id = '$of_id' AND jenis = '2.2' "
        ]);
        $this->view->ttd_data1 = $ttd_data1->toArray();

        $this->view->data_pelanggan = $data[0];
        $this->view->data_barang = $data2;
        $this->view->data_profile = $profile;
        $this->view->pdamid = $pdamid;
    }

    /**
     * @routePost('/createPdfLaporanDetailRAB')
     * @routeGet('/createPdfLaporanDetailRAB')
     */
    public function createPdfLaporanDetailRABAction()
    {
        $pdamid = 6;

        $sql = "CALL sp_billing_profilesetting('$pdamid')";
        $data = $this->db->fetchAll($sql);
        $profile = $data;
        unset($data);

        $this->view->data_profile = $profile;
        $this->view->pdamid = $pdamid;
    }

    /**
     * @routeGet('/cetakPemasangan')
     */
    public function cetakPemasanganAction($id)
    {
        $this->view->setVar('module', $id);
        $pdam_id = 6;
        $idPelangganbaru = $this->request->get('id');

        $sql = "CALL sp_cater_profilesetting($pdam_id)";
        $data = $this->db->fetchAll($sql);
        $profile = $data;
        unset($data);

        $sql = "CALL sp_billing_hublangpelangganbaru_detail_sukabumi('$idPelangganbaru', '$pdam_id')";
        $data = $this->db->fetchAll($sql);


        $this->view->pdamid = $pdam_id;
        $this->view->data_profile = $profile;
        $this->view->data_pelanggan = $data[0];
        $this->view->gambar_kop = "kopsurat_harmony.jpg";
    }

    /**
     * @routeGet('/cetakSPK')
     */
    public function cetakSPKAction($id)
    {
        $this->view->setVar('module', $id);
        $pdam_id = 6;
        $idPelangganbaru = $this->request->get('id');
        $of_id = $this->session->user['of_id'];

        $sql = "CALL sp_cater_profilesetting($pdam_id)";
        $data = $this->db->fetchAll($sql);
        $profile = $data;
        unset($data);

        $sql = "CALL sp_billing_hublangpelangganbaru_detail_sukabumi('$idPelangganbaru', '$pdam_id')";
        $data = $this->db->fetchAll($sql);

        $idPaketBarang = $data[0]['svy_paketbarang'];
        $sql2 = "CALL sp_billing_hublang_paketBarang('$idPaketBarang')";
        $data2 = $this->db->fetchAll($sql2);

        $ttd_data1 = TTDModel::find([
            "of_id = '$of_id' AND jenis = '3.1' "
        ]);
        $this->view->ttd_data1 = $ttd_data1->toArray();

        $ttd_data2 = TTDModel::find([
            "of_id = '$of_id' AND jenis = '3.2' "
        ]);
        $this->view->ttd_data2 = $ttd_data2->toArray();

        $ttd_data3 = TTDModel::find([
            "of_id = '$of_id' AND jenis = '3.3' "
        ]);
        $this->view->ttd_data3 = $ttd_data3->toArray();

        $this->view->pdamid = $pdam_id;
        $this->view->data_barang = $data2;
        $this->view->data_profile = $profile;
        $this->view->data_pelanggan = $data[0];
        $this->view->gambar_kop = "kopsurat_harmony.jpg";
    }

    /**
     * @routeGet('/done')
     */
    public function doneAction()
    {
        $this->view->setVar('module', 'pdam-tbw');

        $pb = $this->request->getQuery('no_registrasi');

        // Gunakan ID untuk mengambil data dari basis data
        $latestData = Model::findFirst(
            "no_reg = '$pb'"
        );
        $latestData = $latestData->toArray();

        $this->view->pb = $latestData;
        // json_encode($latestData);die;
    }
}
