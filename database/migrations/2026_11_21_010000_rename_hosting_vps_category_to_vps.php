<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $categoryId = DB::table('affiliate_category_translations')
            ->where('slug', 'hosting-vps')
            ->value('affiliate_category_id');

        if ($categoryId === null) {
            return;
        }

        $slugTaken = DB::table('affiliate_category_translations')
            ->where('slug', 'vps')
            ->where('affiliate_category_id', '!=', $categoryId)
            ->exists();

        if ($slugTaken) {
            return;
        }

        DB::table('affiliate_category_translations')
            ->where('affiliate_category_id', $categoryId)
            ->update([
                'name' => 'VPS',
                'slug' => 'vps',
                'updated_at' => now(),
            ]);

        foreach (['id', 'en'] as $locale) {
            $translation = DB::table('affiliate_category_translations')
                ->where('affiliate_category_id', $categoryId)
                ->where('locale', $locale)
                ->first();

            if ($translation === null) {
                continue;
            }

            // Perbarui hanya konten lama yang masih menyebut harga per bulan,
            // agar suntingan admin berikutnya tidak tertimpa.
            if (! str_contains((string) $translation->intro, 'harga per bulan dalam rupiah')
                && ! str_contains((string) $translation->intro, 'monthly price in rupiah')) {
                continue;
            }

            $editorial = $locale === 'en' ? $this->englishEditorial() : $this->indonesianEditorial();

            DB::table('affiliate_category_translations')
                ->where('id', $translation->id)
                ->update([
                    'intro' => $editorial['intro'],
                    'meta_title' => $editorial['meta_title'],
                    'meta_description' => $editorial['meta_description'],
                    'faq' => json_encode($editorial['faq'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        // Perubahan nama kategori dan konten editorial tidak dikembalikan.
    }

    /**
     * @return array{intro: string, meta_title: string, meta_description: string, faq: list<array{question: string, answer: string}>}
     */
    private function indonesianEditorial(): array
    {
        return [
            'intro' => "Kategori VPS di NainPicks merangkum paket server virtual dari beberapa penyedia, termasuk promo tahunan RackNerd, paket bulanan Hostinger, dan Droplet DigitalOcean. Fokusnya bukan hanya harga termurah, tetapi resource yang benar-benar didapat: vCPU, RAM, penyimpanan, bandwidth, dan periode tagihan.\n\nPerhatikan periode tagihan. Sebagian paket ditagih per bulan, sementara promo RackNerd ditagih per tahun di muka, sehingga angka yang tampil adalah harga setahun. Untuk membandingkan biaya bulanan, bagi harga tahunan dengan 12.\n\nUntuk blog atau company profile, 1 vCPU dengan RAM 1–2 GB biasanya cukup. Naikkan RAM dan penyimpanan jika menjalankan panel kontrol, database, Docker, atau beberapa aplikasi sekaligus. Selalu cek ulang halaman penyedia sebelum membeli karena promo, stok lokasi, dan kurs dapat berubah.",
            'meta_title' => 'VPS Murah: Harga, Spesifikasi, dan Perbandingan Paket',
            'meta_description' => 'Bandingkan paket VPS dari RackNerd, Hostinger, dan DigitalOcean: vCPU, RAM, penyimpanan SSD, bandwidth, dan periode tagihan. Cek sebelum membeli.',
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
                    'question' => 'Kenapa ada harga tahunan dan harga bulanan?',
                    'answer' => 'Beberapa penyedia menagih per bulan, sementara promo Black Friday RackNerd menagih setahun di muka. Bagi harga tahunan dengan 12 untuk melihat ekuivalen bulanannya.',
                ],
                [
                    'question' => 'Kenapa harga promo VPS terlihat lebih murah?',
                    'answer' => 'Harga promo umumnya berlaku untuk pembayaran di muka, misalnya 12–24 bulan. Harga perpanjangan bisa lebih tinggi, jadi bandingkan total biaya, bukan hanya angka di bulan pertama.',
                ],
            ],
        ];
    }

    /**
     * @return array{intro: string, meta_title: string, meta_description: string, faq: list<array{question: string, answer: string}>}
     */
    private function englishEditorial(): array
    {
        return [
            'intro' => "The VPS category on NainPicks gathers virtual server plans from several providers, including RackNerd's annual promos, Hostinger's monthly plans, and DigitalOcean Droplets. The focus is not just the lowest sticker price, but the resources you actually get: vCPU, RAM, storage, bandwidth, and billing period.\n\nPay attention to the billing period. Some plans are billed monthly, while RackNerd promos require an annual upfront payment, so the figure shown is the yearly price. Divide the annual price by 12 to compare monthly costs.\n\nFor a blog or company profile, 1 vCPU with 1–2 GB of RAM is usually enough. Add more RAM and storage when running a control panel, database, Docker, or several apps at once. Always recheck the provider page before buying, as promos, location availability, and exchange rates change.",
            'meta_title' => 'Affordable VPS: Pricing, Specs, and Plan Comparison',
            'meta_description' => 'Compare VPS plans from RackNerd, Hostinger, and DigitalOcean: vCPU, RAM, SSD storage, bandwidth, and billing period. Check the specs before you buy.',
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
                    'question' => 'Why are some prices yearly and others monthly?',
                    'answer' => 'Some providers bill monthly, while RackNerd Black Friday promos require an annual upfront payment. Divide the annual price by 12 to see the monthly equivalent.',
                ],
                [
                    'question' => 'Why do VPS promo prices look so cheap?',
                    'answer' => 'Promo pricing usually requires an upfront payment, for example 12–24 months. Renewal pricing can be higher, so compare the total cost rather than the first-month figure.',
                ],
            ],
        ];
    }
};
