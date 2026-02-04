# DatabaseSP Library

Library PHP untuk memanggil Stored Procedure dengan mudah, mendukung MySQL dan SQL Server (MSSQL).

## 🚀 Fitur

- ✅ Support MySQL dan SQL Server
- ✅ Parameter binding otomatis (aman dari SQL injection)
- ✅ Auto-detect database driver
- ✅ Mudah digunakan dengan Phalcon DB
- ✅ Method chaining untuk fleksibilitas
- ✅ Support berbagai fetch mode

## 📦 Instalasi

Library sudah tersedia di `app/Libraries/DatabaseSP/`. Tidak perlu instalasi tambahan.

## 🎯 Quick Start

### Contoh 1: SQL Server (Paling Sering Digunakan)

```php
use App\Libraries\DatabaseSP\SPHelper;

// Di dalam Controller
$sp = SPHelper::fromPhalcon($this->db, 'sqlsrv');

// Panggil stored procedure
$result = $sp->call('sp_proses_periode', [
    'vm' => $m_periode,
    'vy' => $y_periode
], 'akuntansi')->fetchOne();

// Atau lebih singkat:
$result = $sp->fetchOne('sp_proses_periode', [
    'vm' => $m_periode,
    'vy' => $y_periode
], 'akuntansi');
```

### Contoh 2: MySQL

```php
use App\Libraries\DatabaseSP\SPHelper;

$sp = SPHelper::fromPhalcon($this->db, 'mysql');

$data = $sp->call('sp_proses_periode', [
    'm_periode' => $m_periode,
    'y_periode' => $y_periode
])->fetchAll();
```

## 📖 Dokumentasi Lengkap

### 1. Setup dengan Phalcon DB (Recommended)

```php
use App\Libraries\DatabaseSP\SPHelper;

// Auto-detect driver
$sp = SPHelper::fromPhalcon($this->db);

// Atau specify driver manual
$sp = SPHelper::fromPhalcon($this->db, 'sqlsrv'); // atau 'mysql'
```

### 2. Memanggil Stored Procedure

#### Method 1: Method Chaining
```php
$result = $sp->call('sp_name', ['param1' => $value1], 'schema')
    ->fetchOne();
```

#### Method 2: Direct Method
```php
$result = $sp->fetchOne('sp_name', ['param1' => $value1], 'schema');
$data = $sp->fetchAll('sp_name', ['param1' => $value1]);
$value = $sp->fetchColumn('sp_name', ['param1' => $value1], 'schema', 0);
```

### 3. Available Methods

#### `call(string $procedureName, array $params = [], ?string $schema = null): DatabaseSP`
Memanggil stored procedure dan return DatabaseSP instance untuk method chaining.

#### `fetchAll(string $procedureName, array $params = [], ?string $schema = null, ?int $fetchMode = null): array`
Memanggil SP dan return semua hasil.

#### `fetchOne(string $procedureName, array $params = [], ?string $schema = null, ?int $fetchMode = null): mixed`
Memanggil SP dan return satu baris hasil.

#### `fetchColumn(string $procedureName, array $params = [], ?string $schema = null, int $columnIndex = 0): mixed`
Memanggil SP dan return satu kolom dari baris pertama.

#### `execute(string $procedureName, array $params = [], ?string $schema = null): int`
Memanggil SP untuk operasi INSERT/UPDATE/DELETE, return jumlah affected rows.

## 🔄 Migrasi dari Raw SQL

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

## 💡 Tips & Best Practices

1. **Parameter Naming**:
   - SQL Server: Parameter di SP harus `@paramName`, di library cukup `'paramName' => value`
   - MySQL: Bisa menggunakan nama apapun

2. **Schema**:
   - SQL Server: Bisa specify schema seperti `'akuntansi'`
   - MySQL: Tidak perlu schema

3. **Security**:
   - Semua parameter otomatis di-bind (prepared statements)
   - Tidak perlu escape string manual
   - Aman dari SQL injection

4. **Performance**:
   - Menggunakan connection yang sudah ada (tidak membuat connection baru)
   - Prepared statements untuk performa optimal

## 📝 Contoh Lengkap

Lihat file `QUICK_START.php` untuk contoh-contoh penggunaan yang lebih lengkap.

## 🐛 Troubleshooting

### Error: "Cannot extract PDO connection from Phalcon adapter"
Pastikan Anda menggunakan Phalcon DB adapter yang valid. Library ini bekerja dengan Phalcon DB adapter yang memiliki method `getInternalHandler()`.

### Error: "Database connection not found"
Jika menggunakan `DB::call()`, pastikan sudah register connection dengan `DB::addConnection()`.

## 📄 License

MIT License

