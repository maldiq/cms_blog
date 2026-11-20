<?php

namespace Database\Seeders;

use App\Domain\Affiliate\Models\AffiliateCategory;
use App\Domain\Language\Models\Language;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AffiliateCategorySeeder extends Seeder
{
    /**
     * @var list<string>
     */
    private array $categories = [
        'Elektronik',
        'Rumah Tangga',
        'Dapur',
        'Kecantikan',
        'Fashion',
        'Otomotif',
        'Buku',
        'Hosting & VPS',
        'Software',
        'Umum',
    ];

    /**
     * @var array<string, string>
     */
    private array $englishNames = [
        'Elektronik' => 'Electronics',
        'Rumah Tangga' => 'Home & Living',
        'Dapur' => 'Kitchen',
        'Kecantikan' => 'Beauty',
        'Fashion' => 'Fashion',
        'Otomotif' => 'Automotive',
        'Buku' => 'Books',
        'Hosting & VPS' => 'Hosting & VPS',
        'Software' => 'Software',
        'Umum' => 'General',
    ];

    /**
     * Konten editorial kategori. Hanya diisi bila kolomnya masih kosong,
     * sehingga suntingan admin tidak tertimpa saat seeder dijalankan ulang.
     *
     * @var array<string, array<string, array{intro: string, meta_title: string, meta_description: string, faq: list<array{question: string, answer: string}>}>>
     */
    private array $editorial = [
        'Hosting & VPS' => [
            'id' => [
                'intro' => "Hosting & VPS di NainPicks merangkum paket server virtual yang bisa dipakai untuk website, aplikasi, dan alat otomasi. Fokusnya bukan hanya harga termurah, tetapi resource yang benar-benar didapat: vCPU, RAM, penyimpanan, bandwidth, dan periode tagihan.\n\nUntuk blog atau website company profile, paket 1 vCPU dengan RAM 1–2 GB biasanya sudah cukup. Naikkan RAM dan penyimpanan jika menjalankan panel kontrol, database, Docker, atau beberapa aplikasi sekaligus. Perhatikan juga periode tagihan: harga promo sering berlaku untuk pembayaran di muka 12–24 bulan, sementara harga perpanjangan bisa lebih tinggi.\n\nTabel di bawah merangkum spesifikasi paket yang ditautkan dan harga per bulan dalam rupiah. Selalu cek ulang halaman penyedia sebelum membeli karena promo, stok lokasi, dan kurs dapat berubah.",
                'meta_title' => 'Hosting & VPS: Harga, Spesifikasi, dan Perbandingan',
                'meta_description' => 'Bandingkan paket Hosting & VPS: vCPU, RAM, penyimpanan NVMe, bandwidth, dan harga per bulan. Cek spesifikasi sebelum membeli.',
                'faq' => [
                    [
                        'question' => 'Berapa RAM yang dibutuhkan untuk VPS website?',
                        'answer' => 'Untuk blog atau company profile, 1–2 GB RAM biasanya cukup. Gunakan 4 GB atau lebih jika menjalankan panel kontrol, database, Docker, atau beberapa aplikasi bersamaan.',
                    ],
                    [
                        'question' => 'Apakah harga VPS di halaman ini sudah termasuk pajak?',
                        'answer' => 'Belum tentu. Harga mengikuti halaman resmi penyedia dan bisa berbeda karena promo, kurs, pajak, serta biaya tambahan seperti backup atau IPv4 tambahan.',
                    ],
                    [
                        'question' => 'Apa bedanya VPS dan shared hosting?',
                        'answer' => 'Shared hosting berbagi resource dengan banyak pengguna dan lebih mudah dikelola. VPS memberi resource terpisah, akses root, dan kebebasan konfigurasi, tetapi server harus Anda urus sendiri.',
                    ],
                    [
                        'question' => 'Kenapa harga promo VPS terlihat lebih murah?',
                        'answer' => 'Harga promo umumnya berlaku untuk pembayaran di muka, misalnya 12–24 bulan. Harga perpanjangan bisa lebih tinggi, jadi bandingkan total biaya, bukan hanya angka di bulan pertama.',
                    ],
                ],
            ],
            'en' => [
                'intro' => "NainPicks gathers virtual server plans you can use for websites, applications, and automation tools. The focus is not just the lowest sticker price, but the resources you actually get: vCPU, RAM, storage, bandwidth, and billing period.\n\nFor a blog or company profile, 1 vCPU with 1–2 GB of RAM is usually enough. Add more RAM and storage when running a control panel, database, Docker, or several apps at once. Check the billing period too: promo prices often require 12–24 months upfront, while renewal prices can be higher.\n\nThe table below summarises the linked plans and their monthly price. Always recheck the provider page before buying, as promos, location availability, and exchange rates change.",
                'meta_title' => 'Hosting & VPS: Pricing, Specs, and Comparison',
                'meta_description' => 'Compare Hosting & VPS plans: vCPU, RAM, NVMe storage, bandwidth, and monthly pricing. Check the specs before you buy.',
                'faq' => [
                    [
                        'question' => 'How much RAM does a website VPS need?',
                        'answer' => 'A blog or company profile usually runs fine on 1–2 GB of RAM. Use 4 GB or more when running a control panel, database, Docker, or several apps together.',
                    ],
                    [
                        'question' => 'Do the VPS prices on this page include tax?',
                        'answer' => 'Not necessarily. Prices follow the provider page and may differ due to promos, exchange rates, taxes, and add-ons such as backups or extra IPv4 addresses.',
                    ],
                    [
                        'question' => 'What is the difference between a VPS and shared hosting?',
                        'answer' => 'Shared hosting pools resources across many users and is easier to manage. A VPS gives you dedicated resources, root access, and full configuration control, but you manage the server yourself.',
                    ],
                    [
                        'question' => 'Why do VPS promo prices look so cheap?',
                        'answer' => 'Promo pricing usually requires an upfront payment, for example 12–24 months. Renewal pricing can be higher, so compare the total cost rather than the first-month figure.',
                    ],
                ],
            ],
        ],
    ];

    public function run(): void
    {
        $locales = Language::getActive()->pluck('code')->all();

        if ($locales === []) {
            $locales = [config('app.locale', 'id')];
        }

        foreach ($this->categories as $index => $name) {
            $baseSlug = Str::slug($name);

            $category = AffiliateCategory::query()
                ->whereHas('translations', fn ($query) => $query->where('slug', $baseSlug))
                ->first();

            $category ??= AffiliateCategory::query()->create([
                'is_active' => true,
                'sort_order' => $index,
            ]);

            foreach ($locales as $locale) {
                if ($category->translations()->where('locale', $locale)->exists()) {
                    continue;
                }

                $localizedName = $locale === 'en'
                    ? ($this->englishNames[$name] ?? $name)
                    : $name;

                $category->translateOrNew($locale)->fill([
                    'name' => $localizedName,
                    'slug' => Str::slug($localizedName),
                ])->save();
            }

            $editorial = $this->editorial[$name] ?? null;

            if ($editorial === null) {
                continue;
            }

            foreach ($locales as $locale) {
                $translation = $category->translations()->where('locale', $locale)->first();
                $content = $editorial[$locale] ?? $editorial['id'];

                if ($translation === null) {
                    continue;
                }

                $dirty = false;

                foreach (['intro', 'meta_title', 'meta_description'] as $field) {
                    if (blank($translation->{$field}) && filled($content[$field] ?? null)) {
                        $translation->{$field} = $content[$field];
                        $dirty = true;
                    }
                }

                if (blank($translation->faq) && ! empty($content['faq'])) {
                    $translation->faq = $content['faq'];
                    $dirty = true;
                }

                if ($dirty) {
                    $translation->save();
                }
            }
        }
    }
}
