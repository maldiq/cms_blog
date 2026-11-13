<?php

namespace App\Domain\Affiliate\Http\Controllers;

use App\Domain\Affiliate\Models\AffiliateBanner;
use App\Domain\Affiliate\Models\AffiliateCategory;
use App\Domain\Affiliate\Models\AffiliatePlatform;
use App\Domain\Affiliate\Models\AffiliateProduct;
use App\Domain\Affiliate\Models\AffiliateProductTranslation;
use App\Domain\Blog\Post\Models\Post;
use App\Domain\Media\Models\Media;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class AffiliateProductController extends Controller
{
    public function home(string $locale): View
    {
        app()->setLocale($locale);

        $categories = $this->navigationCategories($locale);
        $featuredProducts = $this->featuredProducts($locale);
        $hasCuratedFeatured = AffiliateProduct::query()
            ->active()
            ->localized($locale)
            ->where('is_featured', true)
            ->exists();
        $newProducts = $this->newProducts($featuredProducts, $locale);

        $heroProducts = $featuredProducts
            ->filter(fn (AffiliateProduct $product): bool => $product->image !== null)
            ->take(3)
            ->values();

        if ($heroProducts->isEmpty()) {
            $heroProducts = $newProducts
                ->filter(fn (AffiliateProduct $product): bool => $product->image !== null)
                ->take(3)
                ->values();
        }

        $posts = Post::query()
            ->published()
            ->with(['featuredImage', 'user', 'translations', 'categories.translations'])
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        $platforms = AffiliatePlatform::query()
            ->active()
            ->with(['translations' => fn ($query) => $query->where('locale', $locale)])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'slug']);

        return view('products.home', [
            'locale' => $locale,
            'categories' => $categories,
            'featuredProducts' => $featuredProducts,
            'featuredTitle' => $hasCuratedFeatured ? __('messages.affiliate_featured_section') : __('messages.affiliate_most_wanted'),
            'newProducts' => $newProducts,
            'heroProducts' => $heroProducts,
            'slides' => $this->heroSlides($locale),
            'posts' => $posts,
            'platforms' => $platforms,
            'totalProducts' => $categories->sum('products_count'),
            'seoContext' => [
                'title' => __('messages.affiliate_home_meta_title', [
                    'site' => trim((string) setting('site_name', 'Rekomendasi Produk')),
                ]),
                'description' => setting('site_description')
                    ?: __('messages.affiliate_home_meta_description'),
                'canonical' => route('home', ['locale' => $locale]),
            ],
            'websiteSchema' => true,
        ]);
    }

    public function index(string $locale, Request $request): View
    {
        return $this->catalog($locale, $request);
    }

    public function category(string $locale, string $slug, Request $request): View
    {
        app()->setLocale($locale);

        $category = AffiliateCategory::query()
            ->where('is_active', true)
            ->whereHas('translations', fn (Builder $query) => $query->where('locale', $locale)->where('slug', $slug))
            ->with(['translations' => fn ($query) => $query->where('locale', $locale)])
            ->firstOrFail();

        return $this->catalog($locale, $request, $category);
    }

    public function show(string $locale, string $slug): View
    {
        app()->setLocale($locale);

        $product = AffiliateProduct::query()
            ->with($this->productRelations($locale))
            ->active()
            ->whereHas('translations', fn (Builder $query) => $query->where('locale', $locale)->where('slug', $slug))
            ->firstOrFail();

        $translation = $product->translate($locale, false);

        $related = AffiliateProduct::query()
            ->with($this->productRelations($locale))
            ->active()
            ->localized($locale)
            ->whereKeyNot($product->getKey())
            ->when(
                $product->affiliate_category_id !== null,
                fn (Builder $query) => $query->where('affiliate_category_id', $product->affiliate_category_id),
            )
            ->orderByDesc('is_featured')
            ->orderByDesc('click_count')
            ->orderByDesc('created_at')
            ->limit(4)
            ->get();

        if ($related->count() < 4) {
            $related = $related
                ->concat(
                    AffiliateProduct::query()
                        ->with($this->productRelations($locale))
                        ->active()
                        ->localized($locale)
                        ->whereKeyNot([$product->getKey(), ...$related->modelKeys()])
                        ->orderByDesc('created_at')
                        ->limit(4 - $related->count())
                        ->get()
                )
                ->values();
        }

        $category = $product->affiliateCategory;

        $metaDescription = trim((string) ($translation?->excerpt ?? ''));

        if ($metaDescription === '') {
            $metaDescription = trim((string) preg_replace(
                '/\s+/u',
                ' ',
                strip_tags((string) ($translation?->description ?? '')),
            ));
        }

        if ($metaDescription === '') {
            $metaDescription = __('messages.affiliate_seo_product_description', [
                'title' => $translation?->title ?? $product->title,
                'platform' => $product->affiliatePlatform?->name ?? 'marketplace',
            ]);
        }

        return view('products.show', [
            'locale' => $locale,
            'product' => $product,
            'relatedProducts' => $related,
            'seo' => $product,
            'seoContext' => [
                'title' => $translation?->title ?? $product->title,
                'description' => $metaDescription,
                'canonical' => route('products.show', ['locale' => $locale, 'slug' => $translation?->slug ?? $product->slug]),
                'og_image' => $this->productImageUrl($product),
            ],
            'breadcrumbs' => [
                ['name' => setting('site_name', config('app.name')), 'url' => route('home', ['locale' => $locale])],
                ['name' => __('messages.affiliate_products'), 'url' => route('products.index', ['locale' => $locale])],
                ['name' => $translation?->title ?? $product->title, 'url' => route('products.show', ['locale' => $locale, 'slug' => $translation?->slug ?? $product->slug])],
            ],
            'categoryName' => $category?->name,
        ]);
    }

    public function redirect(string $locale, string $slug): RedirectResponse
    {
        $product = AffiliateProduct::query()
            ->active()
            ->whereHas('translations', fn (Builder $query) => $query->where('locale', $locale)->where('slug', $slug))
            ->firstOrFail();
        $url = parse_url($product->affiliate_url);

        abort_unless(
            is_array($url)
                && in_array(strtolower($url['scheme'] ?? ''), ['http', 'https'], true)
                && filled($url['host'] ?? null),
            404,
        );

        $product->increment('click_count');

        return redirect()->away($product->affiliate_url);
    }

    private function catalog(string $locale, Request $request, ?AffiliateCategory $selectedCategory = null): View
    {
        app()->setLocale($locale);

        $sort = is_string($request->input('sort')) ? $request->input('sort') : 'latest';
        $sort = array_key_exists($sort, $this->sortOptions()) ? $sort : 'latest';

        $categorySlug = is_string($request->input('category')) && filled($request->input('category'))
            ? $request->input('category')
            : null;

        if ($selectedCategory === null && $categorySlug !== null) {
            $selectedCategory = AffiliateCategory::query()
                ->where('is_active', true)
                ->whereHas('translations', fn (Builder $query) => $query->where('locale', $locale)->where('slug', $categorySlug))
                ->with(['translations' => fn ($query) => $query->where('locale', $locale)])
                ->first();
        }

        $products = AffiliateProduct::query()
            ->with($this->productRelations($locale))
            ->localized($locale)
            ->active()
            ->when($request->filled('q'), function (Builder $query) use ($request, $locale): void {
                $search = mb_substr($request->string('q')->trim()->value(), 0, 100);
                $query->whereHas('translations', function (Builder $query) use ($search, $locale): void {
                    $query->where('locale', $locale)
                        ->where(function (Builder $query) use ($search): void {
                            $query->where('title', 'like', "%{$search}%")
                                ->orWhere('description', 'like', "%{$search}%");
                        });
                });
            })
            ->when(
                $selectedCategory !== null,
                fn (Builder $query) => $query->where('affiliate_category_id', $selectedCategory->getKey()),
            )
            ->when(
                $selectedCategory === null && $categorySlug !== null,
                fn (Builder $query) => $query->whereHas(
                    'affiliateCategory',
                    fn (Builder $query) => $query->where('slug', $categorySlug),
                ),
            )
            ->when(
                is_string($request->input('platform')) && filled($request->input('platform')),
                fn (Builder $query) => $query->whereHas(
                    'affiliatePlatform',
                    fn (Builder $query) => $query->where('slug', $request->input('platform')),
                ),
            )
            ->when($request->boolean('featured'), fn (Builder $query) => $query->where('is_featured', true))
            ->when($request->filled('min_price'), fn (Builder $query) => $query->where('price', '>=', (float) $request->input('min_price')))
            ->when($request->filled('max_price'), fn (Builder $query) => $query->where('price', '<=', (float) $request->input('max_price')));

        $this->applySort($products, $sort, $locale);

        $products = $products->paginate(12)->withQueryString();

        $categories = $this->navigationCategories($locale);
        $platforms = AffiliatePlatform::query()
            ->active()
            ->with(['translations' => fn ($query) => $query->where('locale', $locale)])
            ->withCount(['products' => fn (Builder $query) => $query
                ->where('is_active', true)
                ->whereHas('translations', fn (Builder $query) => $query->where('locale', $locale))])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get(['id', 'slug']);

        $activeFilters = collect([
            'q' => $request->input('q'),
            'platform' => $request->input('platform'),
            'min_price' => $request->input('min_price'),
            'max_price' => $request->input('max_price'),
            'sort' => $sort === 'latest' ? null : $sort,
        ])->filter(fn ($value): bool => is_string($value) && filled($value))->all();

        return view('products.index', [
            'locale' => $locale,
            'products' => $products,
            'categories' => $categories,
            'platforms' => $platforms,
            'seo' => $selectedCategory,
            'selectedCategory' => $selectedCategory,
            'activeFilters' => $activeFilters,
            'sort' => $sort,
            'sortOptions' => $this->sortOptions(),
            'seoContext' => [
                'title' => $selectedCategory !== null
                    ? __('messages.affiliate_category_meta_title', ['category' => $selectedCategory->name])
                    : __('messages.affiliate_all_products'),
                'description' => $selectedCategory !== null
                    ? __('messages.affiliate_category_meta_description', ['category' => $selectedCategory->name])
                    : __('messages.affiliate_catalog_meta_description'),
                'canonical' => $selectedCategory !== null
                    ? affiliate_category_url($locale, $selectedCategory->slug)
                    : route('products.index', ['locale' => $locale]),
            ],
        ]);
    }

    /**
     * @return array<string, string>
     */
    private function sortOptions(): array
    {
        return [
            'latest' => 'Terbaru',
            'popular' => 'Paling diminati',
            'price_asc' => 'Harga terendah',
            'price_desc' => 'Harga tertinggi',
            'name' => 'Nama A-Z',
        ];
    }

    /**
     * @param  Builder<AffiliateProduct>  $query
     */
    private function applySort(Builder $query, string $sort, string $locale): void
    {
        match ($sort) {
            'popular' => $query->orderByDesc('click_count')->orderByDesc('created_at'),
            'price_asc' => $query->orderByRaw('price IS NULL')->orderBy('price')->orderByDesc('created_at'),
            'price_desc' => $query->orderByRaw('price IS NULL')->orderByDesc('price')->orderByDesc('created_at'),
            'name' => $query->orderBy(
                AffiliateProductTranslation::query()
                    ->select('title')
                    ->whereColumn('affiliate_product_id', 'affiliate_products.id')
                    ->where('locale', $locale)
                    ->limit(1),
            ),
            default => $query->orderByDesc('created_at')->orderByDesc('id'),
        };
    }

    /**
     * @return Collection<int, AffiliateCategory>
     */
    private function navigationCategories(string $locale): Collection
    {
        return AffiliateCategory::query()
            ->where('is_active', true)
            ->with(['translations' => fn ($query) => $query->where('locale', $locale)])
            ->withCount(['products' => fn (Builder $query) => $query
                ->where('is_active', true)
                ->whereHas('translations', fn (Builder $query) => $query->where('locale', $locale))])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();
    }

    /**
     * @return array<int|string, mixed>
     */
    private function productRelations(string $locale): array
    {
        $localizedTranslations = fn ($query) => $query->where('locale', $locale);

        return [
            'image',
            'affiliateCategory' => fn ($query) => $query->with(['translations' => $localizedTranslations]),
            'affiliatePlatform' => fn ($query) => $query->with(['translations' => $localizedTranslations]),
            'translations' => $localizedTranslations,
        ];
    }

    /**
     * @return Collection<int, AffiliateProduct>
     */
    private function featuredProducts(string $locale): Collection
    {
        $limit = (int) config('affiliate.hero.featured_limit', 8);

        $featured = AffiliateProduct::query()
            ->with($this->productRelations($locale))
            ->localized($locale)
            ->active()
            ->where('is_featured', true)
            ->orderByDesc('click_count')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();

        if ($featured->isNotEmpty()) {
            if ($featured->count() < $limit) {
                $featured = $featured
                    ->concat(
                        AffiliateProduct::query()
                            ->with($this->productRelations($locale))
                            ->localized($locale)
                            ->active()
                            ->whereKeyNot($featured->modelKeys())
                            ->orderByDesc('click_count')
                            ->orderByDesc('created_at')
                            ->limit($limit - $featured->count())
                            ->get()
                    )
                    ->values();
            }

            return $featured;
        }

        return AffiliateProduct::query()
            ->with($this->productRelations($locale))
            ->localized($locale)
            ->active()
            ->orderByDesc('click_count')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * @param  Collection<int, AffiliateProduct>  $featuredProducts
     * @return Collection<int, AffiliateProduct>
     */
    private function newProducts(Collection $featuredProducts, string $locale): Collection
    {
        $limit = (int) config('affiliate.hero.new_limit', 8);

        $newProducts = AffiliateProduct::query()
            ->with($this->productRelations($locale))
            ->localized($locale)
            ->active()
            ->whereKeyNot($featuredProducts->modelKeys())
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();

        if ($newProducts->count() < $limit) {
            $newProducts = $newProducts
                ->concat(
                    AffiliateProduct::query()
                        ->with($this->productRelations($locale))
                        ->localized($locale)
                        ->active()
                        ->whereKeyNot($newProducts->modelKeys())
                        ->orderByDesc('created_at')
                        ->orderByDesc('id')
                        ->limit($limit - $newProducts->count())
                        ->get()
                )
                ->values();
        }

        return $newProducts;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function heroSlides(string $locale): array
    {
        $banners = AffiliateBanner::query()
            ->with(['image', 'translations'])
            ->active()
            ->ordered()
            ->get();

        $slides = $banners
            ->map(function (AffiliateBanner $banner) use ($locale): ?array {
                $translation = $banner->translate($locale, false) ?? $banner->translate('id', false);

                if ($translation === null) {
                    return null;
                }

                return [
                    'id' => 'banner-'.$banner->getKey(),
                    'badge' => $translation->badge,
                    'title' => $translation->title,
                    'subtitle' => $translation->subtitle,
                    'theme' => array_key_exists((string) $banner->theme, AffiliateBanner::themeOptions()) ? $banner->theme : 'brand',
                    'cta_label' => filled($translation->cta_label) ? $translation->cta_label : __('messages.affiliate_shop_now'),
                    'link' => $banner->link_url,
                    'image_url' => $this->mediaUrl($banner->image, 'large'),
                ];
            })
            ->filter()
            ->values();

        if ($slides->isNotEmpty()) {
            return $slides->all();
        }

        $fallbackSlides = $locale === 'en'
            ? config('affiliate.hero.fallback_slides_en', [])
            : [];

        if ($fallbackSlides === []) {
            $fallbackSlides = config('affiliate.hero.fallback_slides', []);
        }

        return collect($fallbackSlides)
            ->values()
            ->map(fn (array $slide, int $index): array => [
                'id' => 'fallback-'.$index,
                'badge' => $slide['badge'] ?? null,
                'title' => $slide['title'] ?? null,
                'subtitle' => $slide['subtitle'] ?? null,
                'theme' => $slide['theme'] ?? 'brand',
                'cta_label' => $slide['cta_label'] ?? __('messages.affiliate_shop_now'),
                'link' => $slide['link'] ?? null,
                'image_url' => null,
            ])
            ->all();
    }

    private function mediaUrl(?Media $media, string $conversion): ?string
    {
        if ($media === null) {
            return null;
        }

        return $media->hasGeneratedConversion($conversion)
            ? $media->getUrl($conversion)
            : $media->getUrl();
    }

    private function productImageUrl(AffiliateProduct $product): ?string
    {
        return $this->mediaUrl($product->image, 'large');
    }
}
