<?php

declare(strict_types=1);

namespace App\Modules\Defaults\Setting\Profile;

use Core\Facades\Response;
use Core\Facades\Request;
use Core\Paginator\DataTables\DataTable;
use App\Libraries\Log;
use App\Modules\Defaults\BaseController;
use Core\Facades\Security;
use App\Modules\Defaults\Auth\Model\MainModel as SystemPdamModel;


/**
 * @routeGroup('/setting/profile')
 * @middleware('RequireUser')
 */
class Controller extends BaseController
{


    /**
     * @routePost('/uploadFile')
     */
    public function uploadFileAction()
    {
        // $tanggal = date("Y_m_d_H_i_s");
        $pdam_id = $this->session->user['pdam_id'];
        $folder = BASEPATH . '/public/external_img/';

        $file_type = array('pdf', 'PNG', 'JPG', 'jpg', 'png', 'Jpg', 'Png', 'JPEG', 'jpeg', 'Jpeg');
        $mime_types = array('application/pdf', 'image/png', 'image/jpeg', 'image/jpg');
        $max_size = 30000000;

        $file_name = $_FILES['upload_file']['name'];
        $file_size = $_FILES['upload_file']['size'];
        $file_tmp = $_FILES['upload_file']['tmp_name'];
        $file_mime = mime_content_type($file_tmp);

        $pra_extensi = (explode(".", $file_name));
        $extensi = end($pra_extensi);

        $filename = "logo-pdam-{$pdam_id}.{$extensi}";

        if (!in_array($extensi, $file_type) || !in_array($file_mime, $mime_types)) {

            $result = array(
                "error" => 1,
                "message" => "Extensi atau MIME Type File Tidak Sesuai !",
                "data" => array()
            );

            return $this->response->setJsonContent($result);
        } else if ($file_size > $max_size) {

            $result = array(
                "error" => 1,
                "message" => "Size File Melebihi Ketentuan !",
                "data" => array()
            );

            return $this->response->setJsonContent($result);
        } else {

            // Delete existing file if exists
            if (file_exists($folder . $filename)) {
                unlink($folder . $filename);
            }

            if (move_uploaded_file($file_tmp, $folder . $filename)) {

                $result = array(
                    "error" => 0,
                    "message" => "Upload File Sukses !",
                    "data" => array(
                        'filename' => $filename,
                        'fullPath' => $folder . $filename,
                    )
                );

                Log::write("Melakukan Upload File Logo PDAM", $filename, null, $result, "Setting/Profile/Controller", "INSERT");

                return $this->response->setJsonContent($result);
            } else {
                $result = array(
                    "error" => 1,
                    "message" => "Upload File gagal !",
                    "data" => array()
                );

                return $this->response->setJsonContent($result);
            }
        }
    }

    /**
     * @routeGet('/')
     */
    public function indexAction()
    {
        $pdam_id = $this->session->user['pdam_id'];

        $dataPdam = SystemPdamModel::findFirst([
            'conditions' => "pdam_id = :pdam_id:",
            'bind' => [
                'pdam_id' => $pdam_id
            ]
        ]);
        $arrValPdam = $dataPdam->toArray();


        $this->view->arr_val_pdam = $arrValPdam;
        $this->view->is_can_insert = $this->is_hak_input;
        $this->view->is_can_update = $this->is_hak_ubah;
        $this->view->is_can_delete = $this->is_hak_delete;
        $this->view->is_can_print = $this->is_hak_cetak;
        $this->view->is_can_verifikasi = $this->is_hak_verifikasi;
        $this->view->is_can_unverifikasi = $this->is_hak_unverifikasi;
    }

    /**
     * @routePost('/updateData')
     */
    public function updateDataAction()
    {
        // Ambil dan bersihkan input dari form
        $image_logo = escape_xss($this->request->getPost('image_logo'));
        $nama_aplikasi = escape_xss($this->request->getPost('nama_aplikasi'));
        $nama_panjang_aplikasi = escape_xss($this->request->getPost('nama_panjang_aplikasi'));
        $nama_pdam = escape_xss($this->request->getPost('nama_pdam'));
        $pemerintah_kota_kab = escape_xss($this->request->getPost('pemerintah_kota_kab'));
        $kota_kab = escape_xss($this->request->getPost('kota_kab'));
        $alamat = escape_xss($this->request->getPost('alamat'));
        $no_telp_pdam = escape_xss($this->request->getPost('no_telp_pdam'));
        $email = escape_xss($this->request->getPost('email'));
        $latitude = escape_xss($this->request->getPost('latitude'));
        $longitude = escape_xss($this->request->getPost('longitude'));

        // Nama aplikasi dan nama PDAM wajib diisi
        if (empty($nama_aplikasi) || empty($nama_pdam)) {
            return $this->response->setJsonContent([
                'error' => 1,
                'message' => 'Nama Aplikasi dan Nama PDAM wajib diisi.'
            ]);
        }

        // Ambil data setting yang sudah ada (diasumsikan hanya satu baris setting)
        $setting = SystemPdamModel::findFirst();

        // Set/update nilai-nilai
        $setting->image_logo = $image_logo;
        $setting->nama_aplikasi = $nama_aplikasi;
        $setting->nama_panjang_aplikasi = $nama_panjang_aplikasi;
        $setting->nama_pdam = $nama_pdam;
        $setting->pemerintah_kota_kab = $pemerintah_kota_kab;
        $setting->kota_kab = $kota_kab;
        $setting->alamat = $alamat;
        $setting->no_telp_pdam = $no_telp_pdam;
        $setting->email = $email;
        $setting->latitude = $latitude;
        $setting->longitude = $longitude;

        if ($setting->save()) {
            Log::write("Update data Profil Perusahaan", $setting->toArray(), TRUE, "Setting/Profile/Controller", "UPDATE");

            return $this->response->setJsonContent([
                'error' => 0,
                'message' => 'Data Profil Perusahaan berhasil diperbarui.',
                'redirect' => 'panel/setting/profile'
            ]);
        } else {
            return $this->response->setJsonContent([
                'error' => 1,
                'message' => 'Gagal menyimpan data. Silakan coba lagi.'
            ]);
        }
    }

}
