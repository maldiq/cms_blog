<?php

namespace Tests\Feature;

use App\Domain\Media\Livewire\MediaPicker;
use App\Domain\Media\Models\Media;
use App\Domain\Media\Services\MediaUploadService;
use App\Domain\User\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class MediaPickerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
        Storage::fake('public');

        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');
        $this->actingAs($admin);
    }

    public function test_image_picker_opens_as_a_media_library_with_image_details_and_pagination(): void
    {
        app(MediaUploadService::class)->uploadFile(UploadedFile::fake()->image('product-photo.jpg'));

        Livewire::test(MediaPicker::class, [
            'multiple' => false,
            'selectedMediaIds' => [],
            'statePath' => 'data.image_id',
        ])
            ->assertSee('Pilih gambar')
            ->call('openModal')
            ->assertSee('Pustaka media')
            ->assertSee('Unggah berkas')
            ->assertSee('Cari nama gambar...')
            ->assertSee('product-photo')
            ->call('toggleSelection', Media::query()->where('name', 'product-photo')->value('id'))
            ->assertSee('image/jpeg')
            ->assertSee('default')
            ->assertSee('Gunakan gambar');
    }

    public function test_empty_library_offers_upload_action_to_authorized_user(): void
    {
        Livewire::test(MediaPicker::class, [
            'multiple' => false,
            'selectedMediaIds' => [],
            'statePath' => 'data.image_id',
        ])
            ->call('openModal')
            ->assertSee('Belum ada gambar di pustaka media server ini.')
            ->assertSee('Unggah gambar')
            ->set('activeTab', 'upload')
            ->assertSee('Pilih gambar untuk diunggah');
    }

    public function test_single_image_selection_is_only_applied_after_confirmation(): void
    {
        $media = app(MediaUploadService::class)->uploadFile(UploadedFile::fake()->image('chosen-image.jpg'));

        Livewire::test(MediaPicker::class, [
            'multiple' => false,
            'selectedMediaIds' => [],
            'statePath' => 'data.image_id',
        ])
            ->call('openModal')
            ->call('toggleSelection', $media->id)
            ->assertSet('selectedMediaIds', [])
            ->assertSet('pendingMediaIds', [$media->id])
            ->call('confirmSelection')
            ->assertSet('selectedMediaIds', [$media->id])
            ->assertDispatched('affiliate-image-picked', mediaId: $media->id);
    }

    public function test_image_upload_adds_file_to_media_library_and_selects_it(): void
    {
        $upload = UploadedFile::fake()->image('new-product-image.jpg');

        $component = Livewire::test(MediaPicker::class, [
            'multiple' => false,
            'selectedMediaIds' => [],
            'statePath' => 'data.image_id',
        ])
            ->call('openModal')
            ->set('activeTab', 'upload')
            ->set('newUploads', [$upload])
            ->call('uploadNew')
            ->assertSet('activeTab', 'library');

        $media = Media::query()->where('name', 'new-product-image')->firstOrFail();

        $component
            ->assertSee('new-product-image')
            ->assertSet('pendingMediaIds', [$media->id])
            ->call('confirmSelection')
            ->assertDispatched('affiliate-image-picked', mediaId: $media->id);

        $this->assertDatabaseHas('media', [
            'name' => 'new-product-image',
            'mime_type' => 'image/jpeg',
        ]);
    }

    public function test_picker_lists_image_uploads_with_missing_or_generic_mime_type(): void
    {
        foreach ([null, 'application/octet-stream'] as $mimeType) {
            $media = Media::query()->create([
                'model_type' => User::class,
                'model_id' => auth()->id(),
                'collection_name' => 'default',
                'name' => 'legacy-image',
                'file_name' => 'legacy-image.jpg',
                'mime_type' => $mimeType,
                'disk' => 'public',
                'conversions_disk' => 'public',
                'size' => 100,
                'manipulations' => [],
                'custom_properties' => [],
                'generated_conversions' => [],
                'responsive_images' => [],
            ]);

            Livewire::test(MediaPicker::class, [
                'multiple' => false,
                'selectedMediaIds' => [],
                'statePath' => 'data.image_id',
            ])
                ->call('openModal')
                ->assertSee('legacy-image')
                ->call('toggleSelection', $media->id);
        }
    }

    public function test_non_image_files_cannot_be_selected(): void
    {
        $media = Media::query()->create([
            'model_type' => User::class,
            'model_id' => auth()->id(),
            'collection_name' => 'default',
            'name' => 'document',
            'file_name' => 'document.pdf',
            'mime_type' => 'application/pdf',
            'disk' => 'public',
            'conversions_disk' => 'public',
            'size' => 100,
            'manipulations' => [],
            'custom_properties' => [],
            'generated_conversions' => [],
            'responsive_images' => [],
        ]);

        Livewire::test(MediaPicker::class, [
            'multiple' => false,
            'selectedMediaIds' => [],
            'statePath' => 'data.image_id',
        ])
            ->call('openModal')
            ->call('toggleSelection', $media->id)
            ->assertNotFound();
    }

    public function test_user_with_view_permission_but_without_upload_permission_cannot_upload(): void
    {
        $viewer = User::factory()->create(['is_active' => true]);
        $viewer->givePermissionTo('media.viewAny');
        $this->actingAs($viewer);

        Livewire::test(MediaPicker::class, [
            'multiple' => false,
            'selectedMediaIds' => [],
            'statePath' => 'data.image_id',
        ])
            ->set('newUploads', [UploadedFile::fake()->image('not-allowed.jpg')])
            ->call('uploadNew')
            ->assertForbidden();

        $this->assertDatabaseMissing('media', ['name' => 'not-allowed']);
    }

    public function test_clearing_selected_image_notifies_parent_form(): void
    {
        $media = app(MediaUploadService::class)->uploadFile(UploadedFile::fake()->image('remove-image.jpg'));

        Livewire::test(MediaPicker::class, [
            'multiple' => false,
            'selectedMediaIds' => [$media->id],
            'statePath' => 'data.image_id',
        ])
            ->call('clearSelection')
            ->assertSet('selectedMediaIds', [])
            ->assertDispatched('affiliate-image-picked', mediaId: null);
    }
}
