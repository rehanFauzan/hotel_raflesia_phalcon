# Cara Penggunaan Library DatabaseSP

Library ini memudahkan pemanggilan stored procedure untuk MySQL dan SQL Server.

## Instalasi & Setup

### 1. Menggunakan dengan Phalcon DB (Paling Mudah)

```php
use App\Libraries\DatabaseSP\SPHelper;

// Di dalam Controller
$sp = SPHelper::fromPhalcon($this->db, 'sqlsrv'); // atau 'mysql'

// Contoh untuk SQL Server
$result = $sp->call('sp_proses_periode', [
    'vm' => $m_periode,
    'vy' => $y_periode
], 'akuntansi')->fetchOne();

// Contoh untuk MySQL
$data = $sp->call('sp_proses_periode', [
    'm_periode' => $m_periode,
    'y_periode' => $y_periode
])->fetchAll();
```

### 2. Menggunakan dengan PDO

```php
use App\Libraries\DatabaseSP\SPHelper;

$pdo = new PDO('sqlsrv:Server=localhost;Database=mydb', 'user', 'pass');
$sp = new SPHelper($pdo, 'sqlsrv', 'akuntansi'); // driver dan schema

$result = $sp->call('sp_proses_periode', [
    'vm' => 12,
    'vy' => 2024
])->fetchOne();
```

## Contoh Penggunaan

### Contoh 1: SQL Server dengan Schema

```php
use App\Libraries\DatabaseSP\SPHelper;

// Setup
$sp = SPHelper::fromPhalcon($this->db, 'sqlsrv');

// Panggil SP dengan parameter
$m_periode = 12;
$y_periode = 2024;

$result = $sp->call('sp_proses_periode', [
    'vm' => $m_periode,
    'vy' => $y_periode
], 'akuntansi')->fetchOne();

// Atau lebih singkat
$result = $sp->fetchOne('sp_proses_periode', [
    'vm' => $m_periode,
    'vy' => $y_periode
], 'akuntansi');
```

### Contoh 2: MySQL

```php
use App\Libraries\DatabaseSP\SPHelper;

// Setup
$sp = SPHelper::fromPhalcon($this->db, 'mysql');

// Panggil SP
$data = $sp->call('sp_proses_periode', [
    'm_periode' => 12,
    'y_periode' => 2024
])->fetchAll();

// Atau lebih singkat
$data = $sp->fetchAll('sp_proses_periode', [
    'm_periode' => 12,
    'y_periode' => 2024
]);
```

### Contoh 3: Method Chaining

```php
use App\Libraries\DatabaseSP\SPHelper;

$sp = SPHelper::fromPhalcon($this->db, 'sqlsrv');

// Method chaining untuk fleksibilitas
$result = $sp->call('sp_proses_periode', [
    'vm' => $m_periode,
    'vy' => $y_periode
], 'akuntansi')
    ->fetchOne(FetchMode::ASSOC);
```

### Contoh 4: Execute (untuk INSERT/UPDATE/DELETE)

```php
use App\Libraries\DatabaseSP\SPHelper;

$sp = SPHelper::fromPhalcon($this->db, 'mysql');

$affectedRows = $sp->execute('sp_update_data', [
    'id' => 1,
    'name' => 'New Name'
]);
```

## Perbandingan dengan Kode Lama

### Sebelum (Raw SQL - SQL Server):
```php
$sql = "SET NOCOUNT ON; EXEC [akuntansi].sp_proses_periode 
        @vm = " . $m_periode . ",
        @vy = " . $y_periode . ";";
$result = $this->db->fetchOne($sql);
```

### Sesudah (Menggunakan Library):
```php
use App\Libraries\DatabaseSP\SPHelper;

$sp = SPHelper::fromPhalcon($this->db, 'sqlsrv');
$result = $sp->call('sp_proses_periode', [
    'vm' => $m_periode,
    'vy' => $y_periode
], 'akuntansi')->fetchOne();
```

### Sebelum (Raw SQL - MySQL):
```php
$sql = "CALL sp_proses_periode('$m_periode', '$y_periode')";
$data = $this->db->fetchAll($sql);
```

### Sesudah (Menggunakan Library):
```php
use App\Libraries\DatabaseSP\SPHelper;

$sp = SPHelper::fromPhalcon($this->db, 'mysql');
$data = $sp->call('sp_proses_periode', [
    'm_periode' => $m_periode,
    'y_periode' => $y_periode
])->fetchAll();
```

## Keuntungan Menggunakan Library

1. **Parameter Binding**: Otomatis menggunakan prepared statements (aman dari SQL injection)
2. **Cross-Database**: Satu API untuk MySQL dan SQL Server
3. **Type Safety**: Otomatis mendeteksi tipe data parameter
4. **Lebih Bersih**: Kode lebih mudah dibaca dan dirawat
5. **Fleksibel**: Support untuk berbagai fetch mode

## Catatan Penting

- Untuk SQL Server, parameter harus menggunakan format `@paramName` di stored procedure
- Untuk MySQL, parameter bisa menggunakan nama apapun (akan di-pass sebagai positional)
- Schema hanya diperlukan untuk SQL Server
- Library otomatis menambahkan `SET NOCOUNT ON` untuk SQL Server

