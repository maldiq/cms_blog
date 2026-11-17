<?php

namespace App\Domain\Affiliate\Console\Commands;

use App\Domain\Affiliate\Services\AffiliateProductLinkImporter;
use Illuminate\Console\Command;
use InvalidArgumentException;

class ImportAffiliateProductLinks extends Command
{
    protected $signature = 'affiliate:import-product-links
        {file : Path ke file CSV}
        {--download-images : Unduh gambar dari kolom image_url}';

    protected $description = 'Impor massal tautan affiliate dan gambar produk dari CSV.';

    public function handle(AffiliateProductLinkImporter $importer): int
    {
        try {
            $result = $importer->importFile(
                (string) $this->argument('file'),
                (bool) $this->option('download-images'),
            );
        } catch (InvalidArgumentException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info(sprintf(
            'Selesai: %d link diperbarui, %d gambar diunduh, %d baris dilewati.',
            $result['updated'],
            $result['images'],
            $result['skipped'],
        ));

        foreach (array_slice($result['errors'], 0, 20) as $error) {
            $this->warn(' - '.$error);
        }

        if (count($result['errors']) > 20) {
            $this->warn(' ... dan '.(count($result['errors']) - 20).' masalah lain.');
        }

        return $result['errors'] === [] ? self::SUCCESS : self::FAILURE;
    }
}
