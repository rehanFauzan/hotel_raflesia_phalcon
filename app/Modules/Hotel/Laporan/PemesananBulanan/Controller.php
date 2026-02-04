<?php

declare(strict_types=1);

namespace App\Modules\Hotel\Laporan\PemesananBulanan;

use Core\Facades\Response;
use Core\Paginator\DataTables\DataTable;
use App\Modules\Defaults\BaseController;
use App\Modules\Defaults\HeaderPdf;
use App\Libraries\MctablePdf;

/**
 * @routeGroup('/hotel/laporan/pemesanan-bulanan')
 * @middleware('RequireUser')
 */
class Controller extends BaseController
{
    /**
     * @routeGet('/')
     */
    public function indexAction()
    {
        // Set view permissions if needed
    }

    /**
     * @routePost('/datatable')
     * @routeGet('/datatable')
     */
    public function datatableAction()
    {
        $builder = $this->modelsManager->createBuilder()
            ->columns('p.id, p.kode_booking, p.tanggal_checkin, p.tanggal_checkout, p.jumlah_malam, p.total_harga, p.status, t.nama_lengkap as tamu_nama, r.nomor_kamar')
            ->from(['p' => 'App\\Modules\\Hotel\\ReferensiData\\Pemesanan\\Model'])
            ->leftJoin('App\\Modules\\Hotel\\Master\\Tamu\\Model', 'p.tamu_id = t.id', 't')
            ->leftJoin('App\\Modules\\Hotel\\Master\\Kamar\\Model', 'p.ruangan_id = r.id', 'r')
            ->where("1=1")
            ->orderBy("p.tanggal_checkin DESC");

        // Filter berdasarkan bulan dan tahun
        $searchBulan = $this->request->getPost('search_bulan');
        if (!empty($searchBulan)) {
            $builder->andWhere("DATE_FORMAT(p.tanggal_checkin, '%Y-%m') = :bulan:", ['bulan' => $searchBulan]);
        }

        // Filter berdasarkan tanggal mulai
        $searchTanggalMulai = $this->request->getPost('search_tanggal_mulai');
        if (!empty($searchTanggalMulai)) {
            $builder->andWhere("(p.tanggal_checkin >= :tanggal_mulai: OR p.tanggal_checkout >= :tanggal_mulai2:)", ['tanggal_mulai' => $searchTanggalMulai, 'tanggal_mulai2' => $searchTanggalMulai]);
        }

        // Filter berdasarkan tanggal selesai
        $searchTanggalSelesai = $this->request->getPost('search_tanggal_selesai');
        if (!empty($searchTanggalSelesai)) {
            $builder->andWhere("(p.tanggal_checkin <= :tanggal_selesai: OR p.tanggal_checkout <= :tanggal_selesai2:)", ['tanggal_selesai' => $searchTanggalSelesai, 'tanggal_selesai2' => $searchTanggalSelesai]);
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
     * @routeGet('/pdf')
     */
    public function pdfAction()
    {
        $builder = $this->modelsManager->createBuilder()
            ->columns('p.id, p.kode_booking, p.tanggal_checkin, p.tanggal_checkout, p.jumlah_malam, p.total_harga, p.status, t.nama_lengkap as tamu_nama, r.nomor_kamar')
            ->from(['p' => 'App\\Modules\\Hotel\\ReferensiData\\Pemesanan\\Model'])
            ->leftJoin('App\\Modules\\Hotel\\Master\\Tamu\\Model', 'p.tamu_id = t.id', 't')
            ->leftJoin('App\\Modules\\Hotel\\Master\\Kamar\\Model', 'p.ruangan_id = r.id', 'r')
            ->where("1=1")
            ->orderBy("p.tanggal_checkin DESC");

        // Filter berdasarkan bulan dan tahun
        $searchBulan = $this->request->getQuery('search_bulan');
        if (!empty($searchBulan)) {
            $builder->andWhere("DATE_FORMAT(p.tanggal_checkin, '%Y-%m') = :bulan:", ['bulan' => $searchBulan]);
        }

        // Filter berdasarkan tanggal mulai
        $searchTanggalMulai = $this->request->getQuery('search_tanggal_mulai');
        if (!empty($searchTanggalMulai)) {
            $builder->andWhere("(p.tanggal_checkin >= :tanggal_mulai: OR p.tanggal_checkout >= :tanggal_mulai2:)", ['tanggal_mulai' => $searchTanggalMulai, 'tanggal_mulai2' => $searchTanggalMulai]);
        }

        // Filter berdasarkan tanggal selesai
        $searchTanggalSelesai = $this->request->getQuery('search_tanggal_selesai');
        if (!empty($searchTanggalSelesai)) {
            $builder->andWhere("(p.tanggal_checkin <= :tanggal_selesai: OR p.tanggal_checkout <= :tanggal_selesai2:)", ['tanggal_selesai' => $searchTanggalSelesai, 'tanggal_selesai2' => $searchTanggalSelesai]);
        }

        // Filter berdasarkan status
        $searchStatus = $this->request->getQuery('search_status');
        if (!empty($searchStatus)) {
            $builder->andWhere("p.status = :status:", ['status' => $searchStatus]);
        }

        $data = $builder->getQuery()->execute();

        // Create PDF
        $pdf = new HeaderPdf('L', 'mm', 'A4'); // Landscape orientation
        $pdf->AddPage();
        
        // Title
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'LAPORAN PEMESANAN KAMAR BULANAN', 0, 1, 'C');
        $pdf->Ln(5);
        
        // Filter info
        if (!empty($searchBulan)) {
            $pdf->SetFont('Arial', '', 10);
            $bulanTahun = date('F Y', strtotime($searchBulan . '-01'));
            $pdf->Cell(0, 6, 'Periode: ' . $bulanTahun, 0, 1, 'L');
        }
        if (!empty($searchStatus)) {
            $pdf->Cell(0, 6, 'Status: ' . ucfirst($searchStatus), 0, 1, 'L');
        }
        $pdf->Ln(3);
        
        // Table header
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->SetFillColor(230, 230, 230);
        
        $header = ['No', 'Kode Booking', 'Nama Tamu', 'No. Kamar', 'Check-in', 'Check-out', 'Malam', 'Total Harga', 'Status'];
        $widths = [10, 25, 40, 20, 25, 25, 15, 30, 25];
        
        foreach ($header as $i => $col) {
            $pdf->Cell($widths[$i], 8, $col, 1, 0, 'C', true);
        }
        $pdf->Ln();
        
        // Table data
        $pdf->SetFont('Arial', '', 8);
        $pdf->SetFillColor(255, 255, 255);
        
        $no = 1;
        $totalHarga = 0;
        
        foreach ($data as $row) {
            $pdf->Cell($widths[0], 6, $no++, 1, 0, 'C');
            $pdf->Cell($widths[1], 6, $row->kode_booking ?: '-', 1, 0, 'C');
            $pdf->Cell($widths[2], 6, $row->tamu_nama ?: '-', 1, 0, 'L');
            $pdf->Cell($widths[3], 6, $row->nomor_kamar ?: '-', 1, 0, 'C');
            $pdf->Cell($widths[4], 6, $row->tanggal_checkin ?: '-', 1, 0, 'C');
            $pdf->Cell($widths[5], 6, $row->tanggal_checkout ?: '-', 1, 0, 'C');
            $pdf->Cell($widths[6], 6, ($row->jumlah_malam ?: '0') . ' malam', 1, 0, 'C');
            $pdf->Cell($widths[7], 6, 'Rp ' . number_format((float)($row->total_harga ?: 0), 0, ',', '.'), 1, 0, 'R');
            $pdf->Cell($widths[8], 6, ucfirst($row->status ?: '-'), 1, 0, 'C');
            $pdf->Ln();
            
            $totalHarga += (float)($row->total_harga ?: 0);
        }
        
        // Total row
        $pdf->SetFont('Arial', 'B', 9);
        $pdf->Cell(array_sum(array_slice($widths, 0, 7)), 6, 'TOTAL', 1, 0, 'C');
        $pdf->Cell($widths[7], 6, 'Rp ' . number_format((float)$totalHarga, 0, ',', '.'), 1, 0, 'R');
        $pdf->Cell($widths[8], 6, '', 1, 0, 'C');
        
        // Output PDF
        $filename = 'Laporan_Pemesanan_Bulanan_' . date('Y-m-d_H-i-s') . '.pdf';
        $pdf->Output('D', $filename);
    }
}