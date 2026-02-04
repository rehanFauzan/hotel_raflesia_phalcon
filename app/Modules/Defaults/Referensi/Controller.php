<?php

declare(strict_types=1);

namespace App\Modules\Defaults\Referensi;

use App\Modules\Defaults\Master\Instalasi\InstalasiModel;
use App\Modules\Defaults\Master\MeterProduksi\MeterProduksiModel;
use Phalcon\Mvc\Controller as BaseController;

use App\Modules\Defaults\Setting\Role\Model as RoleModel;
use App\Modules\Defaults\Setting\User\UserModel as UserModel;
use App\Modules\Defaults\ReferensiData\SatuanKerja\Model as SatuanKerjaModel;
use App\Modules\Defaults\ModelStandAlone\PegawaiMasterModel;

use App\Modules\Defaults\ModelStandAlone\SubDepartementModel;
use App\Modules\Defaults\ModelStandAlone\OccupationModel;
use App\Modules\Defaults\ModelStandAlone\OfficeModel;
use App\Modules\Defaults\ModelStandAlone\JenisAuditModel;
use App\Modules\Defaults\ModelStandAlone\ObjekAuditModel;
use App\Modules\Defaults\ModelStandAlone\MasterBagianSubModel;
use App\Modules\Defaults\ModelStandAlone\MasterBagianViewModel;
use App\Modules\Defaults\ModelStandAlone\MasterUserViewModel;
use App\Modules\Defaults\ModelStandAlone\PenempatanModel;
use App\Modules\Defaults\ModelStandAlone\RefMasterJenisModel;
use App\Modules\Defaults\ModelStandAlone\RefMasterTingkatResikoModel;
use App\Modules\Defaults\ModelStandAlone\AuditorEksternalModel;
use App\Modules\Defaults\ModelStandAlone\DirektoratModel;
use App\Modules\Defaults\ModelStandAlone\BagianModel;
use App\Modules\Defaults\ModelStandAlone\SubBagianModel;
use App\Modules\Defaults\ModelStandAlone\LevelJabatanModel;
use App\Modules\Defaults\ModelStandAlone\MasterUserAuditorViewModel;
use App\Modules\Defaults\ModelStandAlone\MasterUserAuditorPenilaianViewModel;
use App\Modules\Defaults\ModelStandAlone\MasterUserDirektoratViewModel;
use App\Modules\Defaults\ModelStandAlone\RefMasterJenisBiayaModel;
use App\Modules\Defaults\ModelStandAlone\RefMasterLingkupAuditModel;
use App\Modules\Defaults\ModelStandAlone\RefMasterAcuanAuditModel;
use App\Modules\Defaults\ModelStandAlone\RefMasterProsedurAuditModel;
use App\Modules\Defaults\ModelStandAlone\RefMasterResikoModel;
use App\Modules\Defaults\ModelStandAlone\RefMasterJenisResikoModel;
use App\Modules\Defaults\PersiapanAudit\SuratPenugasan\PersiapanAuditSuratPenugasanModel;
use App\Modules\Defaults\PersiapanAudit\ProgramAudit\ProgramAuditModel;
use App\Modules\Defaults\ProgramKerja\JadwalPelaksanaan\ProgramKerjaDetailModel;
use Core\Facades\Response;

