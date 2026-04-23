<x-app-layout>
    <x-slot name="title">Beranda</x-slot>

    {{-- ============ HERO ============ --}}
    <section class="relative bg-gradient-to-br from-herbal-900 via-herbal-800 to-herbal-700 text-white overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" viewBox="0 0 800 600" fill="none">
                <circle cx="700" cy="100" r="300" fill="white"/>
                <circle cx="100" cy="500" r="200" fill="white"/>
            </svg>
        </div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-28 md:py-36">
            <div class="max-w-2xl">
                <span class="inline-flex items-center gap-2 bg-herbal-700 border border-herbal-600 text-herbal-200 text-xs font-semibold px-3 py-1.5 rounded-full mb-6">
                    🌿 100% Alami & Terpercaya
                </span>
                <h1 class="text-4xl md:text-6xl font-extrabold leading-tight tracking-tight">
                    Produk Alami dari<br>
                    <span class="text-herbal-300">Alam Nusantara</span>
                </h1>
                <p class="mt-6 text-lg text-herbal-200 leading-relaxed max-w-xl">
                    Temukan rangkaian produk herbal premium yang diformulasikan dari bahan-bahan alami terbaik pilihan nusantara untuk menjaga kesehatan dan vitalitas Anda.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 mt-10">
                    <a href="{{ route('shop') }}"
                       class="inline-flex items-center justify-center gap-2 bg-white text-herbal-800 font-bold px-8 py-4 rounded-xl hover:bg-herbal-50 transition-all shadow-lg hover:shadow-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        Belanja Sekarang
                    </a>
                    <a href="{{ route('about') }}"
                       class="inline-flex items-center justify-center gap-2 border-2 border-herbal-500 text-white font-semibold px-8 py-4 rounded-xl hover:bg-herbal-700 transition-all">
                        Tentang Kami
                    </a>
                </div>
            </div>
        </div>
        {{-- Wave --}}
        <div class="absolute bottom-0 left-0 right-0">
            <svg viewBox="0 0 1440 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M0 60L1440 60L1440 20C1200 60 800 0 480 30C160 60 0 20 0 20V60Z" fill="white"/>
            </svg>
        </div>
    </section>

    {{-- ============ TRUST STATS ============ --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-1 py-16">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-8">
            <div class="text-center p-8 card bg-herbal-50 border-herbal-100">
                <div class="text-4xl font-extrabold text-herbal-800">10+</div>
                <div class="text-gray-600 font-medium mt-2">Produk Herbal</div>
                <div class="text-sm text-gray-400 mt-1">Berkualitas premium</div>
            </div>
            <div class="text-center p-8 card bg-herbal-50 border-herbal-100">
                <div class="text-4xl font-extrabold text-herbal-800">6.000+</div>
                <div class="text-gray-600 font-medium mt-2">Pelanggan Puas</div>
                <div class="text-sm text-gray-400 mt-1">Di seluruh Indonesia</div>
            </div>
            <div class="text-center p-8 card bg-herbal-50 border-herbal-100">
                <div class="text-4xl font-extrabold text-herbal-800">4.8 ⭐</div>
                <div class="text-gray-600 font-medium mt-2">Rating Rata-rata</div>
                <div class="text-sm text-gray-400 mt-1">Dari pelanggan kami</div>
            </div>
        </div>
    </section>

    {{-- ============ PRODUK TERBARU ============ --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="flex items-end justify-between mb-10">
            <div>
                <h2 class="section-title">Produk Terbaru</h2>
                <p class="section-subtitle">Temukan produk herbal terbaru kami</p>
            </div>
            <a href="{{ route('shop') }}" class="btn-outline text-sm py-2 px-4 hidden sm:inline-flex">
                Lihat Semua →
            </a>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
            @forelse ($featuredProducts as $product)
                <x-product-card :product="$product" />
            @empty
                <div class="col-span-4 text-center py-16 text-gray-400">
                    <p>Belum ada produk tersedia.</p>
                </div>
            @endforelse
        </div>
        <div class="text-center mt-8 sm:hidden">
            <a href="{{ route('shop') }}" class="btn-outline">Lihat Semua Produk →</a>
        </div>
    </section>

    {{-- ============ PRODUK TERLARIS ============ --}}
    @if ($bestsellerProducts->isNotEmpty())
    <section class="bg-herbal-50 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between mb-10">
                <div>
                    <h2 class="section-title">🔥 Produk Terlaris</h2>
                    <p class="section-subtitle">Paling banyak dibeli pelanggan kami</p>
                </div>
                <a href="{{ route('shop') }}?sort=rating" class="btn-outline text-sm py-2 px-4 hidden sm:inline-flex">
                    Lihat Semua →
                </a>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                @foreach ($bestsellerProducts as $product)
                    <x-product-card :product="$product" />
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ============ KATEGORI ============ --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center mb-12">
            <h2 class="section-title">Kategori Produk</h2>
            <p class="section-subtitle">Pilih kategori sesuai kebutuhan kesehatan Anda</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            @php
                $categoryIcons = [
                    'pencernaan' => ['icon' => '🫁', 'desc' => 'Solusi alami untuk sistem pencernaan yang sehat'],
                    'persendian' => ['icon' => '💪', 'desc' => 'Jaga fleksibilitas dan kekuatan sendi Anda'],
                    'ginjal'     => ['icon' => '💧', 'desc' => 'Dukung fungsi ginjal yang optimal'],
                ];
            @endphp
            @forelse ($categories as $category)
                @php $info = $categoryIcons[$category->slug] ?? ['icon' => '🌿', 'desc' => $category->description ?? ''] @endphp
                <a href="{{ route('shop') }}?category={{ $category->slug }}"
                   class="group card p-8 text-center hover:border-herbal-300 hover:bg-herbal-50 transition-all">
                    <div class="text-5xl mb-4">{{ $info['icon'] }}</div>
                    <h3 class="text-xl font-bold text-gray-800 group-hover:text-herbal-700 transition-colors">{{ $category->name }}</h3>
                    <p class="text-sm text-gray-500 mt-2">{{ $info['desc'] }}</p>
                    <p class="text-xs text-herbal-600 font-semibold mt-3">{{ $category->products_count }} Produk →</p>
                </a>
            @empty
                <div class="col-span-3 text-center text-gray-400 py-10">Kategori belum tersedia.</div>
            @endforelse
        </div>
    </section>

    {{-- ============ TESTIMONI ============ --}}
    @if ($testimonials->isNotEmpty())
    <section class="bg-gradient-to-br from-herbal-900 to-herbal-800 py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-white">Apa Kata Pelanggan Kami?</h2>
                <p class="text-herbal-300 mt-2">Ribuan pelanggan telah merasakan manfaatnya</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($testimonials as $review)
                <div class="bg-white/10 backdrop-blur-sm border border-white/10 rounded-2xl p-6">
                    <div class="flex items-center gap-1 mb-4">
                        @for ($i = 1; $i <= 5; $i++)
                            <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-white/20' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        @endfor
                    </div>
                    <p class="text-white/90 text-sm leading-relaxed italic">"{{ $review->comment }}"</p>
                    <div class="flex items-center gap-3 mt-5">
                        <div class="w-10 h-10 bg-herbal-600 rounded-full flex items-center justify-center text-white font-bold text-sm">
                            {{ strtoupper(substr($review->reviewer_name ?? $review->user->name, 0, 2)) }}
                        </div>
                        <div>
                            <p class="text-white font-semibold text-sm">{{ $review->reviewer_name ?? $review->user->name }}</p>
                            @if ($review->reviewer_title)
                                <p class="text-herbal-300 text-xs">{{ $review->reviewer_title }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ============ CTA ============ --}}
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
        <div class="bg-gradient-to-r from-herbal-700 to-herbal-600 rounded-3xl p-12 text-center text-white shadow-2xl">
            <span class="text-4xl mb-4 block">🌿</span>
            <h2 class="text-3xl md:text-4xl font-extrabold">Siap Memulai Hidup Lebih Sehat?</h2>
            <p class="mt-4 text-herbal-100 text-lg max-w-xl mx-auto">
                Bergabung dengan ribuan pelanggan yang telah merasakan manfaat nyata dari produk herbal alami Bharata Herbal.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 mt-10 justify-center">
                <a href="{{ route('shop') }}"
                   class="inline-flex items-center justify-center gap-2 bg-white text-herbal-800 font-bold px-8 py-4 rounded-xl hover:bg-herbal-50 transition-all shadow-lg">
                    Mulai Belanja Sekarang
                </a>
                @guest
                <a href="{{ route('register') }}"
                   class="inline-flex items-center justify-center gap-2 border-2 border-white text-white font-semibold px-8 py-4 rounded-xl hover:bg-herbal-600 transition-all">
                    Daftar Gratis
                </a>
                @endguest
            </div>
        </div>
    </section>

</x-app-layout>
