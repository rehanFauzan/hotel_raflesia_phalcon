-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Waktu pembuatan: 29 Jan 2026 pada 05.25
-- Versi server: 10.4.21-MariaDB
-- Versi PHP: 8.0.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hotel_reservasi_raflesia_bdg`
--

DELIMITER $$
--
-- Prosedur
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `hotel_cek_ketersediaan_kamar` (IN `p_tipe_id` INT, IN `p_checkin` DATE, IN `p_checkout` DATE)   BEGIN
    SELECT 
        r.id,
        r.nomor_kamar,
        r.lantai,
        r.status,
        tr.nama AS tipe_ruangan,
        tr.harga_per_malam,
        tr.kapasitas,
        tr.fasilitas
    FROM hotel_ruangan r
    JOIN hotel_tipe_ruangan tr ON r.tipe_ruangan_id = tr.id
    WHERE r.tipe_ruangan_id = p_tipe_id 
    AND r.status = 'tersedia'
    AND r.id NOT IN (
        SELECT ruangan_id 
        FROM hotel_pemesanan 
        WHERE NOT (tanggal_checkout <= p_checkin OR tanggal_checkin >= p_checkout)
        AND status IN ('dikonfirmasi', 'checkin')
    );
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_proses_periode` (IN `vm` INT, IN `vy` INT)   BEGIN
    DECLARE stts INT DEFAULT 0;

    -- Insert akan berhasil hanya jika (periode_m, periode_y) belum ada
    INSERT INTO periodeaktif (periode_m, periode_y, is_closed, create_dt)
    VALUES (vm, vy, 0, NOW())
    ON DUPLICATE KEY UPDATE
        -- tidak mengubah data, hanya “memicu” jalur duplicate
        periode_m = periode_m;

    IF ROW_COUNT() = 1 THEN
        SET stts = 1;   -- baris baru berhasil diinsert
    ELSE
        SET stts = 0;   -- sudah ada (kena duplicate key)
    END IF;

    SELECT stts AS stts;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `system_sp_set_menu_aksi` (IN `p_RoleID` INT, IN `p_MenuID` INT, IN `p_Value` INT, IN `p_Type` VARCHAR(255))   BEGIN
    DECLARE v_privilegeFound INT DEFAULT 0;
    DECLARE v_columnName VARCHAR(50);
    DECLARE v_rowsAffected INT DEFAULT 0;
    -- Cari apakah privilege sudah ada
    SELECT IFNULL(id, 0) INTO v_privilegeFound
    FROM system_menu_otorisasi
    WHERE id_menu = p_MenuID AND id_role = p_RoleID
    LIMIT 1;
    -- Validasi: jika record tidak ditemukan, kembalikan error
    IF v_privilegeFound = 0 THEN
        SELECT 0 AS last_id, 0 AS state;
    ELSE
        -- Mapping type ke column name
        CASE p_Type
            WHEN 'input' THEN
                UPDATE system_menu_otorisasi 
                SET hak_input = p_Value 
                WHERE id_menu = p_MenuID AND id_role = p_RoleID;
                
            WHEN 'ubah' THEN
                UPDATE system_menu_otorisasi 
                SET hak_ubah = p_Value 
                WHERE id_menu = p_MenuID AND id_role = p_RoleID;
                
            WHEN 'hapus' THEN
                UPDATE system_menu_otorisasi 
                SET hak_hapus = p_Value 
                WHERE id_menu = p_MenuID AND id_role = p_RoleID;
                
            WHEN 'cetak' THEN
                UPDATE system_menu_otorisasi 
                SET hak_cetak = p_Value 
                WHERE id_menu = p_MenuID AND id_role = p_RoleID;
                
            WHEN 'verifikasi' THEN
                UPDATE system_menu_otorisasi 
                SET hak_verifikasi = p_Value 
                WHERE id_menu = p_MenuID AND id_role = p_RoleID;
                
            WHEN 'unverifikasi' THEN
                UPDATE system_menu_otorisasi 
                SET hak_unverifikasi = p_Value 
                WHERE id_menu = p_MenuID AND id_role = p_RoleID;
                
            ELSE
                -- Type tidak valid
                SELECT 0 AS last_id, 0 AS state;
        END CASE;
        -- Cek apakah ada row yang terupdate
        SET v_rowsAffected = ROW_COUNT();
        
        IF v_rowsAffected > 0 THEN
            SELECT 1 AS state;
        ELSE
            SELECT 0 AS last_id, 0 AS state;
        END IF;
        
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `system_sp_set_menu_hak` (IN `p_RoleID` INT, IN `p_MenuID` INT, IN `p_Value` INT)   BEGIN
    DECLARE v_privilegeFound INT DEFAULT 0;
    DECLARE v_insertedId INT;
    -- Cari apakah privilege sudah ada
    SELECT IFNULL(id, 0) INTO v_privilegeFound
    FROM system_menu_otorisasi
    WHERE id_menu = p_MenuID AND id_role = p_RoleID
    LIMIT 1;
    IF p_Value = 1 THEN
        -- Jika ingin menambahkan hak akses
        IF v_privilegeFound = 0 THEN
            INSERT INTO system_menu_otorisasi (id_menu, id_role)
            VALUES (p_MenuID, p_RoleID);
            
            SET v_insertedId = LAST_INSERT_ID();
            SELECT v_insertedId AS last_id, 1 AS state;
        ELSE
            -- Sudah ada, kembalikan state 0
            SELECT 0 AS last_id, 0 AS state;
        END IF;
        
    ELSEIF p_Value = 0 THEN
        -- Jika ingin menghapus hak akses
        IF v_privilegeFound != 0 THEN
            DELETE FROM system_menu_otorisasi 
            WHERE id_menu = p_MenuID AND id_role = p_RoleID;
            
            SELECT v_privilegeFound AS last_id, 1 AS state;
        ELSE
            -- Tidak ditemukan, kembalikan state 0
            SELECT 0 AS last_id, 0 AS state;
        END IF;
    ELSE
        -- Value tidak valid
        SELECT 0 AS last_id, 0 AS state;
    END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `trans_jurnal_sp_insert_ttd_jurnal_from_lap` (IN `p_reff_jurnal` VARCHAR(100), IN `p_id_data` INT, IN `p_jt_id` INT, IN `p_user_input` VARCHAR(100), IN `p_no_ref_verifikasi` VARCHAR(100))   BEGIN
    -- Hapus data ttd_jurnal yang sudah ada untuk kombinasi jt_id dan id_data yang sama
    DELETE FROM ttd_jurnal
    WHERE jt_id = p_jt_id
      AND id_data = p_id_data;
      
    INSERT INTO ttd_jurnal
    (
        jt_id,
        id_data,
        urutan_ke,
        keterangan,
        jabatan,
        nama,
        nup,
        create_dt,
        update_dt,
        user_input,
        is_ttd,
        no_ref_anggaran_verifikasi
    )
    SELECT
        p_jt_id,
        p_id_data,
        l.urutan_ke,
        l.keterangan,
        l.jabatan,
        l.nama,
        l.nup,
        NOW(),          -- SYSDATETIME() padanan
        NULL,
        p_user_input,
        0,
        p_no_ref_verifikasi
    FROM ttd_ref_pengesahan AS l
    WHERE l.jenis_lap = p_reff_jurnal
      AND l.jt_id = p_jt_id;
      
    IF ROW_COUNT() > 0 THEN
        SELECT 1 AS stts;
    ELSE
        SELECT 0 AS stts;
    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `hotel_checkin_checkout`
--

