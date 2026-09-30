# Theme CryptoPro

Theme gelap untuk **konsultasi blockchain, Web3, dan kripto**, struktur sama dengan ServicePro (section, Theme Editor, menu). Inspirasi layout: [GeneratePress Crypto demo](https://sites.generatepress.com/crypto/).

## Aktivasi

1. Buka **Kelola → Theme Situs** (`/kelola/themes`).
2. Kartu **CryptoPro** → **Aktifkan** (pengaturan default otomatis terisi jika belum ada).
3. Sesuaikan teks/warna di **Theme Editor**.

Seed lengkap (theme aktif + sample data seperti ServicePro):

```bash
php artisan db:seed --class=CryptoProThemeSeeder
```

Opsional lanjutkan dengan `ServiceProBlogSeeder` jika perlu artikel demo.

## Branding default

| Token | Nilai |
|--------|--------|
| Primary | `#eab308` (gold) |
| Secondary | `#312e81` (indigo) |
| Accent | `#06b6d4` (cyan) |

## Konten default

Hero, about, process (Register → Fund wallet → Start trading), stats, FAQ, pricing, footer — semua **id/en** bertema blockchain (bukan lorem).

## Duplikasi / varian

Salin folder `resources/views/themes/cryptopro` → `resources/views/themes/{slug-baru}`, ubah `theme.json`, buat `{Slug}ThemeSettingsSeeder`, daftarkan di `App\Support\Theme\ThemeDefaultSettingsSeeder`.

Lihat juga: [THEME_SERVICEPRO.md](THEME_SERVICEPRO.md)
