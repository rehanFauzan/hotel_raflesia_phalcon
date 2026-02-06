<?php

declare(strict_types=1);

namespace App\Modules\Hotel\Dashboard;

use App\Modules\Defaults\BaseController;
use Core\Facades\Response;

/**
 * @routeGroup('/hotel/dashboard')
 * @middleware('RequireUser')
 */
class Controller extends BaseController
{
    /**
     * @routeGet('/')
     */
    public function indexAction()
    {
        // Dashboard view will load data via AJAX
    }

    /**
     * @routeGet('/stats')
     */
    public function statsAction()
    {
        $this->response->setContentType('application/json', 'UTF-8');
        
        try {
            $bulanIni = date('Y-m');
            
            // Pemesanan bulan ini
            $pemesananResult = $this->db->fetchOne("
                SELECT COUNT(*) as total 
                FROM hotel_pemesanan 
                WHERE DATE_FORMAT(created_at, '%Y-%m') = '$bulanIni'
            ");
            $pemesananBulanIni = $pemesananResult ? $pemesananResult['total'] : 0;
            
            // Kamar tersedia
            $kamarResult = $this->db->fetchOne("
                SELECT COUNT(*) as total 
                FROM hotel_ruangan 
                WHERE status = 'tersedia' OR status IS NULL OR status = ''
            ");
            $kamarTersedia = $kamarResult ? $kamarResult['total'] : 0;
            
            // Pembatalan bulan ini
            $pembatalanResult = $this->db->fetchOne("
                SELECT COUNT(*) as total 
                FROM hotel_pemesanan 
                WHERE DATE_FORMAT(created_at, '%Y-%m') = '$bulanIni' 
                AND status = 'dibatalkan'
            ");
            $pembatalanBulanIni = $pembatalanResult ? $pembatalanResult['total'] : 0;
            
            // Pendapatan bulan ini
            $pendapatanResult = $this->db->fetchOne("
                SELECT COALESCE(SUM(total_harga), 0) as total 
                FROM hotel_pemesanan 
                WHERE DATE_FORMAT(created_at, '%Y-%m') = '$bulanIni' 
                AND status IN ('checkin', 'checkout')
            ");
            $pendapatanBulanIni = $pendapatanResult ? $pendapatanResult['total'] : 0;
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'pemesanan_bulan_ini' => (int)$pemesananBulanIni,
                    'kamar_tersedia' => (int)$kamarTersedia,
                    'pembatalan_bulan_ini' => (int)$pembatalanBulanIni,
                    'pendapatan_bulan_ini' => (float)$pendapatanBulanIni
                ]
            ]);
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        
        return false;
    }

    /**
     * @routeGet('/chart-pemesanan')
     */
    public function chartPemesananAction()
    {
        $this->response->setContentType('application/json', 'UTF-8');
        
        try {
            $data = $this->db->fetchAll("
                SELECT 
                    DATE_FORMAT(created_at, '%Y-%m') as bulan,
                    COUNT(*) as jumlah
                FROM hotel_pemesanan 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                ORDER BY bulan ASC
            ");
            
            $labels = [];
            $values = [];
            
            if ($data) {
                foreach ($data as $row) {
                    $labels[] = date('M Y', strtotime($row['bulan'] . '-01'));
                    $values[] = (int)$row['jumlah'];
                }
            }
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'labels' => $labels,
                    'values' => $values
                ]
            ]);
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        
        return false;
    }

    /**
     * @routeGet('/chart-kamar')
     */
    public function chartKamarAction()
    {
        $this->response->setContentType('application/json', 'UTF-8');
        
        try {
            $today = date('Y-m-d');
            
            // Kamar ditempati
            $kamarDitempatResult = $this->db->fetchOne("
                SELECT COUNT(*) as total 
                FROM hotel_ruangan 
                WHERE status = 'ditempati'
            ");
            $kamarDitempati = $kamarDitempatResult ? $kamarDitempatResult['total'] : 0;
            
            // Kamar tersedia
            $kamarTersediaResult = $this->db->fetchOne("
                SELECT COUNT(*) as total 
                FROM hotel_ruangan 
                WHERE status = 'tersedia' OR status IS NULL OR status = ''
            ");
            $kamarTersedia = $kamarTersediaResult ? $kamarTersediaResult['total'] : 0;
            
            // Kamar maintenance
            $kamarMaintenanceResult = $this->db->fetchOne("
                SELECT COUNT(*) as total 
                FROM hotel_ruangan 
                WHERE status = 'maintenance'
            ");
            $kamarMaintenance = $kamarMaintenanceResult ? $kamarMaintenanceResult['total'] : 0;
            
            echo json_encode([
                'success' => true,
                'data' => [
                    'ditempati' => (int)$kamarDitempati,
                    'tersedia' => (int)$kamarTersedia,
                    'maintenance' => (int)$kamarMaintenance
                ]
            ]);
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        
        return false;
    }

    /**
     * @routeGet('/recent-bookings')
     */
    public function recentBookingsAction()
    {
        $this->response->setContentType('application/json', 'UTF-8');
        
        try {
            $data = $this->db->fetchAll("
                SELECT 
                    p.kode_booking,
                    t.nama_lengkap as tamu_nama,
                    r.nomor_kamar,
                    p.tanggal_checkin,
                    p.status,
                    p.total_harga
                FROM hotel_pemesanan p
                LEFT JOIN hotel_tamu t ON p.tamu_id = t.id
                LEFT JOIN hotel_ruangan r ON p.ruangan_id = r.id
                ORDER BY p.id DESC
                LIMIT 10
            ");
            
            echo json_encode([
                'success' => true,
                'data' => $data ?: []
            ]);
        } catch (\Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
        
        return false;
    }
}