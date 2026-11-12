<?php

namespace Tests\Feature;

use App\Domain\Blog\Post\Filament\Resources\PostResource\Pages\CreatePost;
use App\Domain\Blog\Post\Filament\Resources\PostResource\Pages\EditPost;
use App\Domain\Blog\Post\Models\Post;
use App\Domain\Media\Models\Media;
use App\Domain\Media\Services\MediaUploadService;
use App\Domain\User\Models\User;
use Database\Seeders\LanguageSeeder;
use Database\Seeders\RolePermissionSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class PostFeaturedImageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            LanguageSeeder::class,
            RolePermissionSeeder::class,
        ]);
    }

    private function actingAdmin(): User
    {
        $admin = User::factory()->create(['is_active' => true]);
        $admin->assignRole('admin');
        $this->actingAs($admin);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        return $admin;
    }

    private function makePost(string $slug): Post
    {
        $post = Post::query()->create([
            'user_id' => User::factory()->create(['is_active' => true])->id,
            'status' => Post::STATUS_DRAFT,
            'type' => 'article',
        ]);

        $post->translateOrNew('id')->fill([
            'title' => 'Post Gambar',
            'slug' => $slug,
            'content' => '<p>Konten</p>',
        ])->save();

        return $post;
    }

    private function makeImage(string $name = 'featured.jpg'): Media
    {
        Storage::fake('public');

        return app(MediaUploadService::class)
            ->uploadFile(UploadedFile::fake()->image($name, 320, 240));
    }

    public function test_edit_page_shows_a_preview_of_the_selected_featured_image(): void
    {
        $this->actingAdmin();

        $post = $this->makePost('post-gambar-pratinjau');
        $media = $this->makeImage('featured-preview.jpg');

        $post->forceFill(['featured_image_id' => $media->id])->saveQuietly();

        Livewire::test(EditPost::class, ['record' => $post->getKey()])
            ->assertSee('Ganti gambar')
            ->assertSee($media->name)
            ->assertSee($media->getFullUrl('thumb'), false);
    }

    public function test_create_page_uses_the_visual_image_picker(): void
    {
        $this->actingAdmin();
        $media = $this->makeImage('create-pick.jpg');

        Livewire::test(CreatePost::class)
            ->assertFormFieldExists('featured_image_id')
            ->assertSee('Pilih gambar')
            ->dispatch('media-image-picked', mediaId: $media->id, statePath: 'data.featured_image_id')
            ->assertSet('data.featured_image_id', $media->id);
    }

    public function test_picking_media_sets_featured_and_og_image_state(): void
    {
        $this->actingAdmin();

        $post = $this->makePost('post-gambar-pilih');
        $media = $this->makeImage('picked.jpg');

        Livewire::test(EditPost::class, ['record' => $post->getKey()])
            ->dispatch('media-image-picked', mediaId: $media->id, statePath: 'data.featured_image_id')
            ->assertSet('data.featured_image_id', $media->id);

        Livewire::test(EditPost::class, ['record' => $post->getKey()])
            ->dispatch('media-image-picked', mediaId: $media->id, statePath: 'data.translations.id.og_image_id')
            ->assertSet('data.translations.id.og_image_id', $media->id);
    }

    public function test_selected_featured_image_is_saved_with_the_post(): void
    {
        $this->actingAdmin();

        $post = $this->makePost('post-gambar-simpan');
        $media = $this->makeImage('saved.jpg');

        Livewire::test(EditPost::class, ['record' => $post->getKey()])
            ->dispatch('media-image-picked', mediaId: $media->id, statePath: 'data.featured_image_id')
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame($media->id, $post->fresh()->featured_image_id);
    }

    public function test_clearing_the_picker_removes_the_featured_image(): void
    {
        $this->actingAdmin();

        $post = $this->makePost('post-gambar-hapus');
        $media = $this->makeImage('removed.jpg');
        $post->forceFill(['featured_image_id' => $media->id])->saveQuietly();

        Livewire::test(EditPost::class, ['record' => $post->getKey()])
            ->dispatch('media-image-picked', mediaId: null, statePath: 'data.featured_image_id')
            ->assertSet('data.featured_image_id', null);
    }
}
