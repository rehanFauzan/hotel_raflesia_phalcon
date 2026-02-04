<?php

declare(strict_types=1);

namespace App\Modules\Defaults\RefSelect2;

// use Phalcon\Mvc\Controller as BaseController;
use App\Modules\Defaults\BaseController;
use Core\Facades\Response;
use App\Modules\Defaults\Aktiva\AktivaTarif\Model as AktivaTarifModel;
use App\Modules\Defaults\ReferensiData\Golongan\Model as GolonganModel;
use App\Modules\Defaults\ReferensiData\Kelompok\Model as KelompokModel;
use App\Modules\Defaults\ReferensiData\Perkiraan\Model as PerkiraanModel;
use App\Modules\Defaults\ReferensiData\SatuanKerja\Model as SatuanKerjaModel;
use App\Modules\Defaults\ReferensiData\Ajuan\Model as AjuanUntukModel;
use App\Modules\Defaults\ReferensiData\Ajuan\Model as AjuanModel;
use App\Modules\Defaults\RefSelect2\MasterKategoriBarangModel;
use App\Modules\Defaults\RefSelect2\MasterGolonganTarifModel;
use App\Modules\Defaults\RefSelect2\MasterTypeLayananModel;
use App\Modules\Defaults\RefSelect2\MasterSatkerInventoriModel;
use App\Modules\Defaults\RefSelect2\VwEmployeeModel;
use App\Modules\Defaults\TransJurnal\Sumberkontrak\Model as KontrakModel;
use App\Modules\Defaults\TransJurnal\Sumberkontrak\ModelView as KontrakViewModel;
use App\Modules\Defaults\RefSelect2\MasterAkunPerkiraanMappingViewModel as  MapAkunMappingViewModel;

/**
 * @routeGroup('/refselect2')
 * @middleware('RequireUser')
 */
class Controller extends BaseController
{

    /**
     * @routeGet("/role-s2")
     */
    public function indexAction()
    {
        echo '[default] /referensi/role-s2';
    }


    /**
     * @routeGet('/getGolonganS2')
     * @routePost('/getGolonganS2')
     */
    public function getGolonganS2Action()
    {

        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;
        $kode_gol = $this->request->get('kode_gol');

        if (isset($kode_gol) && !empty($kode_gol)) {
            $data = GolonganModel::find(
                array(
                    'limit'     => 21,
                    'offset'    => $offset,
                    'conditions' => "(acc_name LIKE '%$nama%' OR acc_code LIKE '%$nama%') AND acc_code = '" . $kode_gol . "'"
                )
            );
        } else {
            $data = GolonganModel::find(
                array(
                    'limit'     => 21,
                    'offset'    => $offset,
                    'conditions' => "acc_name LIKE '%$nama%' OR acc_code LIKE '%$nama%'"
                )
            );
        }

        $data_array = $data->toArray();
        $has_more = count($data_array);
        $json_data = array(
            "data" => $data_array,
            "has_more" => $has_more,
        );
        echo json_encode($json_data);
    }


