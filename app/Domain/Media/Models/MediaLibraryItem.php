<?php

namespace App\Domain\Media\Models;

use App\Domain\Media\Concerns\RegistersDefaultMediaConversions;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaLibraryItem extends Model implements HasMedia
{
    use InteractsWithMedia;
    use RegistersDefaultMediaConversions;

    protected $table = 'media_library_items';

    /**
     * @var list<string>
     */
    protected $fillable = [];

    public static function library(): self
    {
        $item = static::query()->find(1);

        if ($item !== null) {
            return $item;
        }

        $item = new static;
        $item->id = 1;
        $item->save();

        return $item;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('library');
        $this->addMediaCollection('default');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->registerConfiguredImageConversions($media);
    }
}
