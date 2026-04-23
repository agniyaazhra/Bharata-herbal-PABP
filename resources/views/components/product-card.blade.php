@props(['product'])

<div class="card group flex flex-col overflow-hidden">
    {{-- Product Image --}}
    <a href="{{ route('product.show', $product->slug) }}" class="block relative overflow-hidden bg-gray-50 aspect-square">
        @if ($product->image)
            <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
        @else
            <div class="w-full h-full flex items-center justify-center bg-herbal-50">
                <svg class="w-16 h-16 text-herbal-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                </svg>
            </div>
        @endif

        {{-- Discount Badge --}}
        @if ($product->discount_price)
            <span class="absolute top-2 left-2 badge bg-red-100 text-red-700 font-bold">
                -{{ $product->discount_percent }}%
            </span>
        @endif

        {{-- Featured Badge --}}
        @if ($product->is_featured)
            <span class="absolute top-2 right-2 badge bg-herbal-100 text-herbal-800 font-semibold">
                🌿 Unggulan
            </span>
        @endif
    </a>

    {{-- Card Body --}}
    <div class="p-4 flex flex-col flex-1">
        {{-- Category --}}
        @if ($product->categories->isNotEmpty())
            <span class="text-xs font-medium text-herbal-600 uppercase tracking-wide">
                {{ $product->categories->first()->name }}
            </span>
        @endif

        {{-- Name --}}
        <a href="{{ route('product.show', $product->slug) }}"
           class="mt-1 font-semibold text-gray-800 hover:text-herbal-700 transition-colors line-clamp-2 leading-snug">
            {{ $product->name }}
        </a>

        {{-- Rating & Sales --}}
        <div class="flex items-center justify-between mt-2 text-xs">
            <div class="flex items-center gap-1">
                @for ($i = 1; $i <= 5; $i++)
                    <svg class="w-3.5 h-3.5 {{ $i <= $product->rating ? 'text-yellow-400' : 'text-gray-200' }}" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                @endfor
                <span class="text-gray-600 font-medium">{{ number_format($product->rating, 1) }}</span>
                <span class="text-gray-400">({{ $product->rating_count }})</span>
            </div>
            <span class="badge bg-blue-100 text-blue-700 font-semibold">📊 {{ $product->sales_count }} terjual</span>
        </div>

        {{-- Price --}}
        <div class="mt-3 flex items-end gap-2">
            <span class="text-lg font-bold text-herbal-800">
                Rp {{ number_format($product->effective_price, 0, ',', '.') }}
            </span>
            @if ($product->discount_price)
                <span class="text-sm text-gray-400 line-through">
                    Rp {{ number_format($product->price, 0, ',', '.') }}
                </span>
            @endif
        </div>

        {{-- Stock indicator --}}
        <p class="text-xs text-gray-400 mt-1">Stok: {{ $product->stock }} tersedia</p>

        {{-- Add to Cart --}}
        <div class="mt-4">
            @auth
                @if (!auth()->user()->isAdmin())
                    <form action="{{ route('cart.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit"
                            class="w-full btn-primary justify-center py-2 px-4 text-sm {{ $product->stock == 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                            {{ $product->stock == 0 ? 'disabled' : '' }}>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            {{ $product->stock == 0 ? 'Stok Habis' : 'Tambah ke Keranjang' }}
                        </button>
                    </form>
                @else
                    <a href="{{ route('product.show', $product->slug) }}" class="w-full btn-secondary justify-center py-2 px-4 text-sm">
                        Lihat Detail
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}" class="w-full btn-primary justify-center py-2 px-4 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    Tambah ke Keranjang
                </a>
            @endauth
        </div>
    </div>
</div>