    /**
     * @routeGet('/getKelompokS2FilterKelompok')
     * @routePost('/getKelompokS2FilterKelompok')
     */
    public function getKelompokS2FilterKelompokAction()
    {
        $kode_kelompok = $this->request->get('kode_kelompok');

        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;

        $data = KelompokModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                // 'conditions' => "acc_name LIKE '%$nama%' OR acc_code LIKE '%$nama%'"
                'conditions' => "acc_code LIKE '$kode_kelompok%'"
            )
        );

        $data_array = $data->toArray();
        $has_more = count($data_array);
        $json_data = array(
            "data" => $data_array,
            "has_more" => $has_more,
        );
        echo json_encode($json_data);
    }


    /**
     * @routeGet('/getKelompokS2')
     * @routePost('/getKelompokS2')
     */
    public function getKelompokS2Action()
    {

        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;

        $data = KelompokModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "acc_name LIKE '%$nama%' OR acc_code LIKE '%$nama%'"
            )
        );

        $data_array = $data->toArray();
        $has_more = count($data_array);
        $json_data = array(
            "data" => $data_array,
            "has_more" => $has_more,
        );
        echo json_encode($json_data);
    }

    /**
     * @routeGet('/getPerkiraanS2')
     * @routePost('/getPerkiraanS2')
     */
    public function getPerkiraanS2Action()
    {

        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;

        $data = PerkiraanModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "acc_name LIKE '%$nama%' OR acc_code LIKE '%$nama%'"
            )
        );

        $data_array = $data->toArray();
        $has_more = count($data_array);
        $json_data = array(
            "data" => $data_array,
            "has_more" => $has_more,
        );
        echo json_encode($json_data);
    }


    /**
     * @routeGet('/getDataSumberKontrakS2')
     * @routePost('/getDataSumberKontrakS2')
     */
    public function getDataSumberKontrakS2Action()
    {

        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;
        $data = KontrakViewModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "(no_kontrak LIKE '%$nama%' OR kode_supplier LIKE '%$nama%') AND ba_nomor IS NULL"
            )
        );

        $data_array = $data->toArray();
        $has_more = count($data_array);
        $json_data = array(
            "data" => $data_array,
            "has_more" => $has_more,
        );
        echo json_encode($json_data);
    }


    /**
     * @routeGet('/getDataSumberKontrakBelumLunasS2')
     * @routePost('/getDataSumberKontrakBelumLunasS2')
     */
    public function getDataSumberKontrakBelumLunasS2Action()
    {

        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;
        $data = KontrakViewModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "(no_kontrak LIKE '%$nama%' OR kode_supplier LIKE '%$nama%') AND ba_nomor IS NULL"
            )
        );

        $data_array = $data->toArray();
        $has_more = count($data_array);
        $json_data = array(
            "data" => $data_array,
            "has_more" => $has_more,
        );
        echo json_encode($json_data);
    }

    /**
     * @routeGet('/getPerkiraanAktivaS2')
     * @routePost('/getPerkiraanAktivaS2')
     */
    public function getPerkiraanAktivaS2Action()
    {

        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;

        $data = PerkiraanModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "(acc_name LIKE '%$nama%' OR acc_code LIKE '%$nama%') AND is_aktiva = 1"
            )
        );

        $data_array = $data->toArray();
        $has_more = count($data_array);
        $json_data = array(
            "data" => $data_array,
            "has_more" => $has_more,
        );
        echo json_encode($json_data);
    }


    /**
     * @routeGet('/getPerkiraanMappingJenis3S2')
     * @routePost('/getPerkiraanMappingJenis3S2')
     */
    public function getPerkiraanMappingJenis3S2Action()
    {

        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;

        $data = MasterAkunPerkiraanMappingViewModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "(acc_name LIKE '%$nama%' OR acc_code LIKE '%$nama%') AND jenis = 3"
            )
        );

        $data_array = $data->toArray();
        $has_more = count($data_array);
        $json_data = array(
            "data" => $data_array,
            "has_more" => $has_more,
        );
        echo json_encode($json_data);
    }


    /**
     * @routeGet('/getPerkiraanMappingJenis4S2')
     * @routePost('/getPerkiraanMappingJenis4S2')
     */
    public function getPerkiraanMappingJenis4S2Action()
    {
        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;

        $data = MasterAkunPerkiraanMappingViewModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "(acc_name LIKE '%$nama%' OR acc_code LIKE '%$nama%') AND jenis = 4"
            )
        );

        $data_array = $data->toArray();
        $has_more = count($data_array);
        $json_data = array(
            "data" => $data_array,
            "has_more" => $has_more,
        );
        echo json_encode($json_data);
    }


    /**
     * @routeGet('/getPerkiraanMappingPpnPphS2')
     * @routePost('/getPerkiraanMappingPpnPphS2')
     */
    public function getPerkiraanMappingPpnPphS2Action()
    {
        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;
        $gunakan_ppn_pph = $this->request->get('gunakan_ppn_pph');

        $string_jenis = "";
        if ($gunakan_ppn_pph == 1) {
            $string_jenis = " AND jenis = 6";
        } else if ($gunakan_ppn_pph == 2) {
            $string_jenis = " AND jenis = 7";
        } else {
            $string_jenis = "";
        }

        $data = MasterAkunPerkiraanMappingViewModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "(acc_name LIKE '%$nama%' OR acc_code LIKE '%$nama%') $string_jenis"
            )
        );

        $data_array = $data->toArray();
        $has_more = count($data_array);
        $json_data = array(
            "data" => $data_array,
            "has_more" => $has_more,
        );
        echo json_encode($json_data);
    }


    /**
     * @routeGet('/getAktivaTarifS2')
     * @routePost('/getAktivaTarifS2')
     */
    public function getAktivaTarifS2Action()
    {

        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;

        $data = AktivaTarifModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "kode_gol LIKE '%$nama%' OR tarif LIKE '%$nama%' OR uraian LIKE '%$nama%'"
            )
        );

        $data_array = $data->toArray();
        $has_more = count($data_array);
        $json_data = array(
            "data" => $data_array,
            "has_more" => $has_more,
        );
        echo json_encode($json_data);
    }


    /**
     * @routeGet('/getAktivaTarifRawS2')
     * @routePost('/getAktivaTarifRawS2')
     */
    public function getAktivaTarifRawS2Action()
    {
        $acc_code = $this->request->get('acc_code');

        $data = VwAktivaTarifMappingAkunModel::findFirst(
            array(
                'conditions' => "acc_code = '$acc_code'"
            )
        );

        if ($data) {
            $dataArray = $data->toArray();
            return $this->response->setJsonContent([
                'error' => 0,
                'message' => 'Data ditemukan',
                'data' => $dataArray
            ]);
        } else {
            return $this->response->setJsonContent([
                'error' => 1,
                'message' => 'Data tidak ditemukan',
                'data' => []
            ]);
        }
    }


    /**
     * @routeGet('/getPerkiraanUangMukaKerjaDebitS2')
     * @routePost('/getPerkiraanUangMukaKerjaDebitS2')
     */
    public function getPerkiraanUangMukaKerjaDebitS2Action()
    {

        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;

        $data = VwMasterPerkiraanUangMukaKerjaModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "(acc_name LIKE '%$nama%' OR acc_code LIKE '%$nama%') AND jenis = 1"
            )
        );

        $data_array = $data->toArray();
        $has_more = count($data_array);
        $json_data = array(
            "data" => $data_array,
            "has_more" => $has_more,
        );
        echo json_encode($json_data);
    }


    /**
     * @routeGet('/getPerkiraanUangMukaKerjaKreditS2')
     * @routePost('/getPerkiraanUangMukaKerjaKreditS2')
     */
    public function getPerkiraanUangMukaKerjaKreditS2Action()
    {

        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;

        $data = VwMasterPerkiraanUangMukaKerjaModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "(acc_name LIKE '%$nama%' OR acc_code LIKE '%$nama%') AND jenis = 2"
            )
        );

        $data_array = $data->toArray();
        $has_more = count($data_array);
        $json_data = array(
            "data" => $data_array,
            "has_more" => $has_more,
        );
        echo json_encode($json_data);
    }

    /**
     * @routeGet('/getDataMasterSaketapS2')
     * @routePost('/getDataMasterSaketapS2')
     */
    public function getDataMasterSaketapS2Action()
    {
        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;

        $data = MasterKodeSaketapModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "kdsub_etap LIKE '%$nama%' OR sub_etap LIKE '%$nama%'"
            )
        );

        $data_array = $data->toArray();
        $has_more = count($data_array);
        $json_data = array(
            "data" => $data_array,
            "has_more" => $has_more,
        );
        echo json_encode($json_data);
    }


    /**
     * @routeGet('/getReffJurnalS2')
     * @routePost('/getReffJurnalS2')
     */
    public function getReffJurnalS2Action()
    {

        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;

        $data = ReffJurnalModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "nama_jurnal LIKE '%$nama%'"
            )
        );

        $data_array = $data->toArray();
        $has_more = count($data_array);
        $json_data = array(
            "data" => $data_array,
            "has_more" => $has_more,
        );
        echo json_encode($json_data);
    }

    /**
     * @routeGet('/getReffTtdLapS2')
     * @routePost('/getReffTtdLapS2')
     */
    public function getReffTtdLapS2Action()
    {

        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;

        $data = ReffTtdLapModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "nama_lap LIKE '%$nama%' OR kelompok LIKE '%$nama%'"
            )
        );

        $data_array = $data->toArray();
        $has_more = count($data_array);
        $json_data = array(
            "data" => $data_array,
            "has_more" => $has_more,
        );
        echo json_encode($json_data);
    }


    /**
     * @routeGet('/getDataAjuanDariS2')
     * @routePost('/getDataAjuanDariS2')
     */
    public function getDataAjuanDariS2Action()
    {

        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;

        $data = AjuanUntukModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "kode LIKE '%$nama%' OR nama LIKE '%$nama%'"
            )
        );

        $data_array = $data->toArray();
        $has_more = count($data_array);
        $json_data = array(
            "data" => $data_array,
            "has_more" => $has_more,
        );
        echo json_encode($json_data);
    }


    /**
     * @routeGet('/getDataAjuanDariBukanRekananS2')
     * @routePost('/getDataAjuanDariBukanRekananS2')
     */
    public function getDataAjuanDariBukanRekananS2Action()
    {

        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;

        $data = AjuanUntukModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "(kode LIKE '%$nama%' OR nama LIKE '%$nama%') AND is_rekanan = 0"
            )
        );

        $data_array = $data->toArray();
        $has_more = count($data_array);
        $json_data = array(
            "data" => $data_array,
            "has_more" => $has_more,
        );
        echo json_encode($json_data);
    }


    /**
     * @routeGet('/getDataAjuanDariRekananS2')
     * @routePost('/getDataAjuanDariRekananS2')
     */
    public function getDataAjuanDariRekananS2Action()
    {

        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;

        $data = AjuanUntukModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "(kode LIKE '%$nama%' OR nama LIKE '%$nama%') AND is_rekanan = 1"
            )
        );

        $data_array = $data->toArray();
        $has_more = count($data_array);
        $json_data = array(
            "data" => $data_array,
            "has_more" => $has_more,
        );
        echo json_encode($json_data);
    }


    /**
     * @routeGet('/getDataAjuanUntukS2')
     * @routePost('/getDataAjuanUntukS2')
     */
    public function getDataAjuanUntukS2Action()
    {
        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;

        $data = AjuanUntukModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "kode LIKE '%$nama%' OR nama LIKE '%$nama%'"
            )
        );

        $data_array = $data->toArray();
        $has_more = count($data_array);
        $json_data = array(
            "data" => $data_array,
            "has_more" => $has_more,
        );
        echo json_encode($json_data);
    }


    /**
     * @routeGet('/getDataSatkerS2')
     * @routePost('/getDataSatkerS2')
     */
    public function getDataSatkerS2Action()
    {
        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;

        $data = SatuanKerjaModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "kode_satker LIKE '%$nama%' OR nama_satker LIKE '%$nama%'"
            )
        );

        $data_array = $data->toArray();
        $has_more = count($data_array);
        $json_data = array(
            "data" => $data_array,
            "has_more" => $has_more,
        );
        echo json_encode($json_data);
    }


    /**
     * @routeGet('/generateNomorReffJurnal')
     * @routePost('/generateNomorReffJurnal')
     */
    public function generateNomorReffJurnalAction()
    {
        $tgl_surat = $this->request->getPost('tgl_surat');
        $tahunNow = date('Y', strtotime($tgl_surat));
        $bulanNow = date('n', strtotime($tgl_surat));
        $type = $this->request->getPost('type');

        /* Old With Sql Server
            $sql = "
                SET NOCOUNT ON; 
                EXEC akuntansi.sp_generate_number_v2
                    @vyear = '$tahunNow',
                    @vmonth = '$bulanNow',
                    @vtype = '$type',
                    @vofficeID = '0';
            ";
            $result = $this->db->fetchOne($sql);
        */


        $result = $this->sp->call('sp_generate_number_v2', [
            'vyear' => $tahunNow,
            'vmonth' => $bulanNow,
            'vtype' => $type,
            'vofficeID' => '0'
        ])->fetchOne();
        
        if ($result && isset($result['number']) && !empty($result['number'])) {
            $error_code = 0;
            $resultNomorOtomatis = $result['number'];
            $message = "Berhasil Generate Nomor Otomatis";
        } else {
            $error_code = 1;
            $resultNomorOtomatis = "-";
            $message = "Gagal mendapatkan nomor otomatis";
        }

        return $this->response->setJsonContent([
            'error' => $error_code,
            'message' => $message,
            'result_nomor' => $resultNomorOtomatis
        ]);
    }


    /**
     * @routePost('/getNilaiAnggaranS2')
     */
    public function getNilaiAnggaranS2Action()
    {
        if ($this->request->isPost() && $this->request->isAjax()) {

            // echo "<pre>";
            // print_r($this->request->getPost());
            // echo "</pre>";
            // exit;

            // <pre>Array
            // (
            //     [acc_code] => 11.01.01.001
            //     [kode_satker] => (01) TELUK BUYUNG
            //     [tanggal] => 2025-12-09
            // )
            // </pre>

            $acc_code = $this->request->getPost('acc_code');
            $tanggal = $this->request->getPost('tanggal');
            $kode_satker = $this->request->getPost('kode_satker');

            if (!isset($tanggal) && empty($tanggal)) {
                $tanggal = date('Y-m-d');
            } else {
                $tanggal = $tanggal;
            }

            $kode_satker = preg_replace('/[^0-9]/', '', $kode_satker);

            $bulan = date('m', strtotime($tanggal));
            $tahun = date('Y', strtotime($tanggal));

            $is_biaya = "X";
            $akun_prefix = intval(substr($acc_code, 0, 2));

            if ($akun_prefix < 90) {
                $is_biaya = "0";
            } else {
                $is_biaya = "1";
            }

            // Get Data 1
            /* Old With Sql Server
                $sql = "SET NOCOUNT ON; EXEC [akuntansi].sp_anggaran_kontrol_verifikasi_get_nilai
                            @vsatker    = '" . $kode_satker . "',
                            @vacc_code  = '" . $acc_code . "',
                            @vtahun     = " . $tahun . ",
                            @vis_biaya  = " . $is_biaya . ";
                ";
                $resultData = $this->db->fetchAll($sql);
            */
            
            $resultData = $this->sp->call('sp_anggaran_kontrol_verifikasi_get_nilai', [
                'vsatker' => $kode_satker,
                'vacc_code' => $acc_code,
                'vtahun' => $tahun,
                'vis_biaya' => $is_biaya
            ])->fetchAll();
            $queryLog = $this->sp->getDatabaseSP()->getQueryLog();

            if (count($resultData) > 0) {

                if ($resultData[2]['total'] < 0) {
                    $anggaran_sudah_digunakan = $resultData[0]['total'] - abs($resultData[2]['total']);
                } else {
                    $anggaran_sudah_digunakan = $resultData[0]['total'] - $resultData[2]['total'];
                }

                return $this->response->setJsonContent([
                    'error' => 0,
                    'message' => "Berhasil mengambil data",
                    'dataFetch' => [
                        'anggaran_total' => $resultData[0]['total'],
                        'anggaran_sudah_digunakan' =>  $anggaran_sudah_digunakan,
                        // 'anggaran_sudah_digunakan' => $resultData[1]['total'],
                        'anggaran_sudah_realisasi' => $resultData[2]['total']
                    ],
                    'sql' => $queryLog
                ]);
            } else {
                return $this->response->setJsonContent([
                    'error' => 1,
                    'message' => "Gagal mengambil data / data tidak tersedia",
                    'dataFetch' => [
                        'anggaran_total' => 0,
                        'anggaran_sudah_digunakan' => 0,
                        'anggaran_sudah_realisasi' => 0
                    ]
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
     * @routeGet('/getDataAkunStatusS2')
     * @routePost('/getDataAkunStatusS2')
     */
    public function getDataAkunStatusS2Action()
    {
        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;
        $jt_id = $this->request->get('jt_id');

        if (isset($jt_id) && !empty($jt_id)) {
            $data = VwMappingAkunJurnalModel::find(
                array(
                    'limit'     => 21,
                    'offset'    => $offset,
                    'conditions' => "(LOWER(acc_code) LIKE '%$nama%') AND jt_id = $jt_id"
                )
            );
        } else {
            $data = VwMappingAkunJurnalModel::find(
                array(
                    'limit'     => 21,
                    'offset'    => $offset,
                    'conditions' => "acc_code LIKE '%$nama%'"
                )
            );
        }

        $data_array = $data->toArray();
        $has_more = count($data_array);
        $json_data = array(
            "data" => $data_array,
            "has_more" => $has_more,
        );
        echo json_encode($json_data);
    }

    /**
     * @routeGet('/getDataAkunJbkS2')
     * @routePost('/getDataAkunJbkS2')
     */
    public function getDataAkunJbkS2Action()
    {
        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;

        $data = VwMappingAkunJurnalModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "(acc_code LIKE '%$nama%' OR acc_name LIKE '%$nama%') AND (acc_code LIKE '11.01%')"
            )
        );

        $data_array = $data->toArray();
        $has_more = count($data_array);
        $json_data = array(
            "data" => $data_array,
            "has_more" => $has_more,
        );
        echo json_encode($json_data);
    }


    /**
     * @routeGet('/getDataAkunJbkS2v2')
     * @routePost('/getDataAkunJbkS2v2')
     */
    public function getDataAkunJbkS2v2Action()
    {
        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;

        $data = PerkiraanModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "(acc_code LIKE '%$nama%' OR acc_name LIKE '%$nama%') AND (acc_code LIKE '11.01%')"
            )
        );

        $data_array = $data->toArray();
        $has_more = count($data_array);
        $json_data = array(
            "data" => $data_array,
            "has_more" => $has_more,
        );
        echo json_encode($json_data);
    }


    /**
     * @routeGet('/getDataAkunJbkS2v3')
     * @routePost('/getDataAkunJbkS2v3')
     */
    public function getDataAkunJbkS2v3Action()
    {
        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;

        $data = MapAkunMappingViewModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "(acc_code LIKE '%$nama%' OR acc_name LIKE '%$nama%') AND jenis = 3"
            )
        );

        $data_array = $data->toArray();
        $has_more = count($data_array);
        $json_data = array(
            "data" => $data_array,
            "has_more" => $has_more,
        );
        echo json_encode($json_data);
    }


    /**
     * @routeGet('/getDataLoketLppBillingRaw')
     * @routePost('/getDataLoketLppBillingRaw')
     */
    public function getDataLoketLppBillingRawAction()
    {
        if ($this->request->isPost() && $this->request->isAjax()) {
            $jenis_loket = $this->request->getPost('jenis_loket');

            $sql = "
                SET NOCOUNT ON;
                EXEC akuntansi.transjurnal_sync_lpp_jpk_get_loket 
                    @vtype = $jenis_loket;
            ";
            $resultDb = $this->db->fetchAll($sql);

            if (count($resultDb) > 0) {
                return $this->response->setJsonContent([
                    'error' => 0,
                    'message' => 'Success get data loket',
                    'data' => $resultDb
                ]);
            } else {
                return $this->response->setJsonContent([
                    'error' => 1,
                    'message' => 'Failed get data loket',
                    'data' => []
                ]);
            }
        } else {
            return $this->response->setJsonContent([
                'error' => 1,
                'message' => 'Method not allowed',
                'data' => []
            ]);
        }
    }


    /**
     * @routeGet('/getDataLoketLppBillingS2')
     * @routePost('/getDataLoketLppBillingS2')
     */
    public function getDataLoketLppBillingS2Action()
    {

        // Ambil parameter pencarian dari request (untuk select2)
        $search = $this->request->get('q', 'string', '');
        $page = (int) $this->request->get('page', 'int', 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;
        $type = (int) $this->request->get('type');


        // Query ke stored procedure
        $sql = "
            SET NOCOUNT ON;
            EXEC akuntansi.transjurnal_sync_lpp_jpk_get_loket 
                @vtype = $type;
        ";
        $resultDb = $this->db->fetchAll($sql);

        // Filter hasil jika ada pencarian
        $filtered = [];
        if ($search !== '') {
            foreach ($resultDb as $row) {
                if (
                    stripos($row['nama'], $search) !== false
                ) {
                    $filtered[] = $row;
                }
            }
        } else {
            $filtered = $resultDb;
        }


        // Pagination manual
        $total = count($filtered);
        $data = array_slice($filtered, $offset, $limit);

        // Format untuk select2
        $results = [];
        foreach ($data as $row) {
            $results[] = [
                'id' => $row['id'],
                'nama' => $row['nama'],
                'text' => $row['nama'],
                // Jika ingin mengirim data tambahan:
                'data' => $row
            ];
        }

        $has_more = ($offset + $limit) < $total;

        $json_data = [
            'results' => $results,
            'pagination' => [
                'more' => $has_more
            ]
        ];

        echo json_encode($json_data);
    }

    /**
     * @routeGet('/getDataVoucherBelumBayar')
     * @routePost('/getDataVoucherBelumBayar')
     */
    public function getDataVoucherBelumBayarAction()
    {
        // Ambil parameter pencarian dari request (untuk select2)
        $search = $this->request->get('q', 'string', '');
        $page = (int) $this->request->get('page', 'int', 1);
        $is_memo = $this->request->get('is_memo', 'int', 0);
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $m_periode = $this->session->m_periode;
        $y_periode = $this->session->y_periode;

        // Query ke stored procedure
        /* 
            $sql = "
                SET NOCOUNT ON;
                EXEC akuntansi.sp_jbk_load_belumbayar 
                    @mperiod = '$m_periode',
                    @yperiod = '$y_periode'
                ;
            ";
            $resultDb = $this->db->fetchAll($sql);
        */

        try {
            $resultDb = $this->sp->call('sp_jbk_load_belumbayar', [
                'mperiod' => $m_periode,
                'yperiod' => $y_periode
            ])->fetchAll();

            $queryLog = $this->sp->getDatabaseSP()->getQueryLog();
        } catch (\Exception $e) {
            // Log query untuk debugging
            $queryLog = $this->sp->getDatabaseSP()->getQueryLog();
            error_log("DatabaseSP Query Log: " . print_r($queryLog, true));
            error_log("Error: " . $e->getMessage());
            throw $e;
        }


        if ($is_memo == 1) {
            // Filter dimana jenis_memo == 2
            $resultDb = array_filter($resultDb, function ($row) {
                return $row['jenis_memo'] == 2;
            });

            $resultDb = array_values($resultDb);
        } else {
            // ambil yang jenis memo nya tidak sama dengan 2 
            $resultDb = array_filter($resultDb, function ($row) {
                return $row['jenis_memo'] != 2;
            });

            $resultDb = array_values($resultDb);
        }

        // Filter hasil jika ada pencarian
        $filtered = [];
        if ($search !== '') {
            foreach ($resultDb as $row) {
                if (
                    stripos($row['mjo_code'], $search) !== false ||
                    stripos($row['mjo_desc'], $search) !== false ||
                    stripos($row['mjo_ajuan_dari'], $search) !== false ||
                    stripos($row['mjo_peruntukan'], $search) !== false
                ) {
                    $filtered[] = $row;
                }
            }
        } else {
            $filtered = $resultDb;
        }

        // Pagination manual
        $total = count($filtered);
        $data = array_slice($filtered, $offset, $limit);

        // Format untuk select2
        $results = [];
        foreach ($data as $row) {
            $results[] = [
                'id' => $row['mjo_id'],
                'text' => sprintf(
                    '[%s] %s | %s | %s | %s | %s | Rp%s',
                    $row['mjo_code'],
                    $row['mjo_desc'],
                    $row['mjo_date'],
                    $row['mjo_ajuan_dari'],
                    $row['mjo_peruntukan'],
                    $row['date_now'],
                    number_format((float)$row['biaya'], 0, ',', '.')
                ),
                // Jika ingin mengirim data tambahan:
                'data' => $row
            ];
        }

        $has_more = ($offset + $limit) < $total;

        $json_data = [
            'results' => $results,
            'pagination' => [
                'more' => $has_more
            ]
        ];

        echo json_encode($json_data);
        // return $this->response->setJsonContent($json_data);
    }


    /**
     * @routeGet('/getDataMemoUmBelumBayar')
     * @routePost('/getDataMemoUmBelumBayar')
     */
    public function getDataMemoUmBelumBayarAction()
    {
        // Ambil parameter pencarian dari request (untuk select2)
        $search = $this->request->get('q', 'string', '');
        $page = (int) $this->request->get('page', 'int', 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $m_periode = $this->session->m_periode;
        $y_periode = $this->session->y_periode;

        // Query ke stored procedure
        $sql = "
            SET NOCOUNT ON;
            EXEC akuntansi.transjurnal_memo_um_load_belumbayar 
                @mperiod = '$m_periode',
                @yperiod = '$y_periode'
            ;
        ";
        $resultDb = $this->db->fetchAll($sql);

        // Filter hasil jika ada pencarian
        $filtered = [];
        if ($search !== '') {
            foreach ($resultDb as $row) {
                if (
                    stripos($row['mjo_code'], $search) !== false ||
                    stripos($row['mjo_desc'], $search) !== false ||
                    stripos($row['mjo_ajuan_dari'], $search) !== false ||
                    stripos($row['mjo_peruntukan'], $search) !== false
                ) {
                    $filtered[] = $row;
                }
            }
        } else {
            $filtered = $resultDb;
        }

        // Pagination manual
        $total = count($filtered);
        $data = array_slice($filtered, $offset, $limit);

        // Format untuk select2
        $results = [];
        foreach ($data as $row) {
            $results[] = [
                'id' => $row['mjo_id'],
                'text' => sprintf(
                    '[%s] %s | %s | %s | %s | %s | Rp%s',
                    $row['mjo_code'],
                    $row['mjo_desc'],
                    $row['mjo_date'],
                    $row['mjo_ajuan_dari'],
                    $row['mjo_peruntukan'],
                    $row['date_now'],
                    number_format((float)$row['biaya'], 0, ',', '.')
                ),
                // Jika ingin mengirim data tambahan:
                'data' => $row
            ];
        }

        $has_more = ($offset + $limit) < $total;

        $json_data = [
            'results' => $results,
            'pagination' => [
                'more' => $has_more
            ]
        ];

        echo json_encode($json_data);
        // return $this->response->setJsonContent($json_data);
    }


    /**
     * @routeGet('/getDataUangMukaPertanggungjawaban')
     * @routePost('/getDataUangMukaPertanggungjawaban')
     */
    public function getDataUangMukaPertanggungjawabanAction()
    {
        // Ambil parameter pencarian dari request (untuk select2)
        $search = $this->request->get('q', 'string', '');
        $page = (int) $this->request->get('page', 'int', 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $m_periode = $this->session->m_periode;
        $y_periode = $this->session->y_periode;


        // Sql = "SELECT * FROM mjournal WHERE jt_id = 6 AND jenis_ju_lainnya = 1 AND id_mjo_ju_pj IS NULL"
        // Query ke stored procedure
        $sql = "
            SELECT
                m.mjo_id,
                m.mjo_code,
                m.mjo_date,
                ISNULL(m.mjo_ket, '') AS mjo_desc,
                ISNULL(SUM(d.djo_credit), 0) AS biaya,
                CONVERT(CHAR(10), GETDATE(), 23) AS date_now  -- yyyy-mm-dd
            FROM akuntansi.mjournal AS m
            LEFT JOIN akuntansi.djournal AS d
                ON d.mjo_id = m.mjo_id
            LEFT JOIN akuntansi.masteraccount_perkiraan AS ms
                ON ms.acc_code = d.acc_code
            WHERE
                m.jt_id = 6 
                AND m.jenis_ju_lainnya = 1 
                AND m.id_mjo_ju_pj IS NULL 
                AND m.id_um_pj IS NULL 
            GROUP BY
                m.mjo_id, m.mjo_code, m.mjo_date, m.mjo_ket 
            ORDER BY
                m.mjo_code DESC;
        ";
        $resultDb = $this->db->fetchAll($sql);

        // Filter hasil jika ada pencarian
        $filtered = [];
        if ($search !== '') {
            foreach ($resultDb as $row) {
                if (
                    stripos($row['mjo_code'], $search) !== false ||
                    stripos($row['mjo_desc'], $search) !== false ||
                    stripos($row['mjo_date'], $search) !== false
                ) {
                    $filtered[] = $row;
                }
            }
        } else {
            $filtered = $resultDb;
        }

        // Pagination manual
        $total = count($filtered);
        $data = array_slice($filtered, $offset, $limit);

        // Format untuk select2
        $results = [];
        foreach ($data as $row) {
            $results[] = [
                'id' => $row['mjo_id'],
                'text' => sprintf(
                    '[%s] %s | %s | %s | Rp%s',
                    $row['mjo_code'],
                    $row['mjo_desc'],
                    $row['mjo_date'],
                    $row['date_now'],
                    number_format((float)$row['biaya'], 0, ',', '.')
                ),
                // Jika ingin mengirim data tambahan:
                'data' => $row
            ];
        }

        $has_more = ($offset + $limit) < $total;

        $json_data = [
            'results' => $results,
            'pagination' => [
                'more' => $has_more
            ]
        ];

        echo json_encode($json_data);
        // return $this->response->setJsonContent($json_data);
    }


    /**
     * @routeGet('/getDataUangMukaSudahTanggungjawabkan')
     * @routePost('/getDataUangMukaSudahTanggungjawabkan')
     */
    public function getDataUangMukaSudahTanggungjawabkanAction()
    {
        // Ambil parameter pencarian dari request (untuk select2)
        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;

        $data = VwTransUangMukaPjModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "(mjo_code LIKE '%$nama%' OR mjo_ket LIKE '%$nama%' OR keterangan LIKE '%$nama%') AND mjo_id_memo IS NULL"
            )
        );

        $data_array = $data->toArray();
        $has_more = count($data_array);
        $json_data = array(
            "data" => $data_array,
            "has_more" => $has_more,
        );
        echo json_encode($json_data);
    }

    /**
     * @routeGet('/getDataMasterTarif2')
     * @routePost('/getDataMasterTarif2')
     */
    public function getDataMasterTarif2Action()
    {
        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;

        $data = RefMasterTarifModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "kode_gol LIKE '%$nama%' OR uraian LIKE '%$nama%'"
            )
        );

        $data_array = $data->toArray();
        $has_more = count($data_array);
        $json_data = array(
            "data" => $data_array,
            "has_more" => $has_more,
        );
        echo json_encode($json_data);
    }

    /**
     * @routeGet('/getMasterKategoriBarangS2')
     * @routePost('/getMasterKategoriBarangS2')
     */
    public function getMasterKategoriBarangS2Action()
    {

        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;

        $data = MasterKategoriBarangModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "kode LIKE '%$nama%' OR nama LIKE '%$nama%'"
            )
        );

        $data_array = $data->toArray();
        $has_more = count($data_array);
        $json_data = array(
            "data" => $data_array,
            "has_more" => $has_more,
        );
        echo json_encode($json_data);
    }

    /**
     * @routeGet('/getMasterGolonganTarifModelS2')
     * @routePost('/getMasterGolonganTarifModelS2')
     */
    public function getMasterGolonganTarifModelS2Action()
    {

        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;

        $data = MasterGolonganTarifModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "kode LIKE '%$nama%' OR nama LIKE '%$nama%'"
            )
        );

        $data_array = $data->toArray();
        $has_more = count($data_array);
        $json_data = array(
            "data" => $data_array,
            "has_more" => $has_more,
        );
        echo json_encode($json_data);
    }

    /**
     * @routeGet('/getMasterTypeLayananModelS2')
     * @routePost('/getMasterTypeLayananModelS2')
     */
    public function getMasterTypeLayananModelS2Action()
    {

        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;

        $data = MasterTypeLayananModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "kode LIKE '%$nama%' OR nama LIKE '%$nama%'"
            )
        );

        $data_array = $data->toArray();
        $has_more = count($data_array);
        $json_data = array(
            "data" => $data_array,
            "has_more" => $has_more,
        );
        echo json_encode($json_data);
    }

    /**
     * @routeGet('/getMasterSatkerInventoriModelS2')
     * @routePost('/getMasterSatkerInventoriModelS2')
     */
    public function getMasterSatkerInventoriModelS2Action()
    {

        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;

        $data = MasterSatkerInventoriModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "kode LIKE '%$nama%' OR nama LIKE '%$nama%'"
            )
        );

        $data_array = $data->toArray();
        $has_more = count($data_array);
        $json_data = array(
            "data" => $data_array,
            "has_more" => $has_more,
        );
        echo json_encode($json_data);
    }

    /**
     * @routeGet('/getMasterUserModelS2')
     * @routePost('/getMasterUserModelS2')
     */
    public function getMasterUserModelS2Action()
    {

        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;

        $data = VwEmployeeModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "nup LIKE '%$nama%' OR nama LIKE '%$nama%'"
            )
        );

        $data_array = $data->toArray();
        $has_more = count($data_array);
        $json_data = array(
            "data" => $data_array,
            "has_more" => $has_more,
        );
        echo json_encode($json_data);
    }
}
