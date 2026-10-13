# Panduan Deploy

Kebutuhan server: **PHP 8.3+** (ekstensi `pdo_mysql`, `mbstring`, `gd`, `dom`, `fileinfo`), **MySQL 8 / MariaDB 10.6+**, Composer, dan Node.js 20+ (cukup untuk build asset).

## 1. Persiapan `.env`

Salin [.env.production.example](.env.production.example) menjadi `.env` di server dan isi:

- `APP_URL` (harus `https://...`), data `DB_*`, `GOOGLE_*`, dan `MAIL_*`.
- `APP_DEBUG=false`. Jangan pernah `true` di production.

## 2. Perintah deploy

```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build          # bisa juga dijalankan di komputer lokal, lalu upload folder public/build
php artisan key:generate         # hanya saat deploy pertama
php artisan migrate --force
php artisan storage:link         # agar foto struk bisa diakses
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Setelah mengubah `.env`, jalankan ulang `php artisan config:cache`.

## 3. Catatan per platform

### Shared hosting (cPanel / hPanel)
- Arahkan **document root** domain ke folder `public/`. Jika tidak bisa, upload proyek di luar `public_html`, lalu isi `public_html` dengan isi folder `public/` dan sesuaikan path di `index.php`.
- Pilih PHP 8.3 atau lebih baru di menu *PHP Version*.
- Jika `storage:link` tidak bisa dijalankan (tanpa SSH), buat symlink `public/storage` → `storage/app/public` lewat File Manager atau cron sekali jalan.

### VPS (Nginx)
- `root /path/proyek/public;` dan `try_files $uri $uri/ /index.php?$query_string;`
- Pastikan user web server bisa menulis ke `storage/` dan `bootstrap/cache/`.

### Railway / Render
- Tambahkan service MySQL dan isi `DB_*` dari kredensialnya.
- Set `LOG_CHANNEL=stderr`.
- Foto struk: pasang **volume** ke `storage/app/public`, atau set `RECEIPT_DISK=s3` dan gunakan Cloudflare R2 / S3.

### Vercel
Tidak disarankan. Filesystem read-only, sehingga wajib `RECEIPT_DISK=s3`, `LOG_CHANNEL=stderr`, MySQL eksternal, dan runtime PHP komunitas (`vercel-php`).

## 4. Backup & restore

Menu **Pengaturan → Cadangkan & Pemulihan Data** menghasilkan file `.json` yang hanya berisi data akun yang sedang login. File backup `.sql` lama dari versi sebelumnya tidak bisa di-restore lagi. Untuk backup seluruh database, gunakan fitur backup dari penyedia hosting atau `mysqldump`.
