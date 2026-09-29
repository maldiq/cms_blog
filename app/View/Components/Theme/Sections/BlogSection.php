<?php

namespace App\View\Components\Theme\Sections;

use App\Domain\Blog\Post\Models\Post;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;

class BlogSection extends Component
{
    public ?string $title;

    public ?string $subtitle;

    /**
     * @var Collection<int, Post>
     */
    public Collection $posts;

    public function __construct()
    {
        $this->title = theme_locale('blog.title');
        $this->subtitle = theme_locale('blog.subtitle');

        $this->posts = Post::query()
            ->with(['translations', 'user', 'featuredImage'])
            ->published()
            ->latest('published_at')
            ->limit(3)
            ->get();
    }

    public function render(): View
    {
        return view('theme::components.blog-section');
    }
}
