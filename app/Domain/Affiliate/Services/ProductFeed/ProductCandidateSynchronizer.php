<?php

namespace App\Domain\Affiliate\Services\ProductFeed;

use App\Domain\Affiliate\Models\AffiliateProductCandidate;

class ProductCandidateSynchronizer
{
    /**
     * Upsert kandidat berdasarkan (platform_slug, external_id). Status kurasi,
     * catatan tren, dan tautan hasil impor tidak ditimpa saat sync ulang.
     *
     * @return array{created: int, updated: int, skipped: int}
     */
    public function sync(ProductFeedAdapter $adapter, int $limit = 50): array
    {
        $result = ['created' => 0, 'updated' => 0, 'skipped' => 0];

        foreach ($adapter->fetch($limit) as $candidate) {
            if (! $candidate->isValid()) {
                $result['skipped']++;

                continue;
            }

            $existing = AffiliateProductCandidate::query()
                ->where('platform_slug', $candidate->platformSlug)
                ->where('external_id', $candidate->externalId)
                ->first();

            $attributes = [
                ...$candidate->toSyncAttributes(),
                'source_type' => $adapter->key(),
                'source_name' => $adapter->label(),
                'fetched_at' => now(),
            ];

            if ($existing === null) {
                AffiliateProductCandidate::query()->create([
                    ...$attributes,
                    'external_id' => $candidate->externalId,
                    'platform_slug' => $candidate->platformSlug,
                    'status' => AffiliateProductCandidate::STATUS_NEW,
                ]);

                $result['created']++;

                continue;
            }

            unset($attributes['source_type'], $attributes['source_name']);

            $existing->fill($attributes)->save();
            $result['updated']++;
        }

        return $result;
    }
}
