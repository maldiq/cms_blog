# Theme ServicePro

Theme front-end untuk agency layanan web & IT. Konten homepage dan halaman tema diatur lewat **Theme Editor** di panel Kelola; entitas (layanan, portfolio, tim, blog) dikelola lewat resource Filament masing-masing.

## Install & aktivasi

1. Pastikan aplikasi sudah ter-install (`composer install`, `.env`, `php artisan key:generate`).
2. Jalankan migrasi dan seed:

```bash
php artisan migrate --force
php artisan migrate --path=database/settings --force
php artisan db:seed
```

Atau sekaligus:

```bash
php artisan migrate:fresh --seed
```

`ServiceProThemeSeeder` akan:

- Mendaftarkan theme **ServicePro** dan mengaktifkannya (`is_active = true`).
- Mengisi `theme_settings` untuk semua group (hero, about, services, portfolio, team, testimonial, process, pricing, faq, cta, stats, contact, newsletter, footer, branding, blog, about_page).
- Menyinkronkan menu **header**, **header-cta**, footer (**footer-company**, **footer-services**, **footer-legal**, **footer-bottom**).
- Mengisi sample: layanan (min. 6), portfolio (6), tim (4), testimonial (6), FAQ (6 item di settings), pricing (3 paket), blog (6 artikel published).

Impor layanan dari Solusiweb bersifat opsional; jika gagal, seeder menambah layanan lokal fallback.

## Mengedit konten via Kelola (Filament)

Panel admin: **`/kelola`** (redirect dari `/admin`).

| Area | Lokasi di Kelola |
|------|------------------|
| Teks section homepage (hero, about, FAQ, dll.) | **Theme → Theme Editor** (tab per group) |
| Layanan | **Services** |
| Portfolio | **Portfolios** |
| Tim | **Team** |
| Testimonial | **Testimonials** |
| Artikel blog | **Posts** |
| Menu header/footer | **Menus** (location: `header`, `header-cta`, `footer-company`, …) |
| Halaman CMS (privacy, dll.) | **Pages** |

Setelah mengubah layanan atau post published, jalankan **sync menu header** otomatis lewat `HeaderMenuFromContentService` (dipanggil saat impor/sync layanan; setelah seed blog juga di-sync).

## Logo & warna

1. Buka **Theme Editor → Branding**.
2. Pilih logo terang/gelap dan favicon lewat **Media Picker** (upload dulu di **Media** jika perlu).
3. Atur **primary**, **secondary**, **accent**, font heading/body.
4. Simpan. CSS variabel theme memakai nilai branding ini.

Logo situs global (meta/fallback) ada di **Settings → General** (`site_logo`).

## Menu

- **Header utama**: location `header` — biasanya di-generate dari layanan + link Blog jika ada post published.
- **CTA header**: location `header-cta` (tombol Hubungi Kami).
- **Kolom footer**: dikonfigurasi di Theme Editor → Footer (`columns` + `menu_location`).
- **Bar bawah footer**: location `footer-bottom`.

Edit item lewat **Menus** → pilih menu berdasarkan location.

## Bahasa

- Locale URL: **`/id/...`** dan **`/en/...`**.
- Switcher bahasa di header (link GET, bukan Livewire POST).
- Konten translatable: isi field **id** dan **en** di Theme Editor, Post, Service, dll.
- Default locale: **Settings → General → default_locale**.

## Troubleshooting

| Masalah | Solusi |
|---------|--------|
| Homepage kosong / theme default | Pastikan satu theme `is_active = true`; `php artisan cache:clear`; cek `themes` table. |
| Section tidak berubah setelah save | Clear cache theme: `Theme::clearCache()` atau `php artisan cache:clear`. |
| Menu Layanan kosong | Pastikan ada layanan `is_active`; jalankan sync header (save layanan atau seed ulang). |
| 419 / Livewire di locale | Gunakan build terbaru; locale switch harus link GET. |
| Settings Spatie error | Jalankan `php artisan migrate --path=database/settings --force`. |
| Gambar tidak muncul | Cek `storage:link`, permission folder, ID media valid. |

## Duplicate theme untuk varian baru

1. Salin folder `resources/views/themes/servicepro` → `resources/views/themes/{slug-baru}`.
2. Edit `theme.json` (name, slug, description).
3. Sesuaikan Blade/CSS/JS di folder baru.
4. Di Kelola → **Themes**, buat record theme baru dengan slug yang sama, atau seed manual:

```php
Theme::query()->create([
    'name' => 'My Variant',
    'slug' => 'my-variant',
    'is_active' => false,
]);
```

5. Salin/adaptasi seeder settings (`ServiceProThemeSettingsSeeder` sebagai template) untuk group key yang sama.
6. Aktifkan theme di **Theme Selector** / set `is_active`.

## Performa (Lighthouse)

Checklist manual setelah deploy:

- Gambar: lazy load + responsive (`media_responsive_image`).
- Cache section homepage (`HomeSectionCache`).
- Sitemap & meta SEO (`/sitemap.xml`, `@seo-meta`).
- Minify assets production: `npm run build`.
- Target: skor **> 90** Performance, Accessibility, Best Practices, SEO di halaman `/id` (ukur via Chrome DevTools → Lighthouse, mode production).
