<?php

namespace App\Domain\Affiliate\Console\Commands;

use App\Domain\Affiliate\Services\ProductFeed\ProductCandidateSynchronizer;
use App\Domain\Affiliate\Services\ProductFeed\ProductFeedException;
use App\Domain\Affiliate\Services\ProductFeed\ProductFeedManager;
use Illuminate\Console\Command;
use InvalidArgumentException;

class SyncAffiliateProductCandidates extends Command
{
    protected $signature = 'affiliate:sync-candidates
        {source=shopee : Kunci sumber di config affiliate.product_sources}
        {--limit=50 : Jumlah maksimum kandidat yang diambil}';

    protected $description = 'Tarik kandidat produk dari sumber resmi (default: Shopee Affiliate Open API).';

    public function handle(ProductFeedManager $manager, ProductCandidateSynchronizer $synchronizer): int
    {
        $source = (string) $this->argument('source');
        $limit = max(1, (int) $this->option('limit'));

        try {
            $adapter = $manager->get($source);
        } catch (InvalidArgumentException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        if (! $adapter->isConfigured()) {
            $this->warn(sprintf('Sumber "%s" belum bisa dipakai.', $adapter->label()));
            $this->line('Variabel yang belum diisi:');

            foreach ($adapter->missingConfiguration() as $variable) {
                $this->line('  - '.$variable);
            }

            $this->newLine();
            $this->line('Setelah kredensial tersedia, isi .env lalu jalankan perintah ini lagi.');
            $this->line('Tidak ada data yang diambil, jadi tidak ada kandidat yang ditambahkan.');

            return self::FAILURE;
        }

        try {
            $result = $synchronizer->sync($adapter, $limit);
        } catch (ProductFeedException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info(sprintf(
            'Selesai: %d kandidat baru, %d diperbarui, %d dilewati.',
            $result['created'],
            $result['updated'],
            $result['skipped'],
        ));

        $this->line('Buka /kelola/affiliate-candidates untuk meninjau dan mengimpor.');

        return self::SUCCESS;
    }
}
