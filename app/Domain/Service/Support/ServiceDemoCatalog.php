<?php

namespace App\Domain\Service\Support;

use App\Domain\Service\Models\Service;
use App\Domain\Service\Models\ServiceTranslation;
use Illuminate\Support\Collection;

class ServiceDemoCatalog
{
    /**
     * Definisi layanan demo (sesuai menu ServicePro).
     *
     * @return list<array{icon: string, sort_order: int, price_from: float|int, id: array<string, string>, en: array<string, string>}>
     */
    public static function definitions(): array
    {
        return [
            [
                'icon' => '💾',
                'sort_order' => 1,
                'price_from' => 1500000,
                'id' => [
                    'title' => 'Backup Website Data',
                    'slug' => 'backup-website-data',
                    'excerpt' => 'Backup otomatis harian dengan restore cepat saat insiden.',
                    'content' => '<p>Paket backup cloud dan on-premise dengan enkripsi, retensi fleksibel, dan laporan restore.</p>',
                ],
                'en' => [
                    'title' => 'Website Data Backup',
                    'slug' => 'website-data-backup',
                    'excerpt' => 'Daily automated backups with fast restore when incidents happen.',
                    'content' => '<p>Cloud and on-premise backup with encryption, flexible retention, and restore reports.</p>',
                ],
            ],
            [
                'icon' => '🖥️',
                'sort_order' => 2,
                'price_from' => 3500000,
                'id' => [
                    'title' => 'Jasa Setting VPS Profesional',
                    'slug' => 'jasa-setting-vps-profesional',
                    'excerpt' => 'Provisioning VPS, SSL, firewall, dan deploy aplikasi.',
                    'content' => '<p>Stack LEMP/Laravel, hardening, monitoring dasar, dan dokumentasi operasional.</p>',
                ],
                'en' => [
                    'title' => 'Professional VPS Setup',
                    'slug' => 'professional-vps-setup',
                    'excerpt' => 'VPS provisioning, SSL, firewall, and application deployment.',
                    'content' => '<p>LEMP/Laravel stack, hardening, basic monitoring, and runbooks.</p>',
                ],
            ],
            [
                'icon' => '🔧',
                'sort_order' => 3,
                'price_from' => 2500000,
                'id' => [
                    'title' => 'Jasa Maintenance WordPress',
                    'slug' => 'jasa-maintenance-wordpress',
                    'excerpt' => 'Update plugin, keamanan, backup, dan perbaikan bug.',
                    'content' => '<p>Paket bulanan WordPress dengan SLA response time dan laporan uptime.</p>',
                ],
                'en' => [
                    'title' => 'WordPress Maintenance',
                    'slug' => 'wordpress-maintenance',
                    'excerpt' => 'Plugin updates, security, backups, and bug fixes.',
                    'content' => '<p>Monthly WordPress care with response SLA and uptime reports.</p>',
                ],
            ],
            [
                'icon' => '🌐',
                'sort_order' => 4,
                'price_from' => 5000000,
                'id' => [
                    'title' => 'Jasa Pembuatan Website',
                    'slug' => 'jasa-pembuatan-website',
                    'excerpt' => 'Website company profile atau CMS dengan desain modern.',
                    'content' => '<p>Desain responsif, SEO dasar, dan panel admin siap pakai.</p>',
                ],
                'en' => [
                    'title' => 'Website Development',
                    'slug' => 'website-development',
                    'excerpt' => 'Company profile or CMS websites with modern design.',
                    'content' => '<p>Responsive design, basic SEO, and ready-to-use admin panel.</p>',
                ],
            ],
            [
                'icon' => '🛠️',
                'sort_order' => 5,
                'price_from' => 2000000,
                'id' => [
                    'title' => 'Jasa Perbaikan Website',
                    'slug' => 'jasa-perbaikan-website',
                    'excerpt' => 'Perbaikan error, optimasi performa, dan migrasi hosting.',
                    'content' => '<p>Audit cepat, perbaikan bug, dan peningkatan Core Web Vitals.</p>',
                ],
                'en' => [
                    'title' => 'Website Repair',
                    'slug' => 'website-repair',
                    'excerpt' => 'Bug fixes, performance tuning, and hosting migration.',
                    'content' => '<p>Quick audit, bug fixes, and Core Web Vitals improvements.</p>',
                ],
            ],
        ];
    }

    public static function seedIfMissing(): void
    {
        if (ServiceTranslation::query()->where('locale', 'id')->where('slug', 'backup-website-data')->exists()) {
            return;
        }

        if (ServiceTranslation::query()->where('locale', 'id')->where('slug', 'backup-recovery')->exists()) {
            return;
        }

        Service::withoutEvents(function (): void {
            foreach (self::definitions() as $definition) {
                $service = Service::query()->create([
                    'icon' => $definition['icon'],
                    'cover_id' => null,
                    'price_from' => $definition['price_from'],
                    'is_active' => true,
                    'sort_order' => $definition['sort_order'],
                ]);

                $service->translateOrNew('id')->fill($definition['id'])->save();
                $service->translateOrNew('en')->fill($definition['en'])->save();
            }
        });
    }

    /**
     * Layanan aktif yang punya konten untuk ditampilkan di menu.
     *
     * @return Collection<int, Service>
     */
    public static function forHeaderMenu(): Collection
    {
        return Service::query()
            ->active()
            ->ordered()
            ->with('translations')
            ->get()
            ->filter(function (Service $service): bool {
                $translation = $service->translate(app()->getLocale(), false)
                    ?: $service->translate('id', false);

                if ($translation === null) {
                    return false;
                }

                $content = trim(strip_tags((string) $translation->content));
                $excerpt = trim((string) $translation->excerpt);

                return $content !== '' || $excerpt !== '';
            })
            ->values();
    }
}
