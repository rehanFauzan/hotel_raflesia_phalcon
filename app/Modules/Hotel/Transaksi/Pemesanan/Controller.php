<?php

declare(strict_types=1);

namespace App\Modules\Hotel\Transaksi\Pemesanan;

use Core\Facades\Response;
use Core\Facades\Request;
use Core\Paginator\DataTables\DataTable;
use App\Libraries\Log;
use App\Modules\Defaults\BaseController;
use Core\Facades\Security;
use Exception;
use DateTime;

/**
 * @routeGroup('/hotel/transaksi/pemesanan')
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
     * @routePost('/datatable')
     * @routeGet('/datatable')
     */
    public function datatableAction()
    {
        $builder = $this->modelsManager->createBuilder()
            ->columns('p.*, t.nama_lengkap as tamu_nama, r.nomor_kamar, tr.nama as tipe_nama')
            ->from(['p' => Model::class])
            ->leftJoin('App\Modules\Hotel\Transaksi\Pemesanan\TamuModel', 't.id = p.tamu_id', 't')
            ->leftJoin('App\Modules\Hotel\Master\Kamar\Model', 'r.id = p.ruangan_id', 'r')
            ->leftJoin('App\Modules\Hotel\Master\TipeKamar\Model', 'tr.id = r.tipe_ruangan_id', 'tr')
            ->where("1=1")
            ->orderBy("p.created_at DESC");

        $dataTables = new DataTable();
        $dataTables->fromBuilder($builder)->sendResponse();
    }

    /**
     * @routeGet('/get-kamar-tersedia')
     */
    public function getKamarTersediaAction()
    {
        $checkin = $this->request->getQuery('checkin');
        $checkout = $this->request->getQuery('checkout');
        $tipe_id = $this->request->getQuery('tipe_id');

        if (!$checkin || !$checkout) {
            return Response::setJsonContent([]);
        }

        // Call stored procedure untuk cek ketersediaan
        $sql = "CALL hotel_cek_ketersediaan_kamar(?, ?, ?)";
        $result = $this->db->query($sql, [$tipe_id, $checkin, $checkout]);
        
        $kamar = [];
        while ($row = $result->fetch()) {
            $kamar[] = [
                'id' => $row['id'],
                'text' => $row['nomor_kamar'] . ' - Lantai ' . $row['lantai'] . ' (Rp ' . number_format($row['harga_per_malam'], 0, ',', '.') . ')'
            ];
        }

        return Response::setJsonContent($kamar);
    }

    /**
     * @routeGet('/get-tipe-kamar')
     */
    public function getTipeKamarAction()
    {
        $tipeKamar = $this->modelsManager->createBuilder()
            ->columns('id, nama, harga_per_malam')
            ->from('App\Modules\Hotel\Master\TipeKamar\Model')
            ->where('status = :status:', ['status' => 'active'])
            ->orderBy('nama ASC')
            ->getQuery()
            ->execute();

        $result = [];
        foreach ($tipeKamar as $item) {
            $result[] = [
                'id' => $item->id,
                'text' => $item->nama . ' (Rp ' . number_format($item->harga_per_malam, 0, ',', '.') . ')'
            ];
        }

        return Response::setJsonContent($result);
    }

    /**
     * Generate kode booking
     */
    private function generateKodeBooking()
    {
        $prefix = 'BOOK';
        $date = date('Ymd');
        
        // Cari nomor urut terakhir hari ini
        $lastBooking = Model::findFirst([
            'conditions' => 'kode_booking LIKE :pattern:',
            'bind' => ['pattern' => $prefix . $date . '%'],
            'order' => 'kode_booking DESC'
        ]);

        if ($lastBooking) {
            $lastNumber = (int)substr($lastBooking->kode_booking, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . $date . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * @routePost('/saveData')
     */
    public function saveDataAction()
    {
        if (!$this->request->isPost()) {
            return Response::setJsonContent([
                'error' => 1,
                'message' => "Method not allowed"
            ]);
        }

        try {
            $this->db->begin();

            // Data tamu
            $nama_lengkap = escape_xss($this->request->getPost('nama_lengkap'));
            $no_identitas = escape_xss($this->request->getPost('no_identitas'));
            $jenis_identitas = escape_xss($this->request->getPost('jenis_identitas'));
            $jenis_kelamin = escape_xss($this->request->getPost('jenis_kelamin'));
            $no_telepon = escape_xss($this->request->getPost('no_telepon'));
            $email = escape_xss($this->request->getPost('email'));
            $alamat = escape_xss($this->request->getPost('alamat'));

            // Data pemesanan
            $ruangan_id = (int)$this->request->getPost('ruangan_id');
            $tanggal_checkin = escape_xss($this->request->getPost('tanggal_checkin'));
            $tanggal_checkout = escape_xss($this->request->getPost('tanggal_checkout'));
            $jumlah_tamu = (int)$this->request->getPost('jumlah_tamu');
            $catatan_khusus = escape_xss($this->request->getPost('catatan_khusus'));

            // Hitung jumlah malam dan total harga
            $checkin = new DateTime($tanggal_checkin);
            $checkout = new DateTime($tanggal_checkout);
            $jumlah_malam = $checkout->diff($checkin)->days;

            // Get harga kamar
            $kamar = $this->modelsManager->createBuilder()
                ->columns('r.*, tr.harga_per_malam')
                ->from(['r' => 'App\Modules\Hotel\Master\Kamar\Model'])
                ->leftJoin('App\Modules\Hotel\Master\TipeKamar\Model', 'tr.id = r.tipe_ruangan_id', 'tr')
                ->where('r.id = :id:', ['id' => $ruangan_id])
                ->getQuery()
                ->getSingleResult();

            if (!$kamar) {
                throw new Exception("Kamar tidak ditemukan");
            }

            $total_harga = $jumlah_malam * $kamar->harga_per_malam;

            // Cek atau buat tamu
            $tamu = TamuModel::findFirst([
                'conditions' => 'no_identitas = :no_identitas:',
                'bind' => ['no_identitas' => $no_identitas]
            ]);

            if (!$tamu) {
                $tamu = new TamuModel();
                $tamu->assign([
                    'nama_lengkap' => $nama_lengkap,
                    'no_identitas' => $no_identitas,
                    'jenis_identitas' => $jenis_identitas,
                    'jenis_kelamin' => $jenis_kelamin,
                    'no_telepon' => $no_telepon,
                    'email' => $email,
                    'alamat' => $alamat,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                if (!$tamu->save()) {
                    throw new Exception("Gagal menyimpan data tamu");
                }
            }

            // Simpan pemesanan
            $pemesanan = new Model();
            $pemesanan->assign([
                'kode_booking' => $this->generateKodeBooking(),
                'tamu_id' => $tamu->id,
                'user_id' => $this->session->user['id'],
                'ruangan_id' => $ruangan_id,
                'tanggal_checkin' => $tanggal_checkin,
                'tanggal_checkout' => $tanggal_checkout,
                'jumlah_tamu' => $jumlah_tamu,
                'jumlah_malam' => $jumlah_malam,
                'total_harga' => $total_harga,
                'catatan_khusus' => $catatan_khusus,
                'status' => 'menunggu',
                'created_at' => date('Y-m-d H:i:s')
            ]);

            if (!$pemesanan->save()) {
                throw new Exception("Gagal menyimpan pemesanan");
            }

            $this->db->commit();

            return Response::setJsonContent([
                'error' => 0,
                'message' => 'Pemesanan berhasil disimpan',
                'lastId' => $pemesanan->id,
                'kode_booking' => $pemesanan->kode_booking
            ]);

        } catch (Exception $e) {
            if ($this->db->isUnderTransaction()) {
                $this->db->rollback();
            }

            return Response::setJsonContent([
                'error' => 1,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * @routePost('/updateStatus')
     */
    public function updateStatusAction()
    {
        if (!$this->request->isPost()) {
            return Response::setJsonContent([
                'error' => 1,
                'message' => "Method not allowed"
            ]);
        }

        $id = $this->request->getPost('id');
        $status = $this->request->getPost('status');

        try {
            $pemesanan = Model::findFirst([
                'conditions' => 'id = :id:',
                'bind' => ['id' => $id]
            ]);

            if (!$pemesanan) {
                throw new Exception("Pemesanan tidak ditemukan");
            }

            $pemesanan->status = $status;
            $pemesanan->user_id = $this->session->user['id'];

            if (!$pemesanan->update()) {
                throw new Exception("Gagal mengupdate status");
            }

            return Response::setJsonContent([
                'error' => 0,
                'message' => 'Status berhasil diupdate'
            ]);

        } catch (Exception $e) {
            return Response::setJsonContent([
                'error' => 1,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * @routePost('/deleteData')
     */
    public function deleteDataAction()
    {
        if (!$this->request->isPost()) {
            return Response::setJsonContent([
                'error' => 1,
                'message' => "Method not allowed"
            ]);
        }

        $id_delete = $this->request->getPost('id_delete');

        $findForDelete = Model::findFirst([
            'conditions' => "id = :id:",
            'bind' => ['id' => $id_delete]
        ]);

        if (!$findForDelete) {
            return Response::setJsonContent([
                'error' => 1,
                'message' => "Data tidak ditemukan"
            ]);
        }

        // Cek apakah bisa dihapus (hanya yang status menunggu)
        if ($findForDelete->status !== 'menunggu') {
            return Response::setJsonContent([
                'error' => 1,
                'message' => "Hanya pemesanan dengan status 'menunggu' yang bisa dihapus"
            ]);
        }

        $resultDelete = $findForDelete->delete();

        if ($resultDelete) {
            return Response::setJsonContent([
                'error' => 0,
                'message' => "Hapus Data Berhasil"
            ]);
        } else {
            return Response::setJsonContent([
                'error' => 1,
                'message' => "Hapus Data Gagal"
            ]);
        }
    }
}