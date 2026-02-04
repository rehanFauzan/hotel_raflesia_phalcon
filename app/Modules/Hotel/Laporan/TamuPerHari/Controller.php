<?php

declare(strict_types=1);

namespace App\Modules\Hotel\Laporan\TamuPerHari;

use Core\Facades\Response;
use Core\Paginator\DataTables\DataTable;
use App\Modules\Defaults\BaseController;

/**
 * @routeGroup('/hotel/laporan/tamu-harian')
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
            ->columns('t.id, t.nama_lengkap, t.jenis_identitas, t.no_identitas, t.jenis_kelamin, t.no_telepon, r.nomor_kamar as nomor_kamar, p.tanggal_checkin, p.status')
            ->from(['t' => 'App\\Modules\\Hotel\\Master\\Tamu\\Model'])
            ->leftJoin('App\\Modules\\Hotel\\ReferensiData\\Pemesanan\\Model', 't.id = p.tamu_id', 'p')
            ->leftJoin('App\\Modules\\Hotel\\Master\\Kamar\\Model', 'p.ruangan_id = r.id', 'r')
            ->where("p.status IN ('checkin', 'checkout')")
            ->orderBy("p.tanggal_checkin DESC");

        // Filter berdasarkan tanggal (default hari ini)
        $searchTanggal = $this->request->getPost('search_tanggal');
        if (empty($searchTanggal)) {
            $searchTanggal = date('Y-m-d'); // Default ke hari ini
        }
        $builder->andWhere("DATE(p.tanggal_checkin) = :tanggal:", ['tanggal' => $searchTanggal]);

        // Filter berdasarkan status
        $searchStatus = $this->request->getPost('search_status');
        if (!empty($searchStatus)) {
            $builder->andWhere("p.status = :status:", ['status' => $searchStatus]);
        }

        // Filter berdasarkan jenis kelamin
        $searchJenisKelamin = $this->request->getPost('search_jenis_kelamin');
        if (!empty($searchJenisKelamin)) {
            $builder->andWhere("t.jenis_kelamin = :jenis_kelamin:", ['jenis_kelamin' => $searchJenisKelamin]);
        }

        $dataTables = new DataTable();
        $dataTables->fromBuilder($builder)->sendResponse();
    }

    /**
     * @routeGet('/pdf')
     */
    public function pdfAction()
    {
        $searchTanggal = $this->request->getQuery('search_tanggal');
        $searchStatus = $this->request->getQuery('search_status');
        $searchJenisKelamin = $this->request->getQuery('search_jenis_kelamin');

        $builder = $this->modelsManager->createBuilder()
            ->columns('t.nama_lengkap, t.jenis_identitas, t.no_identitas, t.jenis_kelamin, t.no_telepon, r.nomor_kamar, p.tanggal_checkin, p.status')
            ->from(['t' => 'App\\Modules\\Hotel\\Master\\Tamu\\Model'])
            ->leftJoin('App\\Modules\\Hotel\\ReferensiData\\Pemesanan\\Model', 't.id = p.tamu_id', 'p')
            ->leftJoin('App\\Modules\\Hotel\\Master\\Kamar\\Model', 'p.ruangan_id = r.id', 'r')
            ->where("p.status IN ('checkin', 'checkout')")
            ->orderBy("p.tanggal_checkin DESC");

        if (!empty($searchTanggal)) {
            $builder->andWhere("DATE(p.tanggal_checkin) = :tanggal:", ['tanggal' => $searchTanggal]);
        }

        if (!empty($searchStatus)) {
            $builder->andWhere("p.status = :status:", ['status' => $searchStatus]);
        }

        if (!empty($searchJenisKelamin)) {
            $builder->andWhere("t.jenis_kelamin = :jenis_kelamin:", ['jenis_kelamin' => $searchJenisKelamin]);
        }

        $data = $builder->getQuery()->execute();

        // Generate PDF
        $pdf = new \App\Modules\Defaults\HeaderPdf('P', 'mm', 'A4');
        $pdf->SetAutoPageBreak(true, 15);
        $pdf->AddPage();
        
        // Title
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'LAPORAN TAMU HARIAN', 0, 1, 'C');
        $pdf->Ln(5);
        
        // Filter info
        $pdf->SetFont('Arial', '', 10);
        if (!empty($searchTanggal)) {
            $pdf->Cell(0, 5, 'Tanggal: ' . date('d/m/Y', strtotime($searchTanggal)), 0, 1);
        }
        if (!empty($searchStatus)) {
            $pdf->Cell(0, 5, 'Status: ' . ucfirst($searchStatus), 0, 1);
        }
        $pdf->Ln(5);
        
        // Table header
        $pdf->SetFont('Arial', 'B', 8);
        $pdf->Cell(10, 8, 'No', 1, 0, 'C');
        $pdf->Cell(35, 8, 'Nama Tamu', 1, 0, 'C');
        $pdf->Cell(20, 8, 'Identitas', 1, 0, 'C');
        $pdf->Cell(30, 8, 'No. Identitas', 1, 0, 'C');
        $pdf->Cell(15, 8, 'JK', 1, 0, 'C');
        $pdf->Cell(25, 8, 'No. Telepon', 1, 0, 'C');
        $pdf->Cell(15, 8, 'Kamar', 1, 0, 'C');
        $pdf->Cell(25, 8, 'Tgl Check-in', 1, 0, 'C');
        $pdf->Cell(20, 8, 'Status', 1, 1, 'C');
        
        // Table data
        $pdf->SetFont('Arial', '', 8);
        $no = 1;
        foreach ($data as $row) {
            $pdf->Cell(10, 6, $no++, 1, 0, 'C');
            $pdf->Cell(35, 6, $row->nama_lengkap ?: '-', 1, 0, 'L');
            $pdf->Cell(20, 6, strtoupper($row->jenis_identitas ?: '-'), 1, 0, 'C');
            $pdf->Cell(30, 6, $row->no_identitas ?: '-', 1, 0, 'C');
            $pdf->Cell(15, 6, $row->jenis_kelamin === 'L' ? 'L' : 'P', 1, 0, 'C');
            $pdf->Cell(25, 6, $row->no_telepon ?: '-', 1, 0, 'C');
            $pdf->Cell(15, 6, $row->nomor_kamar ?: '-', 1, 0, 'C');
            $pdf->Cell(25, 6, date('d/m/Y', strtotime($row->tanggal_checkin)), 1, 0, 'C');
            $pdf->Cell(20, 6, ucfirst($row->status), 1, 1, 'C');
        }
        
        $pdf->Output('D', 'Laporan_Tamu_Harian_' . date('Y-m-d') . '.pdf');
    }
}