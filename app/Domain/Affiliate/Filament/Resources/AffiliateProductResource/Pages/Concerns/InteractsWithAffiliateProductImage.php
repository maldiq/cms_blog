<?php

namespace App\Domain\Affiliate\Filament\Resources\AffiliateProductResource\Pages\Concerns;

use App\Domain\Media\Models\Media;
use Livewire\Attributes\On;

trait InteractsWithAffiliateProductImage
{
    #[On('affiliate-image-picked')]
    public function setAffiliateProductImage(?int $mediaId): void
    {
        $this->authorizeAccess();

        if ($mediaId !== null) {
            abort_unless(
                Media::query()
                    ->images()
                    ->whereKey($mediaId)
                    ->exists(),
                404,
            );
        }

        $this->data = [
            ...($this->data ?? []),
            'image_id' => $mediaId,
        ];
    }
}
