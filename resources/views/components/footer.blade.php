<footer class="bg-herbal-900 text-white mt-20">
    @php
        $storeName = \App\Models\Setting::get('store', 'store_name', 'Bharata Herbal');
        $storeDescription = \App\Models\Setting::get('store', 'store_description', 'Produk herbal alami berkualitas tinggi dari Nusantara untuk mendukung gaya hidup sehat Anda.');
        $storeAddress = \App\Models\Setting::get('store', 'store_address', 'Jl. Nusantara No. 1, Jakarta, Indonesia');
        $businessEmail = \App\Models\Setting::get('store', 'business_email', 'info@bharataherbal.id');
        $whatsappNumber = \App\Models\Setting::get('store', 'whatsapp_number', '+62 812-3456-7890');
        $instagram = trim((string) \App\Models\Setting::get('store', 'instagram', ''));
        $instagramUrl = $instagram !== '' ? 'https://instagram.com/' . ltrim($instagram, '@') : '#';
    @endphp
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-10">

            {{-- Brand --}}
            <div class="md:col-span-1">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-9 h-9 bg-herbal-600 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                    </div>
                    <span class="font-bold text-lg">{{ str_replace(' ', '', $storeName) }}</span>
                </div>
                <p class="text-herbal-300 text-sm leading-relaxed">
                    {{ $storeDescription }}
                </p>
                <div class="flex gap-3 mt-5">
                    <a href="#" class="w-9 h-9 bg-herbal-800 hover:bg-herbal-700 rounded-lg flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>
                    <a href="{{ $instagramUrl }}" class="w-9 h-9 bg-herbal-800 hover:bg-herbal-700 rounded-lg flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </a>
                    <a href="#" class="w-9 h-9 bg-herbal-800 hover:bg-herbal-700 rounded-lg flex items-center justify-center transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 01-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 01-2.88 2.5 2.89 2.89 0 01-2.89-2.89 2.89 2.89 0 012.89-2.89c.28 0 .54.04.79.1V9.01a6.3 6.3 0 00-.79-.05 6.34 6.34 0 00-6.34 6.34 6.34 6.34 0 006.34 6.34 6.34 6.34 0 006.33-6.34V8.69a8.18 8.18 0 004.78 1.52V6.76a4.85 4.85 0 01-1.01-.07z"/></svg>
                    </a>
                </div>
            </div>

            <div>
                <h4 class="font-semibold text-white mb-4">Produk</h4>
                <ul class="space-y-2 text-sm text-herbal-300">
                    <li><a href="{{ route('shop') }}?category=pencernaan" class="hover:text-white transition-colors">Pencernaan</a></li>
                    <li><a href="{{ route('shop') }}?category=persendian" class="hover:text-white transition-colors">Persendian</a></li>
                    <li><a href="{{ route('shop') }}?category=ginjal" class="hover:text-white transition-colors">Ginjal</a></li>
                    <li><a href="{{ route('shop') }}" class="hover:text-white transition-colors">Semua Produk</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-semibold text-white mb-4">Informasi</h4>
                <ul class="space-y-2 text-sm text-herbal-300">
                    <li><a href="{{ route('about') }}" class="hover:text-white transition-colors">Tentang Kami</a></li>
                    <li><a href="{{ route('contact') }}" class="hover:text-white transition-colors">Hubungi Kami</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Kebijakan Privasi</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Syarat & Ketentuan</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-semibold text-white mb-4">Kontak</h4>
                <ul class="space-y-3 text-sm text-herbal-300">
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ $storeAddress }}
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        {{ $businessEmail }}
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        {{ $whatsappNumber }}
                    </li>
                </ul>
            </div>
        </div>

        <div class="border-t border-herbal-800 mt-12 pt-6 text-center text-herbal-400 text-sm">
            <p>&copy; {{ date('Y') }} {{ $storeName }}. Seluruh hak cipta dilindungi.</p>
        </div>
    </div>
</footer>
