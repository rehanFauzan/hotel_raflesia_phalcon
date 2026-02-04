<?php

declare(strict_types=1);

namespace App\Modules\Hotel\ReferensiData\Pemesanan;

use Core\Facades\Response;
use Core\Paginator\DataTables\DataTable;
use App\Modules\Defaults\BaseController;
use Exception;

/**
 * @routeGroup('/hotel/referensi-data/pemesanan')
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
            ->columns('p.id, p.kode_booking, p.tamu_id, p.ruangan_id, p.tanggal_checkin, p.tanggal_checkout, p.jumlah_malam, p.jumlah_tamu, p.total_harga, p.status, p.catatan_khusus, t.nama_lengkap as tamu_nama, r.nomor_kamar as nomor_kamar')
            ->from(['p' => Model::class])
            ->leftJoin('App\\Modules\\Hotel\\Master\\Tamu\\Model', 'p.tamu_id = t.id', 't')
            ->leftJoin('App\\Modules\\Hotel\\Master\\Kamar\\Model', 'p.ruangan_id = r.id', 'r')
            ->where("1=1")
            ->orderBy("p.id DESC");

        // Filter berdasarkan tamu
        $searchTamu = $this->request->getPost('search_tamu');
        if (!empty($searchTamu)) {
            $builder->andWhere("p.tamu_id = :tamu_id:", ['tamu_id' => $searchTamu]);
        }

        // Filter berdasarkan tanggal check in
        $searchCheckin = $this->request->getPost('search_checkin');
        if (!empty($searchCheckin)) {
            $builder->andWhere("p.tanggal_checkin = :checkin:", ['checkin' => $searchCheckin]);
        }

        // Filter berdasarkan tanggal check out
        $searchCheckout = $this->request->getPost('search_checkout');
        if (!empty($searchCheckout)) {
            $builder->andWhere("p.tanggal_checkout = :checkout:", ['checkout' => $searchCheckout]);
        }

        // Filter berdasarkan status
        $searchStatus = $this->request->getPost('search_status');
        if (!empty($searchStatus)) {
            $builder->andWhere("p.status = :status:", ['status' => $searchStatus]);
        }

        $dataTables = new DataTable();
        $dataTables->fromBuilder($builder)->sendResponse();
    }

    /**
     * @routeGet('/getTamu')
     */
    public function getTamuAction()
    {
        // Hanya tampilkan tamu yang tidak memiliki pemesanan aktif
        $tamu = $this->modelsManager->createBuilder()
            ->columns('t.id, t.nama_lengkap')
            ->from(['t' => '\App\Modules\Hotel\Master\Tamu\Model'])
            ->leftJoin('\App\Modules\Hotel\ReferensiData\Pemesanan\Model', 'p.tamu_id = t.id AND p.status IN ("menunggu", "dikonfirmasi", "checkin")', 'p')
            ->where('p.id IS NULL')
            ->orderBy('t.nama_lengkap ASC')
            ->getQuery()
            ->execute();

        return $this->response->setJsonContent([
            'error' => 0,
            'data' => $tamu->toArray()
        ]);
    }

    /**
     * @routeGet('/getTamuOptions')
     */
    public function getTamuOptionsAction()
    {
        $search = $this->request->get('q', 'string', '');
        
        $builder = $this->modelsManager->createBuilder()
            ->columns('t.id, t.nama_lengkap')
            ->from(['t' => '\App\Modules\Hotel\Master\Tamu\Model'])
            ->where('1=1');
            
        if (!empty($search)) {
            $builder->andWhere('t.nama_lengkap LIKE :search:', ['search' => '%' . $search . '%']);
        }
        
        $builder->orderBy('t.nama_lengkap ASC');
        $results = $builder->getQuery()->execute();
        
        $data = [];
        foreach ($results as $result) {
            $data[] = [
                'id' => $result->id,
                'text' => $result->nama_lengkap
            ];
        }
        
        return $this->response->setJsonContent($data);
    }

    /**
     * @routeGet('/getKamar')
     */
    public function getKamarAction()
    {
        $kamar = $this->modelsManager->createBuilder()
            ->columns('r.id, r.nomor_kamar, t.nama as tipe_nama, t.harga_per_malam')
            ->from(['r' => 'App\\Modules\\Hotel\\Master\\Kamar\\Model'])
            ->leftJoin('App\\Modules\\Hotel\\Master\\TipeKamar\\Model', 'r.tipe_ruangan_id = t.id', 't')
            ->where('r.status = :status: OR r.status IS NULL', ['status' => 'tersedia'])
            ->orderBy('r.nomor_kamar ASC')
            ->getQuery()
            ->execute();

        return $this->response->setJsonContent([
            'error' => 0,
            'data' => $kamar->toArray()
        ]);
    }

    /**
     * @routeGet('/getKamarHarga')
     */
    public function getKamarHargaAction()
    {
        $kamarId = $this->request->getQuery('kamar_id');
        
        if (!$kamarId) {
            return $this->response->setJsonContent([
                'error' => 1,
                'message' => 'Kamar ID required'
            ]);
        }
        
        $kamar = $this->modelsManager->createBuilder()
            ->columns('r.id, r.nomor_kamar, t.nama as tipe_nama, t.harga_per_malam')
            ->from(['r' => 'App\\Modules\\Hotel\\Master\\Kamar\\Model'])
            ->leftJoin('App\\Modules\\Hotel\\Master\\TipeKamar\\Model', 'r.tipe_ruangan_id = t.id', 't')
            ->where('r.id = :id:', ['id' => $kamarId])
            ->getQuery()
            ->execute()
            ->getFirst();

        if (!$kamar) {
            return $this->response->setJsonContent([
                'error' => 1,
                'message' => 'Kamar tidak ditemukan'
            ]);
        }

        return $this->response->setJsonContent([
            'error' => 0,
            'data' => $kamar->toArray()
        ]);
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

        try {
            $this->db->begin();

            // Simpan ruangan_id sebelum dihapus
            $ruanganId = $findForDelete->ruangan_id;

            if (!$findForDelete->delete()) {
                throw new Exception("Gagal menghapus data pemesanan");
            }

            // Kembalikan status kamar ke 'tersedia' setelah pemesanan dihapus
            if ($ruanganId) {
                $kamar = \App\Modules\Hotel\Master\Kamar\Model::findFirst($ruanganId);
                if ($kamar) {
                    $kamar->status = 'tersedia';
                    $kamar->update();
                }
            }

            $this->db->commit();

            return Response::setJsonContent([
                'error' => 0,
                'message' => "Hapus Data Berhasil"
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
     * @routePost('/updateData')
     */
    public function updateDataAction()
    {
        if (!$this->request->isPost()) {
            return Response::setJsonContent([
                'error' => 1,
                'message' => "Method not allowed"
            ]);
        }

        $id_edit = escape_xss($this->request->getPost('id_edit'));
        $tamu_id = (int)$this->request->getPost('tamu_id');
        $ruangan_id = (int)$this->request->getPost('ruangan_id');
        $tanggal_checkin = escape_xss($this->request->getPost('tanggal_checkin'));
        $tanggal_checkout = escape_xss($this->request->getPost('tanggal_checkout'));
        $jumlah_tamu = (int)$this->request->getPost('jumlah_tamu');
        $total_harga = (float)$this->request->getPost('total_harga');
        $status = escape_xss($this->request->getPost('status'));
        $catatan_khusus = escape_xss($this->request->getPost('catatan_khusus'));

        // Hitung jumlah malam dan validasi tanggal
        try {
            $checkin = new \DateTime($tanggal_checkin);
            $checkout = new \DateTime($tanggal_checkout);
        } catch (\Exception $e) {
            return $this->response->setJsonContent([
                'error' => 1,
                'message' => 'Format tanggal tidak valid'
            ]);
        }

        $jumlah_malam = $checkout->diff($checkin)->days;
        if ($jumlah_malam <= 0) {
            return $this->response->setJsonContent([
                'error' => 1,
                'message' => 'Tanggal checkout harus setelah tanggal checkin'
            ]);
        }

        // Validasi: cek apakah tamu sudah memiliki pemesanan aktif lainnya
        $existingBooking = Model::findFirst([
            'conditions' => 'tamu_id = :tamu_id: AND id != :current_id: AND status IN (:status1:, :status2:, :status3:)',
            'bind' => [
                'tamu_id' => $tamu_id,
                'current_id' => $id_edit,
                'status1' => 'menunggu',
                'status2' => 'dikonfirmasi',
                'status3' => 'checkin'
            ]
        ]);
        
        if ($existingBooking) {
            return $this->response->setJsonContent([
                'error' => 1,
                'message' => 'Tamu ini sudah memiliki pemesanan aktif lainnya dengan kode booking: ' . $existingBooking->kode_booking
            ]);
        }

        try {
            $this->db->begin();

            $actTransactData = Model::findFirst([
                'conditions' => "id = :id:",
                'bind' => ['id' => $id_edit]
            ]);

            if (!$actTransactData) {
                throw new Exception("Data tidak ditemukan");
            }

            // Simpan ruangan_id lama untuk perbandingan
            $oldRuanganId = $actTransactData->ruangan_id;

            $actTransactData->tamu_id = $tamu_id;
            $actTransactData->ruangan_id = $ruangan_id;
            $actTransactData->tanggal_checkin = $tanggal_checkin;
            $actTransactData->tanggal_checkout = $tanggal_checkout;
            $actTransactData->jumlah_tamu = $jumlah_tamu;
            $actTransactData->jumlah_malam = $jumlah_malam;
            $actTransactData->total_harga = $total_harga;
            $actTransactData->status = $status;
            $actTransactData->catatan_khusus = $catatan_khusus;

            if (!$actTransactData->update()) {
                $errorMessage = implode(', ', array_map(function ($m) {
                    return $m->getMessage();
                }, $actTransactData->getMessages()));
                throw new Exception($errorMessage ?: "Gagal update data");
            }

            // Jika kamar berubah, kembalikan status kamar lama ke 'tersedia'
            if ($oldRuanganId != $ruangan_id && $oldRuanganId) {
                $oldKamar = \App\Modules\Hotel\Master\Kamar\Model::findFirst($oldRuanganId);
                if ($oldKamar) {
                    $oldKamar->status = 'tersedia';
                    $oldKamar->update();
                }
            }

            // Update status pembayaran jika total harga berubah
            $pembayaran = \App\Modules\Hotel\ReferensiData\Pembayaran\Model::findFirst([
                'conditions' => 'pemesanan_id = :pemesanan_id:',
                'bind' => ['pemesanan_id' => $id_edit]
            ]);
            
            if ($pembayaran) {
                $jumlahBayar = (float)$pembayaran->jumlah_bayar;
                $newStatus = ($jumlahBayar >= $total_harga) ? 'lunas' : 'pending';
                
                if ($pembayaran->status !== $newStatus) {
                    $pembayaran->status = $newStatus;
                    $pembayaran->update();
                }
            }

            // Update status kamar berdasarkan status pemesanan
            $this->updateKamarStatusByBookingStatus($ruangan_id, $status);

            $this->db->commit();

            return $this->response->setJsonContent([
                'error' => 0,
                'message' => 'Update Data Berhasil'
            ]);
        } catch (Exception $e) {
            if ($this->db->isUnderTransaction()) {
                $this->db->rollback();
            }

            return $this->response->setJsonContent([
                'error' => 1,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
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

        $tamu_id = (int)$this->request->getPost('tamu_id');
        $ruangan_id = (int)$this->request->getPost('ruangan_id');
        $tanggal_checkin = escape_xss($this->request->getPost('tanggal_checkin'));
        $tanggal_checkout = escape_xss($this->request->getPost('tanggal_checkout'));
        $jumlah_tamu = (int)$this->request->getPost('jumlah_tamu');
        $total_harga = (float)$this->request->getPost('total_harga');
        $status = escape_xss($this->request->getPost('status'));
        $catatan_khusus = escape_xss($this->request->getPost('catatan_khusus'));

        // Validasi: cek apakah tamu sudah memiliki pemesanan aktif
        $existingBooking = Model::findFirst([
            'conditions' => 'tamu_id = :tamu_id: AND status IN (:status1:, :status2:, :status3:)',
            'bind' => [
                'tamu_id' => $tamu_id,
                'status1' => 'menunggu',
                'status2' => 'dikonfirmasi', 
                'status3' => 'checkin'
            ]
        ]);
        
        if ($existingBooking) {
            return $this->response->setJsonContent([
                'error' => 1,
                'message' => 'Tamu ini sudah memiliki pemesanan aktif dengan kode booking: ' . $existingBooking->kode_booking
            ]);
        }

        // Generate kode booking
        $kode_booking = 'BK' . date('Ymd') . str_pad((string)rand(1, 9999), 4, '0', STR_PAD_LEFT);

        // Hitung jumlah malam
        $checkin = new \DateTime($tanggal_checkin);
        $checkout = new \DateTime($tanggal_checkout);
        $jumlah_malam = $checkout->diff($checkin)->days;

        try {
            $this->db->begin();

            $actTransactData = new Model();
            $actTransactData->kode_booking = $kode_booking;
            $actTransactData->tamu_id = $tamu_id;
            $actTransactData->ruangan_id = $ruangan_id;
            $actTransactData->tanggal_checkin = $tanggal_checkin;
            $actTransactData->tanggal_checkout = $tanggal_checkout;
            $actTransactData->jumlah_tamu = $jumlah_tamu;
            $actTransactData->jumlah_malam = $jumlah_malam;
            $actTransactData->total_harga = $total_harga;
            $actTransactData->status = $status;
            $actTransactData->catatan_khusus = $catatan_khusus;

            if (!$actTransactData->save()) {
                $errorMessage = implode(', ', array_map(function ($m) {
                    return $m->getMessage();
                }, $actTransactData->getMessages()));
                throw new Exception($errorMessage ?: "Gagal simpan data");
            }

            // Update status pembayaran jika ada pembayaran terkait
            $pembayaran = \App\Modules\Hotel\ReferensiData\Pembayaran\Model::findFirst([
                'conditions' => 'pemesanan_id = :pemesanan_id:',
                'bind' => ['pemesanan_id' => $actTransactData->id]
            ]);
            
            if ($pembayaran) {
                $jumlahBayar = (float)$pembayaran->jumlah_bayar;
                $newStatus = ($jumlahBayar >= $total_harga) ? 'lunas' : 'pending';
                
                if ($pembayaran->status !== $newStatus) {
                    $pembayaran->status = $newStatus;
                    $pembayaran->update();
                }
            }

            // Update status kamar berdasarkan status pemesanan
            $this->updateKamarStatusByBookingStatus($ruangan_id, $status);

            $this->db->commit();

            return $this->response->setJsonContent([
                'error' => 0,
                'message' => 'Simpan Data Berhasil'
            ]);
        } catch (Exception $e) {
            if ($this->db->isUnderTransaction()) {
                $this->db->rollback();
            }

            return $this->response->setJsonContent([
                'error' => 1,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Helper function to update room status based on booking status
     */
    private function updateKamarStatusByBookingStatus($ruanganId, $bookingStatus)
    {
        if (!$ruanganId) return;
        
        $kamar = \App\Modules\Hotel\Master\Kamar\Model::findFirst($ruanganId);
        if (!$kamar) return;
        
        switch ($bookingStatus) {
            case 'dikonfirmasi':
            case 'checkin':
                $kamar->status = 'ditempati';
                break;
            case 'menunggu':
            case 'checkout':
            case 'dibatalkan':
                $kamar->status = 'tersedia';
                break;
        }
        
        $kamar->update();
    }
}