/**
 * @routeGroup('/referensi')
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
    // Referensi Produksi
    /**
     * @routePost('/getInstalasi')
     * @routeGet('/getInstalasi')
     */
    public function getInstalasiAction()
    {

        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;

        $data = InstalasiModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "nama_installasi LIKE '%$nama%' OR kode_installasi LIKE '%$nama%'"
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
     * @routeGet('/getUser')
     * @routePost('/getUser')
     */
    public function getUserAction()
    {

        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;

        $data = UserModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "nama LIKE '%$nama%' "
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
     * @routeGet('/getRolemaster')
     * @routePost('/getRolemaster')
     */
    public function getRolemasterAction()
    {

        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;

        $data = RoleModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "role LIKE '%$nama%' "
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
     * @routeGet('/getMasterpegawai')
     * @routePost('/getMasterpegawai')
     */
    public function getMasterpegawaiAction()
    {

        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;

        $data = PegawaiMasterModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "nama LIKE '%$nama%' OR nup LIKE '%$nama%' "
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
     * @routeGet('/getSubDepartement')
     * @routePost('/getSubDepartement')
     */
    public function getSubDepartementAction()
    {

        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;

        $data = SubDepartementModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "subdept_name LIKE '%$nama%' "
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
     * @routeGet('/getOccupation')
     * @routePost('/getOccupation')
     */
    public function getOccupationAction()
    {

        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;

        $data = OccupationModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "occ_name LIKE '%$nama%' "
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
     * @routeGet('/getOffice')
     * @routePost('/getOffice')
     */
    public function getOfficeAction()
    {

        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;

        $data = OfficeModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "off_name LIKE '%$nama%' "
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
     * @routeGet('/refreshToken')
     */
    public function refreshTokenAction()
    {
        return Response::setJsonContent([
            'token' => $this->security->getToken(),
            'tokenKey' => $this->security->getTokenKey()
        ]);
        // return Response::send();
    }

    /**
     * @routeGet('/encryptSimple')
     * @routePost('/encryptSimple')
     */
    public function encryptSimpleAction()
    {
        if ($this->request->isPost()) {
            $idEncrypt = $this->request->getPost('id');
            $hasil_encrypt = enkripsiBase64($idEncrypt);

            // Response::setStatusCode(200, "OK");
            return Response::setJsonContent([
                'error' => 0,
                'message' => "Decrypt Data Berhasil",
                'data' => $hasil_encrypt
            ]);
        } else {
            return Response::setJsonContent([
                'error' => 1,
                'message' => "Methode not allowed"
            ]);
        }
    }


    /**
     * @routeGet('/encryptPassword')
     * @routePost('/encryptPassword')
     */
    public function encryptPasswordAction()
    {
        if ($this->request->isPost()) {
            $idEncrypt = $this->request->getPost('id');
            $hasil_encrypt = encrypt($idEncrypt, $this->config->appKey);

            // Response::setStatusCode(200, "OK");
            return Response::setJsonContent([
                'error' => 0,
                'message' => "Decrypt Data Berhasil",
                'data' => $hasil_encrypt
            ]);
        } else {
            return Response::setJsonContent([
                'error' => 1,
                'message' => "Methode not allowed"
            ]);
        }
    }

    /**
     * @routeGet('/decryptPassword')
     * @routePost('/decryptPassword')
     */
    public function decryptPasswordAction()
    {
        if ($this->request->isPost()) {
            // if ($this->security->checkToken()) {
            $passwordEncypt = $this->request->getPost('password');
            $hasil_decrypt = decrypt($passwordEncypt, $this->config->appKey);

            Response::setStatusCode(200, "OK");
            return Response::setJsonContent([
                'error' => 0,
                'message' => "Decrypt Data Berhasil",
                'data' => $hasil_decrypt
            ]);
            // } else {
            //     Response::setStatusCode(602, "Failed");
            //     return Response::setJsonContent([
            //         'error' => 1,
            //         'message' => "Terjadi kesalahan karena masalah keamanan, silahkan coba refresh ulang"
            //     ]);
            // }
        } else {
            Response::setStatusCode(601, "Failed");
            return Response::setJsonContent([
                'error' => 1,
                'message' => "Methode not allowed"
            ]);
        }
    }


    /**
     * @routePost('/getMasterpenempatan')
     * @routeGet('/getMasterpenempatan')
     */
    public function getMasterpenempatanAction()
    {
        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;
        $pdam_id = $this->session->user['pdam_id'];

        $data = PenempatanModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "(off_code LIKE '%$nama%' OR off_name LIKE '%$nama%')"
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
     * @routePost('/getKategori')
     * @routeGet('/getKategori')
     */
    public function getKategoriAction()
    {
        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;
        $pdam_id = $this->session->user['pdam_id'];

        $data = KategoriModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "(nama LIKE '%$nama%' OR kode LIKE '%$nama%')"
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
     * @routePost('/getMasterDirektorat')
     * @routeGet('/getMasterDirektorat')
     */
    public function getMasterDirektoratAction()
    {
        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $pdam_id = $this->session->user['pdam_id'];
        $offset = ($page - 1) * 20;

        $conditions = "(kode LIKE '%$nama%' OR nama LIKE '%$nama%')";
        if (!empty($pdam_id)) {
            $conditions .= " AND pdam_id = '$pdam_id'";
        }

        $data = DirektoratModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => $conditions
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
     * @routePost('/getMasterBagian')
     * @routeGet('/getMasterBagian')
     */
    public function getMasterBagianAction()
    {
        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $pdam_id = $this->session->user['pdam_id'];
        $offset = ($page - 1) * 20;

        $conditions = "(kode LIKE '%$nama%' OR nama LIKE '%$nama%')";
        if (!empty($pdam_id)) {
            $conditions .= " AND pdam_id = '$pdam_id'";
        }

        $data = BagianModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => $conditions
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
     * @routePost('/getMasterSub')
     * @routeGet('/getMasterSub')
     */
    public function getMasterSubAction()
    {
        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $pdam_id = $this->session->user['pdam_id'];
        $offset = ($page - 1) * 20;

        $conditions = "(kode LIKE '%$nama%' OR nama LIKE '%$nama%')";
        if (!empty($pdam_id)) {
            $conditions .= " AND pdam_id = '$pdam_id'";
        }

        $data = SubBagianModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => $conditions
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
     * @routePost('/getMasterSubBagian')
     * @routeGet('/getMasterSubBagian')
     */
    public function getMasterSubBagianAction()
    {
        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $bagian_id = $this->request->get('bagian_id'); // parameter bagian yang dipilih
        $pdam_id = $this->session->user['pdam_id'];
        $offset = ($page - 1) * 20;

        $conditions = [];
        $bind = [];

        if (!empty($nama)) {
            $conditions[] = "(kode LIKE :nama: OR nama LIKE :nama:)";
            $bind['nama'] = "%$nama%";
        }

        if (!empty($pdam_id)) {
            $conditions[] = "pdam_id = :pdam_id:";
            $bind['pdam_id'] = $pdam_id;
        }

        if (!empty($bagian_id)) {
            $conditions[] = "bagian_id = :bagian_id:";
            $bind['bagian_id'] = $bagian_id;
        }

        $data = SubBagianModel::find([
            'limit'     => 21,
            'offset'    => $offset,
            'conditions' => implode(" AND ", $conditions),
            'bind'       => $bind
        ]);

        $data_array = $data->toArray();
        $has_more = count($data_array);
        $json_data = [
            "data" => $data_array,
            "has_more" => $has_more,
        ];
        echo json_encode($json_data);
    }


    /**
     * @routePost('/getMasterLevelJabatan')
     * @routeGet('/getMasterLevelJabatan')
     */
    public function getMasterLevelJabatanAction()
    {
        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $pdam_id = $this->session->user['pdam_id'];
        $offset = ($page - 1) * 20;

        $conditions = "(kode LIKE '%$nama%' OR nama LIKE '%$nama%')";
        if (!empty($pdam_id)) {
            $conditions .= " AND pdam_id = '$pdam_id'";
        }

        $data = LevelJabatanModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => $conditions
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
     * @routePost('/getRefMasterAcuanAudit')
     * @routeGet('/getRefMasterAcuanAudit')
     */
    public function getRefMasterAcuanAuditAction()
    {
        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;
        $pdam_id = $this->session->user['pdam_id'];

        $data = RefMasterAcuanAuditModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "nama LIKE '%$nama%' AND pdam_id = $pdam_id"
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
     * @routePost('/getRefMasterProsedurAudit')
     * @routeGet('/getRefMasterProsedurAudit')
     */
    public function getRefMasterProsedurAuditAction()
    {
        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;
        $pdam_id = $this->session->user['pdam_id'];

        $data = RefMasterProsedurAuditModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "nama LIKE '%$nama%' AND pdam_id = $pdam_id"
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
     * @routePost('/getMasterAuditorEksternal')
     * @routeGet('/getMasterAuditorEksternal')
     */
    public function getMasterAuditorEksternalAction()
    {
        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $pdam_id = $this->session->user['pdam_id'];
        $offset = ($page - 1) * 20;

        $conditions = "(email LIKE '%$nama%' OR nama LIKE '%$nama%')";
        if (!empty($pdam_id)) {
            $conditions .= " AND pdam_id = '$pdam_id'";
        }

        $data = AuditorEksternalModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => $conditions
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
     * @routePost('/getMasterJenisAudit')
     * @routeGet('/getMasterJenisAudit')
     */
    public function getMasterJenisAuditAction()
    {
        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $pdam_id = $this->session->user['pdam_id'];
        $offset = ($page - 1) * 20;

        $conditions = "(deskripsi LIKE '%$nama%' OR nama LIKE '%$nama%')";
        if (!empty($pdam_id)) {
            $conditions .= " AND pdam_id = '$pdam_id'";
        }

        $data = JenisAuditModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => $conditions
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
     * @routePost('/getMasterObjekAudit')
     * @routeGet('/getMasterObjekAudit')
     */
    public function getMasterObjekAuditAction()
    {
        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $pdam_id = $this->session->user['pdam_id'];
        $offset = ($page - 1) * 20;

        $conditions = "(kode LIKE '%$nama%' OR nama LIKE '%$nama%')";
        if (!empty($pdam_id)) {
            $conditions .= " AND pdam_id = '$pdam_id'";
        }

        $data = ObjekAuditModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => $conditions
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
     * @routePost('/getRefMasterResiko')
     * @routeGet('/getRefMasterResiko')
     */
    public function getRefMasterResikoAction()
    {
        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;
        $pdam_id = $this->session->user['pdam_id'];

        $data = RefMasterResikoModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "nama LIKE '%$nama%' AND pdam_id = $pdam_id"
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
     * @routePost('/getRefMasterJenisResiko')
     * @routeGet('/getRefMasterJenisResiko')
     */
    public function getRefMasterJenisResikoAction()
    {
        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;
        $pdam_id = $this->session->user['pdam_id'];

        $data = RefMasterJenisResikoModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "nama LIKE '%$nama%' AND pdam_id = $pdam_id"
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
     * @routePost('/getGlobalDataTembusanDireksiDanPengawasS2')
     * @routeGet('/getGlobalDataTembusanDireksiDanPengawasS2')
     */
    public function getGlobalDataTembusanDireksiDanPengawasS2Action()
    {
        $id_program_audit_surat_penugasan = $this->request->get('id');
        $pdam_id = $this->session->user['pdam_id'];
        $id_tembusan = $this->request->get('arr_tembusan');
        $pdam_id = $this->session->user['pdam_id'];

        if (!empty($id_tembusan) && isset($id_tembusan)) {

            $data = MasterUserDirektoratViewModel::find(
                array(
                    'conditions' => "id IN ($id_tembusan) AND pdam_id = $pdam_id"
                )
            );
            $data_array = $data->toArray();

            return $this->response->setJsonContent($data_array);
        } else {
            return $this->response->setJsonContent([]);
        }
    }

    //====================================================================================== BELOW HERE IS OLD AND WILL BE DELETED ===========================================================================


    /**
     * @routePost('/getRefMasterJenisAudit')
     * @routeGet('/getRefMasterJenisAudit')
     */
    public function getRefMasterJenisAuditAction()
    {
        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;
        $pdam_id = $this->session->user['pdam_id'];

        $data = RefMasterJenisModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "nama LIKE '%$nama%' AND pdam_id = $pdam_id"
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
     * @routePost('/getRefMasterJenisBiaya')
     * @routeGet('/getRefMasterJenisBiaya')
     */
    public function getRefMasterJenisBiayaAction()
    {
        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;
        $pdam_id = $this->session->user['pdam_id'];

        $data = RefMasterJenisBiayaModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "nama LIKE '%$nama%' AND pdam_id = $pdam_id"
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
     * @routePost('/getRefMasterTingkatResiko')
     * @routeGet('/getRefMasterTingkatResiko')
     */
    public function getRefMasterTingkatResikoAction()
    {
        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;
        $pdam_id = $this->session->user['pdam_id'];

        $data = RefMasterTingkatResikoModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "nama LIKE '%$nama%' AND pdam_id = $pdam_id"
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

    // /**
    //  * @routePost('/getMasterBagian')
    //  * @routeGet('/getMasterBagian')
    //  */
    // public function getMasterBagianAction()
    // {
    //     $nama = $this->request->get('q');
    //     $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
    //     $offset = ($page - 1) * 20;

    //     $data = MasterBagianModel::find(
    //         array(
    //             'limit'     => 21,
    //             'offset'    => $offset,
    //             'conditions' => "nama LIKE '%$nama%'"
    //         )
    //     );

    //     $data_array = $data->toArray();
    //     $has_more = count($data_array);
    //     $json_data = array(
    //         "data" => $data_array,
    //         "has_more" => $has_more,
    //     );
    //     echo json_encode($json_data);
    // }


    /**
     * @routePost('/getMasterBagianSub')
     * @routeGet('/getMasterBagianSub')
     */
    public function getMasterBagianSubAction()
    {
        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $id_bagian = $this->request->get('id_bagian');
        $offset = ($page - 1) * 20;
        $pdam_id = $this->session->user['pdam_id'];

        $data = MasterBagianSubModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "bagian_id = '$id_bagian' AND nama LIKE '%$nama%' AND pdam_id = $pdam_id"
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
     * @routePost('/getBagianheadS2')
     * @routeGet('/getBagianheadS2')
     */
    public function getBagianheadS2Action()
    {
        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;
        $pdam_id = $this->session->user['pdam_id'];

        $data = BagianModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "(kode LIKE '%$nama%' OR nama LIKE '%$nama%') AND pdam_id = $pdam_id"
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
     * @routePost('/getBagianS2')
     * @routeGet('/getBagianS2')
     */
    public function getBagianS2Action()
    {
        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $id_bagian = $this->request->get('id_bagian');
        $offset = ($page - 1) * 20;
        $pdam_id = $this->session->user['pdam_id'];

        $data = MasterBagianViewModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "(bagian_sub_kode LIKE '%$nama%' OR bagian_nama LIKE '%$nama%' OR bagian_sub_nama LIKE '%$nama%') AND pdam_id = $pdam_id"
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
     * @routePost('/getDataPjS2')
     * @routeGet('/getDataPjS2')
     */
    public function getDataPjS2Action()
    {
        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $pdam_id = $this->session->user['pdam_id'];
        $offset = ($page - 1) * 20;

        $conditions = "(nama LIKE '%$nama%')";
        if (!empty($pdam_id)) {
            $conditions .= " AND pdam_id = '$pdam_id'";
        }

        $data = MasterUserAuditorViewModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => $conditions
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
     * @routePost('/getDataPengendaliTeknisS2')
     * @routeGet('/getDataPengendaliTeknisS2')
     */
    public function getDataPengendaliTeknisS2Action()
    {
        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $pdam_id = $this->session->user['pdam_id'];
        $offset = ($page - 1) * 20;

        $conditions = "(nama LIKE '%$nama%')";
        if (!empty($pdam_id)) {
            $conditions .= " AND pdam_id = '$pdam_id'";
        }

        $data = MasterUserAuditorViewModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => $conditions
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
     * @routePost('/getDataKetuaTim2')
     * @routeGet('/getDataKetuaTim2')
     */
    public function getDataKetuaTim2Action()
    {
        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $pdam_id = $this->session->user['pdam_id'];
        $offset = ($page - 1) * 20;

        $conditions = "(nama LIKE '%$nama%')";
        if (!empty($pdam_id)) {
            $conditions .= " AND pdam_id = '$pdam_id'";
        }

        $data = MasterUserAuditorViewModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => $conditions
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
     * @routePost('/getDataAnggotaTim2')
     * @routeGet('/getDataAnggotaTim2')
     */
    public function getDataAnggotaTim2Action()
    {
        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $pdam_id = $this->session->user['pdam_id'];
        $offset = ($page - 1) * 20;

        $conditions = "(nama LIKE '%$nama%')";
        if (!empty($pdam_id)) {
            $conditions .= " AND pdam_id = '$pdam_id'";
        }

        $data = MasterUserAuditorViewModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => $conditions
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
     * @routePost('/getDataDariAuditorS2')
     * @routeGet('/getDataDariAuditorS2')
     */
    public function getDataDariAuditorS2Action()
    {
        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;
        $pdam_id = $this->session->user['pdam_id'];

        $data = MasterUserAuditorViewModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "nama LIKE '%$nama%' AND pdam_id = $pdam_id"
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
     * @routePost('/getDataKepadaAuditeeS2')
     * @routeGet('/getDataKepadaAuditeeS2')
     */
    public function getDataKepadaAuditeeS2Action()
    {
        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;
        $id_bagian = $this->request->get('id_bagian');
        $pdam_id = $this->session->user['pdam_id'];

        if (isset($id_bagian) && !empty($id_bagian)) {
            $data = MasterUserViewModel::find(
                array(
                    'limit'     => 21,
                    'offset'    => $offset,
                    'conditions' => "satuan_kerja_id = '$id_bagian' AND nama LIKE '%$nama%' AND pdam_id = $pdam_id"
                )
            );
        } else {
            $data = MasterUserViewModel::find(
                array(
                    'limit'     => 21,
                    'offset'    => $offset,
                    'conditions' => "nama LIKE '%$nama%' AND pdam_id = $pdam_id"
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
     * @routePost('/getDataTembusanDireksidanPengawasS2')
     * @routeGet('/getDataTembusanDireksidanPengawasS2')
     */
    public function getDataTembusanDireksidanPengawasS2Action()
    {
        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;
        $pdam_id = $this->session->user['pdam_id'];

        $data = MasterUserDirektoratViewModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "nama LIKE '%$nama%' AND pdam_id = '" . $pdam_id . "'"
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
     * @routePost('/getRefMasterLingkupAudit')
     * @routeGet('/getRefMasterLingkupAudit')
     */
    public function getRefMasterLingkupAuditAction()
    {
        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $offset = ($page - 1) * 20;
        $pdam_id = $this->session->user['pdam_id'];

        $data = RefMasterLingkupAuditModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => "nama LIKE '%$nama%' AND pdam_id = $pdam_id"
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
     * @routePost('/getGlobalDataMasterTembusanDireksiDanPengawasS2')
     * @routeGet('/getGlobalDataMasterTembusanDireksiDanPengawasS2')
     */
    public function getGlobalDataMasterTembusanDireksiDanPengawasS2Action()
    {
        $id_program_audit_surat_penugasan = $this->request->get('id');
        $pdam_id = $this->session->user['pdam_id'];
        $id_tembusan = $this->request->get('arr_tembusan');
        $pdam_id = $this->session->user['pdam_id'];

        if (!empty($id_tembusan) && isset($id_tembusan)) {

            $data = MasterUserDirektoratViewModel::find(
                array(
                    'conditions' => "id IN ($id_tembusan) AND pdam_id = $pdam_id"
                )
            );
            $data_array = $data->toArray();

            return $this->response->setJsonContent($data_array);
        } else {
            return $this->response->setJsonContent([]);
        }
    }


    /**
     * @routePost('/getGlobalDataMasterKepadaAuditeeS2')
     * @routeGet('/getGlobalDataMasterKepadaAuditeeS2')
     */
    public function getGlobalDataMasterKepadaAuditeeS2Action()
    {
        $id_program_audit_surat_penugasan = $this->request->get('id');
        $pdam_id = $this->session->user['pdam_id'];
        $arr_id_kepada_auditee = $this->request->get('arr_kepada_auditee');
        $pdam_id = $this->session->user['pdam_id'];

        if (!empty($arr_id_kepada_auditee) && isset($arr_id_kepada_auditee)) {

            $data = MasterUserViewModel::find(
                array(
                    'conditions' => "id IN ($arr_id_kepada_auditee) AND pdam_id = $pdam_id"
                )
            );
            $data_array = $data->toArray();

            return $this->response->setJsonContent($data_array);
        } else {
            return $this->response->setJsonContent([]);
        }
    }

    /**
     * @routePost('/getGlobalDataMasterKepadaAuditorS2')
     * @routeGet('/getGlobalDataMasterKepadaAuditorS2')
     */
    public function getGlobalDataMasterKepadaAuditorS2Action()
    {
        $id_program_audit_surat_penugasan = $this->request->get('id');
        $pdam_id = $this->session->user['pdam_id'];
        $arr_id_kepada_auditor = $this->request->get('arr_kepada_auditor');
        $pdam_id = $this->session->user['pdam_id'];

        if (!empty($arr_id_kepada_auditor) && isset($arr_id_kepada_auditor)) {

            $data = MasterUserAuditorViewModel::find(
                array(
                    'conditions' => "id IN ($arr_id_kepada_auditor) AND pdam_id = $pdam_id"
                )
            );
            $data_array = $data->toArray();

            return $this->response->setJsonContent($data_array);
        } else {
            return $this->response->setJsonContent([]);
        }
    }

    /**
     * @routePost('/getDataAuditorPenilaianS2')
     * @routeGet('/getDataAuditorPenilaianS2')
     */
    public function getDataAuditorPenilaianS2Action()
    {
        $nama = $this->request->get('q');
        $page = ($this->request->get('page')) ? $this->request->get('page') : 0;
        $pdam_id = $this->session->user['pdam_id'];
        $offset = ($page - 1) * 20;

        $conditions = "(nama LIKE '%$nama%')";
        if (!empty($pdam_id)) {
            $conditions .= " AND pdam_id = '$pdam_id'";
        }

        $data = MasterUserAuditorPenilaianViewModel::find(
            array(
                'limit'     => 21,
                'offset'    => $offset,
                'conditions' => $conditions
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
