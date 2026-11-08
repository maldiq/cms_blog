# Deploy ke VPS aaPanel

Panduan ini untuk aplikasi Laravel 12 pada domain `dev.naintin.com`. Sesuaikan path, alamat database, dan versi PHP jika konfigurasi VPS berbeda. Jangan kirim atau commit `.env`, password, private key, maupun token repository.

## 1. Siapkan VPS dan DNS

1. Arahkan DNS record `A` untuk `dev.naintin.com` ke IP publik VPS. Tambahkan record `A` untuk `www.dev.naintin.com` hanya jika subdomain `www` juga akan digunakan.
2. Di aaPanel, pasang Nginx, MySQL 8+, PHP 8.3, Composer 2, serta Node.js 18+ untuk proses build aset.
3. Aktifkan ekstensi PHP `pdo_mysql`, `mbstring`, `openssl`, `intl`, `fileinfo`, dan `gd` atau `imagick`.
4. Buat website `dev.naintin.com` dengan direktori proyek `/www/wwwroot/dev.naintin.com`. Atur **document root / run directory** website ke:

   ```text
   /www/wwwroot/dev.naintin.com/public
   ```

   Jangan arahkan document root ke folder proyek. Hanya folder `public` yang boleh diakses langsung oleh web.
5. Buat database MySQL dan user database dari aaPanel. Batasi akses database ke localhost; jangan buka port MySQL ke internet.
6. Setelah DNS aktif, pasang sertifikat SSL untuk domain melalui aaPanel dan aktifkan pengalihan HTTPS.

## 2. Ambil kode aplikasi

Buka Terminal aaPanel atau SSH sebagai user deployment. Jika repository privat, siapkan deploy key read-only di Git hosting dan verifikasi host SSH terlebih dahulu. Jangan menaruh token atau password di URL clone.

```bash
cd /www/wwwroot
git clone <URL-REPOSITORY> dev.naintin.com
cd /www/wwwroot/dev.naintin.com
```

Pastikan PHP CLI yang dipakai adalah PHP 8.3 yang sama dengan versi website aaPanel:

```bash
php -v
composer --version
node --version
npm --version
```

Jika `php -v` menunjukkan versi berbeda, pilih PHP CLI aaPanel (umumnya tersedia di `/www/server/php/83/bin`) sebelum melanjutkan, misalnya:

```bash
export PATH="/www/server/php/83/bin:$PATH"
php -v
```

## 3. Konfigurasi environment dan database

Salin `.env.example` menjadi `.env`, lalu isi nilainya secara langsung di server. Gunakan kredensial database yang dibuat di aaPanel; jangan gunakan nilai `.env` lokal Laragon.

```bash
cp .env.example .env
```

Setidaknya, pastikan pengaturan berikut sesuai:

```dotenv
APP_NAME="Nama Website"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://dev.naintin.com
APP_LOCALE=id
APP_FALLBACK_LOCALE=id

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database
DB_USERNAME=user_database
DB_PASSWORD="password_database"

FILESYSTEM_DISK=public
MEDIA_DISK=public
```

Atur juga konfigurasi SMTP yang benar bila aplikasi akan mengirim email. Jangan membiarkan `APP_DEBUG=true` pada server publik. Lindungi file environment dan pastikan tidak dapat diakses dari browser:

```bash
chown "$(whoami):www" .env
chmod 640 .env
```

Sesuaikan owner/group `www` dengan user/group PHP-FPM aaPanel yang dipakai situs.

Buat application key satu kali pada instalasi pertama:

```bash
php artisan key:generate
```

Jangan jalankan perintah itu lagi pada deployment berikutnya; mengganti `APP_KEY` dapat membuat data terenkripsi lama tidak dapat dibaca.

## 4. Install dependency dan siapkan Laravel

```bash
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan migrate --path=database/settings --force
php artisan storage:link
```

Pastikan user PHP-FPM aaPanel (sering bernama `www`) dapat menulis ke direktori runtime. Ganti `www:www` jika user/group website Anda berbeda:

```bash
chown -R www:www storage bootstrap/cache
chmod -R ug+rwX storage bootstrap/cache
```

### Bootstrap awal tanpa akun default

**Jangan jalankan `php artisan db:seed` pada instalasi produksi.** `DatabaseSeeder` memanggil `SuperAdminSeeder` yang membuat `admin@admin.com` dengan password `password`, juga memuat data demo dan pengaturan ServicePro.

Buat data role/bahasa yang diperlukan, kemudian buat akun admin dengan prompt password interaktif:

```bash
php artisan db:seed --class=LanguageSeeder --force
php artisan db:seed --class=RolePermissionSeeder --force
php artisan make:filament-user
```

Setelah itu, berikan role super-admin melalui Tinker:

