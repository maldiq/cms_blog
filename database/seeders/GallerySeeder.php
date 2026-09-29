<?php

namespace Database\Seeders;

use App\Domain\Gallery\Album\Models\Album;
use App\Domain\Gallery\Album\Models\AlbumTranslation;
use App\Domain\Media\Services\MediaUploadService;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class GallerySeeder extends Seeder
{
    /**
     * @var list<array{id: array{title: string, slug: string, description: string}, en: array{title: string, slug: string, description: string}, photos: int}>
     */
    private array $albumDefinitions = [
        [
            'id' => [
                'title' => 'Pemandangan Alam',
                'slug' => 'pemandangan-alam',
                'description' => 'Koleksi foto alam dan pegunungan.',
            ],
            'en' => [
                'title' => 'Nature Landscapes',
                'slug' => 'nature-landscapes',
                'description' => 'Collection of nature and mountain photos.',
            ],
            'photos' => 8,
        ],
        [
            'id' => [
                'title' => 'Acara Perusahaan',
                'slug' => 'acara-perusahaan',
                'description' => 'Dokumentasi kegiatan internal dan gathering.',
            ],
            'en' => [
                'title' => 'Company Events',
                'slug' => 'company-events',
                'description' => 'Internal activities and team gatherings.',
            ],
            'photos' => 6,
        ],
        [
            'id' => [
                'title' => 'Produk & Studio',
                'slug' => 'produk-studio',
                'description' => 'Sesi foto produk dengan lighting studio.',
            ],
            'en' => [
                'title' => 'Product Studio',
                'slug' => 'product-studio',
                'description' => 'Product photography with studio lighting.',
            ],
            'photos' => 10,
        ],
    ];

    public function run(): void
    {
        if (AlbumTranslation::query()->where('locale', 'id')->where('slug', 'pemandangan-alam')->exists()) {
            return;
        }

        $uploadService = app(MediaUploadService::class);
        $sortOrder = 0;

        foreach ($this->albumDefinitions as $definition) {
            $album = Album::query()->create([
                'is_active' => true,
                'sort_order' => $sortOrder++,
            ]);

            $album->translateOrNew('id')->fill($definition['id'])->save();
            $album->translateOrNew('en')->fill($definition['en'])->save();

            $mediaIds = [];
            $photoCount = $definition['photos'];

            for ($i = 1; $i <= $photoCount; $i++) {
                $mediaIds[] = $this->seedImage($uploadService, "album-{$album->id}-{$i}.jpg");
            }

            $album->update(['cover_id' => $mediaIds[0]]);

            $attach = [];
            foreach ($mediaIds as $index => $mediaId) {
                $attach[$mediaId] = [
                    'caption' => 'Foto #' . ($index + 1),
                    'sort_order' => $index,
                ];
            }

            $album->media()->attach($attach);
        }
    }

    private function seedImage(MediaUploadService $uploadService, string $filename): int
    {
        $path = Storage::disk('public')->path('seed/' . $filename);
        $directory = dirname($path);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        if (! is_file($path)) {
            $image = imagecreatetruecolor(800, 600);
            $color = imagecolorallocate($image, random_int(80, 220), random_int(80, 220), random_int(80, 220));
            imagefill($image, 0, 0, $color);
            imagejpeg($image, $path, 85);
            imagedestroy($image);
        }

        $media = $uploadService->uploadFromPath('seed/' . $filename);

        return $media->id;
    }
}