CREATE TABLE `hotel_checkin_checkout` (
  `id` int(11) NOT NULL,
  `pemesanan_id` int(11) NOT NULL,
  `waktu_checkin` datetime DEFAULT NULL,
  `waktu_checkout` datetime DEFAULT NULL,
  `petugas_checkin` int(11) DEFAULT NULL,
  `petugas_checkout` int(11) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `hotel_laporan`
--

CREATE TABLE `hotel_laporan` (
  `id` int(11) NOT NULL,
  `jenis_laporan` enum('bulanan','tahunan','harian','khusus') NOT NULL,
  `bulan` int(11) DEFAULT NULL,
  `tahun` int(11) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `total_pendapatan` decimal(15,2) DEFAULT NULL,
  `jumlah_pemesanan` int(11) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `generated_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `generated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `hotel_pembayaran`
--

CREATE TABLE `hotel_pembayaran` (
  `id` int(11) NOT NULL,
  `pemesanan_id` int(11) NOT NULL,
  `metode_pembayaran` enum('tunai','kartu_kredit','debit','transfer','qris') NOT NULL,
  `jumlah_bayar` decimal(10,2) NOT NULL,
  `tanggal_bayar` datetime NOT NULL,
  `status` enum('pending','lunas','gagal','refund') DEFAULT 'pending',
  `bukti_bayar` varchar(255) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `hotel_pemesanan`
--

CREATE TABLE `hotel_pemesanan` (
  `id` int(11) NOT NULL,
  `kode_booking` varchar(20) NOT NULL,
  `tamu_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ruangan_id` int(11) NOT NULL,
  `tanggal_checkin` date NOT NULL,
  `tanggal_checkout` date NOT NULL,
  `jumlah_tamu` int(11) DEFAULT 1,
  `jumlah_malam` int(11) NOT NULL,
  `total_harga` decimal(10,2) NOT NULL,
  `catatan_khusus` text DEFAULT NULL,
  `status` enum('menunggu','dikonfirmasi','checkin','checkout','dibatalkan') DEFAULT 'menunggu',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Trigger `hotel_pemesanan`
--
DELIMITER $$
CREATE TRIGGER `hotel_after_pemesanan_update` AFTER UPDATE ON `hotel_pemesanan` FOR EACH ROW BEGIN
    -- Jika status berubah ke dikonfirmasi
    IF NEW.status = 'dikonfirmasi' AND OLD.status != 'dikonfirmasi' THEN
        UPDATE hotel_ruangan SET status = 'dipesan' WHERE id = NEW.ruangan_id;
    END IF;
    
    -- Jika status berubah ke checkin
    IF NEW.status = 'checkin' THEN
        UPDATE hotel_ruangan SET status = 'ditempati' WHERE id = NEW.ruangan_id;
        
        -- Auto insert checkin log
        INSERT INTO hotel_checkin_checkout (pemesanan_id, waktu_checkin, petugas_checkin)
        VALUES (NEW.id, NOW(), NEW.user_id);
    END IF;
    
    -- Jika status berubah ke checkout atau dibatalkan
    IF (NEW.status = 'checkout' OR NEW.status = 'dibatalkan') 
       AND OLD.status NOT IN ('checkout', 'dibatalkan') THEN
        UPDATE hotel_ruangan SET status = 'tersedia' WHERE id = NEW.ruangan_id;
        
        -- Auto insert checkout log jika checkin
        IF NEW.status = 'checkout' AND OLD.status = 'checkin' THEN
            UPDATE hotel_checkin_checkout 
            SET waktu_checkout = NOW(), petugas_checkout = NEW.user_id
            WHERE pemesanan_id = NEW.id AND waktu_checkout IS NULL;
        END IF;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Struktur dari tabel `hotel_ruangan`
--

CREATE TABLE `hotel_ruangan` (
  `id` int(11) NOT NULL,
  `nomor_kamar` varchar(10) NOT NULL,
  `tipe_ruangan_id` int(11) NOT NULL,
  `status` enum('tersedia','dipesan','ditempati','maintenance') DEFAULT 'tersedia',
  `lantai` int(11) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `hotel_ruangan`
--

INSERT INTO `hotel_ruangan` (`id`, `nomor_kamar`, `tipe_ruangan_id`, `status`, `lantai`, `keterangan`, `created_at`) VALUES
(1, '101', 1, 'tersedia', 1, NULL, '2026-01-29 02:53:05'),
(2, '102', 1, 'tersedia', 1, NULL, '2026-01-29 02:53:05'),
(3, '201', 2, 'tersedia', 2, NULL, '2026-01-29 02:53:05'),
(4, '202', 2, 'tersedia', 2, NULL, '2026-01-29 02:53:05'),
(5, '301', 3, 'tersedia', 3, NULL, '2026-01-29 02:53:05'),
(6, '302', 3, 'maintenance', 3, NULL, '2026-01-29 02:53:05');

-- --------------------------------------------------------

--
-- Struktur dari tabel `hotel_setting`
--

CREATE TABLE `hotel_setting` (
  `id` int(11) NOT NULL,
  `nama_hotel` varchar(255) DEFAULT NULL,
  `alamat_hotel` text DEFAULT NULL,
  `telepon_hotel` varchar(20) DEFAULT NULL,
  `email_hotel` varchar(100) DEFAULT NULL,
  `logo_hotel` varchar(255) DEFAULT NULL,
  `check_in_time` time DEFAULT '14:00:00',
  `check_out_time` time DEFAULT '12:00:00',
  `pajak_persen` decimal(5,2) DEFAULT 0.00,
  `service_charge_persen` decimal(5,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `hotel_setting`
--

INSERT INTO `hotel_setting` (`id`, `nama_hotel`, `alamat_hotel`, `telepon_hotel`, `email_hotel`, `logo_hotel`, `check_in_time`, `check_out_time`, `pajak_persen`, `service_charge_persen`, `created_at`, `updated_at`) VALUES
(1, 'Hotel Raflesia Bandung', 'Jl. Raya Bandung No. 123', '022-1234567', 'info@hotelraflesia.com', NULL, '14:00:00', '12:00:00', '0.00', '0.00', '2026-01-29 02:53:05', '2026-01-29 02:53:05');

-- --------------------------------------------------------

--
-- Struktur dari tabel `hotel_tamu`
--

CREATE TABLE `hotel_tamu` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `no_identitas` varchar(30) NOT NULL,
  `jenis_identitas` enum('KTP','SIM','Passport','Lainnya') NOT NULL,
  `jenis_kelamin` enum('L','P') DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `kebangsaan` varchar(50) DEFAULT 'Indonesia',
  `pekerjaan` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `no_telepon` varchar(20) NOT NULL,
  `alamat` text DEFAULT NULL,
  `kota` varchar(100) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `hotel_tipe_ruangan`
--

CREATE TABLE `hotel_tipe_ruangan` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `harga_per_malam` decimal(10,2) NOT NULL,
  `kapasitas` int(11) NOT NULL,
  `fasilitas` text DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `status` enum('active','maintenance') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `hotel_tipe_ruangan`
--

INSERT INTO `hotel_tipe_ruangan` (`id`, `nama`, `deskripsi`, `harga_per_malam`, `kapasitas`, `fasilitas`, `gambar`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Standard Room', 'Kamar standar dengan fasilitas lengkap', '350000.00', 2, 'AC, TV, WiFi, Kamar Mandi Dalam', NULL, 'active', '2026-01-29 02:53:05', '2026-01-29 02:53:05'),
(2, 'Deluxe Room', 'Kamar deluxe dengan pemandangan kota', '550000.00', 2, 'AC, TV LCD, WiFi, Kamar Mandi Dalam, Balkon', NULL, 'active', '2026-01-29 02:53:05', '2026-01-29 02:53:05'),
(3, 'Suite Room', 'Kamar suite mewah dengan ruang tamu', '850000.00', 4, 'AC, TV LCD, WiFi, Kamar Mandi Dalam, Ruang Tamu, Mini Bar', NULL, 'active', '2026-01-29 02:53:05', '2026-01-29 02:53:05');

-- --------------------------------------------------------

--
-- Struktur dari tabel `masteraccount_golongan`
--

CREATE TABLE `masteraccount_golongan` (
  `id` int(11) NOT NULL,
  `acc_code` int(11) NOT NULL,
  `acc_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `create_dt` datetime DEFAULT NULL,
  `update_dt` datetime DEFAULT NULL,
  `user_input` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data untuk tabel `masteraccount_golongan`
--

INSERT INTO `masteraccount_golongan` (`id`, `acc_code`, `acc_name`, `create_dt`, `update_dt`, `user_input`) VALUES
(1, 11, 'KAS DAN BANK', '2026-01-02 10:14:23', NULL, 'SYSTEM'),
(2, 12, 'INVESTASI JANGKA PENDEK', '2026-01-02 10:14:23', NULL, 'SYSTEM'),
(3, 13, 'PIUTANG USAHA', '2026-01-02 10:14:23', NULL, 'SYSTEM'),
(4, 14, 'PIUTANG LAIN-LAIN', '2026-01-02 10:14:23', NULL, 'SYSTEM'),
(5, 15, 'PERSEDIAAN', '2026-01-02 10:14:23', NULL, 'SYSTEM'),
(6, 16, 'PEMBAYARAN DIMUKA', '2026-01-02 10:14:23', NULL, 'SYSTEM'),
(7, 21, 'INVESTASI JANGKA PANJANG', '2026-01-02 10:14:23', NULL, 'SYSTEM'),
(8, 31, 'ASET TETAP PRODUKTIF', '2026-01-02 10:14:23', NULL, 'SYSTEM'),
(9, 32, 'ASET TETAP LEASING', '2026-01-02 10:14:23', NULL, 'SYSTEM'),
(10, 41, 'ASET LAIN-LAIN BERWUJUD', '2026-01-02 10:14:23', NULL, 'SYSTEM'),
(11, 42, 'ASET TAK BERWUJUD', '2026-01-02 10:14:23', NULL, 'SYSTEM'),
(12, 50, 'KEWAJIBAN JANGKA PENDEK', '2026-01-02 10:14:23', NULL, 'SYSTEM'),
(13, 61, 'KEWAJIBAN JANGKA PANJANG', '2026-01-02 10:14:23', NULL, 'SYSTEM'),
(14, 62, 'KEWAJIBAN LAIN-LAIN', '2026-01-02 10:14:23', NULL, 'SYSTEM'),
(15, 70, 'EKUITAS DAN CADANGAN', '2026-01-02 10:14:23', NULL, 'SYSTEM'),
(16, 81, 'PENDAPATAN USAHA', '2026-01-02 10:14:23', NULL, 'SYSTEM'),
(17, 88, 'PENDAPATAN DILUAR USAHA', '2026-01-02 10:14:23', NULL, 'SYSTEM'),
(18, 89, 'KEUNTUNGAN LUAR BIASA', '2026-01-02 10:14:23', NULL, 'SYSTEM'),
(19, 91, 'BEBAN SUMBER AIR', '2026-01-02 10:14:23', NULL, 'SYSTEM'),
(20, 92, 'BEBAN PENGOLAHAN AIR', '2026-01-02 10:14:23', NULL, 'SYSTEM'),
(21, 93, 'BEBAN TRANSMISI DAN DISTRIBUSI', '2026-01-02 10:14:23', NULL, 'SYSTEM'),
(22, 94, 'BEBAN KEMITRAAN', '2026-01-02 10:14:23', NULL, 'SYSTEM'),
(23, 95, 'BEBAN AIR LIMBAH', '2026-01-02 10:14:23', NULL, 'SYSTEM'),
(24, 96, 'BEBAN UMUM DAN ADMINISTRASI', '2026-01-02 10:14:23', NULL, 'SYSTEM'),
(25, 98, 'BEBAN DILUAR USAHA', '2026-01-02 10:14:23', NULL, 'SYSTEM'),
(26, 99, 'KERUGIAN LUAR BIASA', '2026-01-02 10:14:23', NULL, 'SYSTEM'),
(27, 17, 'REKENING ANTAR KANTOR', '2026-01-02 10:14:23', NULL, 'SYSTEM'),
(28, 97, 'BEBAN PPH BADAN PS. 25', '2026-01-02 10:14:23', NULL, 'SYSTEM'),
(29, 87, 'PENDAPATAN KOMPREHENSIF LAIN', '2026-01-02 10:14:23', NULL, 'SYSTEM');

-- --------------------------------------------------------

--
-- Struktur dari tabel `masteraccount_kelompok`
--

CREATE TABLE `masteraccount_kelompok` (
  `id` int(11) NOT NULL,
  `acc_code` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `acc_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `acc_parent_id` int(11) DEFAULT NULL,
  `acc_parent_gol` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `create_dt` datetime DEFAULT NULL,
  `update_dt` datetime DEFAULT NULL,
  `user_input` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data untuk tabel `masteraccount_kelompok`
--

INSERT INTO `masteraccount_kelompok` (`id`, `acc_code`, `acc_name`, `acc_parent_id`, `acc_parent_gol`, `create_dt`, `update_dt`, `user_input`) VALUES
(1, '11.01', 'KAS / BANK', 1, '11', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(2, '11.02', 'KAS KECIL', 1, '11', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(3, '12.01', 'DEPOSITO', 2, '12', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(4, '12.02', 'SURAT BERHARGA', 2, '12', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(5, '13.01', 'PIUTANG REKENING AIR', 3, '13', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(6, '13.02', 'PIUTANG REKENING NON AIR', 3, '13', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(7, '13.03', 'PIUTANG KEMITRAAN', 3, '13', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(8, '13.04', 'PIUTANG REKENING AIR LIMBAH', 3, '13', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(9, '13.05', 'PIUTANG RAGU-RAGU', 3, '13', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(10, '13.09', 'PENYISIHAN PIUTANG USAHA', 3, '13', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(11, '14.01', 'TAGIHAN NON USAHA', 4, '14', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(12, '14.02', 'PIUTANG PAJAK', 4, '14', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(13, '14.03', 'PENDAPATAN YANG BELUM DITERIMA', 4, '14', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(14, '14.04', 'PIUTANG PEGAWAI', 4, '14', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(15, '14.09', 'RUPA-RUPA PIUTANG LAINNYA', 4, '14', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(16, '15.01', 'PERSEDIAAN BAHAN OPERASI KIMIA', 5, '15', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(17, '15.02', 'PERSEDIAAN BAHAN OPERASI LAINNYA', 5, '15', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(18, '15.09', 'PENURUNAN NILAI PERSEDIAAN', 5, '15', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(19, '16.01', 'BEBAN DIBAYAR DIMUKA', 6, '16', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(20, '16.02', 'UANG MUKA KERJA', 6, '16', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(21, '16.03', 'UANG MUKA PEMBELIAN', 6, '16', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(22, '16.04', 'UANG MUKA KEPADA KONTRAKTOR', 6, '16', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(23, '16.05', 'PEMBAYARAN DIMUKA PAJAK', 6, '16', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(24, '16.06', 'PEMBAYARAN DIMUKA PADA PEMDA', 6, '16', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(25, '16.09', 'RUPA-RUPA PEMBAYARAN DIMUKA LAINNYA', 6, '16', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(26, '21.01', 'DEPOSITO BERJANGKA LEBIH DARI 1 TAHUN', 7, '21', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(27, '21.02', 'PENYERTAAN', 7, '21', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(28, '21.03', 'PENANAMAN DALAM AKTIVA BERWUJUD', 7, '21', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(29, '21.04', 'INVESTASI JANGKA PANJANG LAINNYA', 7, '21', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(30, '31.01', 'TANAH DAN PENYEMPURNAAN TANAH', 8, '31', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(31, '31.02', 'INSTALASI SUMBER AIR', 8, '31', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(32, '31.03', 'INSTALASI POMPA', 8, '31', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(33, '31.04', 'INSTALASI PENGOLAHAN AIR', 8, '31', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(34, '31.05', 'INSTALASI TRANSMISI DAN DISTRIBUSI', 8, '31', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(35, '31.06', 'BANGUNAN / GEDUNG', 8, '31', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(36, '31.07', 'PERALATAN DAN PERLENGKAPAN', 8, '31', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(37, '31.08', 'KENDARAAN / ALAT PENGANGKUTAN', 8, '31', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(38, '31.09', 'INVENTARIS / PERABOTAN KANTOR', 8, '31', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(39, '31.10', 'AKUMULASI PENYUSUTAN', 8, '31', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(40, '32.01', 'ASET TETAP LEASING', 9, '32', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(41, '41.01', 'ASET TETAP DALAM PENYELESAIAN', 10, '41', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(42, '41.02', 'BAHAN INSTALASI', 10, '41', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(43, '41.03', 'UANG JAMINAN', 10, '41', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(44, '41.04', 'PENGELUARAN SEMENTARA', 10, '41', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(45, '41.05', 'ASET TETAP YANG TIDAK BERFUNGSI', 10, '41', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(46, '41.06', 'DANA PEMBAYARAN UTANG JANGKA PANJANG', 10, '41', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(47, '41.07', 'SAMBUNG BARU YANG AKAN DITERIMA', 10, '41', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(48, '41.08', 'PEMBAYARAN DIMUKA KEPADA PEMERINTAH DAERAH', 10, '41', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(49, '42.01', 'BEBAN DITANGGUHKAN', 11, '42', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(50, '42.02', 'AKUMULASI AMORTISASI BEBAN DITANGGUHKAN', 11, '42', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(51, '42.03', 'TRADE MARK', 11, '42', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(52, '42.04', 'AKUMULASI AMORTISASI TRADE MARK', 11, '42', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(53, '42.05', 'GOODWILL', 11, '42', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(54, '42.06', 'AKUMULASI AMORTISASI GOODWILL', 11, '42', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(55, '50.01', 'UTANG USAHA', 12, '50', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(56, '50.02', 'UTANG NON USAHA', 12, '50', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(57, '50.03', 'BEBAN YANG MASIH HARUS DIBAYAR', 12, '50', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(58, '50.04', 'PENDAPATAN DITERIMA DIMUKA', 12, '50', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(59, '50.05', 'PINJAMAN JANGKA PENDEK', 12, '50', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(60, '50.06', 'UTANG PAJAK', 12, '50', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(61, '50.07', 'UTANG JANGKA PANJANG JATUH TEMPO', 12, '50', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(62, '50.08', 'UTANG BUNGA', 12, '50', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(63, '50.09', 'UTANG IURAN PENSIUN', 12, '50', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(64, '50.10', 'KEWAJIBAN IMBALAN PASCA KERJA JK PENDEK', 12, '50', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(65, '50.11', 'KEWAJIBAN JANGKA PENDEK LAINNYA', 12, '50', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(66, '61.01', 'PINJAMAN DALAM NEGERI', 13, '61', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(67, '61.02', 'PINJAMAN LUAR NEGERI', 13, '61', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(68, '61.03', 'BUNGA MASA TENGGANG', 13, '61', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(69, '61.04', 'UTANG LEASING', 13, '61', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(70, '62.01', 'PENDAPATAN YANG DITANGGUHKAN', 14, '62', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(71, '62.02', 'CADANGAN DANA METER', 14, '62', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(72, '62.03', 'CADANGAN DANA', 14, '62', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(73, '62.04', 'RUPA-RUPA KEWAJIBAN LAINNYA', 14, '62', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(74, '70.01', 'KEKAYAAN PEMDA YANG DIPISAHKAN', 15, '70', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(75, '70.02', 'PENYERTAAN PEMERINTAH  PUSAT', 15, '70', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(76, '70.03', 'EKUITAS', 15, '70', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(77, '70.04', 'HIBAH', 15, '70', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(78, '70.05', 'SELISIH PENILAIAN KEMBALI AKTIVA TETAP', 15, '70', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(79, '70.06', 'CADANGAN', 15, '70', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(80, '70.07', 'LABA DITAHAN/(AKUMULASI KERUGIAN)', 15, '70', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(81, '70.09', 'LABA (RUGI) PERIODE BERJALAN', 15, '70', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(82, '81.01', 'PENDAPATAN PENJUALAN AIR', 16, '81', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(83, '81.02', 'PENDAPATAN NON AIR', 16, '81', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(84, '81.10', 'PENDAPATAN KEMITRAAN', 16, '81', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(85, '81.20', 'PENDAPATAN AIR LIMBAH', 16, '81', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(86, '88.01', 'PENDAPATAN LAIN-LAIN', 17, '88', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(87, '91.01', 'BEBAN PEGAWAI', 19, '91', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(88, '91.02', 'BEBAN LISTRIK PLN', 19, '91', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(89, '91.03', 'BEBAN PEMELIHARAAN', 19, '91', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(90, '91.09', 'BEBAN PENYUSUTAN SUMBER AIR', 19, '91', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(91, '92.01', 'BEBAN PEGAWAI', 20, '92', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(92, '92.02', 'BEBAN BAHAN KIMIA', 20, '92', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(93, '92.03', 'BEBAN PEMELIHARAAN PENGOLAHAN AIR', 20, '92', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(94, '92.09', 'BEBAN PENYUSUTAN PENGOLAHAN AIR', 20, '92', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(95, '93.01', 'BEBAN PEGAWAI', 21, '93', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(96, '93.02', 'BEBAN PEMAKAIAN BAHAN', 21, '93', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(97, '93.09', 'BEBAN PENYUSUTAN TRANSMIS & DISTRIBUSI', 21, '93', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(98, '94.10', 'BEBAN KEMITRAAN', 22, '94', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(99, '95.10', 'BEBAN AIR LIMBAH', 23, '95', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(100, '96.01', 'BEBAN PEGAWAI', 24, '96', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(101, '96.02', 'BEBAN KANTOR', 24, '96', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(102, '96.03', 'BEBAN HUBUNGAN LANGGANAN', 24, '96', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(103, '96.04', 'BEBAN PENELITIAN DAN PENGEMBANGAN', 24, '96', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(104, '96.05', 'BEBAN KEUANGAN', 24, '96', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(105, '96.06', 'BEBAN PEMELIHARAAN', 24, '96', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(106, '96.07', 'BEBAN PENYISIHAN DAN PENGHAPUSAN PIUTANG', 24, '96', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(107, '96.08', 'RUPA-RUPA BEBAN UMUM', 24, '96', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(108, '96.09', 'PENYUSUTAN&AMORTISASI INST.NON.PABRIKAIR', 24, '96', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(109, '98.01', 'BEBAN LAIN-LAIN', 25, '98', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(110, '99.01', 'RUPA-RUPA BEBAN KERUGIAN', 26, '99', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(111, '89.01', 'KEUNTUNGAN LUAR BIASA', 18, '89', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(112, '99.99', 'IKHTISAR LABA/RUGI', 26, '99', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(113, '41.09', 'ASET TETAP IPAL', 10, '41', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(114, '21.05', 'Penanaman Dalam Aktiva Berwujud', 7, '21', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(115, '42.07', 'REKENING ANTAR KANTOR', 11, '42', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(116, '17.01', 'REKENING ANTAR KANTOR', 27, '17', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(117, '13.07', 'PIUTANG TAK TERTAGIH', 3, '13', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(118, '91.04', 'BEBAN AIR BAKU', 19, '91', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(119, '93.03', 'BEBAN PEMELIHARAAN TRANS DIST', 21, '93', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(120, '97.01', 'BEBAN PPH BADAN PS. 25', 28, '97', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(121, '31.11', 'PENURUNAN NILAI ASET TETAP', 8, '31', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(122, '15.03', 'PERSEDIAAN BAHAN INSTALASI', 5, '15', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(123, '61.05', 'KEWAJIBAN IMBALAN PASCA KERJA JK PANJANG', 13, '61', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(124, '87.10', 'PENGHASILAN KOMPREHENSIF LAIN', 29, '87', '2026-01-02 10:24:06', NULL, 'SYSTEM'),
(125, '70.10', 'PENGHASILAN KOMPREHENSIF LAIN', 15, '70', '2026-01-02 10:24:06', NULL, 'SYSTEM');

-- --------------------------------------------------------

--
-- Struktur dari tabel `master_satker`
--

CREATE TABLE `master_satker` (
  `id` int(11) NOT NULL,
  `kode_satker` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_satker` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data untuk tabel `master_satker`
--

INSERT INTO `master_satker` (`id`, `kode_satker`, `nama_satker`) VALUES
(1, '0000', 'Transaksi/Laporan Gabungan'),
(2, '0100', 'Rekap Kantor Pusat'),
(3, '0101', 'Cabang I'),
(4, '0102', 'Kantor Utama'),
(5, '0200', 'Sragen Bagian Utara'),
(6, '0201', 'Unit Sukodono'),
(7, '0202', 'Unit Gemolong'),
(8, '0203', 'Unit Sumber Lawang'),
(9, '0204', 'Unit Tanon'),
(10, '0205', 'Unit Kalijambe'),
(11, '0206', 'Unit Plupuh'),
(12, '0207', 'Unit Mondokan'),
(13, '0300', 'Sragen Bagian Selatan'),
(14, '0301', 'Unit Masaran'),
(15, '0302', 'Unit Sidoharjo'),
(16, '0303', 'Unit Mojokerto'),
(17, '0304', 'Unit Pengkok'),
(18, '0305', 'Unit Gondang'),
(19, '0306', 'Unit Sambirejo'),
(20, '0307', 'Unit Sambung Macan'),
(21, '0308', 'Unit Ngrampal'),
(22, '0309', 'Unit Jirapan'),
(23, '0310', 'Unit Jenar'),
(24, '0401', 'Unit Pelayanan Area 1'),
(25, '0402', 'Unit Pelayanan Area 2'),
(26, '0403', 'Unit Pelayanan Area 3');

-- --------------------------------------------------------

--
-- Struktur dari tabel `periodeaktif`
--

CREATE TABLE `periodeaktif` (
  `id` int(11) NOT NULL,
  `periode_m` int(11) DEFAULT NULL,
  `periode_y` int(11) DEFAULT NULL,
  `is_closed` smallint(6) DEFAULT NULL,
  `create_dt` datetime DEFAULT NULL,
  `update_dt` datetime DEFAULT NULL,
  `user_input` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data untuk tabel `periodeaktif`
--

INSERT INTO `periodeaktif` (`id`, `periode_m`, `periode_y`, `is_closed`, `create_dt`, `update_dt`, `user_input`) VALUES
(1, 1, 2026, 0, '2026-01-06 16:38:13', NULL, NULL),
(2, 1, 2026, 0, '2026-01-06 16:53:32', NULL, NULL),
(3, 1, 2026, 0, '2026-01-07 09:07:58', NULL, NULL),
(4, 1, 2026, 0, '2026-01-08 09:11:29', NULL, NULL),
(5, 1, 2026, 0, '2026-01-08 17:21:13', NULL, NULL),
(6, 1, 2026, 0, '2026-01-08 17:21:15', NULL, NULL),
(7, 1, 2026, 0, '2026-01-08 17:29:39', NULL, NULL),
(8, 1, 2026, 0, '2026-01-08 17:32:30', NULL, NULL),
(9, 1, 2026, 0, '2026-01-08 17:36:42', NULL, NULL),
(10, 1, 2026, 0, '2026-01-08 17:40:28', NULL, NULL),
(11, 1, 2026, 0, '2026-01-08 17:44:44', NULL, NULL),
(12, 1, 2026, 0, '2026-01-09 10:12:57', NULL, NULL),
(13, 1, 2026, 0, '2026-01-12 09:22:29', NULL, NULL),
(14, 1, 2026, 0, '2026-01-12 10:02:43', NULL, NULL),
(15, 1, 2026, 0, '2026-01-12 10:04:09', NULL, NULL),
(16, 1, 2026, 0, '2026-01-12 10:09:26', NULL, NULL),
(17, 1, 2026, 0, '2026-01-12 10:27:32', NULL, NULL),
(18, 1, 2026, 0, '2026-01-29 09:56:50', NULL, NULL),
(19, 1, 2026, 0, '2026-01-29 10:04:18', NULL, NULL),
(20, 2, 2026, 0, '2026-01-29 10:05:23', NULL, NULL),
(21, 1, 2026, 0, '2026-01-29 10:16:07', NULL, NULL),
(22, 1, 2026, 0, '2026-01-29 10:23:54', NULL, NULL),
(23, 1, 2026, 0, '2026-01-29 10:27:38', NULL, NULL),
(24, 1, 2026, 0, '2026-01-29 10:28:44', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `profilesetting`
--

CREATE TABLE `profilesetting` (
  `id` int(11) NOT NULL,
  `nama_pdam` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_aplikasi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telp` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kota_kab` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo_pdam` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_footer` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_header` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_watermark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data untuk tabel `profilesetting`
--

INSERT INTO `profilesetting` (`id`, `nama_pdam`, `nama_aplikasi`, `alamat`, `telp`, `kota_kab`, `logo_pdam`, `image_footer`, `image_header`, `image_watermark`) VALUES
(13, 'PERUMDA AIR MINUM TIRTO NEGORO', 'AKSARA', 'Jl. Ronggowarsito No.18, Dusun Kebayanan Sragen Manggis, Sragen Wetan, Kec. Sragen', '08112631515', 'Kabupaten Sragen, Jawa Tengah 57214', 'logo-pdam-1.png', 'logo-pdam-1.png', 'logo-pdam-1.png', 'logo-pdam-1.png');

-- --------------------------------------------------------

--
-- Struktur dari tabel `reff_jurnal`
--

CREATE TABLE `reff_jurnal` (
  `jt_id` int(11) NOT NULL,
  `nama_jurnal` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data untuk tabel `reff_jurnal`
--

INSERT INTO `reff_jurnal` (`jt_id`, `nama_jurnal`) VALUES
(1, 'DHHD'),
(2, 'JR'),
(3, 'JPK'),
(4, 'JBK'),
(5, 'JPBIK'),
(6, 'JU');

-- --------------------------------------------------------

--
-- Struktur dari tabel `reff_ttd_lap`
--

CREATE TABLE `reff_ttd_lap` (
  `id` int(11) NOT NULL,
  `nama_lap` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kelompok` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Struktur dari tabel `system_log_akses`
--

CREATE TABLE `system_log_akses` (
  `id` int(11) NOT NULL,
  `times` timestamp NULL DEFAULT current_timestamp(),
  `id_user` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_before` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_after` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `response` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `controller` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data untuk tabel `system_log_akses`
--

INSERT INTO `system_log_akses` (`id`, `times`, `id_user`, `username`, `name`, `ip`, `url`, `message`, `data_before`, `data_after`, `response`, `controller`, `action`) VALUES
(42, '2026-01-06 09:49:08', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/setting/otoritas_aksi//setAkses', 'Melakukan perubahan data otorisasi akses', '{\"queryLog\":[{\"sql\":\"CALL `system_sp_set_menu_aksi`(?, ?, ?, ?)\",\"bindings\":[{\"value\":\"1\",\"type\":2},{\"value\":\"1\",\"type\":2},{\"value\":\"0\",\"type\":2},{\"value\":\"input\",\"type\":2}],\"time\":0.14838504791259766}]}', 'true', '\"Setting\\/OtorisasiAksi\\/Controller\"', 'CALL SP', ''),
(43, '2026-01-06 09:49:12', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/setting/otoritas_aksi//setAkses', 'Melakukan perubahan data otorisasi akses', '{\"queryLog\":[{\"sql\":\"CALL `system_sp_set_menu_aksi`(?, ?, ?, ?)\",\"bindings\":[{\"value\":\"1\",\"type\":2},{\"value\":\"1\",\"type\":2},{\"value\":\"1\",\"type\":2},{\"value\":\"input\",\"type\":2}],\"time\":0.06638503074645996}]}', 'true', '\"Setting\\/OtorisasiAksi\\/Controller\"', 'CALL SP', ''),
(44, '2026-01-06 09:52:40', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/setting/otoritas_aksi//setAkses', 'Melakukan perubahan data otorisasi akses', '{\"queryLog\":[{\"sql\":\"CALL `system_sp_set_menu_aksi`(?, ?, ?, ?)\",\"bindings\":[{\"value\":\"1\",\"type\":2},{\"value\":\"10\",\"type\":2},{\"value\":\"0\",\"type\":2},{\"value\":\"input\",\"type\":2}],\"time\":0.09356117248535156}]}', 'true', '\"Setting\\/OtorisasiAksi\\/Controller\"', 'CALL SP', ''),
(45, '2026-01-06 09:53:47', '1', 'superadmin', 'Super Admin', '125.163.75.26', 'tirta-negoro.erpsystempdam.com/akuntansi/panel/setting/otoritas_aksi//setAkses', 'Melakukan perubahan data otorisasi akses', '{\"queryLog\":[{\"sql\":\"CALL `system_sp_set_menu_aksi`(?, ?, ?, ?)\",\"bindings\":[{\"value\":\"1\",\"type\":2},{\"value\":\"10\",\"type\":2},{\"value\":\"1\",\"type\":2},{\"value\":\"input\",\"type\":2}],\"time\":0.08208107948303223}]}', 'true', '\"Setting\\/OtorisasiAksi\\/Controller\"', 'CALL SP', ''),
(46, '2026-01-06 09:53:52', '1', 'superadmin', 'Super Admin', '125.163.75.26', 'tirta-negoro.erpsystempdam.com/akuntansi/panel/setting/otoritas_aksi//setAkses', 'Melakukan perubahan data otorisasi akses', '{\"queryLog\":[{\"sql\":\"CALL `system_sp_set_menu_aksi`(?, ?, ?, ?)\",\"bindings\":[{\"value\":\"1\",\"type\":2},{\"value\":\"10\",\"type\":2},{\"value\":\"0\",\"type\":2},{\"value\":\"input\",\"type\":2}],\"time\":0.0641489028930664}]}', 'true', '\"Setting\\/OtorisasiAksi\\/Controller\"', 'CALL SP', ''),
(47, '2026-01-06 09:54:02', '1', 'superadmin', 'Super Admin', '125.163.75.26', 'tirta-negoro.erpsystempdam.com/akuntansi/panel/setting/otoritas_aksi//setAkses', 'Melakukan perubahan data otorisasi akses', '{\"queryLog\":[{\"sql\":\"CALL `system_sp_set_menu_aksi`(?, ?, ?, ?)\",\"bindings\":[{\"value\":\"1\",\"type\":2},{\"value\":\"10\",\"type\":2},{\"value\":\"1\",\"type\":2},{\"value\":\"input\",\"type\":2}],\"time\":0.04169106483459473}]}', 'true', '\"Setting\\/OtorisasiAksi\\/Controller\"', 'CALL SP', ''),
(48, '2026-01-06 09:54:09', '1', 'superadmin', 'Super Admin', '125.163.75.26', 'tirta-negoro.erpsystempdam.com/akuntansi/panel/setting/otoritas_aksi//setAkses', 'Melakukan perubahan data otorisasi akses', '{\"queryLog\":[{\"sql\":\"CALL `system_sp_set_menu_aksi`(?, ?, ?, ?)\",\"bindings\":[{\"value\":\"1\",\"type\":2},{\"value\":\"10\",\"type\":2},{\"value\":\"0\",\"type\":2},{\"value\":\"input\",\"type\":2}],\"time\":0.056321144104003906}]}', 'true', '\"Setting\\/OtorisasiAksi\\/Controller\"', 'CALL SP', ''),
(49, '2026-01-06 09:54:16', '1', 'superadmin', 'Super Admin', '125.163.75.26', 'tirta-negoro.erpsystempdam.com/akuntansi/panel/setting/otoritas_aksi//setAkses', 'Melakukan perubahan data otorisasi akses', '{\"queryLog\":[{\"sql\":\"CALL `system_sp_set_menu_aksi`(?, ?, ?, ?)\",\"bindings\":[{\"value\":\"1\",\"type\":2},{\"value\":\"10\",\"type\":2},{\"value\":\"1\",\"type\":2},{\"value\":\"input\",\"type\":2}],\"time\":0.05592513084411621}]}', 'true', '\"Setting\\/OtorisasiAksi\\/Controller\"', 'CALL SP', ''),
(50, '2026-01-08 02:36:16', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd/saveDataOrUpdate', 'Melakukan penambahan data DHHD Mjournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"\",\"type\":2},{\"value\":\"2026-01-07\",\"type\":2},{\"value\":\"0001.1.01.2026\",\"type\":2},{\"value\":\"Test\",\"type\":2},{\"value\":\"Ramdani\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.057531118392944336}]}', 'true', 'Transjurnal/Dhhd/Controller', 'insert'),
(51, '2026-01-08 02:37:23', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd/saveDataOrUpdate', 'Melakukan penambahan data DHHD Mjournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"\",\"type\":2},{\"value\":\"2026-01-07\",\"type\":2},{\"value\":\"0001.1.01.2026\",\"type\":2},{\"value\":\"Test\",\"type\":2},{\"value\":\"Ramdani\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.0718998908996582}]}', 'true', 'Transjurnal/Dhhd/Controller', 'insert'),
(52, '2026-01-08 02:44:29', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd/saveDataOrUpdate', 'Melakukan penambahan data DHHD Mjournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"\",\"type\":2},{\"value\":\"2026-01-07\",\"type\":2},{\"value\":\"0001.1.01.2026\",\"type\":2},{\"value\":\"Test\",\"type\":2},{\"value\":\"Ramdani\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.0878748893737793}]}', 'true', 'Transjurnal/Dhhd/Controller', 'insert'),
(53, '2026-01-08 02:44:29', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd/saveDataOrUpdate', 'Melakukan penambahan data DHHD Djournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"\",\"type\":2},{\"value\":\"2026-01-07\",\"type\":2},{\"value\":\"0001.1.01.2026\",\"type\":2},{\"value\":\"Test\",\"type\":2},{\"value\":\"Ramdani\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.0878748893737793}]}', 'true', 'Transjurnal/Dhhd/Controller', 'insert'),
(54, '2026-01-08 02:44:29', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd/saveDataOrUpdate', 'Melakukan penambahan data DHHD Djournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"\",\"type\":2},{\"value\":\"2026-01-07\",\"type\":2},{\"value\":\"0001.1.01.2026\",\"type\":2},{\"value\":\"Test\",\"type\":2},{\"value\":\"Ramdani\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.0878748893737793}]}', 'true', 'Transjurnal/Dhhd/Controller', 'insert'),
(55, '2026-01-08 02:46:20', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd/saveDataOrUpdate', 'Melakukan penambahan data DHHD Mjournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"\",\"type\":2},{\"value\":\"2026-01-07\",\"type\":2},{\"value\":\"0002.1.01.2026\",\"type\":2},{\"value\":\"Test\",\"type\":2},{\"value\":\"Ramdani\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.28411197662353516}]}', 'true', 'Transjurnal/Dhhd/Controller', 'insert'),
(56, '2026-01-08 02:47:34', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd/saveDataOrUpdate', 'Melakukan penambahan data DHHD Mjournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"\",\"type\":2},{\"value\":\"2026-01-07\",\"type\":2},{\"value\":\"0003.1.01.2026\",\"type\":2},{\"value\":\"Test\",\"type\":2},{\"value\":\"Ramdani\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.09759807586669922}]}', 'true', 'Transjurnal/Dhhd/Controller', 'insert'),
(57, '2026-01-08 02:47:34', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd/saveDataOrUpdate', 'Melakukan penambahan data DHHD Djournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"\",\"type\":2},{\"value\":\"2026-01-07\",\"type\":2},{\"value\":\"0003.1.01.2026\",\"type\":2},{\"value\":\"Test\",\"type\":2},{\"value\":\"Ramdani\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.09759807586669922}]}', 'true', 'Transjurnal/Dhhd/Controller', 'insert'),
(58, '2026-01-08 02:47:34', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd/saveDataOrUpdate', 'Melakukan penambahan data DHHD Djournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"\",\"type\":2},{\"value\":\"2026-01-07\",\"type\":2},{\"value\":\"0003.1.01.2026\",\"type\":2},{\"value\":\"Test\",\"type\":2},{\"value\":\"Ramdani\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.09759807586669922}]}', 'true', 'Transjurnal/Dhhd/Controller', 'insert'),
(59, '2026-01-08 02:47:34', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd/saveDataOrUpdate', 'Melakukan penambahan data DHHD Djournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"\",\"type\":2},{\"value\":\"2026-01-07\",\"type\":2},{\"value\":\"0003.1.01.2026\",\"type\":2},{\"value\":\"Test\",\"type\":2},{\"value\":\"Ramdani\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.09759807586669922}]}', 'true', 'Transjurnal/Dhhd/Controller', 'insert'),
(60, '2026-01-08 02:47:51', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd/deleteData', 'Melakukan penghapusan data pada modul atau class App\\Modules\\Defaults\\TransJurnal\\Model_Mjournal', '{\"mjo_id\":\"1\",\"mjo_date\":\"2026-01-07\",\"mjo_code\":\"0001.1.01.2026\",\"mjo_ket\":\"Test\",\"mjo_ajuan_untuk\":\"Ramdani\",\"mjo_ajuan_dari\":\"(0000) Transaksi\\/Laporan Gabungan\",\"jt_id\":\"1\",\"mjo_paymentdate\":null,\"mjo_ceque\":null,\"mjo_jbkid\":null,\"mjo_parentid\":null,\"is_jbk_multivoucher\":null,\"mjo_mperiod\":\"1\",\"mjo_yperiod\":\"2026\",\"mjo_ref\":null,\"is_auto\":null,\"auto_kode\":null,\"is_aktiva\":null,\"is_persediaan\":null,\"is_ju_susut\":null,\"is_air\":null,\"jr_jml_rek\":null,\"is_batal\":null,\"is_verify_voucher\":null,\"jenis_ju_lainnya\":null,\"id_mjo_ju_pj\":null,\"id_um_pj\":null,\"id_kontrak\":null,\"file_lampiran\":null,\"is_closed\":null,\"create_dt\":\"2026-01-08 09:44:28\",\"update_dt\":null,\"user_input\":\"superadmin\",\"jenis_jbk\":null,\"dph_id\":null,\"jenis_memo\":\"1\"}', '{\"mjo_id\":\"1\",\"mjo_date\":\"2026-01-07\",\"mjo_code\":\"0001.1.01.2026\",\"mjo_ket\":\"Test\",\"mjo_ajuan_untuk\":\"Ramdani\",\"mjo_ajuan_dari\":\"(0000) Transaksi\\/Laporan Gabungan\",\"jt_id\":\"1\",\"mjo_paymentdate\":null,\"mjo_ceque\":null,\"mjo_jbkid\":null,\"mjo_parentid\":null,\"is_jbk_multivoucher\":null,\"mjo_mperiod\":\"1\",\"mjo_yperiod\":\"2026\",\"mjo_ref\":null,\"is_auto\":null,\"auto_kode\":null,\"is_aktiva\":null,\"is_persediaan\":null,\"is_ju_susut\":null,\"is_air\":null,\"jr_jml_rek\":null,\"is_batal\":null,\"is_verify_voucher\":null,\"jenis_ju_lainnya\":null,\"id_mjo_ju_pj\":null,\"id_um_pj\":null,\"id_kontrak\":null,\"file_lampiran\":null,\"is_closed\":null,\"create_dt\":\"2026-01-08 09:44:28\",\"update_dt\":null,\"user_input\":\"superadmin\",\"jenis_jbk\":null,\"dph_id\":null,\"jenis_memo\":\"1\"}', 'true', 'Defaults\\TransJurnal\\Dhhd', 'delete'),
(61, '2026-01-08 02:47:55', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd/deleteData', 'Melakukan penghapusan data pada modul atau class App\\Modules\\Defaults\\TransJurnal\\Model_Mjournal', '{\"mjo_id\":\"2\",\"mjo_date\":\"2026-01-07\",\"mjo_code\":\"0002.1.01.2026\",\"mjo_ket\":\"Test\",\"mjo_ajuan_untuk\":\"Ramdani\",\"mjo_ajuan_dari\":\"(0000) Transaksi\\/Laporan Gabungan\",\"jt_id\":\"1\",\"mjo_paymentdate\":null,\"mjo_ceque\":null,\"mjo_jbkid\":null,\"mjo_parentid\":null,\"is_jbk_multivoucher\":null,\"mjo_mperiod\":\"1\",\"mjo_yperiod\":\"2026\",\"mjo_ref\":null,\"is_auto\":null,\"auto_kode\":null,\"is_aktiva\":null,\"is_persediaan\":null,\"is_ju_susut\":null,\"is_air\":null,\"jr_jml_rek\":null,\"is_batal\":null,\"is_verify_voucher\":null,\"jenis_ju_lainnya\":null,\"id_mjo_ju_pj\":null,\"id_um_pj\":null,\"id_kontrak\":null,\"file_lampiran\":null,\"is_closed\":null,\"create_dt\":\"2026-01-08 09:46:19\",\"update_dt\":null,\"user_input\":\"superadmin\",\"jenis_jbk\":null,\"dph_id\":null,\"jenis_memo\":\"1\"}', '{\"mjo_id\":\"2\",\"mjo_date\":\"2026-01-07\",\"mjo_code\":\"0002.1.01.2026\",\"mjo_ket\":\"Test\",\"mjo_ajuan_untuk\":\"Ramdani\",\"mjo_ajuan_dari\":\"(0000) Transaksi\\/Laporan Gabungan\",\"jt_id\":\"1\",\"mjo_paymentdate\":null,\"mjo_ceque\":null,\"mjo_jbkid\":null,\"mjo_parentid\":null,\"is_jbk_multivoucher\":null,\"mjo_mperiod\":\"1\",\"mjo_yperiod\":\"2026\",\"mjo_ref\":null,\"is_auto\":null,\"auto_kode\":null,\"is_aktiva\":null,\"is_persediaan\":null,\"is_ju_susut\":null,\"is_air\":null,\"jr_jml_rek\":null,\"is_batal\":null,\"is_verify_voucher\":null,\"jenis_ju_lainnya\":null,\"id_mjo_ju_pj\":null,\"id_um_pj\":null,\"id_kontrak\":null,\"file_lampiran\":null,\"is_closed\":null,\"create_dt\":\"2026-01-08 09:46:19\",\"update_dt\":null,\"user_input\":\"superadmin\",\"jenis_jbk\":null,\"dph_id\":null,\"jenis_memo\":\"1\"}', 'true', 'Defaults\\TransJurnal\\Dhhd', 'delete'),
(69, '2026-01-08 02:50:04', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd/deleteData', 'Melakukan penghapusan data pada modul atau class App\\Modules\\Defaults\\TransJurnal\\Model_Mjournal', '{\"mjo_id\":\"3\",\"mjo_date\":\"2026-01-07\",\"mjo_code\":\"0003.1.01.2026\",\"mjo_ket\":\"Test\",\"mjo_ajuan_untuk\":\"Ramdani\",\"mjo_ajuan_dari\":\"(0000) Transaksi\\/Laporan Gabungan\",\"jt_id\":\"1\",\"mjo_paymentdate\":null,\"mjo_ceque\":null,\"mjo_jbkid\":null,\"mjo_parentid\":null,\"is_jbk_multivoucher\":null,\"mjo_mperiod\":\"1\",\"mjo_yperiod\":\"2026\",\"mjo_ref\":null,\"is_auto\":null,\"auto_kode\":null,\"is_aktiva\":null,\"is_persediaan\":null,\"is_ju_susut\":null,\"is_air\":null,\"jr_jml_rek\":null,\"is_batal\":null,\"is_verify_voucher\":null,\"jenis_ju_lainnya\":null,\"id_mjo_ju_pj\":null,\"id_um_pj\":null,\"id_kontrak\":null,\"file_lampiran\":null,\"is_closed\":null,\"create_dt\":\"2026-01-08 09:47:34\",\"update_dt\":null,\"user_input\":\"superadmin\",\"jenis_jbk\":null,\"dph_id\":null,\"jenis_memo\":\"1\"}', '{\"mjo_id\":\"3\",\"mjo_date\":\"2026-01-07\",\"mjo_code\":\"0003.1.01.2026\",\"mjo_ket\":\"Test\",\"mjo_ajuan_untuk\":\"Ramdani\",\"mjo_ajuan_dari\":\"(0000) Transaksi\\/Laporan Gabungan\",\"jt_id\":\"1\",\"mjo_paymentdate\":null,\"mjo_ceque\":null,\"mjo_jbkid\":null,\"mjo_parentid\":null,\"is_jbk_multivoucher\":null,\"mjo_mperiod\":\"1\",\"mjo_yperiod\":\"2026\",\"mjo_ref\":null,\"is_auto\":null,\"auto_kode\":null,\"is_aktiva\":null,\"is_persediaan\":null,\"is_ju_susut\":null,\"is_air\":null,\"jr_jml_rek\":null,\"is_batal\":null,\"is_verify_voucher\":null,\"jenis_ju_lainnya\":null,\"id_mjo_ju_pj\":null,\"id_um_pj\":null,\"id_kontrak\":null,\"file_lampiran\":null,\"is_closed\":null,\"create_dt\":\"2026-01-08 09:47:34\",\"update_dt\":null,\"user_input\":\"superadmin\",\"jenis_jbk\":null,\"dph_id\":null,\"jenis_memo\":\"1\"}', 'true', 'Defaults\\TransJurnal\\Dhhd', 'delete'),
(70, '2026-01-08 02:50:04', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd/deleteData', 'Melakukan penghapusan data pada modul atau class App\\Modules\\Defaults\\TransJurnal\\Model_Djournal', '{\"id\":\"1\",\"kode_satker\":\"0000\",\"acc_code\":\"91.01.15\",\"djo_debit\":\"40000000\",\"djo_credit\":\"0\",\"mjo_id\":\"3\",\"jt_id\":\"1\",\"djo_mperiod\":\"1\",\"djo_yperiod\":\"2026\",\"param\":\"Beban Pakaian Dinas\"}', '{\"id\":\"1\",\"kode_satker\":\"0000\",\"acc_code\":\"91.01.15\",\"djo_debit\":\"40000000\",\"djo_credit\":\"0\",\"mjo_id\":\"3\",\"jt_id\":\"1\",\"djo_mperiod\":\"1\",\"djo_yperiod\":\"2026\",\"param\":\"Beban Pakaian Dinas\"}', 'true', 'Defaults\\TransJurnal\\Dhhd', 'delete'),
(71, '2026-01-08 02:50:04', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd/deleteData', 'Melakukan penghapusan data pada modul atau class App\\Modules\\Defaults\\TransJurnal\\Model_Djournal', '{\"id\":\"2\",\"kode_satker\":\"0000\",\"acc_code\":\"50.02.10\",\"djo_debit\":\"0\",\"djo_credit\":\"40000000\",\"mjo_id\":\"3\",\"jt_id\":\"1\",\"djo_mperiod\":\"1\",\"djo_yperiod\":\"2026\",\"param\":\"Kredit Beban Pakaian Dinas\"}', '{\"id\":\"2\",\"kode_satker\":\"0000\",\"acc_code\":\"50.02.10\",\"djo_debit\":\"0\",\"djo_credit\":\"40000000\",\"mjo_id\":\"3\",\"jt_id\":\"1\",\"djo_mperiod\":\"1\",\"djo_yperiod\":\"2026\",\"param\":\"Kredit Beban Pakaian Dinas\"}', 'true', 'Defaults\\TransJurnal\\Dhhd', 'delete'),
(72, '2026-01-08 02:51:21', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd/saveDataOrUpdate', 'Melakukan penambahan data DHHD Mjournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"\",\"type\":2},{\"value\":\"2026-01-07\",\"type\":2},{\"value\":\"0001.1.01.2026\",\"type\":2},{\"value\":\"Test Insert Voucher Biaya Nilai Decimal\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.09836888313293457}]}', 'true', 'Transjurnal/Dhhd/Controller', 'insert'),
(73, '2026-01-08 02:51:22', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd/saveDataOrUpdate', 'Melakukan penambahan data DHHD Djournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"\",\"type\":2},{\"value\":\"2026-01-07\",\"type\":2},{\"value\":\"0001.1.01.2026\",\"type\":2},{\"value\":\"Test Insert Voucher Biaya Nilai Decimal\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.09836888313293457}]}', 'true', 'Transjurnal/Dhhd/Controller', 'insert'),
(74, '2026-01-08 02:51:22', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd/saveDataOrUpdate', 'Melakukan penambahan data DHHD Djournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"\",\"type\":2},{\"value\":\"2026-01-07\",\"type\":2},{\"value\":\"0001.1.01.2026\",\"type\":2},{\"value\":\"Test Insert Voucher Biaya Nilai Decimal\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.09836888313293457}]}', 'true', 'Transjurnal/Dhhd/Controller', 'insert'),
(75, '2026-01-08 02:51:22', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd/saveDataOrUpdate', 'Melakukan penambahan data DHHD Djournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"\",\"type\":2},{\"value\":\"2026-01-07\",\"type\":2},{\"value\":\"0001.1.01.2026\",\"type\":2},{\"value\":\"Test Insert Voucher Biaya Nilai Decimal\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.09836888313293457}]}', 'true', 'Transjurnal/Dhhd/Controller', 'insert'),
(76, '2026-01-08 03:04:08', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd/saveDataOrUpdate', 'Melakukan perubahan data DHHD Mjournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"4\",\"type\":2},{\"value\":\"2026-01-07\",\"type\":2},{\"value\":\"0001.1.01.2026\",\"type\":2},{\"value\":\"Test Insert Voucher Biaya Nilai Decimal Update\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.01674795150756836}]}', 'true', 'Transjurnal/Dhhd/Controller', 'insert'),
(77, '2026-01-08 03:04:08', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd/saveDataOrUpdate', 'Melakukan penambahan data DHHD Djournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"4\",\"type\":2},{\"value\":\"2026-01-07\",\"type\":2},{\"value\":\"0001.1.01.2026\",\"type\":2},{\"value\":\"Test Insert Voucher Biaya Nilai Decimal Update\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.01674795150756836}]}', 'true', 'Transjurnal/Dhhd/Controller', 'insert'),
(78, '2026-01-08 03:04:08', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd/saveDataOrUpdate', 'Melakukan penambahan data DHHD Djournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"4\",\"type\":2},{\"value\":\"2026-01-07\",\"type\":2},{\"value\":\"0001.1.01.2026\",\"type\":2},{\"value\":\"Test Insert Voucher Biaya Nilai Decimal Update\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.01674795150756836}]}', 'true', 'Transjurnal/Dhhd/Controller', 'insert'),
(79, '2026-01-08 03:04:08', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd/saveDataOrUpdate', 'Melakukan penambahan data DHHD Djournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"4\",\"type\":2},{\"value\":\"2026-01-07\",\"type\":2},{\"value\":\"0001.1.01.2026\",\"type\":2},{\"value\":\"Test Insert Voucher Biaya Nilai Decimal Update\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.01674795150756836}]}', 'true', 'Transjurnal/Dhhd/Controller', 'insert'),
(80, '2026-01-08 03:06:16', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd/saveDataOrUpdate', 'Melakukan perubahan data DHHD Mjournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"4\",\"type\":2},{\"value\":\"2026-01-07\",\"type\":2},{\"value\":\"0001.1.01.2026\",\"type\":2},{\"value\":\"Test Insert Voucher Biaya Nilai Decimal Update\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.0170900821685791}]}', 'true', 'Transjurnal/Dhhd/Controller', 'insert'),
(81, '2026-01-08 03:06:16', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd/saveDataOrUpdate', 'Melakukan penambahan data DHHD Djournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"4\",\"type\":2},{\"value\":\"2026-01-07\",\"type\":2},{\"value\":\"0001.1.01.2026\",\"type\":2},{\"value\":\"Test Insert Voucher Biaya Nilai Decimal Update\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.0170900821685791}]}', 'true', 'Transjurnal/Dhhd/Controller', 'insert'),
(82, '2026-01-08 03:06:16', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd/saveDataOrUpdate', 'Melakukan penambahan data DHHD Djournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"4\",\"type\":2},{\"value\":\"2026-01-07\",\"type\":2},{\"value\":\"0001.1.01.2026\",\"type\":2},{\"value\":\"Test Insert Voucher Biaya Nilai Decimal Update\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.0170900821685791}]}', 'true', 'Transjurnal/Dhhd/Controller', 'insert'),
(83, '2026-01-08 03:06:16', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd/saveDataOrUpdate', 'Melakukan penambahan data DHHD Djournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"4\",\"type\":2},{\"value\":\"2026-01-07\",\"type\":2},{\"value\":\"0001.1.01.2026\",\"type\":2},{\"value\":\"Test Insert Voucher Biaya Nilai Decimal Update\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.0170900821685791}]}', 'true', 'Transjurnal/Dhhd/Controller', 'insert'),
(84, '2026-01-08 03:07:59', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd/saveDataOrUpdate', 'Melakukan perubahan data DHHD Mjournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"4\",\"type\":2},{\"value\":\"2026-01-07\",\"type\":2},{\"value\":\"0001.1.01.2026\",\"type\":2},{\"value\":\"Test Insert Voucher Biaya Nilai Decimal Update\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.03434491157531738}]}', 'true', 'Transjurnal/Dhhd/Controller', 'insert'),
(85, '2026-01-08 03:08:00', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd/saveDataOrUpdate', 'Melakukan penambahan data DHHD Djournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"4\",\"type\":2},{\"value\":\"2026-01-07\",\"type\":2},{\"value\":\"0001.1.01.2026\",\"type\":2},{\"value\":\"Test Insert Voucher Biaya Nilai Decimal Update\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.03434491157531738}]}', 'true', 'Transjurnal/Dhhd/Controller', 'insert'),
(86, '2026-01-08 03:08:01', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd/saveDataOrUpdate', 'Melakukan penambahan data DHHD Djournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"4\",\"type\":2},{\"value\":\"2026-01-07\",\"type\":2},{\"value\":\"0001.1.01.2026\",\"type\":2},{\"value\":\"Test Insert Voucher Biaya Nilai Decimal Update\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.03434491157531738}]}', 'true', 'Transjurnal/Dhhd/Controller', 'insert'),
(87, '2026-01-08 03:08:01', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd/saveDataOrUpdate', 'Melakukan penambahan data DHHD Djournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"4\",\"type\":2},{\"value\":\"2026-01-07\",\"type\":2},{\"value\":\"0001.1.01.2026\",\"type\":2},{\"value\":\"Test Insert Voucher Biaya Nilai Decimal Update\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.03434491157531738}]}', 'true', 'Transjurnal/Dhhd/Controller', 'insert'),
(88, '2026-01-08 03:10:44', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd/saveDataOrUpdate', 'Melakukan perubahan data DHHD Mjournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"4\",\"type\":2},{\"value\":\"2026-01-07\",\"type\":2},{\"value\":\"0001.1.01.2026\",\"type\":2},{\"value\":\"Test Insert Voucher Biaya Nilai Decimal Update\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.11064004898071289}]}', 'true', 'Transjurnal/Dhhd/Controller', 'insert'),
(89, '2026-01-08 03:10:44', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd/saveDataOrUpdate', 'Melakukan penambahan data DHHD Djournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"4\",\"type\":2},{\"value\":\"2026-01-07\",\"type\":2},{\"value\":\"0001.1.01.2026\",\"type\":2},{\"value\":\"Test Insert Voucher Biaya Nilai Decimal Update\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.11064004898071289}]}', 'true', 'Transjurnal/Dhhd/Controller', 'insert'),
(90, '2026-01-08 03:10:44', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd/saveDataOrUpdate', 'Melakukan penambahan data DHHD Djournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"4\",\"type\":2},{\"value\":\"2026-01-07\",\"type\":2},{\"value\":\"0001.1.01.2026\",\"type\":2},{\"value\":\"Test Insert Voucher Biaya Nilai Decimal Update\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.11064004898071289}]}', 'true', 'Transjurnal/Dhhd/Controller', 'insert'),
(91, '2026-01-08 03:10:44', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd/saveDataOrUpdate', 'Melakukan penambahan data DHHD Djournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"4\",\"type\":2},{\"value\":\"2026-01-07\",\"type\":2},{\"value\":\"0001.1.01.2026\",\"type\":2},{\"value\":\"Test Insert Voucher Biaya Nilai Decimal Update\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.11064004898071289}]}', 'true', 'Transjurnal/Dhhd/Controller', 'insert'),
(92, '2026-01-08 03:10:51', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd/deleteData', 'Melakukan penghapusan data pada modul atau class App\\Modules\\Defaults\\TransJurnal\\Model_Mjournal', '{\"mjo_id\":\"4\",\"mjo_date\":\"2026-01-07\",\"mjo_code\":\"0001.1.01.2026\",\"mjo_ket\":\"Test Insert Voucher Biaya Nilai Decimal Update\",\"mjo_ajuan_untuk\":\"[TEST]\",\"mjo_ajuan_dari\":\"(0000) Transaksi\\/Laporan Gabungan\",\"jt_id\":\"1\",\"mjo_paymentdate\":null,\"mjo_ceque\":null,\"mjo_jbkid\":null,\"mjo_parentid\":null,\"is_jbk_multivoucher\":null,\"mjo_mperiod\":\"1\",\"mjo_yperiod\":\"2026\",\"mjo_ref\":null,\"is_auto\":null,\"auto_kode\":null,\"is_aktiva\":null,\"is_persediaan\":null,\"is_ju_susut\":null,\"is_air\":null,\"jr_jml_rek\":null,\"is_batal\":null,\"is_verify_voucher\":null,\"jenis_ju_lainnya\":null,\"id_mjo_ju_pj\":null,\"id_um_pj\":null,\"id_kontrak\":null,\"file_lampiran\":null,\"is_closed\":\"0\",\"create_dt\":\"2026-01-08 09:51:21\",\"update_dt\":\"2026-01-08 10:10:44\",\"user_input\":\"superadmin\",\"jenis_jbk\":null,\"dph_id\":null,\"jenis_memo\":\"1\"}', '{\"mjo_id\":\"4\",\"mjo_date\":\"2026-01-07\",\"mjo_code\":\"0001.1.01.2026\",\"mjo_ket\":\"Test Insert Voucher Biaya Nilai Decimal Update\",\"mjo_ajuan_untuk\":\"[TEST]\",\"mjo_ajuan_dari\":\"(0000) Transaksi\\/Laporan Gabungan\",\"jt_id\":\"1\",\"mjo_paymentdate\":null,\"mjo_ceque\":null,\"mjo_jbkid\":null,\"mjo_parentid\":null,\"is_jbk_multivoucher\":null,\"mjo_mperiod\":\"1\",\"mjo_yperiod\":\"2026\",\"mjo_ref\":null,\"is_auto\":null,\"auto_kode\":null,\"is_aktiva\":null,\"is_persediaan\":null,\"is_ju_susut\":null,\"is_air\":null,\"jr_jml_rek\":null,\"is_batal\":null,\"is_verify_voucher\":null,\"jenis_ju_lainnya\":null,\"id_mjo_ju_pj\":null,\"id_um_pj\":null,\"id_kontrak\":null,\"file_lampiran\":null,\"is_closed\":\"0\",\"create_dt\":\"2026-01-08 09:51:21\",\"update_dt\":\"2026-01-08 10:10:44\",\"user_input\":\"superadmin\",\"jenis_jbk\":null,\"dph_id\":null,\"jenis_memo\":\"1\"}', 'true', 'Defaults\\TransJurnal\\Dhhd', 'delete'),
(93, '2026-01-08 03:10:51', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd/deleteData', 'Melakukan penghapusan data pada modul atau class App\\Modules\\Defaults\\TransJurnal\\Model_Djournal', '{\"id\":\"11\",\"kode_satker\":\"0000\",\"acc_code\":\"91.01.15\",\"djo_debit\":\"5000000.66\",\"djo_credit\":\"0\",\"mjo_id\":\"4\",\"jt_id\":\"1\",\"djo_mperiod\":\"1\",\"djo_yperiod\":\"2026\",\"param\":\"Debit\"}', '{\"id\":\"11\",\"kode_satker\":\"0000\",\"acc_code\":\"91.01.15\",\"djo_debit\":\"5000000.66\",\"djo_credit\":\"0\",\"mjo_id\":\"4\",\"jt_id\":\"1\",\"djo_mperiod\":\"1\",\"djo_yperiod\":\"2026\",\"param\":\"Debit\"}', 'true', 'Defaults\\TransJurnal\\Dhhd', 'delete'),
(94, '2026-01-08 03:10:51', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd/deleteData', 'Melakukan penghapusan data pada modul atau class App\\Modules\\Defaults\\TransJurnal\\Model_Djournal', '{\"id\":\"12\",\"kode_satker\":\"0000\",\"acc_code\":\"50.02.10\",\"djo_debit\":\"0\",\"djo_credit\":\"5000000.66\",\"mjo_id\":\"4\",\"jt_id\":\"1\",\"djo_mperiod\":\"1\",\"djo_yperiod\":\"2026\",\"param\":\"Kredit\"}', '{\"id\":\"12\",\"kode_satker\":\"0000\",\"acc_code\":\"50.02.10\",\"djo_debit\":\"0\",\"djo_credit\":\"5000000.66\",\"mjo_id\":\"4\",\"jt_id\":\"1\",\"djo_mperiod\":\"1\",\"djo_yperiod\":\"2026\",\"param\":\"Kredit\"}', 'true', 'Defaults\\TransJurnal\\Dhhd', 'delete'),
(95, '2026-01-08 03:15:08', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd/saveDataOrUpdate', 'Melakukan penambahan data DHHD Mjournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"\",\"type\":2},{\"value\":\"2026-01-07\",\"type\":2},{\"value\":\"0001.1.01.2026\",\"type\":2},{\"value\":\"Test Keterangan\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.13386797904968262}]}', 'true', 'Transjurnal/Dhhd/Controller', 'insert'),
(96, '2026-01-08 03:15:09', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd/saveDataOrUpdate', 'Melakukan penambahan data DHHD Djournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"\",\"type\":2},{\"value\":\"2026-01-07\",\"type\":2},{\"value\":\"0001.1.01.2026\",\"type\":2},{\"value\":\"Test Keterangan\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.13386797904968262}]}', 'true', 'Transjurnal/Dhhd/Controller', 'insert'),
(97, '2026-01-08 03:15:09', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd/saveDataOrUpdate', 'Melakukan penambahan data DHHD Djournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"\",\"type\":2},{\"value\":\"2026-01-07\",\"type\":2},{\"value\":\"0001.1.01.2026\",\"type\":2},{\"value\":\"Test Keterangan\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.13386797904968262}]}', 'true', 'Transjurnal/Dhhd/Controller', 'insert'),
(98, '2026-01-08 03:15:09', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd/saveDataOrUpdate', 'Melakukan penambahan data DHHD Djournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"\",\"type\":2},{\"value\":\"2026-01-07\",\"type\":2},{\"value\":\"0001.1.01.2026\",\"type\":2},{\"value\":\"Test Keterangan\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.13386797904968262}]}', 'true', 'Transjurnal/Dhhd/Controller', 'insert'),
(99, '2026-01-08 03:17:45', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd/batchVerify', 'Melakukan perubahan data pada modul atau class App\\Modules\\Defaults\\TransJurnal\\Model_Mjournal', '{\"mjo_id\":\"5\",\"mjo_date\":\"2026-01-07\",\"mjo_code\":\"0001.1.01.2026\",\"mjo_ket\":\"Test Keterangan\",\"mjo_ajuan_untuk\":\"[TEST]\",\"mjo_ajuan_dari\":\"(0000) Transaksi\\/Laporan Gabungan\",\"jt_id\":\"1\",\"mjo_paymentdate\":null,\"mjo_ceque\":null,\"mjo_jbkid\":null,\"mjo_parentid\":null,\"is_jbk_multivoucher\":null,\"mjo_mperiod\":\"1\",\"mjo_yperiod\":\"2026\",\"mjo_ref\":\"0\",\"is_auto\":null,\"auto_kode\":null,\"is_aktiva\":\"0\",\"is_persediaan\":\"0\",\"is_ju_susut\":\"0\",\"is_air\":null,\"jr_jml_rek\":null,\"is_batal\":null,\"is_verify_voucher\":\"0\",\"jenis_ju_lainnya\":null,\"id_mjo_ju_pj\":null,\"id_um_pj\":null,\"id_kontrak\":null,\"file_lampiran\":null,\"is_closed\":\"0\",\"create_dt\":\"2026-01-08 10:15:08\",\"update_dt\":\"2026-01-08 10:15:08\",\"user_input\":\"superadmin\",\"jenis_jbk\":null,\"dph_id\":null,\"jenis_memo\":\"1\"}', '{\"mjo_id\":\"5\",\"mjo_date\":\"2026-01-07\",\"mjo_code\":\"0001.1.01.2026\",\"mjo_ket\":\"Test Keterangan\",\"mjo_ajuan_untuk\":\"[TEST]\",\"mjo_ajuan_dari\":\"(0000) Transaksi\\/Laporan Gabungan\",\"jt_id\":\"1\",\"mjo_paymentdate\":null,\"mjo_ceque\":null,\"mjo_jbkid\":null,\"mjo_parentid\":null,\"is_jbk_multivoucher\":null,\"mjo_mperiod\":\"1\",\"mjo_yperiod\":\"2026\",\"mjo_ref\":\"0\",\"is_auto\":null,\"auto_kode\":null,\"is_aktiva\":\"0\",\"is_persediaan\":\"0\",\"is_ju_susut\":\"0\",\"is_air\":null,\"jr_jml_rek\":null,\"is_batal\":null,\"is_verify_voucher\":1,\"jenis_ju_lainnya\":null,\"id_mjo_ju_pj\":null,\"id_um_pj\":null,\"id_kontrak\":null,\"file_lampiran\":null,\"is_closed\":\"0\",\"create_dt\":\"2026-01-08 10:15:08\",\"update_dt\":\"2026-01-08 10:15:08\",\"user_input\":\"superadmin\",\"jenis_jbk\":null,\"dph_id\":null,\"jenis_memo\":\"1\"}', 'true', 'Defaults\\TransJurnal\\Dhhd', 'update'),
(100, '2026-01-08 03:17:50', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd/batchUnverify', 'Melakukan perubahan data pada modul atau class App\\Modules\\Defaults\\TransJurnal\\Model_Mjournal', '{\"mjo_id\":\"5\",\"mjo_date\":\"2026-01-07\",\"mjo_code\":\"0001.1.01.2026\",\"mjo_ket\":\"Test Keterangan\",\"mjo_ajuan_untuk\":\"[TEST]\",\"mjo_ajuan_dari\":\"(0000) Transaksi\\/Laporan Gabungan\",\"jt_id\":\"1\",\"mjo_paymentdate\":null,\"mjo_ceque\":null,\"mjo_jbkid\":null,\"mjo_parentid\":null,\"is_jbk_multivoucher\":null,\"mjo_mperiod\":\"1\",\"mjo_yperiod\":\"2026\",\"mjo_ref\":\"0\",\"is_auto\":null,\"auto_kode\":null,\"is_aktiva\":\"0\",\"is_persediaan\":\"0\",\"is_ju_susut\":\"0\",\"is_air\":null,\"jr_jml_rek\":null,\"is_batal\":null,\"is_verify_voucher\":\"1\",\"jenis_ju_lainnya\":null,\"id_mjo_ju_pj\":null,\"id_um_pj\":null,\"id_kontrak\":null,\"file_lampiran\":null,\"is_closed\":\"0\",\"create_dt\":\"2026-01-08 10:15:08\",\"update_dt\":\"2026-01-08 10:17:45\",\"user_input\":\"superadmin\",\"jenis_jbk\":null,\"dph_id\":null,\"jenis_memo\":\"1\"}', '{\"mjo_id\":\"5\",\"mjo_date\":\"2026-01-07\",\"mjo_code\":\"0001.1.01.2026\",\"mjo_ket\":\"Test Keterangan\",\"mjo_ajuan_untuk\":\"[TEST]\",\"mjo_ajuan_dari\":\"(0000) Transaksi\\/Laporan Gabungan\",\"jt_id\":\"1\",\"mjo_paymentdate\":null,\"mjo_ceque\":null,\"mjo_jbkid\":null,\"mjo_parentid\":null,\"is_jbk_multivoucher\":null,\"mjo_mperiod\":\"1\",\"mjo_yperiod\":\"2026\",\"mjo_ref\":\"0\",\"is_auto\":null,\"auto_kode\":null,\"is_aktiva\":\"0\",\"is_persediaan\":\"0\",\"is_ju_susut\":\"0\",\"is_air\":null,\"jr_jml_rek\":null,\"is_batal\":null,\"is_verify_voucher\":0,\"jenis_ju_lainnya\":null,\"id_mjo_ju_pj\":null,\"id_um_pj\":null,\"id_kontrak\":null,\"file_lampiran\":null,\"is_closed\":\"0\",\"create_dt\":\"2026-01-08 10:15:08\",\"update_dt\":\"2026-01-08 10:17:45\",\"user_input\":\"superadmin\",\"jenis_jbk\":null,\"dph_id\":null,\"jenis_memo\":\"1\"}', 'true', 'Defaults\\TransJurnal\\Dhhd', 'update'),
(101, '2026-01-08 03:17:55', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd/batchVerify', 'Melakukan perubahan data pada modul atau class App\\Modules\\Defaults\\TransJurnal\\Model_Mjournal', '{\"mjo_id\":\"5\",\"mjo_date\":\"2026-01-07\",\"mjo_code\":\"0001.1.01.2026\",\"mjo_ket\":\"Test Keterangan\",\"mjo_ajuan_untuk\":\"[TEST]\",\"mjo_ajuan_dari\":\"(0000) Transaksi\\/Laporan Gabungan\",\"jt_id\":\"1\",\"mjo_paymentdate\":null,\"mjo_ceque\":null,\"mjo_jbkid\":null,\"mjo_parentid\":null,\"is_jbk_multivoucher\":null,\"mjo_mperiod\":\"1\",\"mjo_yperiod\":\"2026\",\"mjo_ref\":\"0\",\"is_auto\":null,\"auto_kode\":null,\"is_aktiva\":\"0\",\"is_persediaan\":\"0\",\"is_ju_susut\":\"0\",\"is_air\":null,\"jr_jml_rek\":null,\"is_batal\":null,\"is_verify_voucher\":\"0\",\"jenis_ju_lainnya\":null,\"id_mjo_ju_pj\":null,\"id_um_pj\":null,\"id_kontrak\":null,\"file_lampiran\":null,\"is_closed\":\"0\",\"create_dt\":\"2026-01-08 10:15:08\",\"update_dt\":\"2026-01-08 10:17:50\",\"user_input\":\"superadmin\",\"jenis_jbk\":null,\"dph_id\":null,\"jenis_memo\":\"1\"}', '{\"mjo_id\":\"5\",\"mjo_date\":\"2026-01-07\",\"mjo_code\":\"0001.1.01.2026\",\"mjo_ket\":\"Test Keterangan\",\"mjo_ajuan_untuk\":\"[TEST]\",\"mjo_ajuan_dari\":\"(0000) Transaksi\\/Laporan Gabungan\",\"jt_id\":\"1\",\"mjo_paymentdate\":null,\"mjo_ceque\":null,\"mjo_jbkid\":null,\"mjo_parentid\":null,\"is_jbk_multivoucher\":null,\"mjo_mperiod\":\"1\",\"mjo_yperiod\":\"2026\",\"mjo_ref\":\"0\",\"is_auto\":null,\"auto_kode\":null,\"is_aktiva\":\"0\",\"is_persediaan\":\"0\",\"is_ju_susut\":\"0\",\"is_air\":null,\"jr_jml_rek\":null,\"is_batal\":null,\"is_verify_voucher\":1,\"jenis_ju_lainnya\":null,\"id_mjo_ju_pj\":null,\"id_um_pj\":null,\"id_kontrak\":null,\"file_lampiran\":null,\"is_closed\":\"0\",\"create_dt\":\"2026-01-08 10:15:08\",\"update_dt\":\"2026-01-08 10:17:50\",\"user_input\":\"superadmin\",\"jenis_jbk\":null,\"dph_id\":null,\"jenis_memo\":\"1\"}', 'true', 'Defaults\\TransJurnal\\Dhhd', 'update'),
(102, '2026-01-08 04:09:52', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd-aktiva/saveDataOrUpdate', 'Melakukan penambahan data DHHD Mjournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_aktiva_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":\"0002.1.01.2026\",\"type\":2},{\"value\":\"Test Voucher Aktiva Nilai Decimal\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.052123069763183594}]}', 'true', 'Transjurnal/DhhdAktiva/Controller', 'insert'),
(103, '2026-01-08 04:12:31', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd-aktiva/saveDataOrUpdate', 'Melakukan penambahan data DHHD Mjournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_aktiva_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":\"0002.1.01.2026\",\"type\":2},{\"value\":\"Test Voucher Aktiva Nilai Decimal\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.0738379955291748}]}', 'true', 'Transjurnal/DhhdAktiva/Controller', 'insert');
INSERT INTO `system_log_akses` (`id`, `times`, `id_user`, `username`, `name`, `ip`, `url`, `message`, `data_before`, `data_after`, `response`, `controller`, `action`) VALUES
(104, '2026-01-08 04:12:31', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd-aktiva/saveDataOrUpdate', 'Melakukan penambahan data DHHD Aktiva Djournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_aktiva_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":\"0002.1.01.2026\",\"type\":2},{\"value\":\"Test Voucher Aktiva Nilai Decimal\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.0738379955291748}]}', 'true', 'Transjurnal/DhhdAktiva/Controller', 'insert'),
(105, '2026-01-08 04:12:31', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd-aktiva/saveDataOrUpdate', 'Melakukan penambahan data DHHD Aktiva Djournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_aktiva_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":\"0002.1.01.2026\",\"type\":2},{\"value\":\"Test Voucher Aktiva Nilai Decimal\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.0738379955291748}]}', 'true', 'Transjurnal/DhhdAktiva/Controller', 'insert'),
(106, '2026-01-08 04:12:31', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd-aktiva/saveDataOrUpdate', 'Melakukan penambahan data DHHD Aktiva Detail', 'null', '{\"queryLog\":[{\"sql\":\"CALL `transjurnal_dhhd_aktiva_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":\"0002.1.01.2026\",\"type\":2},{\"value\":\"Test Voucher Aktiva Nilai Decimal\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.0738379955291748},{\"sql\":\"CALL `transjurnal_dhhd_beforeinsert_djournal_v2`(?)\",\"bindings\":[{\"value\":\"7\",\"type\":2}],\"time\":0.018095970153808594},{\"sql\":\"CALL `transjurnal_dhhd_insert_djournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"7\",\"type\":2},{\"value\":\"31.02.90\",\"type\":2},{\"value\":4500000.21,\"type\":2},{\"value\":0,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":\"Debit\",\"type\":2}],\"time\":0.04907417297363281},{\"sql\":\"CALL `transjurnal_dhhd_insert_djournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"7\",\"type\":2},{\"value\":\"91.03.11\",\"type\":2},{\"value\":0,\"type\":1},{\"value\":4500000.21,\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":\"\",\"type\":2}],\"time\":0.0693359375},{\"sql\":\"CALL `transjurnal_dhhd_beforeinsert_aktiva_v2`(?)\",\"bindings\":[{\"value\":\"7\",\"type\":2}],\"time\":0.022871971130371094},{\"sql\":\"CALL `transjurnal_dhhd_aktiva_detail_insert`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"31.02.90\",\"type\":2},{\"value\":\"12345\",\"type\":2},{\"value\":\"Split 1\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":2000000.15,\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"Unit\",\"type\":2},{\"value\":2000000.15,\"type\":2},{\"value\":\"BB3\",\"type\":2},{\"value\":\"0002.1.01.2026\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"7\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.02827906608581543}]}', 'true', 'TransJurnal/DhhdAktiva/Controller', 'insert'),
(107, '2026-01-08 04:12:32', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd-aktiva/saveDataOrUpdate', 'Melakukan penambahan data DHHD Aktiva Detail', 'null', '{\"queryLog\":[{\"sql\":\"CALL `transjurnal_dhhd_aktiva_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":\"0002.1.01.2026\",\"type\":2},{\"value\":\"Test Voucher Aktiva Nilai Decimal\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.0738379955291748},{\"sql\":\"CALL `transjurnal_dhhd_beforeinsert_djournal_v2`(?)\",\"bindings\":[{\"value\":\"7\",\"type\":2}],\"time\":0.018095970153808594},{\"sql\":\"CALL `transjurnal_dhhd_insert_djournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"7\",\"type\":2},{\"value\":\"31.02.90\",\"type\":2},{\"value\":4500000.21,\"type\":2},{\"value\":0,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":\"Debit\",\"type\":2}],\"time\":0.04907417297363281},{\"sql\":\"CALL `transjurnal_dhhd_insert_djournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"7\",\"type\":2},{\"value\":\"91.03.11\",\"type\":2},{\"value\":0,\"type\":1},{\"value\":4500000.21,\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":\"\",\"type\":2}],\"time\":0.0693359375},{\"sql\":\"CALL `transjurnal_dhhd_beforeinsert_aktiva_v2`(?)\",\"bindings\":[{\"value\":\"7\",\"type\":2}],\"time\":0.022871971130371094},{\"sql\":\"CALL `transjurnal_dhhd_aktiva_detail_insert`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"31.02.90\",\"type\":2},{\"value\":\"12345\",\"type\":2},{\"value\":\"Split 1\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":2000000.15,\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"Unit\",\"type\":2},{\"value\":2000000.15,\"type\":2},{\"value\":\"BB3\",\"type\":2},{\"value\":\"0002.1.01.2026\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"7\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.02827906608581543},{\"sql\":\"CALL `transjurnal_dhhd_aktiva_detail_insert`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"31.02.90\",\"type\":2},{\"value\":\"678910\",\"type\":2},{\"value\":\"Split 2\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":2500000.06,\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"Unit\",\"type\":2},{\"value\":2500000.06,\"type\":2},{\"value\":\"BB3\",\"type\":2},{\"value\":\"0002.1.01.2026\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"7\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.036949872970581055}]}', 'true', 'TransJurnal/DhhdAktiva/Controller', 'insert'),
(108, '2026-01-08 04:12:32', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd-aktiva/saveDataOrUpdate', 'Melakukan penambahan tanda tangan dhhd', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_aktiva_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":\"0002.1.01.2026\",\"type\":2},{\"value\":\"Test Voucher Aktiva Nilai Decimal\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.0738379955291748},{\"sql\":\"CALL `transjurnal_dhhd_beforeinsert_djournal_v2`(?)\",\"bindings\":[{\"value\":\"7\",\"type\":2}],\"time\":0.018095970153808594},{\"sql\":\"CALL `transjurnal_dhhd_insert_djournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"7\",\"type\":2},{\"value\":\"31.02.90\",\"type\":2},{\"value\":4500000.21,\"type\":2},{\"value\":0,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":\"Debit\",\"type\":2}],\"time\":0.04907417297363281},{\"sql\":\"CALL `transjurnal_dhhd_insert_djournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"7\",\"type\":2},{\"value\":\"91.03.11\",\"type\":2},{\"value\":0,\"type\":1},{\"value\":4500000.21,\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":\"\",\"type\":2}],\"time\":0.0693359375},{\"sql\":\"CALL `transjurnal_dhhd_beforeinsert_aktiva_v2`(?)\",\"bindings\":[{\"value\":\"7\",\"type\":2}],\"time\":0.022871971130371094},{\"sql\":\"CALL `transjurnal_dhhd_aktiva_detail_insert`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"31.02.90\",\"type\":2},{\"value\":\"12345\",\"type\":2},{\"value\":\"Split 1\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":2000000.15,\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"Unit\",\"type\":2},{\"value\":2000000.15,\"type\":2},{\"value\":\"BB3\",\"type\":2},{\"value\":\"0002.1.01.2026\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"7\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.02827906608581543},{\"sql\":\"CALL `transjurnal_dhhd_aktiva_detail_insert`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"31.02.90\",\"type\":2},{\"value\":\"678910\",\"type\":2},{\"value\":\"Split 2\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":2500000.06,\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"Unit\",\"type\":2},{\"value\":2500000.06,\"type\":2},{\"value\":\"BB3\",\"type\":2},{\"value\":\"0002.1.01.2026\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"7\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.036949872970581055},{\"sql\":\"CALL `trans_jurnal_sp_insert_ttd_jurnal_from_lap`(?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"DHHD\",\"type\":2},{\"value\":\"7\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"superadmin\",\"type\":2},{\"value\":null,\"type\":0}],\"time\":0.035685062408447266}]}', 'true', 'Transjurnal/DhhdAktiva/Controller', 'insert'),
(109, '2026-01-08 04:15:28', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd-aktiva/batchVerify', 'Melakukan perubahan data pada modul atau class App\\Modules\\Defaults\\TransJurnal\\Model_Mjournal', '{\"mjo_id\":\"7\",\"mjo_date\":\"2026-01-08\",\"mjo_code\":\"0002.1.01.2026\",\"mjo_ket\":\"Test Voucher Aktiva Nilai Decimal\",\"mjo_ajuan_untuk\":\"[TEST]\",\"mjo_ajuan_dari\":\"(0000) Transaksi\\/Laporan Gabungan\",\"jt_id\":\"1\",\"mjo_paymentdate\":null,\"mjo_ceque\":null,\"mjo_jbkid\":null,\"mjo_parentid\":null,\"is_jbk_multivoucher\":null,\"mjo_mperiod\":\"1\",\"mjo_yperiod\":\"2026\",\"mjo_ref\":\"0\",\"is_auto\":null,\"auto_kode\":null,\"is_aktiva\":\"1\",\"is_persediaan\":\"0\",\"is_ju_susut\":\"0\",\"is_air\":null,\"jr_jml_rek\":null,\"is_batal\":null,\"is_verify_voucher\":\"0\",\"jenis_ju_lainnya\":null,\"id_mjo_ju_pj\":null,\"id_um_pj\":null,\"id_kontrak\":null,\"file_lampiran\":null,\"is_closed\":\"0\",\"create_dt\":\"2026-01-08 11:12:31\",\"update_dt\":\"2026-01-08 11:12:31\",\"user_input\":\"superadmin\",\"jenis_jbk\":null,\"dph_id\":null,\"jenis_memo\":\"1\"}', '{\"mjo_id\":\"7\",\"mjo_date\":\"2026-01-08\",\"mjo_code\":\"0002.1.01.2026\",\"mjo_ket\":\"Test Voucher Aktiva Nilai Decimal\",\"mjo_ajuan_untuk\":\"[TEST]\",\"mjo_ajuan_dari\":\"(0000) Transaksi\\/Laporan Gabungan\",\"jt_id\":\"1\",\"mjo_paymentdate\":null,\"mjo_ceque\":null,\"mjo_jbkid\":null,\"mjo_parentid\":null,\"is_jbk_multivoucher\":null,\"mjo_mperiod\":\"1\",\"mjo_yperiod\":\"2026\",\"mjo_ref\":\"0\",\"is_auto\":null,\"auto_kode\":null,\"is_aktiva\":\"1\",\"is_persediaan\":\"0\",\"is_ju_susut\":\"0\",\"is_air\":null,\"jr_jml_rek\":null,\"is_batal\":null,\"is_verify_voucher\":1,\"jenis_ju_lainnya\":null,\"id_mjo_ju_pj\":null,\"id_um_pj\":null,\"id_kontrak\":null,\"file_lampiran\":null,\"is_closed\":\"0\",\"create_dt\":\"2026-01-08 11:12:31\",\"update_dt\":\"2026-01-08 11:12:31\",\"user_input\":\"superadmin\",\"jenis_jbk\":null,\"dph_id\":null,\"jenis_memo\":\"1\"}', 'true', 'Defaults\\TransJurnal\\DhhdAktiva', 'update'),
(110, '2026-01-08 04:15:33', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd-aktiva/batchUnverify', 'Melakukan perubahan data pada modul atau class App\\Modules\\Defaults\\TransJurnal\\Model_Mjournal', '{\"mjo_id\":\"7\",\"mjo_date\":\"2026-01-08\",\"mjo_code\":\"0002.1.01.2026\",\"mjo_ket\":\"Test Voucher Aktiva Nilai Decimal\",\"mjo_ajuan_untuk\":\"[TEST]\",\"mjo_ajuan_dari\":\"(0000) Transaksi\\/Laporan Gabungan\",\"jt_id\":\"1\",\"mjo_paymentdate\":null,\"mjo_ceque\":null,\"mjo_jbkid\":null,\"mjo_parentid\":null,\"is_jbk_multivoucher\":null,\"mjo_mperiod\":\"1\",\"mjo_yperiod\":\"2026\",\"mjo_ref\":\"0\",\"is_auto\":null,\"auto_kode\":null,\"is_aktiva\":\"1\",\"is_persediaan\":\"0\",\"is_ju_susut\":\"0\",\"is_air\":null,\"jr_jml_rek\":null,\"is_batal\":null,\"is_verify_voucher\":\"1\",\"jenis_ju_lainnya\":null,\"id_mjo_ju_pj\":null,\"id_um_pj\":null,\"id_kontrak\":null,\"file_lampiran\":null,\"is_closed\":\"0\",\"create_dt\":\"2026-01-08 11:12:31\",\"update_dt\":\"2026-01-08 11:15:28\",\"user_input\":\"superadmin\",\"jenis_jbk\":null,\"dph_id\":null,\"jenis_memo\":\"1\"}', '{\"mjo_id\":\"7\",\"mjo_date\":\"2026-01-08\",\"mjo_code\":\"0002.1.01.2026\",\"mjo_ket\":\"Test Voucher Aktiva Nilai Decimal\",\"mjo_ajuan_untuk\":\"[TEST]\",\"mjo_ajuan_dari\":\"(0000) Transaksi\\/Laporan Gabungan\",\"jt_id\":\"1\",\"mjo_paymentdate\":null,\"mjo_ceque\":null,\"mjo_jbkid\":null,\"mjo_parentid\":null,\"is_jbk_multivoucher\":null,\"mjo_mperiod\":\"1\",\"mjo_yperiod\":\"2026\",\"mjo_ref\":\"0\",\"is_auto\":null,\"auto_kode\":null,\"is_aktiva\":\"1\",\"is_persediaan\":\"0\",\"is_ju_susut\":\"0\",\"is_air\":null,\"jr_jml_rek\":null,\"is_batal\":null,\"is_verify_voucher\":0,\"jenis_ju_lainnya\":null,\"id_mjo_ju_pj\":null,\"id_um_pj\":null,\"id_kontrak\":null,\"file_lampiran\":null,\"is_closed\":\"0\",\"create_dt\":\"2026-01-08 11:12:31\",\"update_dt\":\"2026-01-08 11:15:28\",\"user_input\":\"superadmin\",\"jenis_jbk\":null,\"dph_id\":null,\"jenis_memo\":\"1\"}', 'true', 'Defaults\\TransJurnal\\DhhdAktiva', 'update'),
(111, '2026-01-08 04:39:00', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd-aktiva/saveDataOrUpdate', 'Melakukan perubahan data DHHD Mjournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_aktiva_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"7\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":\"0002.1.01.2026\",\"type\":2},{\"value\":\"Test Voucher Aktiva Nilai Decimal\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.08608412742614746}]}', 'true', 'Transjurnal/DhhdAktiva/Controller', 'insert'),
(112, '2026-01-08 04:39:01', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd-aktiva/saveDataOrUpdate', 'Melakukan penambahan data DHHD Aktiva Djournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_aktiva_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"7\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":\"0002.1.01.2026\",\"type\":2},{\"value\":\"Test Voucher Aktiva Nilai Decimal\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.08608412742614746}]}', 'true', 'Transjurnal/DhhdAktiva/Controller', 'insert'),
(113, '2026-01-08 04:39:01', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd-aktiva/saveDataOrUpdate', 'Melakukan penambahan data DHHD Aktiva Djournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_aktiva_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"7\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":\"0002.1.01.2026\",\"type\":2},{\"value\":\"Test Voucher Aktiva Nilai Decimal\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.08608412742614746}]}', 'true', 'Transjurnal/DhhdAktiva/Controller', 'insert'),
(114, '2026-01-08 04:39:01', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd-aktiva/saveDataOrUpdate', 'Melakukan penambahan data DHHD Aktiva Detail', 'null', '{\"queryLog\":[{\"sql\":\"CALL `transjurnal_dhhd_aktiva_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"7\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":\"0002.1.01.2026\",\"type\":2},{\"value\":\"Test Voucher Aktiva Nilai Decimal\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.08608412742614746},{\"sql\":\"CALL `transjurnal_dhhd_beforeinsert_djournal_v2`(?)\",\"bindings\":[{\"value\":\"7\",\"type\":2}],\"time\":0.05705404281616211},{\"sql\":\"CALL `transjurnal_dhhd_insert_djournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"7\",\"type\":2},{\"value\":\"31.02.90\",\"type\":2},{\"value\":6000000.66,\"type\":2},{\"value\":0,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":\"Debit\",\"type\":2}],\"time\":0.07487607002258301},{\"sql\":\"CALL `transjurnal_dhhd_insert_djournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"7\",\"type\":2},{\"value\":\"91.03.11\",\"type\":2},{\"value\":0,\"type\":1},{\"value\":6000000.66,\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":\"\",\"type\":2}],\"time\":0.05638909339904785},{\"sql\":\"CALL `transjurnal_dhhd_beforeinsert_aktiva_v2`(?)\",\"bindings\":[{\"value\":\"7\",\"type\":2}],\"time\":0.04799008369445801},{\"sql\":\"CALL `transjurnal_dhhd_aktiva_detail_insert`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"31.02.90\",\"type\":2},{\"value\":\"12345\",\"type\":2},{\"value\":\"Split 1 Pipa\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":3000000.33,\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"Unit\",\"type\":2},{\"value\":3000000.33,\"type\":2},{\"value\":\"BB4\",\"type\":2},{\"value\":\"0002.1.01.2026\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"7\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.043045997619628906}]}', 'true', 'TransJurnal/DhhdAktiva/Controller', 'insert'),
(115, '2026-01-08 04:39:01', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd-aktiva/saveDataOrUpdate', 'Melakukan penambahan data DHHD Aktiva Detail', 'null', '{\"queryLog\":[{\"sql\":\"CALL `transjurnal_dhhd_aktiva_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"7\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":\"0002.1.01.2026\",\"type\":2},{\"value\":\"Test Voucher Aktiva Nilai Decimal\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.08608412742614746},{\"sql\":\"CALL `transjurnal_dhhd_beforeinsert_djournal_v2`(?)\",\"bindings\":[{\"value\":\"7\",\"type\":2}],\"time\":0.05705404281616211},{\"sql\":\"CALL `transjurnal_dhhd_insert_djournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"7\",\"type\":2},{\"value\":\"31.02.90\",\"type\":2},{\"value\":6000000.66,\"type\":2},{\"value\":0,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":\"Debit\",\"type\":2}],\"time\":0.07487607002258301},{\"sql\":\"CALL `transjurnal_dhhd_insert_djournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"7\",\"type\":2},{\"value\":\"91.03.11\",\"type\":2},{\"value\":0,\"type\":1},{\"value\":6000000.66,\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":\"\",\"type\":2}],\"time\":0.05638909339904785},{\"sql\":\"CALL `transjurnal_dhhd_beforeinsert_aktiva_v2`(?)\",\"bindings\":[{\"value\":\"7\",\"type\":2}],\"time\":0.04799008369445801},{\"sql\":\"CALL `transjurnal_dhhd_aktiva_detail_insert`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"31.02.90\",\"type\":2},{\"value\":\"12345\",\"type\":2},{\"value\":\"Split 1 Pipa\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":3000000.33,\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"Unit\",\"type\":2},{\"value\":3000000.33,\"type\":2},{\"value\":\"BB4\",\"type\":2},{\"value\":\"0002.1.01.2026\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"7\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.043045997619628906},{\"sql\":\"CALL `transjurnal_dhhd_aktiva_detail_insert`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"31.02.90\",\"type\":2},{\"value\":\"678910\",\"type\":2},{\"value\":\"Split 2 Pipa\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":3000000.33,\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"Unit\",\"type\":2},{\"value\":3000000.33,\"type\":2},{\"value\":\"BB4\",\"type\":2},{\"value\":\"0002.1.01.2026\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"7\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.03755784034729004}]}', 'true', 'TransJurnal/DhhdAktiva/Controller', 'insert'),
(116, '2026-01-08 04:39:01', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd-aktiva/saveDataOrUpdate', 'Melakukan penambahan tanda tangan dhhd', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_aktiva_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"7\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":\"0002.1.01.2026\",\"type\":2},{\"value\":\"Test Voucher Aktiva Nilai Decimal\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.08608412742614746},{\"sql\":\"CALL `transjurnal_dhhd_beforeinsert_djournal_v2`(?)\",\"bindings\":[{\"value\":\"7\",\"type\":2}],\"time\":0.05705404281616211},{\"sql\":\"CALL `transjurnal_dhhd_insert_djournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"7\",\"type\":2},{\"value\":\"31.02.90\",\"type\":2},{\"value\":6000000.66,\"type\":2},{\"value\":0,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":\"Debit\",\"type\":2}],\"time\":0.07487607002258301},{\"sql\":\"CALL `transjurnal_dhhd_insert_djournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"7\",\"type\":2},{\"value\":\"91.03.11\",\"type\":2},{\"value\":0,\"type\":1},{\"value\":6000000.66,\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":\"\",\"type\":2}],\"time\":0.05638909339904785},{\"sql\":\"CALL `transjurnal_dhhd_beforeinsert_aktiva_v2`(?)\",\"bindings\":[{\"value\":\"7\",\"type\":2}],\"time\":0.04799008369445801},{\"sql\":\"CALL `transjurnal_dhhd_aktiva_detail_insert`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"31.02.90\",\"type\":2},{\"value\":\"12345\",\"type\":2},{\"value\":\"Split 1 Pipa\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":3000000.33,\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"Unit\",\"type\":2},{\"value\":3000000.33,\"type\":2},{\"value\":\"BB4\",\"type\":2},{\"value\":\"0002.1.01.2026\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"7\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.043045997619628906},{\"sql\":\"CALL `transjurnal_dhhd_aktiva_detail_insert`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"31.02.90\",\"type\":2},{\"value\":\"678910\",\"type\":2},{\"value\":\"Split 2 Pipa\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":3000000.33,\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"Unit\",\"type\":2},{\"value\":3000000.33,\"type\":2},{\"value\":\"BB4\",\"type\":2},{\"value\":\"0002.1.01.2026\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"7\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.03755784034729004},{\"sql\":\"CALL `trans_jurnal_sp_insert_ttd_jurnal_from_lap`(?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"DHHD\",\"type\":2},{\"value\":\"7\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"superadmin\",\"type\":2},{\"value\":null,\"type\":0}],\"time\":0.017519235610961914}]}', 'true', 'Transjurnal/DhhdAktiva/Controller', 'insert'),
(117, '2026-01-08 07:23:48', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd-sl/saveDataOrUpdate', 'Melakukan penambahan data DHHD SL Mjournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_aktiva_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":\"0003.1.01.2026\",\"type\":2},{\"value\":\"Test Memo SL Nilai Decimal\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.06408905982971191}]}', 'true', 'Transjurnal/DhhdSl/Controller', 'insert'),
(118, '2026-01-08 07:25:10', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd-sl/saveDataOrUpdate', 'Melakukan penambahan data DHHD SL Mjournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_aktiva_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":\"0003.1.01.2026\",\"type\":2},{\"value\":\"Test Memo SL Nilai Decimal\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.0516200065612793}]}', 'true', 'Transjurnal/DhhdSl/Controller', 'insert'),
(119, '2026-01-08 07:25:11', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd-sl/saveDataOrUpdate', 'Melakukan penambahan data DHHD SL Djournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_aktiva_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":\"0003.1.01.2026\",\"type\":2},{\"value\":\"Test Memo SL Nilai Decimal\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.0516200065612793}]}', 'true', 'Transjurnal/DhhdSl/Controller', 'insert'),
(120, '2026-01-08 07:25:11', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd-sl/saveDataOrUpdate', 'Melakukan penambahan data DHHD SL Djournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_aktiva_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":\"0003.1.01.2026\",\"type\":2},{\"value\":\"Test Memo SL Nilai Decimal\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.0516200065612793}]}', 'true', 'Transjurnal/DhhdSl/Controller', 'insert'),
(121, '2026-01-08 07:25:11', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd-sl/saveDataOrUpdate', 'Melakukan penambahan data DHHD SL Aktiva Detail', 'null', '{\"queryLog\":[{\"sql\":\"CALL `transjurnal_dhhd_aktiva_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":\"0003.1.01.2026\",\"type\":2},{\"value\":\"Test Memo SL Nilai Decimal\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.0516200065612793},{\"sql\":\"CALL `transjurnal_dhhd_beforeinsert_djournal_v2`(?)\",\"bindings\":[{\"value\":\"9\",\"type\":2}],\"time\":0.023283004760742188},{\"sql\":\"CALL `transjurnal_dhhd_insert_djournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"9\",\"type\":2},{\"value\":\"31.05.41\",\"type\":2},{\"value\":4000000,\"type\":1},{\"value\":0,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":null,\"type\":0}],\"time\":0.04469799995422363},{\"sql\":\"CALL `transjurnal_dhhd_insert_djournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"9\",\"type\":2},{\"value\":\"91.03.90\",\"type\":2},{\"value\":0,\"type\":1},{\"value\":4000000,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":null,\"type\":0}],\"time\":0.08607006072998047},{\"sql\":\"CALL `transjurnal_dhhd_beforeinsert_aktiva_v2`(?)\",\"bindings\":[{\"value\":\"9\",\"type\":2}],\"time\":0.04311108589172363},{\"sql\":\"CALL `transjurnal_dhhd_aktiva_detail_insert`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"31.05.41\",\"type\":2},{\"value\":\"12345\",\"type\":2},{\"value\":\"Split 1\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":2000000,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"Unit\",\"type\":2},{\"value\":2000000,\"type\":1},{\"value\":\"BB3\",\"type\":2},{\"value\":\"0003.1.01.2026\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"9\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.08664989471435547}]}', 'true', 'TransJurnal/DhhdSl/Controller', 'insert'),
(122, '2026-01-08 07:25:12', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd-sl/saveDataOrUpdate', 'Melakukan penambahan data DHHD SL Aktiva Detail', 'null', '{\"queryLog\":[{\"sql\":\"CALL `transjurnal_dhhd_aktiva_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":\"0003.1.01.2026\",\"type\":2},{\"value\":\"Test Memo SL Nilai Decimal\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.0516200065612793},{\"sql\":\"CALL `transjurnal_dhhd_beforeinsert_djournal_v2`(?)\",\"bindings\":[{\"value\":\"9\",\"type\":2}],\"time\":0.023283004760742188},{\"sql\":\"CALL `transjurnal_dhhd_insert_djournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"9\",\"type\":2},{\"value\":\"31.05.41\",\"type\":2},{\"value\":4000000,\"type\":1},{\"value\":0,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":null,\"type\":0}],\"time\":0.04469799995422363},{\"sql\":\"CALL `transjurnal_dhhd_insert_djournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"9\",\"type\":2},{\"value\":\"91.03.90\",\"type\":2},{\"value\":0,\"type\":1},{\"value\":4000000,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":null,\"type\":0}],\"time\":0.08607006072998047},{\"sql\":\"CALL `transjurnal_dhhd_beforeinsert_aktiva_v2`(?)\",\"bindings\":[{\"value\":\"9\",\"type\":2}],\"time\":0.04311108589172363},{\"sql\":\"CALL `transjurnal_dhhd_aktiva_detail_insert`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"31.05.41\",\"type\":2},{\"value\":\"12345\",\"type\":2},{\"value\":\"Split 1\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":2000000,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"Unit\",\"type\":2},{\"value\":2000000,\"type\":1},{\"value\":\"BB3\",\"type\":2},{\"value\":\"0003.1.01.2026\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"9\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.08664989471435547},{\"sql\":\"CALL `transjurnal_dhhd_aktiva_detail_insert`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"31.05.41\",\"type\":2},{\"value\":\"6789\",\"type\":2},{\"value\":\"Split 2\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":2000000,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"Unit\",\"type\":2},{\"value\":2000000,\"type\":1},{\"value\":\"BB3\",\"type\":2},{\"value\":\"0003.1.01.2026\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"9\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.32878899574279785}]}', 'true', 'TransJurnal/DhhdSl/Controller', 'insert'),
(123, '2026-01-08 07:25:12', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd-sl/saveDataOrUpdate', 'Melakukan penambahan tanda tangan dhhd', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_aktiva_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":\"0003.1.01.2026\",\"type\":2},{\"value\":\"Test Memo SL Nilai Decimal\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.0516200065612793},{\"sql\":\"CALL `transjurnal_dhhd_beforeinsert_djournal_v2`(?)\",\"bindings\":[{\"value\":\"9\",\"type\":2}],\"time\":0.023283004760742188},{\"sql\":\"CALL `transjurnal_dhhd_insert_djournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"9\",\"type\":2},{\"value\":\"31.05.41\",\"type\":2},{\"value\":4000000,\"type\":1},{\"value\":0,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":null,\"type\":0}],\"time\":0.04469799995422363},{\"sql\":\"CALL `transjurnal_dhhd_insert_djournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"9\",\"type\":2},{\"value\":\"91.03.90\",\"type\":2},{\"value\":0,\"type\":1},{\"value\":4000000,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":null,\"type\":0}],\"time\":0.08607006072998047},{\"sql\":\"CALL `transjurnal_dhhd_beforeinsert_aktiva_v2`(?)\",\"bindings\":[{\"value\":\"9\",\"type\":2}],\"time\":0.04311108589172363},{\"sql\":\"CALL `transjurnal_dhhd_aktiva_detail_insert`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"31.05.41\",\"type\":2},{\"value\":\"12345\",\"type\":2},{\"value\":\"Split 1\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":2000000,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"Unit\",\"type\":2},{\"value\":2000000,\"type\":1},{\"value\":\"BB3\",\"type\":2},{\"value\":\"0003.1.01.2026\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"9\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.08664989471435547},{\"sql\":\"CALL `transjurnal_dhhd_aktiva_detail_insert`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"31.05.41\",\"type\":2},{\"value\":\"6789\",\"type\":2},{\"value\":\"Split 2\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":2000000,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"Unit\",\"type\":2},{\"value\":2000000,\"type\":1},{\"value\":\"BB3\",\"type\":2},{\"value\":\"0003.1.01.2026\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"9\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.32878899574279785},{\"sql\":\"CALL `trans_jurnal_sp_insert_ttd_jurnal_from_lap`(?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"DHHD_AKTIVA\",\"type\":2},{\"value\":\"9\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"superadmin\",\"type\":2},{\"value\":null,\"type\":0}],\"time\":0.02283000946044922}]}', 'true', 'Transjurnal/DhhdAktiva/Controller', 'insert'),
(124, '2026-01-08 07:32:50', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd-sl/saveDataOrUpdate', 'Melakukan perubahan data DHHD SL Mjournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_aktiva_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"9\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":\"0003.1.01.2026\",\"type\":2},{\"value\":\"Test Memo SL Nilai Decimal\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.04404592514038086}]}', 'true', 'Transjurnal/DhhdSl/Controller', 'insert'),
(125, '2026-01-08 07:32:50', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd-sl/saveDataOrUpdate', 'Melakukan penambahan data DHHD SL Djournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_aktiva_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"9\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":\"0003.1.01.2026\",\"type\":2},{\"value\":\"Test Memo SL Nilai Decimal\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.04404592514038086}]}', 'true', 'Transjurnal/DhhdSl/Controller', 'insert'),
(126, '2026-01-08 07:32:50', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd-sl/saveDataOrUpdate', 'Melakukan penambahan data DHHD SL Djournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_aktiva_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"9\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":\"0003.1.01.2026\",\"type\":2},{\"value\":\"Test Memo SL Nilai Decimal\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.04404592514038086}]}', 'true', 'Transjurnal/DhhdSl/Controller', 'insert'),
(127, '2026-01-08 07:32:50', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd-sl/saveDataOrUpdate', 'Melakukan penambahan data DHHD SL Aktiva Detail', 'null', '{\"queryLog\":[{\"sql\":\"CALL `transjurnal_dhhd_aktiva_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"9\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":\"0003.1.01.2026\",\"type\":2},{\"value\":\"Test Memo SL Nilai Decimal\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.04404592514038086},{\"sql\":\"CALL `transjurnal_dhhd_beforeinsert_djournal_v2`(?)\",\"bindings\":[{\"value\":\"9\",\"type\":2}],\"time\":0.040895938873291016},{\"sql\":\"CALL `transjurnal_dhhd_insert_djournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"9\",\"type\":2},{\"value\":\"31.05.41\",\"type\":2},{\"value\":4500000.66,\"type\":2},{\"value\":0,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":null,\"type\":0}],\"time\":0.04237484931945801},{\"sql\":\"CALL `transjurnal_dhhd_insert_djournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"9\",\"type\":2},{\"value\":\"91.03.90\",\"type\":2},{\"value\":0,\"type\":1},{\"value\":4500000.66,\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":null,\"type\":0}],\"time\":0.03327202796936035},{\"sql\":\"CALL `transjurnal_dhhd_beforeinsert_aktiva_v2`(?)\",\"bindings\":[{\"value\":\"9\",\"type\":2}],\"time\":0.0498499870300293},{\"sql\":\"CALL `transjurnal_dhhd_aktiva_detail_insert`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"31.05.41\",\"type\":2},{\"value\":\"111\",\"type\":2},{\"value\":\"Split 1 SL\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":2250000.33,\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"Unit\",\"type\":2},{\"value\":2250000.33,\"type\":2},{\"value\":\"BB4\",\"type\":2},{\"value\":\"0003.1.01.2026\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"9\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.043643951416015625}]}', 'true', 'TransJurnal/DhhdSl/Controller', 'insert'),
(128, '2026-01-08 07:32:50', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd-sl/saveDataOrUpdate', 'Melakukan penambahan data DHHD SL Aktiva Detail', 'null', '{\"queryLog\":[{\"sql\":\"CALL `transjurnal_dhhd_aktiva_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"9\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":\"0003.1.01.2026\",\"type\":2},{\"value\":\"Test Memo SL Nilai Decimal\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.04404592514038086},{\"sql\":\"CALL `transjurnal_dhhd_beforeinsert_djournal_v2`(?)\",\"bindings\":[{\"value\":\"9\",\"type\":2}],\"time\":0.040895938873291016},{\"sql\":\"CALL `transjurnal_dhhd_insert_djournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"9\",\"type\":2},{\"value\":\"31.05.41\",\"type\":2},{\"value\":4500000.66,\"type\":2},{\"value\":0,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":null,\"type\":0}],\"time\":0.04237484931945801},{\"sql\":\"CALL `transjurnal_dhhd_insert_djournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"9\",\"type\":2},{\"value\":\"91.03.90\",\"type\":2},{\"value\":0,\"type\":1},{\"value\":4500000.66,\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":null,\"type\":0}],\"time\":0.03327202796936035},{\"sql\":\"CALL `transjurnal_dhhd_beforeinsert_aktiva_v2`(?)\",\"bindings\":[{\"value\":\"9\",\"type\":2}],\"time\":0.0498499870300293},{\"sql\":\"CALL `transjurnal_dhhd_aktiva_detail_insert`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"31.05.41\",\"type\":2},{\"value\":\"111\",\"type\":2},{\"value\":\"Split 1 SL\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":2250000.33,\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"Unit\",\"type\":2},{\"value\":2250000.33,\"type\":2},{\"value\":\"BB4\",\"type\":2},{\"value\":\"0003.1.01.2026\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"9\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.043643951416015625},{\"sql\":\"CALL `transjurnal_dhhd_aktiva_detail_insert`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"31.05.41\",\"type\":2},{\"value\":\"222\",\"type\":2},{\"value\":\"Split 2 SL\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":2250000.33,\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"Unit\",\"type\":2},{\"value\":2250000.33,\"type\":2},{\"value\":\"BB4\",\"type\":2},{\"value\":\"0003.1.01.2026\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"9\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.043092966079711914}]}', 'true', 'TransJurnal/DhhdSl/Controller', 'insert'),
(129, '2026-01-08 07:32:50', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd-sl/saveDataOrUpdate', 'Melakukan penambahan tanda tangan dhhd', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_aktiva_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"9\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":\"0003.1.01.2026\",\"type\":2},{\"value\":\"Test Memo SL Nilai Decimal\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.04404592514038086},{\"sql\":\"CALL `transjurnal_dhhd_beforeinsert_djournal_v2`(?)\",\"bindings\":[{\"value\":\"9\",\"type\":2}],\"time\":0.040895938873291016},{\"sql\":\"CALL `transjurnal_dhhd_insert_djournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"9\",\"type\":2},{\"value\":\"31.05.41\",\"type\":2},{\"value\":4500000.66,\"type\":2},{\"value\":0,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":null,\"type\":0}],\"time\":0.04237484931945801},{\"sql\":\"CALL `transjurnal_dhhd_insert_djournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"9\",\"type\":2},{\"value\":\"91.03.90\",\"type\":2},{\"value\":0,\"type\":1},{\"value\":4500000.66,\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":null,\"type\":0}],\"time\":0.03327202796936035},{\"sql\":\"CALL `transjurnal_dhhd_beforeinsert_aktiva_v2`(?)\",\"bindings\":[{\"value\":\"9\",\"type\":2}],\"time\":0.0498499870300293},{\"sql\":\"CALL `transjurnal_dhhd_aktiva_detail_insert`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"31.05.41\",\"type\":2},{\"value\":\"111\",\"type\":2},{\"value\":\"Split 1 SL\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":2250000.33,\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"Unit\",\"type\":2},{\"value\":2250000.33,\"type\":2},{\"value\":\"BB4\",\"type\":2},{\"value\":\"0003.1.01.2026\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"9\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.043643951416015625},{\"sql\":\"CALL `transjurnal_dhhd_aktiva_detail_insert`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"31.05.41\",\"type\":2},{\"value\":\"222\",\"type\":2},{\"value\":\"Split 2 SL\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":2250000.33,\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"Unit\",\"type\":2},{\"value\":2250000.33,\"type\":2},{\"value\":\"BB4\",\"type\":2},{\"value\":\"0003.1.01.2026\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"9\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.043092966079711914},{\"sql\":\"CALL `trans_jurnal_sp_insert_ttd_jurnal_from_lap`(?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"DHHD_AKTIVA\",\"type\":2},{\"value\":\"9\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"superadmin\",\"type\":2},{\"value\":null,\"type\":0}],\"time\":0.0161130428314209}]}', 'true', 'Transjurnal/DhhdAktiva/Controller', 'insert');
INSERT INTO `system_log_akses` (`id`, `times`, `id_user`, `username`, `name`, `ip`, `url`, `message`, `data_before`, `data_after`, `response`, `controller`, `action`) VALUES
(130, '2026-01-08 07:33:35', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd-sl/saveDataOrUpdate', 'Melakukan perubahan data DHHD SL Mjournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_aktiva_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"9\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":\"0003.1.01.2026\",\"type\":2},{\"value\":\"Test Memo SL Nilai Decimal Update\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.07380485534667969}]}', 'true', 'Transjurnal/DhhdSl/Controller', 'insert'),
(131, '2026-01-08 07:33:35', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd-sl/saveDataOrUpdate', 'Melakukan penambahan data DHHD SL Djournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_aktiva_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"9\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":\"0003.1.01.2026\",\"type\":2},{\"value\":\"Test Memo SL Nilai Decimal Update\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.07380485534667969}]}', 'true', 'Transjurnal/DhhdSl/Controller', 'insert'),
(132, '2026-01-08 07:33:36', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd-sl/saveDataOrUpdate', 'Melakukan penambahan data DHHD SL Djournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_aktiva_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"9\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":\"0003.1.01.2026\",\"type\":2},{\"value\":\"Test Memo SL Nilai Decimal Update\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.07380485534667969}]}', 'true', 'Transjurnal/DhhdSl/Controller', 'insert'),
(133, '2026-01-08 07:33:36', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd-sl/saveDataOrUpdate', 'Melakukan penambahan data DHHD SL Aktiva Detail', 'null', '{\"queryLog\":[{\"sql\":\"CALL `transjurnal_dhhd_aktiva_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"9\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":\"0003.1.01.2026\",\"type\":2},{\"value\":\"Test Memo SL Nilai Decimal Update\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.07380485534667969},{\"sql\":\"CALL `transjurnal_dhhd_beforeinsert_djournal_v2`(?)\",\"bindings\":[{\"value\":\"9\",\"type\":2}],\"time\":0.05411696434020996},{\"sql\":\"CALL `transjurnal_dhhd_insert_djournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"9\",\"type\":2},{\"value\":\"31.05.41\",\"type\":2},{\"value\":4500000.66,\"type\":2},{\"value\":0,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":null,\"type\":0}],\"time\":0.031321048736572266},{\"sql\":\"CALL `transjurnal_dhhd_insert_djournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"9\",\"type\":2},{\"value\":\"91.03.90\",\"type\":2},{\"value\":0,\"type\":1},{\"value\":4500000.66,\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":null,\"type\":0}],\"time\":0.03392291069030762},{\"sql\":\"CALL `transjurnal_dhhd_beforeinsert_aktiva_v2`(?)\",\"bindings\":[{\"value\":\"9\",\"type\":2}],\"time\":0.29081201553344727},{\"sql\":\"CALL `transjurnal_dhhd_aktiva_detail_insert`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"31.05.41\",\"type\":2},{\"value\":\"111\",\"type\":2},{\"value\":\"Split 1 SL\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":2250000.33,\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"Unit\",\"type\":2},{\"value\":2250000.33,\"type\":2},{\"value\":\"BB4\",\"type\":2},{\"value\":\"0003.1.01.2026\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"9\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.12129497528076172}]}', 'true', 'TransJurnal/DhhdSl/Controller', 'insert'),
(134, '2026-01-08 07:33:36', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd-sl/saveDataOrUpdate', 'Melakukan penambahan data DHHD SL Aktiva Detail', 'null', '{\"queryLog\":[{\"sql\":\"CALL `transjurnal_dhhd_aktiva_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"9\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":\"0003.1.01.2026\",\"type\":2},{\"value\":\"Test Memo SL Nilai Decimal Update\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.07380485534667969},{\"sql\":\"CALL `transjurnal_dhhd_beforeinsert_djournal_v2`(?)\",\"bindings\":[{\"value\":\"9\",\"type\":2}],\"time\":0.05411696434020996},{\"sql\":\"CALL `transjurnal_dhhd_insert_djournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"9\",\"type\":2},{\"value\":\"31.05.41\",\"type\":2},{\"value\":4500000.66,\"type\":2},{\"value\":0,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":null,\"type\":0}],\"time\":0.031321048736572266},{\"sql\":\"CALL `transjurnal_dhhd_insert_djournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"9\",\"type\":2},{\"value\":\"91.03.90\",\"type\":2},{\"value\":0,\"type\":1},{\"value\":4500000.66,\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":null,\"type\":0}],\"time\":0.03392291069030762},{\"sql\":\"CALL `transjurnal_dhhd_beforeinsert_aktiva_v2`(?)\",\"bindings\":[{\"value\":\"9\",\"type\":2}],\"time\":0.29081201553344727},{\"sql\":\"CALL `transjurnal_dhhd_aktiva_detail_insert`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"31.05.41\",\"type\":2},{\"value\":\"111\",\"type\":2},{\"value\":\"Split 1 SL\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":2250000.33,\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"Unit\",\"type\":2},{\"value\":2250000.33,\"type\":2},{\"value\":\"BB4\",\"type\":2},{\"value\":\"0003.1.01.2026\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"9\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.12129497528076172},{\"sql\":\"CALL `transjurnal_dhhd_aktiva_detail_insert`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"31.05.41\",\"type\":2},{\"value\":\"222\",\"type\":2},{\"value\":\"Split 2 SL\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":2250000.33,\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"Unit\",\"type\":2},{\"value\":2250000.33,\"type\":2},{\"value\":\"BB4\",\"type\":2},{\"value\":\"0003.1.01.2026\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"9\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.046527862548828125}]}', 'true', 'TransJurnal/DhhdSl/Controller', 'insert'),
(135, '2026-01-08 07:33:37', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd-sl/saveDataOrUpdate', 'Melakukan penambahan tanda tangan dhhd', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_dhhd_aktiva_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"9\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":\"0003.1.01.2026\",\"type\":2},{\"value\":\"Test Memo SL Nilai Decimal Update\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"(0000) Transaksi\\/Laporan Gabungan\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.07380485534667969},{\"sql\":\"CALL `transjurnal_dhhd_beforeinsert_djournal_v2`(?)\",\"bindings\":[{\"value\":\"9\",\"type\":2}],\"time\":0.05411696434020996},{\"sql\":\"CALL `transjurnal_dhhd_insert_djournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"9\",\"type\":2},{\"value\":\"31.05.41\",\"type\":2},{\"value\":4500000.66,\"type\":2},{\"value\":0,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":null,\"type\":0}],\"time\":0.031321048736572266},{\"sql\":\"CALL `transjurnal_dhhd_insert_djournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"9\",\"type\":2},{\"value\":\"91.03.90\",\"type\":2},{\"value\":0,\"type\":1},{\"value\":4500000.66,\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":null,\"type\":0}],\"time\":0.03392291069030762},{\"sql\":\"CALL `transjurnal_dhhd_beforeinsert_aktiva_v2`(?)\",\"bindings\":[{\"value\":\"9\",\"type\":2}],\"time\":0.29081201553344727},{\"sql\":\"CALL `transjurnal_dhhd_aktiva_detail_insert`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"31.05.41\",\"type\":2},{\"value\":\"111\",\"type\":2},{\"value\":\"Split 1 SL\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":2250000.33,\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"Unit\",\"type\":2},{\"value\":2250000.33,\"type\":2},{\"value\":\"BB4\",\"type\":2},{\"value\":\"0003.1.01.2026\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"9\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.12129497528076172},{\"sql\":\"CALL `transjurnal_dhhd_aktiva_detail_insert`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"31.05.41\",\"type\":2},{\"value\":\"222\",\"type\":2},{\"value\":\"Split 2 SL\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":2250000.33,\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"Unit\",\"type\":2},{\"value\":2250000.33,\"type\":2},{\"value\":\"BB4\",\"type\":2},{\"value\":\"0003.1.01.2026\",\"type\":2},{\"value\":\"[TEST]\",\"type\":2},{\"value\":\"9\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.046527862548828125},{\"sql\":\"CALL `trans_jurnal_sp_insert_ttd_jurnal_from_lap`(?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"DHHD_AKTIVA\",\"type\":2},{\"value\":\"9\",\"type\":2},{\"value\":1,\"type\":1},{\"value\":\"superadmin\",\"type\":2},{\"value\":null,\"type\":0}],\"time\":0.01706099510192871}]}', 'true', 'Transjurnal/DhhdAktiva/Controller', 'insert'),
(136, '2026-01-08 07:34:06', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd-sl/batchVerify', 'Melakukan perubahan data pada modul atau class App\\Modules\\Defaults\\TransJurnal\\Model_Mjournal', '{\"mjo_id\":\"9\",\"mjo_date\":\"2026-01-08\",\"mjo_code\":\"0003.1.01.2026\",\"mjo_ket\":\"Test Memo SL Nilai Decimal Update\",\"mjo_ajuan_untuk\":\"[TEST]\",\"mjo_ajuan_dari\":\"(0000) Transaksi\\/Laporan Gabungan\",\"jt_id\":\"1\",\"mjo_paymentdate\":null,\"mjo_ceque\":null,\"mjo_jbkid\":null,\"mjo_parentid\":null,\"is_jbk_multivoucher\":null,\"mjo_mperiod\":\"1\",\"mjo_yperiod\":\"2026\",\"mjo_ref\":\"0\",\"is_auto\":null,\"auto_kode\":null,\"is_aktiva\":\"1\",\"is_persediaan\":\"0\",\"is_ju_susut\":\"0\",\"is_air\":null,\"jr_jml_rek\":null,\"is_batal\":null,\"is_verify_voucher\":\"0\",\"jenis_ju_lainnya\":null,\"id_mjo_ju_pj\":null,\"id_um_pj\":null,\"id_kontrak\":null,\"file_lampiran\":null,\"is_closed\":\"0\",\"create_dt\":\"2026-01-08 14:25:10\",\"update_dt\":\"2026-01-08 14:33:35\",\"user_input\":\"superadmin\",\"jenis_jbk\":null,\"dph_id\":null,\"jenis_memo\":\"3\"}', '{\"mjo_id\":\"9\",\"mjo_date\":\"2026-01-08\",\"mjo_code\":\"0003.1.01.2026\",\"mjo_ket\":\"Test Memo SL Nilai Decimal Update\",\"mjo_ajuan_untuk\":\"[TEST]\",\"mjo_ajuan_dari\":\"(0000) Transaksi\\/Laporan Gabungan\",\"jt_id\":\"1\",\"mjo_paymentdate\":null,\"mjo_ceque\":null,\"mjo_jbkid\":null,\"mjo_parentid\":null,\"is_jbk_multivoucher\":null,\"mjo_mperiod\":\"1\",\"mjo_yperiod\":\"2026\",\"mjo_ref\":\"0\",\"is_auto\":null,\"auto_kode\":null,\"is_aktiva\":\"1\",\"is_persediaan\":\"0\",\"is_ju_susut\":\"0\",\"is_air\":null,\"jr_jml_rek\":null,\"is_batal\":null,\"is_verify_voucher\":1,\"jenis_ju_lainnya\":null,\"id_mjo_ju_pj\":null,\"id_um_pj\":null,\"id_kontrak\":null,\"file_lampiran\":null,\"is_closed\":\"0\",\"create_dt\":\"2026-01-08 14:25:10\",\"update_dt\":\"2026-01-08 14:33:35\",\"user_input\":\"superadmin\",\"jenis_jbk\":null,\"dph_id\":null,\"jenis_memo\":\"3\"}', 'true', 'Defaults\\TransJurnal\\DhhdSl', 'update'),
(137, '2026-01-08 07:34:11', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/dhhd-sl/batchUnverify', 'Melakukan perubahan data pada modul atau class App\\Modules\\Defaults\\TransJurnal\\Model_Mjournal', '{\"mjo_id\":\"9\",\"mjo_date\":\"2026-01-08\",\"mjo_code\":\"0003.1.01.2026\",\"mjo_ket\":\"Test Memo SL Nilai Decimal Update\",\"mjo_ajuan_untuk\":\"[TEST]\",\"mjo_ajuan_dari\":\"(0000) Transaksi\\/Laporan Gabungan\",\"jt_id\":\"1\",\"mjo_paymentdate\":null,\"mjo_ceque\":null,\"mjo_jbkid\":null,\"mjo_parentid\":null,\"is_jbk_multivoucher\":null,\"mjo_mperiod\":\"1\",\"mjo_yperiod\":\"2026\",\"mjo_ref\":\"0\",\"is_auto\":null,\"auto_kode\":null,\"is_aktiva\":\"1\",\"is_persediaan\":\"0\",\"is_ju_susut\":\"0\",\"is_air\":null,\"jr_jml_rek\":null,\"is_batal\":null,\"is_verify_voucher\":\"1\",\"jenis_ju_lainnya\":null,\"id_mjo_ju_pj\":null,\"id_um_pj\":null,\"id_kontrak\":null,\"file_lampiran\":null,\"is_closed\":\"0\",\"create_dt\":\"2026-01-08 14:25:10\",\"update_dt\":\"2026-01-08 14:34:06\",\"user_input\":\"superadmin\",\"jenis_jbk\":null,\"dph_id\":null,\"jenis_memo\":\"3\"}', '{\"mjo_id\":\"9\",\"mjo_date\":\"2026-01-08\",\"mjo_code\":\"0003.1.01.2026\",\"mjo_ket\":\"Test Memo SL Nilai Decimal Update\",\"mjo_ajuan_untuk\":\"[TEST]\",\"mjo_ajuan_dari\":\"(0000) Transaksi\\/Laporan Gabungan\",\"jt_id\":\"1\",\"mjo_paymentdate\":null,\"mjo_ceque\":null,\"mjo_jbkid\":null,\"mjo_parentid\":null,\"is_jbk_multivoucher\":null,\"mjo_mperiod\":\"1\",\"mjo_yperiod\":\"2026\",\"mjo_ref\":\"0\",\"is_auto\":null,\"auto_kode\":null,\"is_aktiva\":\"1\",\"is_persediaan\":\"0\",\"is_ju_susut\":\"0\",\"is_air\":null,\"jr_jml_rek\":null,\"is_batal\":null,\"is_verify_voucher\":0,\"jenis_ju_lainnya\":null,\"id_mjo_ju_pj\":null,\"id_um_pj\":null,\"id_kontrak\":null,\"file_lampiran\":null,\"is_closed\":\"0\",\"create_dt\":\"2026-01-08 14:25:10\",\"update_dt\":\"2026-01-08 14:34:06\",\"user_input\":\"superadmin\",\"jenis_jbk\":null,\"dph_id\":null,\"jenis_memo\":\"3\"}', 'true', 'Defaults\\TransJurnal\\DhhdSl', 'update'),
(138, '2026-01-08 08:19:21', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/jurnalrekair/saveDataOrUpdate', 'Melakukan penambahan data Jurnal Rekening Air Mjournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_jr_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":\"0001.2.01.2026\",\"type\":2},{\"value\":\"Test Penambahan Jurnal Rekening Air Decimal\",\"type\":2},{\"value\":null,\"type\":0},{\"value\":null,\"type\":0},{\"value\":2,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"1\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.07869100570678711}]}', 'true', 'Transjurnal/Jurnalrekair/Controller', 'insert'),
(139, '2026-01-08 08:19:22', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/jurnalrekair/saveDataOrUpdate', 'Melakukan penambahan data DHHD Djournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_jr_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":\"0001.2.01.2026\",\"type\":2},{\"value\":\"Test Penambahan Jurnal Rekening Air Decimal\",\"type\":2},{\"value\":null,\"type\":0},{\"value\":null,\"type\":0},{\"value\":2,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"1\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.07869100570678711},{\"sql\":\"CALL `transjurnal_dhhd_beforeinsert_djournal_v2`(?)\",\"bindings\":[{\"value\":\"10\",\"type\":2}],\"time\":0.016280174255371094},{\"sql\":\"CALL `transjurnal_dhhd_insert_djournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"10\",\"type\":2},{\"value\":\"13.01.21\",\"type\":2},{\"value\":2300000.33,\"type\":2},{\"value\":0,\"type\":1},{\"value\":2,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":null,\"type\":0}],\"time\":0.029793977737426758}]}', 'true', 'Transjurnal/Jurnalrekair/Controller', 'insert'),
(140, '2026-01-08 08:19:22', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/jurnalrekair/saveDataOrUpdate', 'Melakukan penambahan data DHHD Djournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_jr_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":\"0001.2.01.2026\",\"type\":2},{\"value\":\"Test Penambahan Jurnal Rekening Air Decimal\",\"type\":2},{\"value\":null,\"type\":0},{\"value\":null,\"type\":0},{\"value\":2,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"1\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.07869100570678711},{\"sql\":\"CALL `transjurnal_dhhd_beforeinsert_djournal_v2`(?)\",\"bindings\":[{\"value\":\"10\",\"type\":2}],\"time\":0.016280174255371094},{\"sql\":\"CALL `transjurnal_dhhd_insert_djournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"10\",\"type\":2},{\"value\":\"13.01.21\",\"type\":2},{\"value\":2300000.33,\"type\":2},{\"value\":0,\"type\":1},{\"value\":2,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":null,\"type\":0}],\"time\":0.029793977737426758},{\"sql\":\"CALL `transjurnal_dhhd_insert_djournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"10\",\"type\":2},{\"value\":\"81.01.10\",\"type\":2},{\"value\":0,\"type\":1},{\"value\":2300000.33,\"type\":2},{\"value\":2,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":null,\"type\":0}],\"time\":0.06918883323669434}]}', 'true', 'Transjurnal/Jurnalrekair/Controller', 'insert'),
(141, '2026-01-08 08:19:22', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/jurnalrekair/saveDataOrUpdate', 'Melakukan penambahan tanda tangan jurnal rek air', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_jr_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":\"0001.2.01.2026\",\"type\":2},{\"value\":\"Test Penambahan Jurnal Rekening Air Decimal\",\"type\":2},{\"value\":null,\"type\":0},{\"value\":null,\"type\":0},{\"value\":2,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"1\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.07869100570678711},{\"sql\":\"CALL `transjurnal_dhhd_beforeinsert_djournal_v2`(?)\",\"bindings\":[{\"value\":\"10\",\"type\":2}],\"time\":0.016280174255371094},{\"sql\":\"CALL `transjurnal_dhhd_insert_djournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"10\",\"type\":2},{\"value\":\"13.01.21\",\"type\":2},{\"value\":2300000.33,\"type\":2},{\"value\":0,\"type\":1},{\"value\":2,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":null,\"type\":0}],\"time\":0.029793977737426758},{\"sql\":\"CALL `transjurnal_dhhd_insert_djournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"10\",\"type\":2},{\"value\":\"81.01.10\",\"type\":2},{\"value\":0,\"type\":1},{\"value\":2300000.33,\"type\":2},{\"value\":2,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":null,\"type\":0}],\"time\":0.06918883323669434},{\"sql\":\"CALL `trans_jurnal_sp_insert_ttd_jurnal_from_lap`(?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"JRA\",\"type\":2},{\"value\":\"10\",\"type\":2},{\"value\":2,\"type\":1},{\"value\":\"superadmin\",\"type\":2},{\"value\":null,\"type\":0}],\"time\":0.04524707794189453}]}', 'true', 'Transjurnal/Jurnalrekair/Controller', 'insert'),
(142, '2026-01-08 08:58:07', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/jurnalreknonair/saveDataOrUpdate', 'Melakukan penambahan data Jurnal Rekening Non Air Mjournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_jr_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":\"0001.9.01.2026\",\"type\":2},{\"value\":\"Test Penambahan Rekening Non Air Jurnal Rek Non Air\",\"type\":2},{\"value\":null,\"type\":0},{\"value\":null,\"type\":0},{\"value\":2,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"1\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.09277200698852539}]}', 'true', 'Transjurnal/Jurnalreknonair/Controller', 'insert'),
(143, '2026-01-08 08:58:07', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/jurnalreknonair/saveDataOrUpdate', 'Melakukan penambahan data Jurnal Rekening Non Air Djournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_jr_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":\"0001.9.01.2026\",\"type\":2},{\"value\":\"Test Penambahan Rekening Non Air Jurnal Rek Non Air\",\"type\":2},{\"value\":null,\"type\":0},{\"value\":null,\"type\":0},{\"value\":2,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"1\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.09277200698852539},{\"sql\":\"CALL `transjurnal_dhhd_beforeinsert_djournal_v2`(?)\",\"bindings\":[{\"value\":\"11\",\"type\":2}],\"time\":0.016315937042236328},{\"sql\":\"CALL `transjurnal_dhhd_insert_djournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"11\",\"type\":2},{\"value\":\"13.02.10\",\"type\":2},{\"value\":3400000.33,\"type\":2},{\"value\":0,\"type\":1},{\"value\":2,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":null,\"type\":0}],\"time\":0.027323007583618164}]}', 'true', 'Transjurnal/Jurnalreknonair/Controller', 'insert'),
(144, '2026-01-08 08:58:08', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/jurnalreknonair/saveDataOrUpdate', 'Melakukan penambahan data Jurnal Rekening Non Air Djournal', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_jr_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":\"0001.9.01.2026\",\"type\":2},{\"value\":\"Test Penambahan Rekening Non Air Jurnal Rek Non Air\",\"type\":2},{\"value\":null,\"type\":0},{\"value\":null,\"type\":0},{\"value\":2,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"1\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.09277200698852539},{\"sql\":\"CALL `transjurnal_dhhd_beforeinsert_djournal_v2`(?)\",\"bindings\":[{\"value\":\"11\",\"type\":2}],\"time\":0.016315937042236328},{\"sql\":\"CALL `transjurnal_dhhd_insert_djournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"11\",\"type\":2},{\"value\":\"13.02.10\",\"type\":2},{\"value\":3400000.33,\"type\":2},{\"value\":0,\"type\":1},{\"value\":2,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":null,\"type\":0}],\"time\":0.027323007583618164},{\"sql\":\"CALL `transjurnal_dhhd_insert_djournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"11\",\"type\":2},{\"value\":\"81.02.90\",\"type\":2},{\"value\":0,\"type\":1},{\"value\":3400000.33,\"type\":2},{\"value\":2,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":null,\"type\":0}],\"time\":0.03849315643310547}]}', 'true', 'Transjurnal/Jurnalreknonair/Controller', 'insert'),
(145, '2026-01-08 08:58:08', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/jurnalreknonair/saveDataOrUpdate', 'Melakukan penambahan tanda tangan jurnal rek non air', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_jr_upsert_mjournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"\",\"type\":2},{\"value\":\"2026-01-08\",\"type\":2},{\"value\":\"0001.9.01.2026\",\"type\":2},{\"value\":\"Test Penambahan Rekening Non Air Jurnal Rek Non Air\",\"type\":2},{\"value\":null,\"type\":0},{\"value\":null,\"type\":0},{\"value\":2,\"type\":1},{\"value\":1,\"type\":1},{\"value\":\"2026\",\"type\":2},{\"value\":\"1\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.09277200698852539},{\"sql\":\"CALL `transjurnal_dhhd_beforeinsert_djournal_v2`(?)\",\"bindings\":[{\"value\":\"11\",\"type\":2}],\"time\":0.016315937042236328},{\"sql\":\"CALL `transjurnal_dhhd_insert_djournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"11\",\"type\":2},{\"value\":\"13.02.10\",\"type\":2},{\"value\":3400000.33,\"type\":2},{\"value\":0,\"type\":1},{\"value\":2,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":null,\"type\":0}],\"time\":0.027323007583618164},{\"sql\":\"CALL `transjurnal_dhhd_insert_djournal_v2`(?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"11\",\"type\":2},{\"value\":\"81.02.90\",\"type\":2},{\"value\":0,\"type\":1},{\"value\":3400000.33,\"type\":2},{\"value\":2,\"type\":1},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":null,\"type\":0}],\"time\":0.03849315643310547},{\"sql\":\"CALL `trans_jurnal_sp_insert_ttd_jurnal_from_lap`(?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"JRNA\",\"type\":2},{\"value\":\"11\",\"type\":2},{\"value\":2,\"type\":1},{\"value\":\"superadmin\",\"type\":2},{\"value\":null,\"type\":0}],\"time\":0.01760387420654297}]}', 'true', 'Transjurnal/Jurnalreknonair/Controller', 'insert'),
(146, '2026-01-09 03:13:53', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/jbksinglevoucher/prosesJbkSingle', 'Melakukan penambahan tanda tangan jbk single voucher', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_jbk_single_proses`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"5\",\"type\":2},{\"value\":\"2026-01-10\",\"type\":2},{\"value\":\"1\",\"type\":2},{\"value\":\"01\",\"type\":2},{\"value\":\"11.01.01.003\",\"type\":2},{\"value\":\"\",\"type\":2},{\"value\":\"3500000.75\",\"type\":2},{\"value\":\"\",\"type\":2},{\"value\":\"\",\"type\":2},{\"value\":\"\",\"type\":2},{\"value\":\"0\",\"type\":2},{\"value\":\"\",\"type\":2},{\"value\":\"\",\"type\":2},{\"value\":\"\",\"type\":2},{\"value\":\"0\",\"type\":2},{\"value\":\"\",\"type\":2},{\"value\":\"\",\"type\":2},{\"value\":\"\",\"type\":2},{\"value\":\"0\",\"type\":2},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.0171968936920166},{\"sql\":\"CALL `trans_jurnal_sp_insert_ttd_jurnal_from_lap`(?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"JBK\",\"type\":2},{\"value\":\"5\",\"type\":2},{\"value\":4,\"type\":1},{\"value\":\"superadmin\",\"type\":2},{\"value\":null,\"type\":0}],\"time\":0.0993490219116211}]}', 'true', 'Transjurnal/Jbksingle/Controller', 'insert'),
(147, '2026-01-09 03:13:59', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/jbksinglevoucher/prosesJbkSingle', 'Melakukan penambahan tanda tangan jbk single voucher', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_jbk_single_proses`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"5\",\"type\":2},{\"value\":\"2026-01-10\",\"type\":2},{\"value\":\"1\",\"type\":2},{\"value\":\"01\",\"type\":2},{\"value\":\"11.01.01.003\",\"type\":2},{\"value\":\"\",\"type\":2},{\"value\":\"3500000.75\",\"type\":2},{\"value\":\"\",\"type\":2},{\"value\":\"\",\"type\":2},{\"value\":\"\",\"type\":2},{\"value\":\"0\",\"type\":2},{\"value\":\"\",\"type\":2},{\"value\":\"\",\"type\":2},{\"value\":\"\",\"type\":2},{\"value\":\"0\",\"type\":2},{\"value\":\"\",\"type\":2},{\"value\":\"\",\"type\":2},{\"value\":\"\",\"type\":2},{\"value\":\"0\",\"type\":2},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.052169084548950195},{\"sql\":\"CALL `trans_jurnal_sp_insert_ttd_jurnal_from_lap`(?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"JBK\",\"type\":2},{\"value\":\"5\",\"type\":2},{\"value\":4,\"type\":1},{\"value\":\"superadmin\",\"type\":2},{\"value\":null,\"type\":0}],\"time\":0.020329952239990234}]}', 'true', 'Transjurnal/Jbksingle/Controller', 'insert'),
(148, '2026-01-09 03:17:19', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/jbksinglevoucher/prosesJbkSingle', 'Melakukan penambahan tanda tangan jbk single voucher', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_jbk_single_proses`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"5\",\"type\":2},{\"value\":\"2026-01-10\",\"type\":2},{\"value\":\"1\",\"type\":2},{\"value\":\"01\",\"type\":2},{\"value\":\"11.01.01.003\",\"type\":2},{\"value\":\"\",\"type\":2},{\"value\":\"3500000.75\",\"type\":2},{\"value\":\"\",\"type\":2},{\"value\":\"\",\"type\":2},{\"value\":\"\",\"type\":2},{\"value\":\"0\",\"type\":2},{\"value\":\"\",\"type\":2},{\"value\":\"\",\"type\":2},{\"value\":\"\",\"type\":2},{\"value\":\"0\",\"type\":2},{\"value\":\"\",\"type\":2},{\"value\":\"\",\"type\":2},{\"value\":\"\",\"type\":2},{\"value\":\"0\",\"type\":2},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.13517403602600098},{\"sql\":\"CALL `trans_jurnal_sp_insert_ttd_jurnal_from_lap`(?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"JBK\",\"type\":2},{\"value\":\"5\",\"type\":2},{\"value\":4,\"type\":1},{\"value\":\"superadmin\",\"type\":2},{\"value\":null,\"type\":0}],\"time\":0.017030000686645508}]}', 'true', 'Transjurnal/Jbksingle/Controller', 'insert'),
(149, '2026-01-09 03:17:26', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/jbksinglevoucher/batalBayarJbk', 'Melakukan pembatalan bayar jbk single voucher', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_jbk_delete_proses`(?)\",\"bindings\":[{\"value\":\"12\",\"type\":2}],\"time\":0.08256196975708008}]}', 'true', 'Transjurnal/Jbksingle/Controller', 'delete'),
(150, '2026-01-09 03:18:08', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/jbksinglevoucher/prosesJbkSingle', 'Melakukan penambahan tanda tangan jbk single voucher', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_jbk_single_proses`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"5\",\"type\":2},{\"value\":\"2026-01-10\",\"type\":2},{\"value\":\"1\",\"type\":2},{\"value\":\"01\",\"type\":2},{\"value\":\"11.01.01.003\",\"type\":2},{\"value\":\"\",\"type\":2},{\"value\":\"3500000.75\",\"type\":2},{\"value\":\"\",\"type\":2},{\"value\":\"\",\"type\":2},{\"value\":\"\",\"type\":2},{\"value\":\"0\",\"type\":2},{\"value\":\"\",\"type\":2},{\"value\":\"\",\"type\":2},{\"value\":\"\",\"type\":2},{\"value\":\"0\",\"type\":2},{\"value\":\"\",\"type\":2},{\"value\":\"\",\"type\":2},{\"value\":\"\",\"type\":2},{\"value\":\"0\",\"type\":2},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.15043282508850098},{\"sql\":\"CALL `trans_jurnal_sp_insert_ttd_jurnal_from_lap`(?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"JBK\",\"type\":2},{\"value\":\"5\",\"type\":2},{\"value\":4,\"type\":1},{\"value\":\"superadmin\",\"type\":2},{\"value\":null,\"type\":0}],\"time\":0.02802109718322754}]}', 'true', 'Transjurnal/Jbksingle/Controller', 'insert'),
(151, '2026-01-09 03:19:02', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/jbksinglevoucher/batalBayarJbk', 'Melakukan pembatalan bayar jbk single voucher', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_jbk_delete_proses`(?)\",\"bindings\":[{\"value\":\"13\",\"type\":2}],\"time\":0.10085678100585938}]}', 'true', 'Transjurnal/Jbksingle/Controller', 'delete'),
(152, '2026-01-09 03:25:25', '1', 'superadmin', 'Super Admin', '::1', 'localhost/dev-sragen-akuntansi/panel/transjurnal/jbksinglevoucher/prosesJbkSingle', 'Melakukan penambahan tanda tangan jbk single voucher', 'null', '{\"query\":[{\"sql\":\"CALL `transjurnal_jbk_single_proses`(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"5\",\"type\":2},{\"value\":\"2026-01-09\",\"type\":2},{\"value\":\"1\",\"type\":2},{\"value\":\"0000\",\"type\":2},{\"value\":\"11.01.11\",\"type\":2},{\"value\":\"\",\"type\":2},{\"value\":\"3500000.75\",\"type\":2},{\"value\":\"\",\"type\":2},{\"value\":\"\",\"type\":2},{\"value\":\"\",\"type\":2},{\"value\":\"0\",\"type\":2},{\"value\":\"\",\"type\":2},{\"value\":\"\",\"type\":2},{\"value\":\"\",\"type\":2},{\"value\":\"0\",\"type\":2},{\"value\":\"\",\"type\":2},{\"value\":\"\",\"type\":2},{\"value\":\"\",\"type\":2},{\"value\":\"0\",\"type\":2},{\"value\":\"01\",\"type\":2},{\"value\":\"2026\",\"type\":2},{\"value\":\"superadmin\",\"type\":2}],\"time\":0.1400918960571289},{\"sql\":\"CALL `trans_jurnal_sp_insert_ttd_jurnal_from_lap`(?, ?, ?, ?, ?)\",\"bindings\":[{\"value\":\"JBK\",\"type\":2},{\"value\":\"5\",\"type\":2},{\"value\":4,\"type\":1},{\"value\":\"superadmin\",\"type\":2},{\"value\":null,\"type\":0}],\"time\":0.018724918365478516}]}', 'true', 'Transjurnal/Jbksingle/Controller', 'insert');

-- --------------------------------------------------------

--
-- Struktur dari tabel `system_menu`
--

CREATE TABLE `system_menu` (
  `id_menu` int(11) NOT NULL,
  `nama_menu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jenis` smallint(6) DEFAULT NULL,
  `parent_menu` smallint(6) DEFAULT NULL,
  `link_menu` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `urutan` smallint(6) DEFAULT NULL,
  `is_aktif` tinyint(4) DEFAULT NULL,
  `is_tampil` tinyint(4) DEFAULT NULL,
  `keterangan` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon_uil` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data untuk tabel `system_menu`
--

INSERT INTO `system_menu` (`id_menu`, `nama_menu`, `jenis`, `parent_menu`, `link_menu`, `icon`, `urutan`, `is_aktif`, `is_tampil`, `keterangan`, `icon_uil`) VALUES
(1, 'Dashboard', 0, 0, 'dashboard', 'pie-chart', 1, 1, 1, NULL, 'chart-pie'),
(2, 'Master', 0, 0, NULL, '#', 2, 1, 1, NULL, 'database'),
(4, 'Pengaturan', 0, 0, NULL, '#', 99, 1, 1, NULL, 'cog'),
(5, 'Manajemen User', 0, 80, 'panel/setting/user', 'user', 1, 1, 1, NULL, NULL),
(6, 'Manajemen Peran', 0, 80, 'panel/setting/role', 'tag', 2, 1, 1, NULL, NULL),
(7, 'Otorisasi Menu', 0, 80, 'panel/setting/otoritas_menu', 'sliders', 8, 1, 1, NULL, NULL),
(8, 'Otorisasi Aksi', 0, 80, 'panel/setting/otoritas_aksi', 'lock', 9, 1, 1, NULL, NULL),
(9, 'Log Akses ', 0, 4, 'panel/setting/log_akses', 'clipboard', 10, 1, 1, NULL, NULL),
(10, 'Golongan', 1, 2, 'panel/refdata/golongan', 'database', 1, 1, 1, NULL, NULL),
(11, 'Kelompok', 1, 2, 'panel/refdata/kelompok', 'database', 2, 1, 1, NULL, NULL),
(15, 'Satuan Kerja', 1, 2, 'panel/refdata/satker', 'database', 8, 1, 1, NULL, NULL),
(63, 'Profile Aplikasi', 1, 4, 'panel/setting/profile-aplikasi', 'aperture', 0, 1, 1, NULL, NULL),
(66, 'TTD Laporan', 1, 79, 'panel/setting/pengaturan-ttd', 'archive', 3, 1, 1, NULL, NULL),
(67, 'TTD Jurnal', 1, 79, 'panel/setting/pengaturan-ttd-jurnal', 'archive', 4, 1, 1, NULL, NULL),
(79, 'Pengaturan TTD', 1, 4, NULL, '#', 3, 1, 1, NULL, NULL),
(80, 'Kontrol Akses', 1, 4, NULL, '#', 1, 1, 1, NULL, NULL),
(112, 'Harga Kamar', 1, 106, 'panel/hotel/master/harga', 'dollar-sign', 3, 1, 1, 'Master Harga Kamar', NULL),
(113, 'Pemesanan', 1, 107, 'panel/hotel/transaksi/pemesanan', 'calendar', 1, 1, 1, 'Pemesanan Kamar', NULL),
(114, 'Check-In', 1, 107, 'panel/hotel/transaksi/checkin', 'log-in', 2, 1, 1, 'Check-In Tamu', NULL),
(115, 'Check-Out', 1, 107, 'panel/hotel/transaksi/checkout', 'log-out', 3, 1, 1, 'Check-Out & Billing', NULL),
(116, 'Laporan Okupansi', 1, 108, 'panel/hotel/laporan/okupansi', 'bar-chart', 1, 1, 1, 'Laporan Okupansi Kamar', NULL),
(117, 'Laporan Pendapatan', 1, 108, 'panel/hotel/laporan/pendapatan', 'trending-up', 2, 1, 1, 'Laporan Keuangan Hotel', NULL),
(147, 'Master Data', 0, 0, NULL, 'database', 3, 1, 1, 'Master Data Hotel', 'database'),
(148, 'Master Jenis Kamar', 1, 147, 'panel/hotel/master/tipe-kamar', 'grid', 1, 1, 1, 'Master Jenis Kamar', 'grid'),
(149, 'Master Harga Kamar', 1, 147, 'panel/hotel/master/kamar', 'dollar-sign', 2, 1, 1, 'Master Harga Kamar', 'dollar-sign'),
(150, 'Master Pengguna', 1, 147, 'panel/hotel/master/user', 'users', 3, 1, 1, 'Master Pengguna', 'users'),
(151, 'Transaksi', 0, 0, NULL, 'credit-card', 4, 1, 1, 'Transaksi Hotel', 'credit-card'),
(152, 'Pemesanan Kamar', 1, 151, 'panel/hotel/transaksi/pemesanan', 'calendar', 1, 1, 1, 'Pemesanan Kamar', 'calendar'),
(153, 'Pembayaran Pemesanan', 1, 151, 'hotel/transaksi/pembayaran', 'credit-card', 2, 1, 1, 'Pembayaran Pemesanan', 'credit-card'),
(154, 'Laporan', 0, 0, NULL, 'file-text', 5, 1, 1, 'Laporan Hotel', 'file-text'),
(155, 'Daftar Tamu Per Hari', 1, 154, 'hotel/laporan/tamu-harian', 'calendar', 1, 1, 1, 'Daftar Tamu Per Hari', 'calendar'),
(156, 'Pemesanan Kamar Dalam Satu Bulan', 1, 154, 'hotel/laporan/pemesanan-bulanan', 'bar-chart', 2, 1, 1, 'Pemesanan Kamar Dalam Satu Bulan', 'bar-chart');

-- --------------------------------------------------------

--
-- Struktur dari tabel `system_menu_otorisasi`
--

CREATE TABLE `system_menu_otorisasi` (
  `id` int(11) NOT NULL,
  `id_menu` int(11) DEFAULT NULL,
  `id_role` int(11) DEFAULT NULL,
  `hak_input` tinyint(4) DEFAULT 1,
  `hak_ubah` tinyint(4) DEFAULT 1,
  `hak_hapus` tinyint(4) DEFAULT 1,
  `hak_cetak` tinyint(4) DEFAULT 1,
  `hak_verifikasi` tinyint(4) DEFAULT 1,
  `hak_unverifikasi` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data untuk tabel `system_menu_otorisasi`
--

INSERT INTO `system_menu_otorisasi` (`id`, `id_menu`, `id_role`, `hak_input`, `hak_ubah`, `hak_hapus`, `hak_cetak`, `hak_verifikasi`, `hak_unverifikasi`) VALUES
(1, 1, 1, 1, 1, 1, 1, 1, 1),
(2, 2, 1, 1, 1, 1, 1, 1, 1),
(4, 4, 1, 1, 1, 1, 1, 1, 1),
(5, 5, 1, 1, 1, 1, 1, 1, 1),
(6, 6, 1, 1, 1, 1, 1, 1, 1),
(7, 7, 1, 1, 1, 1, 1, 1, 1),
(8, 8, 1, 1, 1, 1, 1, 1, 1),
(9, 9, 1, 1, 1, 1, 1, 1, 1),
(10, 10, 1, 1, 1, 1, 1, 1, 1),
(11, 11, 1, 1, 1, 1, 1, 1, 1),
(63, 63, 1, 1, 1, 1, 1, 1, 1),
(66, 66, 1, 1, 1, 1, 1, 1, 1),
(67, 67, 1, 1, 1, 1, 1, 1, 1),
(79, 79, 1, 1, 1, 1, 1, 1, 1),
(80, 80, 1, 1, 1, 1, 1, 1, 1),
(106, 15, 1, 1, 1, 1, 1, 1, 1),
(115, 112, 1, 1, 1, 1, 1, 1, 1),
(116, 113, 1, 1, 1, 1, 1, 1, 1),
(117, 114, 1, 1, 1, 1, 1, 1, 1),
(118, 115, 1, 1, 1, 1, 1, 1, 1),
(119, 116, 1, 1, 1, 1, 1, 1, 1),
(120, 117, 1, 1, 1, 1, 1, 1, 1),
(130, 112, 9, 1, 1, 1, 1, 1, 1),
(131, 113, 9, 1, 1, 1, 1, 1, 1),
(132, 114, 9, 1, 1, 1, 1, 1, 1),
(133, 115, 9, 1, 1, 1, 1, 1, 1),
(134, 116, 9, 1, 1, 1, 1, 1, 1),
(135, 117, 9, 1, 1, 1, 1, 1, 1),
(145, 112, 10, 1, 1, 1, 1, 0, 0),
(146, 113, 10, 1, 1, 1, 1, 0, 0),
(147, 114, 10, 1, 1, 1, 1, 0, 0),
(148, 115, 10, 1, 1, 1, 1, 0, 0),
(149, 116, 10, 1, 1, 1, 1, 0, 0),
(150, 117, 10, 1, 1, 1, 1, 0, 0),
(253, 147, 9, 1, 1, 1, 1, 1, 1),
(254, 148, 9, 1, 1, 1, 1, 1, 1),
(255, 149, 9, 1, 1, 1, 1, 1, 1),
(256, 150, 9, 1, 1, 1, 1, 1, 1),
(257, 151, 9, 1, 1, 1, 1, 1, 1),
(258, 152, 9, 1, 1, 1, 1, 1, 1),
(259, 153, 9, 1, 1, 1, 1, 1, 1),
(260, 154, 9, 1, 1, 1, 1, 1, 1),
(261, 155, 9, 1, 1, 1, 1, 1, 1),
(262, 156, 9, 1, 1, 1, 1, 1, 1),
(268, 151, 10, 1, 1, 0, 1, 0, 0),
(269, 152, 10, 1, 1, 0, 1, 0, 0),
(270, 153, 10, 1, 1, 0, 1, 0, 0),
(271, 147, 1, 1, 1, 1, 1, 1, 1),
(272, 148, 1, 1, 1, 1, 1, 1, 1),
(273, 149, 1, 1, 1, 1, 1, 1, 1),
(274, 150, 1, 1, 1, 1, 1, 1, 1),
(275, 151, 1, 1, 1, 1, 1, 1, 1),
(276, 152, 1, 1, 1, 1, 1, 1, 1),
(277, 153, 1, 1, 1, 1, 1, 1, 1),
(278, 154, 1, 1, 1, 1, 1, 1, 1),
(279, 155, 1, 1, 1, 1, 1, 1, 1),
(280, 156, 1, 1, 1, 1, 1, 1, 1),
(286, 157, 1, 1, 1, 1, 1, 1, 1),
(287, 157, 2, 1, 1, 1, 1, 1, 1),
(288, 157, 9, 1, 1, 1, 1, 1, 1),
(289, 157, 10, 1, 1, 1, 1, 1, 1),
(290, 157, 11, 1, 1, 1, 1, 1, 1),
(291, 157, 12, 1, 1, 1, 1, 1, 1),
(292, 157, 13, 1, 1, 1, 1, 1, 1),
(293, 157, 14, 1, 1, 1, 1, 1, 1),
(294, 157, 15, 1, 1, 1, 1, 1, 1),
(295, 157, 16, 1, 1, 1, 1, 1, 1),
(296, 157, 17, 1, 1, 1, 1, 1, 1),
(297, 157, 18, 1, 1, 1, 1, 1, 1),
(298, 157, 19, 1, 1, 1, 1, 1, 1),
(299, 157, 20, 1, 1, 1, 1, 1, 1),
(301, 150, 2, 1, 1, 1, 1, 1, 1),
(302, 150, 10, 1, 1, 1, 1, 1, 1),
(303, 150, 11, 1, 1, 1, 1, 1, 1),
(304, 150, 12, 1, 1, 1, 1, 1, 1),
(305, 150, 13, 1, 1, 1, 1, 1, 1),
(306, 150, 14, 1, 1, 1, 1, 1, 1),
(307, 150, 15, 1, 1, 1, 1, 1, 1),
(308, 150, 16, 1, 1, 1, 1, 1, 1),
(309, 150, 17, 1, 1, 1, 1, 1, 1),
(310, 150, 18, 1, 1, 1, 1, 1, 1),
(311, 150, 19, 1, 1, 1, 1, 1, 1),
(312, 150, 20, 1, 1, 1, 1, 1, 1),
(316, 158, 1, 1, 1, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `system_pdam`
--

CREATE TABLE `system_pdam` (
  `id` int(11) NOT NULL,
  `direktori` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_pdam` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_aplikasi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_panjang_aplikasi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pemerintah_kota_kab` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kota_kab` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_telp_pdam` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `latitude` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `longitude` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_header` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_watermark` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image_footer` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `img_x` double DEFAULT NULL,
  `img_y` double DEFAULT NULL,
  `img_w` double DEFAULT NULL,
  `img_h` double DEFAULT NULL,
  `versi_header` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data untuk tabel `system_pdam`
--

INSERT INTO `system_pdam` (`id`, `direktori`, `nama_pdam`, `nama_aplikasi`, `nama_panjang_aplikasi`, `pemerintah_kota_kab`, `kota_kab`, `alamat`, `no_telp_pdam`, `email`, `latitude`, `longitude`, `image_logo`, `image_header`, `image_watermark`, `image_footer`, `img_x`, `img_y`, `img_w`, `img_h`, `versi_header`) VALUES
(13, 'aurora', 'AURORA TEKNO GLOBAL', 'APP NAME', 'APPLICATION FULL NAMES', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'logo-pdam-13.png', 'logo-pdam-13.png', NULL, NULL, 1, 0.5, 2, 2, '1');

-- --------------------------------------------------------

--
-- Struktur dari tabel `system_role`
--

CREATE TABLE `system_role` (
  `id` int(11) NOT NULL,
  `satker_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `dir_dashboard` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data untuk tabel `system_role`
--

INSERT INTO `system_role` (`id`, `satker_id`, `role`, `status`, `dir_dashboard`) VALUES
(1, '0102', 'Superadmin', 1, 'Readonly'),
(2, '0102', 'Admin', 1, 'Admin'),
(9, '0102', 'Admin Hotel', 1, 'Admin'),
(10, '0102', 'Resepsionis', 1, 'Readonly'),
(11, '0102', 'Admin Hotel', 1, 'Admin'),
(12, '0102', 'Resepsionis', 1, 'Readonly'),
(13, '0102', 'Admin Hotel', 1, 'Admin'),
(14, '0102', 'Resepsionis', 1, 'Readonly'),
(15, '0102', 'Admin Hotel', 1, 'Admin'),
(16, '0102', 'Resepsionis', 1, 'Resepsionis'),
(17, '0102', 'Admin Hotel', 1, 'Admin'),
(18, '0102', 'Resepsionis', 1, 'Resepsionis'),
(19, '0102', 'Admin Hotel', 1, 'Admin'),
(20, '0102', 'Resepsionis', 1, 'Resepsionis');

-- --------------------------------------------------------

--
-- Struktur dari tabel `system_user`
--

CREATE TABLE `system_user` (
  `id` int(11) NOT NULL,
  `satker_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nip` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pass_enc` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_role` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

--
-- Dumping data untuk tabel `system_user`
--

INSERT INTO `system_user` (`id`, `satker_id`, `username`, `nip`, `nama`, `password`, `pass_enc`, `id_role`, `state`) VALUES
(1, '0102', 'superadmin', '111', 'Super Admin', '$2y$10$dHM5ZWx6N2pETzRGL3ArRekYIUwLpcZPyVibZtT9usg96ts0/rIAC', 'J2Nmh1bbVtiz2HIJ6YtIyLeq62UBVvPdbq2IcSZgE71O0anwuRAdoC9EI6VyB8QrOxHv4DuzrhB6CP/RwWapnQ==', '1', 1),
(6, '0102', 'admin_hotel', '2001', 'Admin Hotel', '$2y$10$dHM5ZWx6N2pETzRGL3ArRekYIUwLpcZPyVibZtT9usg96ts0/rIAC', 'J2Nmh1bbVtiz2HIJ6YtIyLeq62UBVvPdbq2IcSZgE71O0anwuRAdoC9EI6VyB8QrOxHv4DuzrhB6CP/RwWapnQ==', '9', 1),
(7, '0102', 'resepsionis', '2002', 'Resepsionis Hotel', '$2y$10$dHM5ZWx6N2pETzRGL3ArRekYIUwLpcZPyVibZtT9usg96ts0/rIAC', 'J2Nmh1bbVtiz2HIJ6YtIyLeq62UBVvPdbq2IcSZgE71O0anwuRAdoC9EI6VyB8QrOxHv4DuzrhB6CP/RwWapnQ==', '10', 1);

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `system_vw_datatable_master_role`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `system_vw_datatable_master_role` (
`id` int(11)
,`role` varchar(255)
,`state` tinyint(4)
,`kode_satker` varchar(255)
,`nama_satker` varchar(100)
);

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `system_vw_datatable_master_user`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `system_vw_datatable_master_user` (
`id` int(11)
,`satker_id` varchar(255)
,`username` varchar(255)
,`nip` varchar(255)
,`nama` varchar(255)
,`PASSWORD` varchar(255)
,`pass_enc` varchar(255)
,`id_role` varchar(255)
,`role` varchar(255)
,`STATUS` tinyint(4)
,`state` int(11)
,`kode_satker` varchar(20)
,`nama_satker` varchar(100)
);

-- --------------------------------------------------------

--
-- Struktur dari tabel `ttd_jurnal`
--

CREATE TABLE `ttd_jurnal` (
  `id` int(11) NOT NULL,
  `jt_id` int(11) DEFAULT NULL,
  `id_data` int(11) DEFAULT NULL,
  `urutan_ke` int(11) DEFAULT NULL,
  `keterangan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jabatan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nup` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `create_dt` datetime DEFAULT NULL,
  `update_dt` datetime DEFAULT NULL,
  `user_input` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_ttd` smallint(6) DEFAULT NULL,
  `no_ref_anggaran_verifikasi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Struktur dari tabel `ttd_lap`
--

CREATE TABLE `ttd_lap` (
  `id` int(11) NOT NULL,
  `jenis_laporan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `urutan_ke` int(11) DEFAULT NULL,
  `keterangan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jabatan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nup` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `create_dt` datetime DEFAULT NULL,
  `update_dt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Struktur dari tabel `ttd_ref_pengesahan`
--

CREATE TABLE `ttd_ref_pengesahan` (
  `id` int(11) NOT NULL,
  `is_ttd_jurnal` smallint(6) DEFAULT NULL,
  `jenis_lap` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jt_id` int(11) DEFAULT NULL,
  `urutan_ke` int(11) DEFAULT NULL,
  `keterangan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jabatan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nup` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Struktur untuk view `system_vw_datatable_master_role`
--
DROP TABLE IF EXISTS `system_vw_datatable_master_role`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `system_vw_datatable_master_role`  AS SELECT `sr`.`id` AS `id`, `sr`.`role` AS `role`, `sr`.`status` AS `state`, `sr`.`satker_id` AS `kode_satker`, `ms`.`nama_satker` AS `nama_satker` FROM (`system_role` `sr` left join `master_satker` `ms` on(`sr`.`satker_id` = `ms`.`kode_satker`))  ;

-- --------------------------------------------------------

--
-- Struktur untuk view `system_vw_datatable_master_user`
--
DROP TABLE IF EXISTS `system_vw_datatable_master_user`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `system_vw_datatable_master_user`  AS SELECT `su`.`id` AS `id`, `su`.`satker_id` AS `satker_id`, `su`.`username` AS `username`, `su`.`nip` AS `nip`, `su`.`nama` AS `nama`, `su`.`password` AS `PASSWORD`, `su`.`pass_enc` AS `pass_enc`, `su`.`id_role` AS `id_role`, `sr`.`role` AS `role`, `sr`.`status` AS `STATUS`, `su`.`state` AS `state`, `ms`.`kode_satker` AS `kode_satker`, `ms`.`nama_satker` AS `nama_satker` FROM ((`system_user` `su` join `system_role` `sr` on(`su`.`id_role` = `sr`.`id`)) left join `master_satker` `ms` on(`su`.`satker_id` = `ms`.`kode_satker`)) WHERE 1 = 11  ;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `hotel_checkin_checkout`
--
ALTER TABLE `hotel_checkin_checkout`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_checkin_pemesanan` (`pemesanan_id`),
  ADD KEY `fk_checkin_petugas_in` (`petugas_checkin`),
  ADD KEY `fk_checkin_petugas_out` (`petugas_checkout`);

--
-- Indeks untuk tabel `hotel_laporan`
--
ALTER TABLE `hotel_laporan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_laporan_user` (`generated_by`);

--
-- Indeks untuk tabel `hotel_pembayaran`
--
ALTER TABLE `hotel_pembayaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pembayaran_pemesanan` (`pemesanan_id`);

--
-- Indeks untuk tabel `hotel_pemesanan`
--
ALTER TABLE `hotel_pemesanan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_booking` (`kode_booking`),
  ADD KEY `fk_pemesanan_tamu` (`tamu_id`),
  ADD KEY `fk_pemesanan_ruangan` (`ruangan_id`),
  ADD KEY `fk_pemesanan_user` (`user_id`);

--
-- Indeks untuk tabel `hotel_ruangan`
--
ALTER TABLE `hotel_ruangan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ruangan_tipe` (`tipe_ruangan_id`);

--
-- Indeks untuk tabel `hotel_setting`
--
ALTER TABLE `hotel_setting`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `hotel_tamu`
--
ALTER TABLE `hotel_tamu`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `no_identitas` (`no_identitas`);

--
-- Indeks untuk tabel `hotel_tipe_ruangan`
--
ALTER TABLE `hotel_tipe_ruangan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `masteraccount_golongan`
--
ALTER TABLE `masteraccount_golongan`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indeks untuk tabel `masteraccount_kelompok`
--
ALTER TABLE `masteraccount_kelompok`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indeks untuk tabel `master_satker`
--
ALTER TABLE `master_satker`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indeks untuk tabel `periodeaktif`
--
ALTER TABLE `periodeaktif`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indeks untuk tabel `reff_jurnal`
--
ALTER TABLE `reff_jurnal`
  ADD PRIMARY KEY (`jt_id`) USING BTREE;

--
-- Indeks untuk tabel `reff_ttd_lap`
--
ALTER TABLE `reff_ttd_lap`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indeks untuk tabel `system_log_akses`
--
ALTER TABLE `system_log_akses`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indeks untuk tabel `system_menu`
--
ALTER TABLE `system_menu`
  ADD PRIMARY KEY (`id_menu`) USING BTREE;

--
-- Indeks untuk tabel `system_menu_otorisasi`
--
ALTER TABLE `system_menu_otorisasi`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indeks untuk tabel `system_pdam`
--
ALTER TABLE `system_pdam`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indeks untuk tabel `system_role`
--
ALTER TABLE `system_role`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indeks untuk tabel `system_user`
--
ALTER TABLE `system_user`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indeks untuk tabel `ttd_jurnal`
--
ALTER TABLE `ttd_jurnal`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indeks untuk tabel `ttd_lap`
--
ALTER TABLE `ttd_lap`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- Indeks untuk tabel `ttd_ref_pengesahan`
--
ALTER TABLE `ttd_ref_pengesahan`
  ADD PRIMARY KEY (`id`) USING BTREE;

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `hotel_checkin_checkout`
--
ALTER TABLE `hotel_checkin_checkout`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `hotel_laporan`
--
ALTER TABLE `hotel_laporan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `hotel_pembayaran`
--
ALTER TABLE `hotel_pembayaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `hotel_pemesanan`
--
ALTER TABLE `hotel_pemesanan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `hotel_ruangan`
--
ALTER TABLE `hotel_ruangan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `hotel_setting`
--
ALTER TABLE `hotel_setting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `hotel_tamu`
--
ALTER TABLE `hotel_tamu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `hotel_tipe_ruangan`
--
ALTER TABLE `hotel_tipe_ruangan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `masteraccount_golongan`
--
ALTER TABLE `masteraccount_golongan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT untuk tabel `masteraccount_kelompok`
--
ALTER TABLE `masteraccount_kelompok`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;

--
-- AUTO_INCREMENT untuk tabel `master_satker`
--
ALTER TABLE `master_satker`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT untuk tabel `periodeaktif`
--
ALTER TABLE `periodeaktif`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT untuk tabel `reff_ttd_lap`
--
ALTER TABLE `reff_ttd_lap`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `system_log_akses`
--
ALTER TABLE `system_log_akses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=153;

--
-- AUTO_INCREMENT untuk tabel `system_menu`
--
ALTER TABLE `system_menu`
  MODIFY `id_menu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=159;

--
-- AUTO_INCREMENT untuk tabel `system_menu_otorisasi`
--
ALTER TABLE `system_menu_otorisasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=317;

--
-- AUTO_INCREMENT untuk tabel `system_pdam`
--
ALTER TABLE `system_pdam`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `system_role`
--
ALTER TABLE `system_role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT untuk tabel `system_user`
--
ALTER TABLE `system_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `ttd_jurnal`
--
ALTER TABLE `ttd_jurnal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `ttd_lap`
--
ALTER TABLE `ttd_lap`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `ttd_ref_pengesahan`
--
ALTER TABLE `ttd_ref_pengesahan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `hotel_checkin_checkout`
--
ALTER TABLE `hotel_checkin_checkout`
  ADD CONSTRAINT `fk_checkin_pemesanan` FOREIGN KEY (`pemesanan_id`) REFERENCES `hotel_pemesanan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_checkin_petugas_in` FOREIGN KEY (`petugas_checkin`) REFERENCES `system_user` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_checkin_petugas_out` FOREIGN KEY (`petugas_checkout`) REFERENCES `system_user` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `hotel_laporan`
--
ALTER TABLE `hotel_laporan`
  ADD CONSTRAINT `fk_laporan_user` FOREIGN KEY (`generated_by`) REFERENCES `system_user` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `hotel_pembayaran`
--
ALTER TABLE `hotel_pembayaran`
  ADD CONSTRAINT `fk_pembayaran_pemesanan` FOREIGN KEY (`pemesanan_id`) REFERENCES `hotel_pemesanan` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `hotel_pemesanan`
--
ALTER TABLE `hotel_pemesanan`
  ADD CONSTRAINT `fk_pemesanan_ruangan` FOREIGN KEY (`ruangan_id`) REFERENCES `hotel_ruangan` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pemesanan_tamu` FOREIGN KEY (`tamu_id`) REFERENCES `hotel_tamu` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pemesanan_user` FOREIGN KEY (`user_id`) REFERENCES `system_user` (`id`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `hotel_ruangan`
--
ALTER TABLE `hotel_ruangan`
  ADD CONSTRAINT `fk_ruangan_tipe` FOREIGN KEY (`tipe_ruangan_id`) REFERENCES `hotel_tipe_ruangan` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
