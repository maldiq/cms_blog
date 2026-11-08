<nav aria-label="Kategori affiliate" class="text-sm font-medium text-gray-700">
    <div x-data="{ open: false }" class="group relative">
        <div class="flex items-center gap-1">
            <a
                href="{{ route('products.index', ['locale' => app()->getLocale()]) }}"
                class="inline-flex items-center gap-1.5 rounded-md px-1 py-2 transition-colors hover:text-emerald-700"
            >
                Kategori Affiliate
            </a>
            <button
                type="button"
                x-on:click="open = !open"
                x-bind:aria-expanded="open.toString()"
                aria-label="Buka kategori affiliate"
                class="rounded-md p-1 text-gray-500 hover:bg-gray-100 hover:text-emerald-700 lg:hidden"
            >
                <svg class="h-4 w-4 transition-transform" x-bind:class="open ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                </svg>
            </button>
            <svg class="hidden h-4 w-4 text-gray-400 transition group-hover:rotate-180 lg:block" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
            </svg>
        </div>

        <ul class="hidden max-h-80 min-w-56 overflow-y-auto rounded-xl border border-gray-200 bg-white p-2 shadow-xl lg:invisible lg:absolute lg:left-0 lg:top-full lg:z-50 lg:mt-1 lg:block lg:translate-y-1 lg:opacity-0 lg:transition lg:duration-150 lg:group-hover:visible lg:group-hover:translate-y-0 lg:group-hover:opacity-100 lg:group-focus-within:visible lg:group-focus-within:translate-y-0 lg:group-focus-within:opacity-100">
            <li>
                <a
                    href="{{ route('products.index', ['locale' => app()->getLocale()]) }}"
                    class="block rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-emerald-700"
                >
                    Semua Produk
                </a>
            </li>
            @foreach ($categories as $category)
                <li>
                    <a
                        href="{{ route('products.index', ['locale' => app()->getLocale(), 'category' => $category->slug]) }}"
                        class="block rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-emerald-700"
                    >
                        {{ $category->name }}
                    </a>
                </li>
            @endforeach
        </ul>

        <ul
            x-cloak
            x-show="open"
            x-transition
            class="mt-1 max-h-80 min-w-56 overflow-y-auto rounded-xl border border-gray-200 bg-white p-2 shadow-xl lg:hidden"
        >
            <li>
                <a href="{{ route('products.index', ['locale' => app()->getLocale()]) }}" class="block rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-emerald-700">
                    Semua Produk
                </a>
            </li>
            @forelse ($categories as $category)
                <li>
                    <a href="{{ route('products.index', ['locale' => app()->getLocale(), 'category' => $category->slug]) }}" class="block rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-emerald-700">
                        {{ $category->name }}
                    </a>
                </li>
            @empty
                <li class="px-3 py-2 text-sm text-gray-500">Kategori akan muncul setelah produk diaktifkan.</li>
            @endforelse
        </ul>
    </div>
</nav>
