<?php

namespace App\Domain\Affiliate\Console\Commands;

use App\Domain\Affiliate\Models\AffiliateDestination;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Throwable;

class CheckAffiliateDestinations extends Command
{
    protected $signature = 'affiliate:check-destinations {--limit=100 : Jumlah tujuan yang diperiksa}';

    protected $description = 'Periksa status HTTP tujuan link aktif. Hanya memberi peringatan, tidak mengubah status manual.';

    public function handle(): int
    {
        $limit = max(1, (int) $this->option('limit'));

        $destinations = AffiliateDestination::query()
            ->available()
            ->orderByRaw('last_checked_at IS NOT NULL, last_checked_at')
            ->limit($limit)
            ->get();

        if ($destinations->isEmpty()) {
            $this->info('Tidak ada tujuan aktif untuk diperiksa.');

            return self::SUCCESS;
        }

        $warnings = 0;

        foreach ($destinations as $destination) {
            try {
                $response = Http::timeout(8)
                    ->withHeaders(['User-Agent' => 'NainPicksLinkChecker/1.0'])
                    ->head($destination->url);

                $status = $response->status();
            } catch (Throwable) {
                $status = null;
            }

            $destination->forceFill([
                'last_checked_at' => now(),
                'last_check_status' => $status,
            ])->saveQuietly();

            $ok = $status !== null && $status < 400;

            if (! $ok) {
                $warnings++;
            }

            $this->line(sprintf(
                '%s #%d %s → %s',
                $ok ? '<info>OK</info>' : '<comment>WARN</comment>',
                $destination->getKey(),
                $destination->label,
                $status ?? 'tidak ada respons',
            ));
        }

        $this->newLine();
        $this->info(sprintf('Selesai. %d tujuan diperiksa, %d peringatan.', $destinations->count(), $warnings));

        return self::SUCCESS;
    }
}
