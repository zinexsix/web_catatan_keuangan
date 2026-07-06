# Finance Manager (Aplikasi Catatan Keuangan Pribadi)

Finance Manager adalah aplikasi berbasis web yang dirancang untuk mempermudah pencatatan pemasukan dan pengeluaran keuangan pribadi. Aplikasi ini dibangun menggunakan standar MVC Laravel dengan desain modern, dinamis, responsif (mobile-first), dan aman.

## 🚀 Fitur Utama

1. **Dashboard Statistik Dinamis**:
   - **4 Summary Cards**: Total Pemasukan, Total Pengeluaran, Pendapatan Bersih, dan Jumlah Transaksi dengan warna unik, ikon representatif, serta perbandingan persentase dibanding bulan lalu.
   - **Interactive Charts**: Grafik Perbandingan Bulanan (Pemasukan vs Pengeluaran), Distribusi Pengeluaran per Kategori (Doughnut), dan Tren Pengeluaran Harian (Line) berbasis **Chart.js**.
   - **Realtime Budget Warning**: Banner peringatan interaktif jika pengeluaran hampir melewati (>80%) atau melebihi (100%+) anggaran bulanan.

2. **Autentikasi Multi-Metode & Google OAuth**:
   - Registrasi & Login menggunakan **Username**, **Email**, atau **Nomor HP**.
   - Login sosial media cepat dengan **Google OAuth (Laravel Socialite)**.
   - Dilengkapi sistem *Remember Me* dan *Reset Password* via Email.

3. **Manajemen Master Kategori (CRUD)**:
   - Pengaturan kategori Pemasukan dan Pengeluaran secara kustom.
   - Dilengkapi pemilihan warna representatif (Color Picker) dan ikon visual (**FontAwesome**).

4. **Pencatatan Transaksi Keuangan (CRUD)**:
   - Pencatatan detail transaksi: Tanggal, Judul, Kategori, Nominal, Metode Pembayaran (Cash, Transfer, E-Wallet, QRIS, dll), Deskripsi, dan Foto Bukti.
   - **Image Preview**: Menampilkan pratinjau gambar bukti transaksi sebelum diunggah.
   - **Filter & Sorting Dinamis**: Filter data berdasarkan Hari, Minggu, Bulan, Tahun, Rentang Tanggal Kustom, Jenis Transaksi, dan Kategori. Diurutkan berdasarkan Terbaru, Terlama, Nominal Terbesar, dan Nominal Terkecil.

5. **Target Tabungan / Keuangan**:
   - Tetapkan target/impian keuangan (Target Nominal, deadline) beserta progress bar dan status pencapaian (Tercapai / Aktif).
   - Menghitung sisa nominal terkumpul dan hari tersisa.

6. **Anggaran Bulanan (Budgeting)**:
   - Tetapkan batas belanja bulanan secara keseluruhan atau per kategori tertentu.
   - Integrasi notifikasi peringatan jika pengeluaran melebihi target anggaran.

7. **Tagihan Rutin**:
   - Catat pengeluaran rutin bulanan (WiFi, Listrik, Air, Netflix, dll) lengkap dengan tanggal jatuh tempo.
   - Sistem auto-overdue (otomatis terlambat jika tanggal lewat).
   - Tombol **Bayar Sekarang** yang otomatis mencatat pembayaran ke sistem pengeluaran.

8. **Laporan & Ekspor**:
   - Laporan keuangan terperinci berdasarkan filter rentang waktu.
   - Ekspor laporan ke berkas **PDF (DomPDF)**.
   - Ekspor laporan ke berkas **Excel / CSV**.
   - Cetak laporan langsung via browser (**Print Layout**).

9. **Keamanan & Preferensi Pengguna**:
   - Toggle **Dark / Light Mode** yang persisten.
   - Ekspor cadangan database (**Backup SQL**) dan impor cadangan data (**Restore SQL**) langsung dari panel pengaturan.
   - Proteksi XSS, CSRF, Mass Assignment, dan Auth Middleware Policy.

---

## 🛠️ Stack Teknologi

- **Framework**: Laravel 13 (PHP 8.4)
- **Database**: MySQL
- **CSS Framework**: Bootstrap 5 + Custom Premium glassmorphism design
- **JS Libraries**: Chart.js, SweetAlert2, DataTables, jQuery
- **Packages**:
  - `laravel/breeze` (Autentikasi dasar)
  - `laravel/socialite` (Integrasi login Google)
  - `barryvdh/laravel-dompdf` (Cetak PDF)

---

## 💻 Cara Instalasi

Ikuti langkah berikut untuk menjalankan project di lokal Anda:

### 1. Clone & Masuk Direktori Project
```bash
cd web_catatan_keuangan
```

### 2. Install Dependencies (Composer & NPM)
```bash
composer install
npm install
```

### 3. Konfigurasi Environment (`.env`)
Salin file `.env.example` ke `.env` jika belum ada:
```bash
cp .env.example .env
```
Sesuaikan konfigurasi database Anda di file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=web_catatan_keuangan
DB_USERNAME=root
DB_PASSWORD=
```
*Catatan: Pastikan Anda telah membuat database kosong bernama `web_catatan_keuangan` di MySQL Anda.*

### 4. Generate Application Key
```bash
php artisan key:generate
```

### 5. Jalankan Migrasi & Pengisian Data Contoh (Seeder)
```bash
php artisan migrate:fresh --seed
```

### 6. Hubungkan Folder Penyimpanan (Storage Link)
```bash
php artisan storage:link
```

### 7. Jalankan Kompilasi Asset Vite
```bash
npm run build
```
*(Untuk mode pengembangan aktif, gunakan perintah `npm run dev`)*

### 8. Jalankan Local Server
```bash
php artisan serve
```
Aplikasi sekarang dapat diakses melalui browser Anda di url: **`http://localhost:8000`**

---

## 🔑 Akun Demo Pengujian

Setelah menjalankan seeder di atas, Anda dapat masuk menggunakan akun demo berikut:
- **Email / Username**: `demo@finance.com` atau `demo`
- **Password**: `password`

Akun demo sudah berisi data contoh transaksi 6 bulan terakhir, kategori kustom, target tabungan, tagihan rutin, dan anggaran belanja bulanan agar Anda langsung dapat melihat dashboard yang kaya visual.
