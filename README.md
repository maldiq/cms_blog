# CMS Blog (Modular Laravel)

CMS modular bilingual (Indonesia / English) dengan admin **Filament v3** di `/kelola`, front-end theme **ServicePro**, blog, layanan, portfolio, SEO, newsletter, dan formulir kontak.

## Requirements

- PHP **8.3+**
- Composer 2
- MySQL **8+**
- Node.js **18+** (build asset Vite)
- Extension PHP: `pdo_mysql`, `mbstring`, `openssl`, `intl`, `gd` atau `imagick` (media)

## Install (development)

```bash
git clone <repo-url> cms_blog
cd cms_blog
cp .env.example .env
composer install
php artisan key:generate
```

Atur `.env` (database, `APP_URL`, mail driver `log` untuk lokal).

```bash
php artisan migrate --force
php artisan migrate --path=database/settings --force
php artisan db:seed
php artisan storage:link
npm install && npm run build
php artisan serve
```

Buka:

- Front: `http://localhost:8000/id` dan `/en`
- Kelola: `http://localhost:8000/kelola` — login **admin@admin.com** / **password** (ubah setelah seed)

Seed lengkap (reset DB):

```bash
php artisan migrate:fresh --seed
```

`DefaultContentSeeder` + `ServiceProThemeSeeder` + sample blog/komentar/newsletter/kontak.

## Deploy (ringkas)

1. Set `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL` HTTPS.
2. `composer install --no-dev --optimize-autoloader`
3. `php artisan migrate --force` dan `php artisan migrate --path=database/settings --force`
4. `php artisan db:seed --force` (atau seed selektif di staging)
5. `php artisan storage:link`, `php artisan config:cache`, `php artisan route:cache`, `php artisan view:cache`
6. `npm ci && npm run build`
7. Queue worker jika mail/notifikasi di-queue: `php artisan queue:work`
8. Scheduler: `* * * * * php /path/artisan schedule:run`

Pastikan folder `storage/` dan `bootstrap/cache/` writable.

## Dokumentasi theme

- Theme **ServicePro** (agency): **[docs/THEME_SERVICEPRO.md](docs/THEME_SERVICEPRO.md)**
- Theme **CryptoPro** (blockchain/kripto): **[docs/THEME_CRYPTOPRO.md](docs/THEME_CRYPTOPRO.md)**
- Theme **Purple Landing** (SaaS landing + hero form): **[docs/THEME_PURPLELANDING.md](docs/THEME_PURPLELANDING.md)**

## Testing

```bash
php artisan test
```

Feature tests theme: `tests/Feature/Theme/`.

## Arsitektur

- Domain: `app/Domain/{DomainName}/` (Models, Actions, Services, Policies, Filament)
- Kontrak antar domain: `app/Support/Contracts/`
- Permission: `{domain}.{resource}.{action}`

## License

MIT.
