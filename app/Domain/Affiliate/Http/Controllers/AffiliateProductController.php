<?php

namespace App\Domain\Affiliate\Http\Controllers;

use App\Domain\Affiliate\Models\AffiliateProduct;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AffiliateProductController extends Controller
{
    public function home(string $locale, Request $request): View
    {
        return $this->catalog($locale, $request, true);
    }

    public function index(string $locale, Request $request): View
    {
        return $this->catalog($locale, $request);
    }

    private function catalog(string $locale, Request $request, bool $isHome = false): View
    {
        app()->setLocale($locale);

        $products = AffiliateProduct::query()
            ->with('image')
            ->active()
            ->when($request->filled('q'), function ($query) use ($request): void {
                $search = mb_substr($request->string('q')->trim()->value(), 0, 100);
                $query->where(function ($query) use ($search): void {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('category'), fn ($query) => $query->where('category', $request->string('category')->value()))
            ->when(
                is_string($request->input('platform'))
                    && array_key_exists($request->input('platform'), AffiliateProduct::PLATFORMS),
                fn ($query) => $query->where('platform', $request->input('platform')),
            )
            ->orderByDesc('is_featured')
            ->orderByDesc('created_at')
            ->paginate(12)
            ->withQueryString();

        $categories = AffiliateProduct::query()
            ->active()
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('products.index', [
            'locale' => $locale,
            'products' => $products,
            'categories' => $categories,
            'seoContext' => [
                'title' => $isHome ? setting('site_name', 'Rekomendasi Produk') : 'Rekomendasi Produk',
                'canonical' => $isHome
                    ? route('home', ['locale' => $locale])
                    : route('products.index', ['locale' => $locale]),
            ],
            'isHome' => $isHome,
            'websiteSchema' => $isHome,
        ]);
    }

    public function show(string $locale, string $slug): View
    {
        app()->setLocale($locale);

        $product = AffiliateProduct::query()
            ->with('image')
            ->active()
            ->where('slug', $slug)
            ->firstOrFail();

        return view('products.show', [
            'locale' => $locale,
            'product' => $product,
            'seoContext' => [
                'title' => $product->title,
                'description' => $product->description,
                'canonical' => route('products.show', ['locale' => $locale, 'slug' => $product->slug]),
            ],
        ]);
    }

    public function redirect(string $locale, string $slug): RedirectResponse
    {
        $product = AffiliateProduct::query()->active()->where('slug', $slug)->firstOrFail();
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
}
