<?php

namespace Tests\Feature;

use App\Domain\Blog\Category\Filament\Resources\CategoryResource\Pages\EditCategory;
use App\Domain\Blog\Category\Models\Category;
use App\Domain\Blog\Series\Filament\Resources\SeriesResource\Pages\CreateSeries;
use App\Domain\Blog\Series\Filament\Resources\SeriesResource\Pages\EditSeries;
use App\Domain\Blog\Series\Models\Series;
use App\Domain\Gallery\Album\Filament\Resources\AlbumResource\Pages\EditAlbum;
use App\Domain\Gallery\Album\Models\Album;
use App\Domain\Media\Services\MediaUploadService;
use App\Domain\User\Models\User;
use Database\Seeders\LanguageSeeder;
use Database\Seeders\RolePermissionSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Cover Series (juga Category dan Album) memakai pemilih gambar visual yang
 * sama dengan featured image Post, bukan dropdown daftar nama media.
 */
class BlogCoverImagePickerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            LanguageSeeder::class,
            RolePermissionSeeder::class,
        ]);

        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    private function actingAdmin(): User
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');
        $this->actingAs($admin);

        return $admin;
    }

    private function makeImage(): int
    {
        return app(MediaUploadService::class)
            ->uploadFile(UploadedFile::fake()->image('cover.jpg'))
            ->getKey();
    }

    public function test_series_cover_uses_the_visual_media_picker_on_create(): void
    {
        $this->actingAdmin();
        $mediaId = $this->makeImage();

        Livewire::test(CreateSeries::class)
            ->assertSee('Pilih gambar')
            ->fillForm([
                'translations' => [
                    'id' => ['title' => 'Seri Uji', 'slug' => 'seri-uji'],
                    'en' => ['title' => 'Test Series', 'slug' => 'test-series'],
                ],
                'is_active' => true,
            ])
            ->dispatch('media-image-picked', mediaId: $mediaId, statePath: 'data.cover_id')
            ->assertSet('data.cover_id', $mediaId)
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertSame($mediaId, (int) Series::query()->firstOrFail()->cover_id);
    }

    public function test_series_cover_can_be_changed_and_cleared_on_edit(): void
    {
        $this->actingAdmin();
        $mediaId = $this->makeImage();

        $series = Series::query()->create(['is_active' => true]);
        $series->translateOrNew('id')->fill(['title' => 'Seri Lama', 'slug' => 'seri-lama'])->save();

        Livewire::test(EditSeries::class, ['record' => $series->getKey()])
            ->assertSee('Pilih gambar')
            ->dispatch('media-image-picked', mediaId: $mediaId, statePath: 'data.cover_id')
            ->assertSet('data.cover_id', $mediaId)
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame($mediaId, (int) $series->fresh()->cover_id);

        Livewire::test(EditSeries::class, ['record' => $series->getKey()])
            ->dispatch('media-image-picked', mediaId: null, statePath: 'data.cover_id')
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertNull($series->fresh()->cover_id);
    }

    public function test_category_cover_uses_the_visual_media_picker(): void
    {
        $this->actingAdmin();
        $mediaId = $this->makeImage();

        $category = Category::query()->create(['is_active' => true]);
        $category->translateOrNew('id')->fill(['name' => 'Kategori Uji', 'slug' => 'kategori-uji'])->save();

        Livewire::test(EditCategory::class, ['record' => $category->getKey()])
            ->assertSee('Pilih gambar')
            ->dispatch('media-image-picked', mediaId: $mediaId, statePath: 'data.cover_id')
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame($mediaId, (int) $category->fresh()->cover_id);
    }

    public function test_album_cover_uses_the_visual_media_picker(): void
    {
        $this->actingAdmin();
        $mediaId = $this->makeImage();

        $album = Album::query()->create(['is_active' => true]);
        $album->translateOrNew('id')->fill(['title' => 'Album Uji', 'slug' => 'album-uji'])->save();

        Livewire::test(EditAlbum::class, ['record' => $album->getKey()])
            ->assertSee('Pilih gambar')
            ->dispatch('media-image-picked', mediaId: $mediaId, statePath: 'data.cover_id')
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame($mediaId, (int) $album->fresh()->cover_id);
    }
}
