@extends('layouts.app')

@section('content')
    <main class="mx-auto max-w-7xl px-4 py-12 lg:px-6">
        <div class="mx-auto max-w-3xl text-center">
            <p class="text-sm font-semibold uppercase tracking-wide text-emerald-700">Pilihan lintas marketplace</p>
            <h1 class="mt-2 text-3xl font-bold text-gray-900 sm:text-4xl">
                {{ ! empty($isHome) ? 'Temukan Produk Pilihan' : 'Rekomendasi Produk' }}
            </h1>
            <p class="mt-3 text-gray-600">Temukan pilihan produk dan layanan dari Shopee, TikTok Shop, Amazon, penyedia VPS, dan lainnya.</p>
        </div>

        <form method="GET" action="{{ route('products.index', ['locale' => $locale]) }}"
              class="mx-auto mt-8 grid max-w-5xl gap-3 rounded-xl border border-gray-200 bg-white p-4 shadow-sm sm:grid-cols-4">
            <label class="sr-only" for="product-search">Cari produk</label>
            <input id="product-search" name="q" value="{{ request('q') }}" placeholder="Cari produk..."
                   class="rounded-lg border-gray-300 text-sm focus:border-emerald-600 focus:ring-emerald-600 sm:col-span-2">
            <label class="sr-only" for="product-category">Kategori</label>
            <select id="product-category" name="category" class="rounded-lg border-gray-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                <option value="">Semua kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>{{ $category->name }}</option>
                @endforeach
            </select>
            <label class="sr-only" for="product-platform">Platform</label>
            <select id="product-platform" name="platform" class="rounded-lg border-gray-300 text-sm focus:border-emerald-600 focus:ring-emerald-600">
                <option value="">Semua platform</option>
                @foreach ($platforms as $platform)
                    <option value="{{ $platform->slug }}" @selected(request('platform') === $platform->slug)>{{ $platform->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="rounded-lg bg-emerald-700 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-800 sm:col-span-4">
                Cari produk
            </button>
        </form>

        <p class="mx-auto mt-5 max-w-5xl text-xs text-gray-500">
            Kami dapat menerima komisi dari pembelian melalui tautan affiliate di halaman ini.
        </p>

        @if ($products->isEmpty())
            <p class="py-16 text-center text-gray-600">Belum ada produk yang cocok dengan pencarian ini.</p>
        @else
            <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($products as $product)
                    <article class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
                        <a href="{{ route('products.show', ['locale' => $locale, 'slug' => $product->slug]) }}" class="block">
                            <div class="aspect-square bg-gray-100">
                                @if ($product->image)
                                    <img src="{{ $product->image->getUrl('medium') }}" alt="{{ $product->title }}" class="h-full w-full object-cover">
                                @else
                                    <div class="flex h-full items-center justify-center text-sm text-gray-400">Gambar belum tersedia</div>
                                @endif
                            </div>
                            <div class="p-4">
                                <span class="text-xs font-semibold uppercase tracking-wide text-emerald-700">
                                    {{ $product->affiliatePlatform?->name ?? 'Lainnya' }}
                                </span>
                                <h2 class="mt-1 line-clamp-2 font-semibold text-gray-900">{{ $product->title }}</h2>
                                <p class="mt-1 text-xs text-gray-500">{{ $product->affiliateCategory?->name }}</p>
                                @if ($product->price)
                                    <p class="mt-3 font-bold text-gray-900">Rp{{ number_format((float) $product->price, 0, ',', '.') }}</p>
                                @endif
                            </div>
                        </a>
                    </article>
                @endforeach
            </div>
            <div class="mt-8">{{ $products->links() }}</div>
        @endif
    </main>
@endsection
