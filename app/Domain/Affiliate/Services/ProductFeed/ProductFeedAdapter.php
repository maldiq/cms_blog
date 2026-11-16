<?php

namespace App\Domain\Affiliate\Services\ProductFeed;

use App\Domain\Affiliate\Data\ProductCandidateData;

interface ProductFeedAdapter
{
    /**
     * Kunci sumber di config affiliate.product_sources.
     */
    public function key(): string;

    public function label(): string;

    /**
     * True bila seluruh kredensial wajib sudah terisi.
     */
    public function isConfigured(): bool;

    /**
     * Nama variabel lingkungan/config yang masih kosong.
     *
     * @return list<string>
     */
    public function missingConfiguration(): array;

    /**
     * Ambil kandidat produk dari sumber. Jumlah hasil bisa kurang dari $limit
     * bila sumber kehabisan data.
     *
     * @return list<ProductCandidateData>
     */
    public function fetch(int $limit = 50): array;
}
