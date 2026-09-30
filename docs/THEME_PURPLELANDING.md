# Theme Purple Landing

**One-page landing** bisnis/SaaS dengan hero ungu + form lead, navigasi anchor, dan section lengkap — inspirasi [Purple Landing Webflow Home 1](https://purple-landing-128.webflow.io/home-1).

## Aktivasi

1. **Kelola → Theme Situs** → **Purple Landing** → **Aktifkan**
2. Sesuaikan di **Theme Editor** (judul form hero: `hero.lead_form_title`, konten section, pricing, FAQ)

Seed lengkap:

```bash
php artisan db:seed --class=PurpleLandingThemeSeeder
```

## One-page

Saat theme ini aktif:

- Semua halaman publik utama (about, services, pricing, FAQ, contact, dll.) **redirect** ke `/{locale}#{fragment}`.
- **Artikel blog** (`/blog/{slug}`) tetap bisa dibuka.
- Header memakai link anchor: `#solutions`, `#about`, `#pricing`, `#reviews`, `#blog`, `#faq`, `#contact`.
- Layout memakai `scroll-smooth` untuk scroll halus.

| Fragment | Section |
|----------|---------|
| `#top` | Hero + form lead |
| `#solutions` | Layanan / fitur |
| `#process` | Langkah memulai |
| `#stats` | Statistik |
| `#about` | Tentang |
| `#pricing` | Paket harga |
| `#reviews` | Testimonial |
| `#blog` | Artikel terbaru |
| `#faq` | FAQ |
| `#contact` | Kontak + form lead |

Middleware: `RedirectThemeOnePage` · map route: `App\Support\Theme\ThemeOnePage`.

## Urutan homepage

hero → services → process → stats → about → pricing → testimonial → blog → FAQ → contact

(Tidak ada halaman terpisah CTA/newsletter — CTA mengarah ke `#contact`.)

## Branding default

Primary `#7c3aed`, secondary `#5b21b6`, hero 2 kolom dengan `HeroLeadForm` → `contact_submissions`.

Lihat juga: [THEME_SERVICEPRO.md](THEME_SERVICEPRO.md)
