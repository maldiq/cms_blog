<?php

namespace Tests\Feature;

use App\Domain\Blog\Post\Filament\Resources\PostResource;
use App\Domain\Blog\Post\Filament\Resources\PostResource\Pages\EditPost;
use App\Domain\Blog\Post\Filament\Resources\PostResource\RelationManagers\GalleryRelationManager;
use App\Domain\Blog\Post\Models\Post;
use App\Domain\Media\Models\Media;
use App\Domain\User\Models\User;
use Database\Seeders\LanguageSeeder;
use Database\Seeders\RolePermissionSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class PostGalleryTest extends TestCase
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

    private function makePost(string $slug, string $status = Post::STATUS_DRAFT): Post
    {
        $post = Post::query()->create([
            'user_id' => User::factory()->create(['is_active' => true])->id,
            'status' => $status,
            'type' => 'article',
            'published_at' => $status === Post::STATUS_PUBLISHED ? now()->subDay() : null,
        ]);

        $post->translateOrNew('id')->fill([
            'title' => 'Post Galeri',
            'slug' => $slug,
            'content' => '<p>Konten</p>',
        ])->save();

        return $post;
    }

    private function jpegBytes(): string
    {
        $image = imagecreatetruecolor(64, 48);
        ob_start();
        imagejpeg($image, null, 80);
        $bytes = (string) ob_get_clean();
        imagedestroy($image);

        return $bytes;
    }

    private function attachGalleryMedia(Post $post, string $fileName): Media
    {
        return $post->addMediaFromString($this->jpegBytes())
            ->usingFileName($fileName)
            ->toMediaCollection('gallery');
    }

    public function test_gallery_relation_manager_lists_saved_media(): void
    {
        Storage::fake('public');
        $this->actingAdmin();

        $post = $this->makePost('post-galeri-daftar');
        $media = $this->attachGalleryMedia($post, 'list-gallery.jpg');

        Livewire::test(GalleryRelationManager::class, [
            'ownerRecord' => $post,
            'pageClass' => EditPost::class,
        ])->assertCanSeeTableRecords([$media]);
    }

    public function test_post_edit_page_renders_the_gallery_relation_manager(): void
    {
        Storage::fake('public');
        $this->actingAdmin();

        $post = $this->makePost('post-galeri-halaman-edit');
        $this->attachGalleryMedia($post, 'edited-gallery.jpg');

        $this->assertContains(GalleryRelationManager::class, PostResource::getRelations());

        // Relation manager dirender secara lazy oleh Filament, jadi judul
        // "Galeri" belum ada di HTML awal; cukup pastikan kontainernya tampil.
        Livewire::test(EditPost::class, ['record' => $post->getKey()])
            ->assertSeeHtml('fi-resource-relation-managers');
    }

    public function test_gallery_upload_action_attaches_media_to_the_post(): void
    {
        Storage::fake('public');
        $this->actingAdmin();

        $post = $this->makePost('post-galeri-upload');

        Livewire::test(GalleryRelationManager::class, [
            'ownerRecord' => $post,
            'pageClass' => EditPost::class,
        ])
            ->callTableAction('upload', data: [
                'attachments' => [UploadedFile::fake()->image('new-photo.jpg', 320, 240)],
            ])
            ->assertHasNoTableActionErrors();

        $this->assertCount(1, $post->fresh()->getMedia('gallery'));
    }

    public function test_gallery_delete_action_removes_media(): void
    {
        Storage::fake('public');
        $this->actingAdmin();

        $post = $this->makePost('post-galeri-hapus');
        $media = $this->attachGalleryMedia($post, 'delete-me.jpg');

        Livewire::test(GalleryRelationManager::class, [
            'ownerRecord' => $post,
            'pageClass' => EditPost::class,
        ])
            ->callTableAction('delete', $media)
            ->assertHasNoTableActionErrors();

        $this->assertDatabaseMissing('media', ['id' => $media->id]);
        $this->assertCount(0, $post->fresh()->getMedia('gallery'));
    }

    public function test_blog_post_page_shows_gallery_images(): void
    {
        Storage::fake('public');
        $this->actingAdmin();

        $post = $this->makePost('post-galeri-tampil', Post::STATUS_PUBLISHED);
        $media = $this->attachGalleryMedia($post, 'shown.jpg');

        $this->get('/id/blog/post-galeri-tampil')
            ->assertOk()
            ->assertSee(__('messages.post_gallery'))
            ->assertSee($media->getFullUrl('medium'), false);
    }
}
