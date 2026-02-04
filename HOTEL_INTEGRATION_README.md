# INTEGRASI SISTEM HOTEL KE PDAM

## Deskripsi
Proyek ini mengintegrasikan sistem manajemen hotel ke dalam sistem akuntansi PDAM yang sudah ada. Sistem hotel menggunakan pola dan struktur yang sama dengan sistem PDAM untuk konsistensi dan kemudahan maintenance.

## Struktur Modul Hotel

```
app/Modules/Hotel/
├── Master/
│   ├── Kamar/          → Master kamar
│   ├── TipeKamar/      → Tipe kamar
│   └── Harga/          → Tarif kamar
├── Transaksi/
│   ├── Pemesanan/      → Booking/reservasi
│   ├── CheckIn/        → Check-in process
│   └── CheckOut/       → Check-out & billing
├── Laporan/
│   ├── Occupancy/      → Laporan okupansi
│   └── Pendapatan/     → Laporan keuangan
└── Setup/
    └── HotelSetting/   → Setting hotel
```

## Fitur yang Sudah Diimplementasi

### ✅ Master Data
- **Master Tipe Kamar**: CRUD lengkap untuk mengelola tipe kamar (Standard, Deluxe, Suite, dll)
- **Master Kamar**: CRUD lengkap untuk mengelola kamar dengan relasi ke tipe kamar
- **Master Harga**: Akan diimplementasi untuk mengelola tarif dinamis

### ✅ Transaksi
- **Pemesanan**: Sistem booking dengan validasi ketersediaan kamar
- **Check-In**: Akan diimplementasi untuk proses check-in tamu
- **Check-Out**: Akan diimplementasi untuk proses check-out dan billing

### 🔄 Laporan
- **Laporan Okupansi**: Akan diimplementasi
- **Laporan Pendapatan**: Akan diimplementasi

### 🔄 Setting
- **Setting Hotel**: Akan diimplementasi untuk konfigurasi hotel

## Cara Instalasi

### 1. Backup Database
```bash
mysqldump -u root -p base > backup_base_$(date +%Y%m%d_%H%M%S).sql
```

### 2. Jalankan Script Integrasi
```bash
mysql -u root -p base < hotel_integration.sql
```

### 3. Verifikasi Instalasi
- Login ke sistem: `http://localhost/hotel_reservasi_raflesia_bandung/panel`
- **User untuk testing**:
  - Superadmin: `superadmin` / `password_existing`
  - Admin Hotel: `admin_hotel` / `password123`
  - Resepsionis: `resepsionis` / `password123`
- Cek menu "Hotel Management" sudah muncul
- Test CRUD pada Master Tipe Kamar dan Master Kamar

## Database Schema

### Tabel Utama
- `hotel_tipe_ruangan`: Master tipe kamar
- `hotel_ruangan`: Master kamar
- `hotel_tamu`: Data tamu
- `hotel_pemesanan`: Data pemesanan
- `hotel_checkin_checkout`: Log check-in/out
- `hotel_pembayaran`: Data pembayaran
- `hotel_laporan`: Data laporan
- `hotel_setting`: Konfigurasi hotel

### Stored Procedures
- `hotel_cek_ketersediaan_kamar`: Cek ketersediaan kamar berdasarkan tanggal

### Triggers
- `hotel_after_pemesanan_update`: Auto update status kamar berdasarkan status pemesanan

## Pola Pengembangan

### Controller Pattern
```php
/**
 * @routeGroup('/hotel/master/tipe-kamar')
 * @middleware('RequireUser')
 */
class Controller extends BaseController
{
    // CRUD methods following PDAM pattern
}
```

### Model Pattern
```php
class Model extends BaseModel
{
    public function initialize()
    {
        $this->setSource('hotel_tipe_ruangan');
        // Relations setup
    }
}
```

### View Pattern
- Menggunakan Volt template engine
- Bootstrap 5 untuk styling
- DataTables untuk listing data
- Modal untuk form input/edit

### JavaScript Pattern
- jQuery untuk DOM manipulation
- AJAX untuk komunikasi dengan server
- Notyf untuk notifikasi
- jQuery Confirm untuk konfirmasi

## User & Role Hotel

Sistem sudah dilengkapi dengan user dan role khusus hotel:

### 👥 **User Hotel**
- **Admin Hotel**: `admin_hotel` / `password123` (full access)
- **Resepsionis**: `resepsionis` / `password123` (terbatas)

### 🔐 **Hak Akses Role**
- **Admin Hotel**: Full CRUD semua modul hotel
- **Resepsionis**: 
  - ✅ Transaksi (pemesanan, check-in, check-out)
  - ✅ Laporan (view only)
  - ❌ Master data (view only)
  - ❌ Setting hotel (no access)

## Alur Sistem Hotel

Berdasarkan dokumen yang diberikan:

1. **Pemesanan** → Tamu melakukan booking
2. **Konfirmasi** → Admin/Resepsionis konfirmasi booking
3. **Check-In** → Tamu datang dan check-in
4. **Check-Out** → Tamu check-out dan pembayaran
5. **Laporan** → Generate laporan okupansi dan pendapatan

## Pengembangan Selanjutnya

### Prioritas Tinggi
1. **Transaksi Check-In/Check-Out**: Implementasi proses check-in dan check-out
2. **Sistem Pembayaran**: Integrasi dengan sistem pembayaran
3. **Laporan Okupansi**: Laporan tingkat hunian kamar
4. **Laporan Pendapatan**: Laporan keuangan hotel

### Prioritas Sedang
1. **Master Harga Dinamis**: Sistem tarif berdasarkan musim/event
2. **Manajemen Fasilitas**: CRUD untuk fasilitas hotel
3. **Sistem Notifikasi**: Email/SMS untuk konfirmasi booking
4. **Dashboard Hotel**: Dashboard khusus untuk monitoring hotel

### Prioritas Rendah
1. **Integrasi Akuntansi**: Jurnal otomatis untuk transaksi hotel
2. **Sistem Loyalty**: Program loyalitas untuk tamu
3. **API Integration**: API untuk booking online
4. **Mobile App**: Aplikasi mobile untuk tamu

## Troubleshooting

### Error Database
- Pastikan MySQL service berjalan
- Cek koneksi database di `app/config/database.php`
- Verifikasi user database memiliki privilege yang cukup

### Error Menu Tidak Muncul
- Cek tabel `system_menu` apakah data menu hotel sudah ada
- Cek tabel `system_menu_otorisasi` untuk hak akses role
- Clear cache jika menggunakan caching

### Error 404 pada Route
- Pastikan struktur folder sesuai dengan namespace
- Cek annotation routing pada controller
- Restart web server jika perlu

## Kontak

Untuk pertanyaan atau bantuan pengembangan, silakan hubungi tim development.

---

**Note**: Sistem ini mengikuti pola dan standar yang sama dengan sistem PDAM untuk memastikan konsistensi dan kemudahan maintenance.