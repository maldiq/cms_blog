# Storefront Affiliate

Halaman depan situs sekarang memakai storefront toko affiliate: menu per kategori, slider promo, produk unggulan, dan produk baru. Semua konten diambil dari data affiliate yang sudah ada di panel admin.

Brand situs: **NainPicks** — tagline *"Global finds, picked for you."* — domain `naintin.my.id`. Nama situs, tagline, dan handle sosial diatur di **Kelola → Pengaturan** (default dari `AppSettingsSeeder`); judul bagian unggulan memakai kunci `affiliate_featured_section` (`Pilihan NainPicks` / `NainPicks Featured`).

## Yang tampil di halaman depan

1. **Header toko** — logo/nama situs dari Pengaturan, pencarian produk, menu utama, dan dropdown **Kategori** berisi seluruh kategori aktif beserta jumlah produk.
2. **Slider promo** — banner yang dikelola di `/kelola/affiliate-banners`.
3. **Belanja sesuai kebutuhan** — kartu kategori aktif. Klik kartu membuka halaman kategori `/kategori/{slug}`.
4. **Produk unggulan** — produk yang dicentang **Rekomendasi unggulan**. Bila belum ada yang dicentang, bagian ini otomatis menampilkan **Paling Dicari** berdasarkan jumlah klik.
5. **Produk baru** — 8 produk terbaru yang aktif.
6. **Blog** — 3 artikel terbit terbaru.
7. **Newsletter** dan footer dengan tautan kategori, halaman, serta disclosure affiliate.

## Mengelola slider

Buka **Kelola → Affiliate → Banner Slider** (`/kelola/affiliate-banners`).

| Field | Keterangan |
| --- | --- |
| Label kecil | Teks singkat di atas judul, misal `Promo 11.11`. |
| Judul slide | Judul besar pada slide. |
| Deskripsi singkat | Satu sampai dua baris penjelasan. |
| Warna latar | Gradien `Biru brand`, `Oranye promo`, `Hijau segar`, atau `Ungu premium`. |
| Label tombol | Default `Jelajahi Produk`. |
| Tautan tombol | Boleh kosong; tanpa tautan, tombol menuju halaman semua produk. |
| Gambar latar | Opsional. Bila diisi, gambar ditampilkan dengan overlay gelap. |
| Urutan | Urutan slide (bisa diatur ulang lewat tabel). |
| Tampilkan di slider | Nonaktifkan untuk menyembunyikan slide tanpa menghapusnya. |

Bila belum ada banner aktif, slider memakai slide cadangan dari `config/affiliate.php` (`hero.fallback_slides` / `hero.fallback_slides_en`). Seeder `AffiliateBannerSeeder` membuat tiga slide contoh (ID dan EN).

## Menandai produk unggulan

Di **Kelola → Affiliate → Produk Affiliate**, aktifkan **Rekomendasi unggulan** pada produk yang ingin ditampilkan di bagian Produk Unggulan. Produk juga harus **Tampilkan di website** agar muncul di storefront.

## Multi bahasa storefront

Semua konten storefront memakai terjemahan per locale, sama seperti halaman dan posting blog. Menambah bahasa aktif di **Kelola → Bahasa** otomatis menambah tab baru di form affiliasi. Data lama disalin ke setiap locale aktif saat migrasi, lalu bisa diedit per bahasa.

| Entitas | Field yang diterjemahkan | Tab admin |
| --- | --- | --- |
| Produk | nama, slug, deskripsi | Produk Affiliate |
| Kategori | nama, slug | Kategori Affiliate |
| Platform | nama (kode `slug` tetap sama) | Platform Affiliate |
| Banner slider | label, judul, deskripsi, label tombol | Banner Slider |

- Form admin menampilkan tab per bahasa (mis. **ID**, **EN**, dan bahasa lain yang aktif).
- Slug boleh berbeda per bahasa, misalnya `headphone-nirkabel` (ID) dan `wireless-headphone` (EN). Halaman produk dan kategori mengikuti slug locale aktif.
- Produk hanya tampil pada locale yang sudah punya terjemahan. Produk berbahasa Indonesia saja tidak akan muncul di `/en`.
- Tombol bahasa di header mengarah ke slug locale tujuan. Jika terjemahan belum ada, pengunjung diarahkan ke katalog locale tersebut.
- `hreflang` dan `sitemap.xml` memakai slug tiap locale.

## Mata uang per bahasa

Harga disimpan dalam **IDR** sebagai base currency, lalu ditampilkan sesuai mata uang locale:

- Indonesia: `Rp 250.000`
- Inggris: `$15.63` (estimasi konversi)

Atur simbol, jumlah desimal, pemisah ribuan/desimal, dan kurs di `config/affiliate.php` pada bagian `currencies`. Default kurs EN memakai 1 USD = 16.000 IDR (`rate` 0.0000625). Halaman detail menampilkan catatan “Perkiraan konversi” saat mata uang tampilan berbeda dari base currency.

## Dashboard admin

Dashboard `/kelola` menampilkan ringkasan yang relevan untuk toko affiliate:

- Kartu **Produk Aktif**, **Total Klik Affiliate**, **Produk Unggulan**, dan **Kategori Aktif**.
- Donut chart **Produk per Kategori**.
- Bar chart **Klik Affiliate per Platform**.
- Tabel **Produk Affiliate Terbaru** dengan gambar, kategori, platform, harga, klik, status, dan tombol edit.

Metrik revenue/stock tidak ditampilkan karena data pesanan dan stok tidak tersedia di katalog affiliate.

## Halaman katalog

- `/{locale}/products` — semua produk, dengan filter kategori, marketplace, rentang harga, pencarian, dan urutan (terbaru, paling diminati, harga, nama).
- `/{locale}/kategori/{slug}` untuk bahasa Indonesia dan `/{locale}/category/{slug}` untuk locale lain — katalog per kategori dengan URL ramah SEO. Kategori aktif yang memiliki produk juga masuk `sitemap.xml`.
- `/{locale}/products/{slug}` — detail produk, harga/diskon, deskripsi, CTA affiliate, produk serupa, dan schema Product.

Semua tautan beli memakai `rel="sponsored nofollow noopener"` dan melalui rute `/go/{slug}` agar klik tercatat.

## Kustomisasi cepat

- `config/affiliate.php` — slide cadangan, batas jumlah produk unggulan/baru, dan warna badge marketplace.
- `lang/id/messages.php` dan `lang/en/messages.php` — label antarmuka storefront (`affiliate_*`).
- `resources/views/products/home.blade.php` — susunan bagian halaman depan.
- `resources/views/products/index.blade.php` — katalog dan filter.
- `resources/views/products/show.blade.php` — detail produk.
- `resources/views/components/storefront/` — header, footer, ikon kategori, judul bagian.
- `resources/views/components/product-card.blade.php` — kartu produk.

## Perintah terkait

```bash
php artisan config:clear                   # agar config/affiliate.php terbaca
php artisan migrate                        # tabel banner/terjemahan affiliate
php artisan db:seed --class=AffiliateBannerSeeder
php artisan db:seed --class=RolePermissionSeeder   # izin affiliate.banner.*
npm run build
```

Hak akses banner memakai permission `affiliate.banner.{viewAny,create,update,delete}`. Role `admin` dan `editor` mendapatkannya melalui `RolePermissionSeeder`.