```bash
php artisan tinker
```

Di prompt Tinker, ganti alamat email dengan email admin yang baru dibuat:

```php
$user = \App\Domain\User\Models\User::where('email', 'admin@domain-anda.com')->firstOrFail();
$user->assignRole('super-admin');
exit
```

Seeder contoh produk affiliate bersifat opsional. Jika dijalankan, produk contoh tetap nonaktif sampai admin mengaktifkannya dan mengganti URL contoh:

```bash
php artisan db:seed --class=AffiliateProductSeeder --force
```

## 5. Atur Nginx aaPanel dan cache

Di **Website → dev.naintin.com → URL Rewrite**, pilih preset **Laravel** jika tersedia. Jika tidak ada preset, pastikan aturan Nginx meneruskan URL yang tidak memiliki file ke `index.php`:

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

Pertahankan blok PHP/FastCGI yang dibuat aaPanel; jangan menggantinya dengan contoh konfigurasi PHP dari server lain. Pastikan root website tetap berakhir di `/public`.

Setelah environment dan database siap:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan view:cache
```

## 6. Cron dan queue (jika diperlukan)

Tambahkan cron melalui **aaPanel → Cron** agar Laravel Scheduler berjalan setiap menit:

```bash
cd /www/wwwroot/dev.naintin.com && /www/server/php/83/bin/php artisan schedule:run >> /dev/null 2>&1
```

Gunakan path website yang benar. Jika aplikasi menggunakan queued jobs, atur `QUEUE_CONNECTION=database`, pastikan migrasi tabel queue sudah dijalankan, lalu jalankan worker persisten melalui **Supervisor Manager** aaPanel:

```bash
cd /www/wwwroot/dev.naintin.com && /www/server/php/83/bin/php artisan queue:work database --sleep=3 --tries=3 --timeout=90
```

Jangan menjalankan worker sebagai proses terminal sementara; Supervisor perlu menghidupkannya kembali jika proses berhenti.

## 7. Verifikasi situs

1. Buka `https://dev.naintin.com/id` dan `https://dev.naintin.com/en`.
2. Buka `https://dev.naintin.com/kelola`, masuk menggunakan akun admin baru, lalu perbarui pengaturan situs.
3. Buat atau aktifkan produk affiliate untuk menampilkannya di beranda. Produk contoh dari seeder memang nonaktif.
4. Uji unggah media, halaman detail produk, tautan affiliate, HTTPS, dan pengiriman email jika SMTP digunakan.
5. Jika terjadi error, cek `storage/logs/laravel.log` dan log Nginx/PHP-FPM dari aaPanel. Jangan menampilkan log yang mengandung rahasia secara publik.

### Login menampilkan 405 pada `POST /kelola/login`

Halaman login Filament memakai Livewire. Jika JavaScript Livewire tidak termuat, form dapat terkirim sebagai POST biasa ke `/kelola/login`, sedangkan route tersebut memang hanya menerima GET/HEAD. Periksa pemuatan script Livewire di tab Network browser.

Jika script `/livewire/livewire.js` atau `/livewire/livewire.min.js` mengembalikan 404:

1. Pastikan `.env` produksi memakai `APP_ENV=production` dan `APP_DEBUG=false`. Jangan membiarkan halaman exception rinci terlihat pada situs publik.
2. Dari direktori aplikasi, bersihkan cache lama dan pastikan route script terdaftar:

   ```bash
   php artisan optimize:clear
   php artisan route:list --path=livewire
   ```

   Harus ada route `POST livewire/update` dan route script `GET livewire/livewire.min.js` saat `APP_DEBUG=false`. Jika route script tidak muncul, periksa instalasi dependency Livewire/Filament dengan `composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader`, kemudian ulangi pemeriksaan.
3. Bangun ulang cache setelah `.env` sudah benar:

   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

4. Muat ulang halaman login dan pastikan request script Livewire berhasil sebelum mencoba login kembali. Jika route tercantum di Laravel tetapi URL masih 404, periksa konfigurasi rewrite Nginx aaPanel: request `/livewire/...` harus diteruskan ke `public/index.php`, tidak diblokir atau diarahkan ke file statis.

## 8. Deployment pembaruan

Sebelum memperbarui aplikasi, buat backup database dan file media dari aaPanel. Kemudian jalankan dari direktori proyek:

```bash
cd /www/wwwroot/dev.naintin.com
php artisan down
git pull
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan migrate --path=database/settings --force
php artisan optimize:clear
php artisan config:cache
php artisan view:cache
php artisan up
```

Jika proses gagal setelah `php artisan down`, periksa penyebabnya lalu jalankan `php artisan up` setelah situs siap kembali. Jangan gunakan `migrate:fresh` pada database produksi.
