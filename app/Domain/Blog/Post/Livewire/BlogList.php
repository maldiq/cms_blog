<?php

namespace App\Domain\Blog\Post\Livewire;

use App\Domain\Blog\Post\Services\PostService;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class BlogList extends Component
{
    use WithPagination;

    public string $locale;

    public string $search = '';

    public ?string $category = null;

    public ?string $tag = null;

    public function mount(string $locale): void
    {
        $this->locale = $locale;
        app()->setLocale($locale);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render(PostService $postService): View
    {
        $posts = $postService->getPublished($this->locale, [
            'search' => $this->search,
            'category' => $this->category,
            'tag' => $this->tag,
        ]);

        return view('domain.blog.livewire.blog-list', compact('posts'));
    }
}
