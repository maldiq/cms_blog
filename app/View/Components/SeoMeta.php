<?php

namespace App\View\Components;

use App\Domain\Seo\Services\SeoService;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class SeoMeta extends Component
{
    /**
     * @var array<string, mixed>
     */
    public array $meta;

    /**
     * @param  list<array{name: string, url: string}>  $breadcrumbs
     * @param  array<string, mixed>  $context
     */
    public function __construct(
        SeoService $seoService,
        public mixed $model = null,
        public ?string $locale = null,
        public ?string $title = null,
        public ?string $description = null,
        public ?string $image = null,
        public array $breadcrumbs = [],
        public array $context = [],
        public bool $websiteSchema = false,
    ) {
        $locale = $this->locale ?? app()->getLocale();

        $context = array_merge($this->context, [
            'breadcrumbs' => $this->breadcrumbs,
            'website_schema' => $this->websiteSchema,
        ]);

        if ($this->title !== null) {
            $context['title'] = $this->title;
        }

        if ($this->description !== null) {
            $context['description'] = $this->description;
        }

        if ($this->image !== null) {
            $context['og_image'] = $this->image;
        }

        $this->meta = $seoService->resolveMeta(
            $this->model,
            $locale,
            $context,
        );
    }

    public function render(): View
    {
        return view('components.seo-meta');
    }
}
