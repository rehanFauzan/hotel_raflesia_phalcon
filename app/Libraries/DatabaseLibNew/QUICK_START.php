<?php

/**
 * QUICK START GUIDE - Contoh Penggunaan Library DatabaseSP
 * 
 * File ini berisi contoh-contoh penggunaan library DatabaseSP
 * yang bisa langsung di-copy paste ke controller Anda.
 */

use App\Libraries\DatabaseLibNew\SPHelper;

// ============================================
// CONTOH 1: SQL Server (seperti di Controller.php baris 235-239)
// ============================================

/**
 * SEBELUM (Raw SQL - Rawan SQL Injection):
 */
/*
$sql = "SET NOCOUNT ON; EXEC [akuntansi].sp_proses_periode 
        @vm   = " . $m_periode . ",
        @vy   = " . $y_periode . ";";
$resultInsert = $this->db->fetchOne($sql);
*/

/**
 * SESUDAH (Menggunakan Library - Aman & Mudah):
 */
// Di dalam Controller, tambahkan di bagian atas:
// use App\Libraries\DatabaseSP\SPHelper;

// Kemudian gunakan seperti ini:
$sp = SPHelper::fromPhalcon($this->db, 'sqlsrv');
$resultInsert = $sp->call('sp_proses_periode', [
    'vm' => $m_periode,
    'vy' => $y_periode
], 'akuntansi')->fetchOne();

// Atau lebih singkat:
$resultInsert = $sp->fetchOne('sp_proses_periode', [
    'vm' => $m_periode,
    'vy' => $y_periode
], 'akuntansi');


// ============================================
// CONTOH 2: MySQL
// ============================================

/**
 * SEBELUM:
 */
/*
$sql = "CALL sp_proses_periode('$m_periode', '$y_periode')";
$data = $this->db->fetchAll($sql);
*/

/**
 * SESUDAH:
 */
$sp = SPHelper::fromPhalcon($this->db, 'mysql');
$data = $sp->call('sp_proses_periode', [
    'm_periode' => $m_periode,
    'y_periode' => $y_periode
])->fetchAll();

// Atau lebih singkat:
$data = $sp->fetchAll('sp_proses_periode', [
    'm_periode' => $m_periode,
    'y_periode' => $y_periode
]);


// ============================================
// CONTOH 3: Multiple Parameters
// ============================================

$sp = SPHelper::fromPhalcon($this->db, 'sqlsrv');

// SQL Server dengan banyak parameter
$result = $sp->call('sp_complex_procedure', [
    'param1' => $value1,
    'param2' => $value2,
    'param3' => $value3,
    'param4' => $value4
], 'akuntansi')->fetchAll();


// ============================================
// CONTOH 4: Execute (untuk INSERT/UPDATE/DELETE)
// ============================================

$sp = SPHelper::fromPhalcon($this->db, 'mysql');

$affectedRows = $sp->execute('sp_update_record', [
    'id' => 123,
    'status' => 'active'
]);

if ($affectedRows > 0) {
    echo "Berhasil update $affectedRows record";
}


// ============================================
// CONTOH 5: Fetch Column (ambil satu kolom)
// ============================================

$sp = SPHelper::fromPhalcon($this->db, 'sqlsrv');

$total = $sp->fetchColumn('sp_get_total', [
    'periode' => 2024
], 'akuntansi', 0); // kolom pertama (index 0)


// ============================================
// CONTOH 6: Method Chaining dengan Fetch Mode
// ============================================

use App\Libraries\DatabaseLibNew\FetchMode;

$sp = SPHelper::fromPhalcon($this->db, 'sqlsrv');

// Fetch sebagai object
$result = $sp->call('sp_get_data', ['id' => 1], 'akuntansi')
    ->fetchOne(FetchMode::OBJ);

// Fetch sebagai array numerik
$result = $sp->call('sp_get_data', ['id' => 1], 'akuntansi')
    ->fetchOne(FetchMode::NUM);


// ============================================
// TIPS PENTING
// ============================================

/**
 * 1. Untuk SQL Server:
 *    - Parameter di SP harus menggunakan format @paramName
 *    - Bisa menggunakan schema seperti 'akuntansi'
 *    - Otomatis menambahkan SET NOCOUNT ON
 * 
 * 2. Untuk MySQL:
 *    - Parameter bisa menggunakan nama apapun
 *    - Tidak perlu schema
 *    - Menggunakan CALL statement
 * 
 * 3. Keamanan:
 *    - Semua parameter otomatis di-bind (aman dari SQL injection)
 *    - Tidak perlu escape string manual
 * 
 * 4. Performance:
 *    - Menggunakan prepared statements
 *    - Connection reuse (menggunakan connection yang sudah ada)
 */

