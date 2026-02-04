<?php

declare(strict_types=1);

namespace App\Modules\Hotel\ReferensiData\Pembayaran;

use Core\Facades\Response;
use Core\Paginator\DataTables\DataTable;
use App\Modules\Defaults\BaseController;
use Exception;

/**
 * @routeGroup('/hotel/referensi-data/pembayaran')
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
            ->columns('pb.id, pb.pemesanan_id, pb.metode_pembayaran, pb.jumlah_bayar, pb.tanggal_bayar, pb.status, pb.keterangan, p.kode_booking as kode_booking, t.nama_lengkap as tamu_nama')
            ->from(['pb' => Model::class])
            ->leftJoin('App\\Modules\\Hotel\\ReferensiData\\Pemesanan\\Model', 'pb.pemesanan_id = p.id', 'p')
            ->leftJoin('App\\Modules\\Hotel\\Master\\Tamu\\Model', 'p.tamu_id = t.id', 't')
            ->where("1=1")
            ->orderBy("pb.id DESC");

        // Filter berdasarkan pemesanan
        $searchPemesanan = $this->request->getPost('search_pemesanan');
        if (!empty($searchPemesanan)) {
            $builder->andWhere("pb.pemesanan_id = :pemesanan_id:", ['pemesanan_id' => $searchPemesanan]);
        }

        // Filter berdasarkan metode pembayaran
        $searchMetode = $this->request->getPost('search_metode');
        if (!empty($searchMetode)) {
            $builder->andWhere("pb.metode_pembayaran = :metode:", ['metode' => $searchMetode]);
        }

        // Filter berdasarkan status
        $searchStatus = $this->request->getPost('search_status');
        if (!empty($searchStatus)) {
            $builder->andWhere("pb.status = :status:", ['status' => $searchStatus]);
        }

        // Filter berdasarkan tanggal bayar
        $searchTanggal = $this->request->getPost('search_tanggal');
        if (!empty($searchTanggal)) {
            $builder->andWhere("DATE(pb.tanggal_bayar) = :tanggal:", ['tanggal' => $searchTanggal]);
        }

        $dataTables = new DataTable();
        $dataTables->fromBuilder($builder)->sendResponse();
    }

    /**
     * @routeGet('/getPemesanan')
     */
    public function getPemesananAction()
    {
        // Hanya tampilkan pemesanan yang belum memiliki pembayaran
        $pemesanan = $this->modelsManager->createBuilder()
            ->columns('p.id, p.kode_booking, p.total_harga, t.nama_lengkap as tamu_nama')
            ->from(['p' => 'App\\Modules\\Hotel\\ReferensiData\\Pemesanan\\Model'])
            ->leftJoin('App\\Modules\\Hotel\\Master\\Tamu\\Model', 'p.tamu_id = t.id', 't')
            ->leftJoin('App\\Modules\\Hotel\\ReferensiData\\Pembayaran\\Model', 'pb.pemesanan_id = p.id', 'pb')
            ->where('p.status IN (:status1:, :status2:) AND pb.id IS NULL', ['status1' => 'dikonfirmasi', 'status2' => 'checkin'])
            ->orderBy('p.kode_booking ASC')
            ->getQuery()
            ->execute();

        return $this->response->setJsonContent([
            'error' => 0,
            'data' => $pemesanan->toArray()
        ]);
    }

    /**
     * @routeGet('/getAllPemesanan')
     */
    public function getAllPemesananAction()
    {
        // Untuk keperluan edit - tampilkan semua pemesanan
        $pemesanan = $this->modelsManager->createBuilder()
            ->columns('p.id, p.kode_booking, p.total_harga, t.nama_lengkap as tamu_nama')
            ->from(['p' => 'App\\Modules\\Hotel\\ReferensiData\\Pemesanan\\Model'])
            ->leftJoin('App\\Modules\\Hotel\\Master\\Tamu\\Model', 'p.tamu_id = t.id', 't')
            ->where('p.status IN (:status1:, :status2:)', ['status1' => 'dikonfirmasi', 'status2' => 'checkin'])
            ->orderBy('p.kode_booking ASC')
            ->getQuery()
            ->execute();

        return $this->response->setJsonContent([
            'error' => 0,
            'data' => $pemesanan->toArray()
        ]);
    }

    /**
     * @routeGet('/getPemesananOptions')
     */
    public function getPemesananOptionsAction()
    {
        $search = $this->request->get('q', 'string', '');

        $builder = $this->modelsManager->createBuilder()
            ->columns('p.id, p.kode_booking, t.nama_lengkap as tamu_nama')
            ->from(['p' => 'App\\Modules\\Hotel\\ReferensiData\\Pemesanan\\Model'])
            ->leftJoin('App\\Modules\\Hotel\\Master\\Tamu\\Model', 'p.tamu_id = t.id', 't')
            ->where('p.status IN (:status1:, :status2:)', ['status1' => 'dikonfirmasi', 'status2' => 'checkin']);

        if (!empty($search)) {
            $builder->andWhere('(p.kode_booking LIKE :search: OR t.nama_lengkap LIKE :search2:)', [
                'search' => '%' . $search . '%',
                'search2' => '%' . $search . '%'
            ]);
        }

        $builder->orderBy('p.kode_booking ASC');
        $results = $builder->getQuery()->execute();

        $data = [];
        foreach ($results as $result) {
            $data[] = [
                'id' => $result->id,
                'text' => $result->kode_booking . ' - ' . $result->tamu_nama
            ];
        }

        return $this->response->setJsonContent($data);
    }

    /**
     * @routeGet('/getPemesananDetail')
     */
    public function getPemesananDetailAction()
    {
        $pemesananId = $this->request->getQuery('pemesanan_id');

        if (!$pemesananId) {
            return $this->response->setJsonContent([
                'error' => 1,
                'message' => 'Pemesanan ID required'
            ]);
        }

        $pemesanan = $this->modelsManager->createBuilder()
            ->columns('p.id, p.kode_booking, p.total_harga, p.tamu_id, p.ruangan_id, p.tanggal_checkin, p.tanggal_checkout, t.nama_lengkap as tamu_nama, r.nomor_kamar')
            ->from(['p' => 'App\\Modules\\Hotel\\ReferensiData\\Pemesanan\\Model'])
            ->leftJoin('App\\Modules\\Hotel\\Master\\Tamu\\Model', 'p.tamu_id = t.id', 't')
            ->leftJoin('App\\Modules\\Hotel\\Master\\Kamar\\Model', 'p.ruangan_id = r.id', 'r')
            ->where('p.id = :id:', ['id' => $pemesananId])
            ->getQuery()
            ->execute()
            ->getFirst();

        if (!$pemesanan) {
            return $this->response->setJsonContent([
                'error' => 1,
                'message' => 'Pemesanan tidak ditemukan'
            ]);
        }

        return $this->response->setJsonContent([
            'error' => 0,
            'data' => $pemesanan->toArray()
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

        if ($findForDelete->delete()) {
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

    /**
     * @routePost('/updateData')
     */
    public function updateDataAction()
    {
        if (!$this->request->isPost()) {
            return $this->response->setJsonContent([
                'error' => 1,
                'message' => "Method not allowed"
            ]);
        }

        $id_edit = escape_xss($this->request->getPost('id_edit'));
        $pemesanan_id = (int)$this->request->getPost('pemesanan_id');
        $metode_pembayaran = escape_xss($this->request->getPost('metode_pembayaran'));
        $jumlah_bayar = (float)$this->request->getPost('jumlah_bayar');
        $tanggal_bayar = escape_xss($this->request->getPost('tanggal_bayar'));
        $keterangan = escape_xss($this->request->getPost('keterangan'));

        // Get total harga from pemesanan
        $pemesanan = \App\Modules\Hotel\ReferensiData\Pemesanan\Model::findFirst([
            'conditions' => 'id = :id:',
            'bind' => ['id' => $pemesanan_id]
        ]);

        if (!$pemesanan) {
            return $this->response->setJsonContent([
                'error' => 1,
                'message' => 'Pemesanan tidak ditemukan'
            ]);
        }

        // Auto-calculate status based on payment amount
        $totalHarga = (float)$pemesanan->total_harga;
        $status = ($jumlah_bayar >= $totalHarga) ? 'lunas' : 'pending';

        // Validasi pemesanan_id exists
        if (!$pemesanan_id) {
            return $this->response->setJsonContent([
                'error' => 1,
                'message' => 'Pemesanan harus dipilih'
            ]);
        }

        // Validasi: cek apakah pemesanan sudah memiliki pembayaran lain
        $existingPayment = Model::findFirst([
            'conditions' => 'pemesanan_id = :pemesanan_id: AND id != :current_id:',
            'bind' => [
                'pemesanan_id' => $pemesanan_id,
                'current_id' => $id_edit
            ]
        ]);

        if ($existingPayment) {
            return $this->response->setJsonContent([
                'error' => 1,
                'message' => 'Pemesanan ini sudah memiliki data pembayaran lainnya'
            ]);
        }

        try {
            $this->db->begin();

            $actTransactData = Model::findFirst([
                'conditions' => "id = :id:",
                'bind' => ['id' => $id_edit]
            ]);

            if (!$actTransactData) {
                throw new Exception("Data pembayaran tidak ditemukan");
            }

            $actTransactData->pemesanan_id = $pemesanan_id;
            $actTransactData->metode_pembayaran = $metode_pembayaran;
            $actTransactData->jumlah_bayar = $jumlah_bayar;
            $actTransactData->tanggal_bayar = $tanggal_bayar;
            $actTransactData->status = $status;
            $actTransactData->keterangan = $keterangan;

            if (!$actTransactData->update()) {
                $messages = [];
                foreach ($actTransactData->getMessages() as $message) {
                    $messages[] = $message->getMessage();
                }
                throw new Exception("Gagal update data: " . implode(', ', $messages));
            }

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
            return $this->response->setJsonContent([
                'error' => 1,
                'message' => "Method not allowed"
            ]);
        }

        $pemesanan_id = (int)$this->request->getPost('pemesanan_id');
        $metode_pembayaran = escape_xss($this->request->getPost('metode_pembayaran'));
        $jumlah_bayar = (float)$this->request->getPost('jumlah_bayar');
        $tanggal_bayar = escape_xss($this->request->getPost('tanggal_bayar'));
        $keterangan = escape_xss($this->request->getPost('keterangan'));

        // Get total harga from pemesanan
        $pemesanan = \App\Modules\Hotel\ReferensiData\Pemesanan\Model::findFirst([
            'conditions' => 'id = :id:',
            'bind' => ['id' => $pemesanan_id]
        ]);

        if (!$pemesanan) {
            return $this->response->setJsonContent([
                'error' => 1,
                'message' => 'Pemesanan tidak ditemukan'
            ]);
        }

        // Auto-calculate status based on payment amount
        $totalHarga = (float)$pemesanan->total_harga;
        $status = ($jumlah_bayar >= $totalHarga) ? 'lunas' : 'pending';

        // Validasi: cek apakah pemesanan sudah memiliki pembayaran
        $existingPayment = Model::findFirst([
            'conditions' => 'pemesanan_id = :pemesanan_id:',
            'bind' => ['pemesanan_id' => $pemesanan_id]
        ]);

        if ($existingPayment) {
            return $this->response->setJsonContent([
                'error' => 1,
                'message' => 'Pemesanan ini sudah memiliki data pembayaran'
            ]);
        }

        // Validasi required fields
        if (!$pemesanan_id) {
            return $this->response->setJsonContent([
                'error' => 1,
                'message' => 'Pemesanan harus dipilih'
            ]);
        }

        if (!$metode_pembayaran) {
            return $this->response->setJsonContent([
                'error' => 1,
                'message' => 'Metode pembayaran harus dipilih'
            ]);
        }

        if (!$jumlah_bayar || $jumlah_bayar <= 0) {
            return $this->response->setJsonContent([
                'error' => 1,
                'message' => 'Jumlah bayar harus diisi dan lebih dari 0'
            ]);
        }

        // Check if pemesanan exists
        $pemesananExists = \App\Modules\Hotel\ReferensiData\Pemesanan\Model::findFirst([
            'conditions' => 'id = :id:',
            'bind' => ['id' => $pemesanan_id]
        ]);

        if (!$pemesananExists) {
            return $this->response->setJsonContent([
                'error' => 1,
                'message' => 'Pemesanan dengan ID ' . $pemesanan_id . ' tidak ditemukan'
            ]);
        }

        try {
            $this->db->begin();

            $actTransactData = new Model();
            $actTransactData->pemesanan_id = $pemesanan_id;
            $actTransactData->metode_pembayaran = $metode_pembayaran;
            $actTransactData->jumlah_bayar = $jumlah_bayar;
            $actTransactData->tanggal_bayar = $tanggal_bayar;
            $actTransactData->status = $status;
            $actTransactData->keterangan = $keterangan;

            if (!$actTransactData->save()) {
                $messages = [];
                foreach ($actTransactData->getMessages() as $message) {
                    $messages[] = $message->getMessage();
                }
                throw new Exception("Gagal simpan data: " . implode(', ', $messages));
            }

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
}
