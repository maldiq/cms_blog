<?php

namespace App\Domain\Setting\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class SiteBranding extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'site_brandings';

    /**
     * @var list<string>
     */
    protected $fillable = [];

    public static function instance(): self
    {
        return static::query()->firstOrCreate(['id' => 1]);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo')->singleFile();
        $this->addMediaCollection('favicon')->singleFile();
    }
}
