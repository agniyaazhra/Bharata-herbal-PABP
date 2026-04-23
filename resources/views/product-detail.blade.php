<x-app-layout>
    <x-slot name="title">{{ $product->name }}</x-slot>

    <x-breadcrumb :crumbs="array_filter([
        ['label' => 'Beranda', 'url' => route('home')],
        ['label' => 'Produk', 'url' => route('shop')],
        $product->categories->isNotEmpty()
            ? ['label' => $product->categories->first()->name, 'url' => route('shop', ['category' => $product->categories->first()->slug])]
            : null,
        ['label' => $product->name],
    ])" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        {{-- Product Top Section --}}
        <div class="flex flex-col lg:flex-row gap-12">

            {{-- Image --}}
            <div class="lg:w-5/12">
                <div class="aspect-square rounded-2xl overflow-hidden bg-herbal-50 border border-gray-100">
                    @if ($product->image)
                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <svg class="w-24 h-24 text-herbal-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                            </svg>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Details --}}
            <div class="lg:w-7/12" x-data="{ qty: 1 }">
                {{-- Categories --}}
                <div class="flex gap-2 flex-wrap mb-3">
                    @foreach ($product->categories as $cat)
                        <a href="{{ route('shop') }}?category={{ $cat->slug }}"
                           class="badge bg-herbal-100 text-herbal-700 font-semibold hover:bg-herbal-200">{{ $cat->name }}</a>
                    @endforeach
                </div>

                <h1 class="text-3xl font-bold text-gray-900">{{ $product->name }}</h1>

                {{-- Rating --}}
                <div class="flex items-center gap-3 mt-3">
                    <div class="flex">
                        @for ($i = 1; $i <= 5; $i++)
                            <svg class="w-5 h-5 {{ $i <= $product->rating ? 'text-yellow-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-600 font-semibold">{{ number_format($product->rating, 1) }}/5.0</span>
                            <span class="text-xs text-gray-500">({{ $product->rating_count }} ulasan)</span>
                        </div>
                        <div class="flex items-center gap-2 mt-1">
                            <svg class="w-4 h-4 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                            </svg>
                            <span class="text-xs font-semibold text-blue-600">{{ $product->sales_count }} orang telah membeli</span>
                        </div>
                    </div>
                </div>

                {{-- Price --}}
                <div class="mt-5 p-5 bg-herbal-50 rounded-xl">
                    <div class="flex items-end gap-3">
                        <span class="text-4xl font-extrabold text-herbal-800">
                            Rp {{ number_format($product->effective_price, 0, ',', '.') }}
                        </span>
                        @if ($product->discount_price)
                            <span class="text-lg text-gray-400 line-through">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                            <span class="badge bg-red-100 text-red-700 font-bold text-sm">Hemat {{ $product->discount_percent }}%</span>
                        @endif
                    </div>
                </div>

                {{-- Stock --}}
                <div class="flex items-center gap-2 mt-4">
                    <div class="w-2 h-2 rounded-full {{ $product->stock > 10 ? 'bg-green-500' : ($product->stock > 0 ? 'bg-yellow-500' : 'bg-red-500') }}"></div>
                    <span class="text-sm text-gray-600">
                        @if ($product->stock > 10) Stok tersedia ({{ $product->stock }} unit)
                        @elseif ($product->stock > 0) Stok terbatas ({{ $product->stock }} unit)
                        @else Stok habis
                        @endif
                    </span>
                </div>

                @if ($product->stock > 0)
                {{-- Quantity Selector + Actions --}}
                <div class="mt-6 space-y-4">
                    <div class="flex items-center gap-4">
                        <label class="form-label mb-0">Jumlah:</label>
                        <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden">
                            <button @click="if(qty > 1) qty--" type="button"
                                    class="px-4 py-2.5 text-gray-600 hover:bg-gray-100 transition-colors font-bold text-lg">−</button>
                            <input x-model="qty" type="number" min="1" max="{{ $product->stock }}"
                                   class="w-16 text-center border-0 border-x border-gray-300 py-2.5 font-semibold focus:ring-0">
                            <button @click="if(qty < {{ $product->stock }}) qty++" type="button"
                                    class="px-4 py-2.5 text-gray-600 hover:bg-gray-100 transition-colors font-bold text-lg">+</button>
                        </div>
                    </div>

                    @auth
                        @if (!auth()->user()->isAdmin())
                        <div class="flex gap-3">
                            <form action="{{ route('cart.add') }}" method="POST" class="flex-1">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="quantity" :value="qty" x-bind:value="qty">
                                <button type="submit" class="w-full btn-outline justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                                    Keranjang
                                </button>
                            </form>
                            <form action="{{ route('cart.add') }}" method="POST" class="flex-1">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="quantity" :value="qty" x-bind:value="qty">
                                <input type="hidden" name="buy_now" value="1">
                                <button type="submit" class="w-full btn-primary justify-center">
                                    ⚡ Beli Sekarang
                                </button>
                            </form>
                        </div>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="w-full btn-primary justify-center">Masuk untuk Membeli</a>
                    @endauth
                </div>
                @else
                    <div class="mt-6 p-4 bg-red-50 border border-red-100 rounded-xl text-red-700 text-sm font-medium">
                        Maaf, produk ini sedang habis stok.
                    </div>
                @endif
            </div>
        </div>

        {{-- ===== PRODUCT DETAIL SECTIONS ===== --}}
        <div class="mt-12 space-y-6">

            @if ($product->usage)
            <div class="card p-6">
                <h3 class="text-lg font-bold text-herbal-800 mb-3 flex items-center gap-2">
                    <span class="w-8 h-8 bg-herbal-100 text-herbal-700 rounded-full flex items-center justify-center text-sm">📋</span>
                    Cara Penggunaan
                </h3>
                <div class="prose max-w-none text-gray-700 leading-relaxed text-sm">
                    {!! nl2br(e($product->usage)) !!}
                </div>
            </div>
            @endif

            @if ($product->benefits)
            <div class="card p-6">
                <h3 class="text-lg font-bold text-herbal-800 mb-3 flex items-center gap-2">
                    <span class="w-8 h-8 bg-green-100 text-green-700 rounded-full flex items-center justify-center text-sm">✅</span>
                    Berkhasiat Untuk
                </h3>
                <div class="prose max-w-none text-gray-700 leading-relaxed text-sm">
                    {!! nl2br(e($product->benefits)) !!}
                </div>
            </div>
            @endif

            @if ($product->composition)
            <div class="card p-6">
                <h3 class="text-lg font-bold text-herbal-800 mb-3 flex items-center gap-2">
                    <span class="w-8 h-8 bg-blue-100 text-blue-700 rounded-full flex items-center justify-center text-sm">🧪</span>
                    Komposisi Produk
                </h3>
                <div class="prose max-w-none text-gray-700 leading-relaxed text-sm">
                    {!! nl2br(e($product->composition)) !!}
                </div>
            </div>
            @endif

            @if ($product->description)
            <div class="card p-6">
                <h3 class="text-lg font-bold text-herbal-800 mb-3 flex items-center gap-2">
                    <span class="w-8 h-8 bg-amber-100 text-amber-700 rounded-full flex items-center justify-center text-sm">📄</span>
                    Deskripsi Produk
                </h3>
                <div class="prose max-w-none text-gray-700 leading-relaxed text-sm">
                    {!! nl2br(e($product->description)) !!}
                </div>
            </div>
            @endif

        </div>

        {{-- ===== REVIEWS ===== --}}
        @php
            $reviews = $product->reviews->sortByDesc('created_at')->values();
            $totalReviews = $reviews->count();
            $avgRating = $totalReviews > 0 ? round((float) $reviews->avg('rating'), 1) : 0;
            $distribution = collect([5, 4, 3, 2, 1])->mapWithKeys(function ($star) use ($reviews, $totalReviews) {
                $count = $reviews->where('rating', $star)->count();
                $percent = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
                return [$star => ['count' => $count, 'percent' => $percent]];
            });
        @endphp
        <section class="mt-12">
            <div class="card p-6">
                <div class="flex flex-col lg:flex-row gap-6">
                    <div class="lg:w-64 shrink-0">
                        <p class="text-sm text-gray-500">Rating Produk</p>
                        <p class="text-4xl font-extrabold text-herbal-800 mt-1">{{ number_format($avgRating, 1) }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ $totalReviews }} ulasan</p>
                    </div>
                    <div class="flex-1 space-y-2">
                        @foreach ($distribution as $star => $row)
                            <div class="flex items-center gap-3 text-sm">
                                <span class="w-12 text-gray-600">{{ $star }} bintang</span>
                                <div class="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-amber-400" style="width: {{ $row['percent'] }}%"></div>
                                </div>
                                <span class="w-10 text-right text-gray-500">{{ $row['count'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-6 space-y-4">
                    @forelse ($reviews as $review)
                        <div class="border border-gray-100 rounded-xl p-4">
                            <div class="flex items-center justify-between gap-3">
                                <p class="text-sm font-semibold text-gray-800">{{ $review->reviewer_name ?? $review->user?->name ?? 'Pelanggan' }}</p>
                                <p class="text-xs text-gray-400">{{ $review->created_at->format('d M Y') }}</p>
                            </div>
                            <p class="text-amber-500 text-sm mt-1">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</p>
                            @if ($review->comment)
                                <p class="text-sm text-gray-600 mt-2 leading-relaxed">{{ $review->comment }}</p>
                            @endif
                            @if ($review->image)
                                <img src="{{ Storage::url($review->image) }}" alt="Foto ulasan" class="mt-3 w-32 h-32 object-cover rounded-lg border border-gray-100">
                            @endif
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Belum ada ulasan untuk produk ini.</p>
                    @endforelse
                </div>
            </div>
        </section>

        {{-- ===== RELATED PRODUCTS ===== --}}
        @if ($relatedProducts->isNotEmpty())
        <section class="mt-20">
            <h2 class="section-title mb-8">Produk Terkait</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                @foreach ($relatedProducts as $related)
                    <x-product-card :product="$related" />
                @endforeach
            </div>
        </section>
        @endif
    </div>
</x-app-layout>
