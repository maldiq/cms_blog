<?php

namespace Tests\Feature;

use App\Domain\Gallery\Album\Models\Album;
use App\Domain\Media\Services\MediaUploadService;
use Database\Seeders\LanguageSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GalleryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            LanguageSeeder::class,
            RolePermissionSeeder::class,
        ]);

        Storage::fake('public');
        app()->setLocale('id');
    }

    public function test_can_create_album_with_media(): void
    {
        $uploadService = app(MediaUploadService::class);
        $fileA = UploadedFile::fake()->image('a.jpg', 640, 480);
        $fileB = UploadedFile::fake()->image('b.jpg', 640, 480);
        $mediaA = $uploadService->uploadFile($fileA);
        $mediaB = $uploadService->uploadFile($fileB);

        $album = Album::query()->create([
            'cover_id' => $mediaA->id,
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $album->translateOrNew('id')->fill([
            'title' => 'Album Test',
            'slug' => 'album-test',
            'description' => 'Deskripsi',
        ])->save();

        $album->media()->attach([
            $mediaA->id => ['caption' => 'Satu', 'sort_order' => 0],
            $mediaB->id => ['caption' => 'Dua', 'sort_order' => 1],
        ]);

        $this->assertDatabaseHas('albums', ['id' => $album->id, 'cover_id' => $mediaA->id]);
        $this->assertDatabaseHas('album_translations', ['album_id' => $album->id, 'locale' => 'id', 'slug' => 'album-test']);
        $this->assertDatabaseCount('album_media', 2);
        $this->assertCount(2, $album->fresh()->media);
    }

    public function test_album_visible_in_frontend(): void
    {
        $uploadService = app(MediaUploadService::class);
        $media = $uploadService->uploadFile(UploadedFile::fake()->image('cover.jpg'));

        $album = Album::query()->create([
            'cover_id' => $media->id,
            'is_active' => true,
            'sort_order' => 0,
        ]);

        $album->translateOrNew('id')->fill([
            'title' => 'Galeri Publik',
            'slug' => 'galeri-publik',
        ])->save();

        $album->translateOrNew('en')->fill([
            'title' => 'Public Gallery',
            'slug' => 'public-gallery',
        ])->save();

        $album->media()->attach($media->id, ['sort_order' => 0]);

        $this->get('/id/gallery')->assertOk()->assertSee('Galeri Publik');
        $this->get('/id/gallery/galeri-publik')->assertOk()->assertSee('Galeri Publik');
        $this->get('/en/gallery/public-gallery')->assertOk()->assertSee('Public Gallery');

        $album->update(['is_active' => false]);
        $this->get('/id/gallery/galeri-publik')->assertNotFound();
    }

    public function test_media_reorder_works(): void
    {
        $uploadService = app(MediaUploadService::class);
        $media1 = $uploadService->uploadFile(UploadedFile::fake()->image('1.jpg'));
        $media2 = $uploadService->uploadFile(UploadedFile::fake()->image('2.jpg'));
        $media3 = $uploadService->uploadFile(UploadedFile::fake()->image('3.jpg'));

        $album = Album::query()->create(['is_active' => true, 'sort_order' => 0]);
        $album->translateOrNew('id')->fill(['title' => 'Urutan', 'slug' => 'urutan'])->save();

        $album->media()->attach([
            $media1->id => ['sort_order' => 0],
            $media2->id => ['sort_order' => 1],
            $media3->id => ['sort_order' => 2],
        ]);

        $album->media()->updateExistingPivot($media3->id, ['sort_order' => 0]);
        $album->media()->updateExistingPivot($media1->id, ['sort_order' => 1]);
        $album->media()->updateExistingPivot($media2->id, ['sort_order' => 2]);

        $orderedIds = $album->fresh()->media->pluck('id')->all();

        $this->assertSame([$media3->id, $media1->id, $media2->id], $orderedIds);
    }
}
