@extends('layouts.app')

@section('content')
    <main class="mx-auto max-w-6xl px-4 py-12 lg:px-6">
        <a href="{{ route('products.index', ['locale' => $locale]) }}" class="text-sm font-medium text-emerald-700 hover:text-emerald-800">
            &larr; Kembali ke rekomendasi produk
        </a>

        <article class="mt-6 grid gap-8 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm md:grid-cols-2 md:p-8">
            <div class="aspect-square overflow-hidden rounded-xl bg-gray-100">
                @if ($product->image)
                    <img src="{{ $product->image->getUrl('large') }}" alt="{{ $product->title }}" class="h-full w-full object-cover">
                @else
                    <div class="flex h-full items-center justify-center text-sm text-gray-400">Gambar belum tersedia</div>
                @endif
            </div>

            <div class="flex flex-col justify-center">
                <p class="text-sm font-semibold uppercase tracking-wide text-emerald-700">
                    {{ \App\Domain\Affiliate\Models\AffiliateProduct::PLATFORMS[$product->platform] ?? 'Lainnya' }}
                    <span class="px-1 text-gray-300">/</span>{{ $product->category }}
                </p>
                <h1 class="mt-3 text-3xl font-bold text-gray-900">{{ $product->title }}</h1>
                @if ($product->price)
                    <div class="mt-5 flex flex-wrap items-baseline gap-3">
                        <p class="text-2xl font-bold text-gray-900">Rp{{ number_format((float) $product->price, 0, ',', '.') }}</p>
                        @if ($product->original_price && (float) $product->original_price > (float) $product->price)
                            <p class="text-base text-gray-500 line-through">Rp{{ number_format((float) $product->original_price, 0, ',', '.') }}</p>
                        @endif
                    </div>
                @endif
                @if ($product->description)
                    <p class="mt-5 whitespace-pre-line leading-7 text-gray-600">{{ $product->description }}</p>
                @endif
                <a href="{{ route('products.redirect', ['locale' => $locale, 'slug' => $product->slug]) }}"
                   rel="sponsored nofollow noopener" class="mt-8 inline-flex justify-center rounded-lg bg-emerald-700 px-6 py-3 font-semibold text-white hover:bg-emerald-800">
                    Lihat penawaran di {{ ['shopee' => 'Shopee', 'tiktok_shop' => 'TikTok Shop', 'amazon' => 'Amazon', 'vps' => 'penyedia VPS'][$product->platform] ?? 'situs resmi' }}
                </a>
                <p class="mt-3 text-xs leading-5 text-gray-500">
                    Tautan ini adalah tautan affiliate. Kami dapat menerima komisi jika Anda membeli melalui tautan tersebut, tanpa biaya tambahan bagi Anda.
                </p>
            </div>
        </article>
    </main>
@endsection
