<x-app-layout>
    <x-slot name="title">Toko Produk</x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        {{-- Page Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Semua Produk</h1>
            <p class="text-gray-500 mt-1">{{ $selectedCategory ? 'Kategori: ' . $selectedCategory->name : 'Temukan produk herbal pilihan Anda' }}</p>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">

            {{-- ===== SIDEBAR ===== --}}
            <aside class="lg:w-64 shrink-0">
                <div class="card p-5 sticky top-24">
                    <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4 text-herbal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        Filter Kategori
                    </h3>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('shop') }}" class="flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium {{ !request('category') ? 'bg-herbal-100 text-herbal-800' : 'text-gray-600 hover:bg-gray-100' }} transition-colors">
                                <span>Semua Produk</span>
                                <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">{{ $categories->sum('products_count') }}</span>
                            </a>
                        </li>
                        @foreach ($categories as $cat)
                        <li>
                            <a href="{{ route('shop') }}?category={{ $cat->slug }}&search={{ request('search') }}"
                               class="flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium {{ request('category') == $cat->slug ? 'bg-herbal-100 text-herbal-800' : 'text-gray-600 hover:bg-gray-100' }} transition-colors">
                                <span>{{ $cat->name }}</span>
                                <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">{{ $cat->products_count }}</span>
                            </a>
                        </li>
                        @endforeach
                    </ul>

                    <hr class="my-5 border-gray-100">
                    <h3 class="font-semibold text-gray-800 mb-3 text-sm">Urutkan</h3>
                    <div class="space-y-1 text-sm">
                        @foreach (['Terbaru' => '', 'Harga Terendah' => 'price_asc', 'Harga Tertinggi' => 'price_desc', 'Rating Tertinggi' => 'rating'] as $label => $val)
                        <a href="{{ route('shop') }}?{{ http_build_query(array_merge(request()->query(), ['sort' => $val])) }}"
                           class="block px-3 py-2 rounded-lg {{ request('sort') == $val ? 'bg-herbal-100 text-herbal-800 font-medium' : 'text-gray-600 hover:bg-gray-100' }} transition-colors">
                            {{ $label }}
                        </a>
                        @endforeach
                    </div>
                </div>
            </aside>

            {{-- ===== PRODUCT GRID ===== --}}
            <div class="flex-1">
                {{-- Search Bar --}}
                <form method="GET" action="{{ route('shop') }}" class="mb-6">
                    @if (request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    @if (request('sort'))
                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                    @endif
                    <div class="flex gap-3">
                        <div class="relative flex-1">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="Cari produk herbal..."
                                   class="form-input pl-10 py-3">
                        </div>
                        <button type="submit" class="btn-primary px-6 py-3 text-sm">Cari</button>
                    </div>
                </form>

                {{-- Results info --}}
                <div class="flex items-center justify-between mb-5 text-sm text-gray-500">
                    <span>Menampilkan <strong class="text-gray-800">{{ $products->total() }}</strong> produk</span>
                    @if (request('search') || request('category'))
                        <a href="{{ route('shop') }}" class="text-herbal-600 hover:underline">Reset Filter</a>
                    @endif
                </div>

                @if ($products->isEmpty())
                    <div class="text-center py-24 text-gray-400">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <h3 class="text-lg font-semibold text-gray-600">Produk tidak ditemukan</h3>
                        <p class="mt-1 text-sm">Coba kata kunci lain atau reset filter.</p>
                        <a href="{{ route('shop') }}" class="btn-primary mt-6 text-sm py-2 px-5">Lihat Semua Produk</a>
                    </div>
                @else
                    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-5">
                        @foreach ($products as $product)
                            <x-product-card :product="$product" />
                        @endforeach
                    </div>
                    <div class="mt-10">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
