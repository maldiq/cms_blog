<?php

namespace App\Domain\Affiliate\Console\Commands;

use App\Domain\Affiliate\Models\AffiliateClick;
use Illuminate\Console\Command;

class PruneAffiliateClicks extends Command
{
    protected $signature = 'affiliate:prune-clicks {--days= : Umur log klik yang disimpan (default dari config)}';

    protected $description = 'Hapus log klik mentah yang lebih lama dari masa retensi.';

    public function handle(): int
    {
        $days = max(1, (int) ($this->option('days') ?: config('affiliate.click.retention_days', 90)));
        $cutoff = now()->subDays($days);

        $deleted = AffiliateClick::query()
            ->where('clicked_at', '<', $cutoff)
            ->delete();

        $this->info(sprintf('Menghapus %d klik lebih lama dari %s.', $deleted, $cutoff->toDateString()));

        return self::SUCCESS;
    }
}
