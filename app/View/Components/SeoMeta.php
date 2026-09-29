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
        public array $breadcrumbs = [],
        public array $context = [],
        public bool $websiteSchema = false,
    ) {
        $locale = $this->locale ?? app()->getLocale();

        $this->meta = $seoService->resolveMeta(
            $this->model,
            $locale,
            array_merge($this->context, [
                'breadcrumbs' => $this->breadcrumbs,
                'website_schema' => $this->websiteSchema,
            ]),
        );
    }

    public function render(): View
    {
        return view('components.seo-meta');
    }
}
