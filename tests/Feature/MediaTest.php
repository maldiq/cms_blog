<?php

namespace Tests\Feature;

use App\Domain\Media\Models\Media;
use App\Domain\Media\Services\MediaUploadService;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
        Storage::fake('public');
    }

    public function test_can_upload_media(): void
    {
        $file = UploadedFile::fake()->image('photo.jpg', 640, 480);

        $media = app(MediaUploadService::class)->uploadFile($file);

        $this->assertInstanceOf(Media::class, $media);
        $this->assertDatabaseHas('media', [
            'id' => $media->id,
            'collection_name' => 'default',
        ]);
        Storage::disk('public')->assertExists($media->getPathRelativeToRoot());
    }

    public function test_media_conversions_generated(): void
    {
        $file = UploadedFile::fake()->image('conversion-test.jpg', 1200, 900);

        $media = app(MediaUploadService::class)->uploadFile($file);
        $media->refresh();

        $this->assertTrue($media->hasGeneratedConversion('thumb'));
        $this->assertTrue($media->hasGeneratedConversion('medium'));
        $this->assertTrue($media->hasGeneratedConversion('large'));
    }

    public function test_can_delete_media(): void
    {
        $file = UploadedFile::fake()->image('delete-me.jpg');
        $media = app(MediaUploadService::class)->uploadFile($file);

        $path = $media->getPathRelativeToRoot();
        $media->delete();

        $this->assertDatabaseMissing('media', ['id' => $media->id]);
        Storage::disk('public')->assertMissing($path);
    }
}
