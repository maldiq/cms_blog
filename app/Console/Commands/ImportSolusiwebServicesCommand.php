<?php

namespace App\Console\Commands;

use App\Domain\Service\Services\SolusiwebServiceImportService;
use Illuminate\Console\Command;

class ImportSolusiwebServicesCommand extends Command
{
    protected $signature = 'services:import-solusiweb
                            {--url= : Base URL WordPress (default: config import.solusiweb_url)}';

    protected $description = 'Hapus layanan lokal dan impor dari halaman /layanan/ di solusiweb (WordPress REST API)';

    public function handle(SolusiwebServiceImportService $importService): int
    {
        $url = $this->option('url') ?: null;

        $this->info('Mengimpor layanan dari WordPress REST API...');

        try {
            $result = $importService->import($url);
        } catch (\Throwable $exception) {
            $this->error('Impor gagal: '.$exception->getMessage());

            return self::FAILURE;
        }

        $this->info("Selesai: {$result['imported']} layanan dari {$result['source']}");
        $this->comment('Menu header telah disinkronkan ulang.');

        return self::SUCCESS;
    }
}
