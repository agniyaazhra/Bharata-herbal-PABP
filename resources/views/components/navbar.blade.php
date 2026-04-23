<nav class="bg-white border-b border-gray-100 sticky top-0 z-40 shadow-sm" x-data="{ mobileOpen: false }">
    @php
    $storeName = \App\Models\Setting::get('store', 'store_name', 'Bharata Herbal');
    $brandParts = preg_split('/\s+/', trim($storeName), 2);
    @endphp
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2 shrink-0">
                <div class="w-9 h-9 bg-herbal-800 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                </div>
                <span class="font-bold text-herbal-800 text-lg tracking-tight">{{ $brandParts[0] ?? 'Bharata' }}<span class="text-herbal-500">{{ isset($brandParts[1]) ? ' ' . $brandParts[1] : '' }}</span></span>
            </a>

            {{-- Desktop Nav --}}
            <div class="hidden md:flex items-center gap-8">
                <a href="{{ route('home') }}" class="text-sm font-medium {{ request()->routeIs('home') ? 'text-herbal-700' : 'text-gray-600 hover:text-herbal-700' }} transition-colors">Beranda</a>
                <a href="{{ route('shop') }}" class="text-sm font-medium {{ request()->routeIs('shop') ? 'text-herbal-700' : 'text-gray-600 hover:text-herbal-700' }} transition-colors">Produk</a>
                <a href="{{ route('about') }}" class="text-sm font-medium {{ request()->routeIs('about') ? 'text-herbal-700' : 'text-gray-600 hover:text-herbal-700' }} transition-colors">Tentang</a>
                <a href="{{ route('contact') }}" class="text-sm font-medium {{ request()->routeIs('contact') ? 'text-herbal-700' : 'text-gray-600 hover:text-herbal-700' }} transition-colors">Kontak</a>
            </div>

            {{-- Right side --}}
            <div class="flex items-center gap-3">
                @auth
                {{-- Cart Icon --}}
                @if (!auth()->user()->isAdmin())
                @php
                $cartCount = auth()->user()->cart?->total_items ?? 0;
                @endphp
                <a href="{{ route('cart.index') }}" class="relative p-2 text-gray-600 hover:text-herbal-700 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    @if ($cartCount > 0)
                    <span class="absolute -top-1 -right-1 bg-herbal-600 text-white text-xs w-5 h-5 rounded-full flex items-center justify-center font-bold">{{ $cartCount }}</span>
                    @endif
                </a>
                @endif

                {{-- User Dropdown --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center gap-2 text-sm font-medium text-gray-700 hover:text-herbal-700 transition-colors">
                        <div class="w-8 h-8 bg-herbal-100 rounded-full flex items-center justify-center">
                            <span class="text-herbal-700 font-semibold text-xs">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                        </div>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="open" @click.away="open = false" x-transition
                        class="absolute right-0 mt-2 w-52 bg-white rounded-xl shadow-lg border border-gray-100 py-1 text-sm">
                        <div class="px-4 py-2 border-b border-gray-100">
                            <p class="font-semibold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                        </div>
                        @if (auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-4 py-2 hover:bg-herbal-50 text-gray-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zm10 0a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                            Panel Admin
                        </a>
                        @else
                        <a href="{{ route('user.profile') }}" class="flex items-center gap-2 px-4 py-2 hover:bg-herbal-50 text-gray-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Profil Saya
                        </a>
                        <a href="{{ route('orders.index') }}" class="flex items-center gap-2 px-4 py-2 hover:bg-herbal-50 text-gray-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            Pesanan Saya
                        </a>
                        @endif
                        <div class="border-t border-gray-100 mt-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2 px-4 py-2 hover:bg-red-50 text-red-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @else
                <a href="{{ route('login') }}" class="hidden md:inline-flex items-center gap-1.5 text-sm font-medium text-gray-600 hover:text-herbal-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    Masuk
                </a>
                <a href="{{ route('shop') }}" class="btn-primary text-sm py-2 px-4">Belanja</a>
                @endauth

                {{-- Mobile menu button --}}
                <button @click="mobileOpen = !mobileOpen" class="md:hidden p-2 text-gray-600 hover:text-herbal-700">
                    <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg x-show="mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div x-show="mobileOpen" x-transition class="md:hidden pb-4 border-t border-gray-100 pt-3 space-y-1">
            <a href="{{ route('home') }}" class="block px-3 py-2 text-sm font-medium text-gray-700 hover:bg-herbal-50 rounded-lg">Beranda</a>
            <a href="{{ route('shop') }}" class="block px-3 py-2 text-sm font-medium text-gray-700 hover:bg-herbal-50 rounded-lg">Produk</a>
            <a href="{{ route('about') }}" class="block px-3 py-2 text-sm font-medium text-gray-700 hover:bg-herbal-50 rounded-lg">Tentang</a>
            <a href="{{ route('contact') }}" class="block px-3 py-2 text-sm font-medium text-gray-700 hover:bg-herbal-50 rounded-lg">Kontak</a>
            @guest
            <a href="{{ route('login') }}" class="block px-3 py-2 text-sm font-medium text-herbal-700 hover:bg-herbal-50 rounded-lg">Masuk</a>
            <a href="{{ route('register') }}" class="block px-3 py-2 text-sm font-medium text-herbal-700 hover:bg-herbal-50 rounded-lg">Daftar</a>
            @endguest
        </div>
    </div>
</nav>