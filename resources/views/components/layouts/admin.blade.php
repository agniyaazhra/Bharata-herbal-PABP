<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin' }} — Bharata Herbal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <script src="//unpkg.com/alpinejs" defer></script>
</head>

<body class="bg-gray-50 font-sans antialiased">

    <div class="flex h-screen overflow-hidden" x-data="{
    sidebarOpen: false,
    menu: '{{ request()->routeIs('admin.settings.*') || request()->routeIs('admin.profile') || request()->routeIs('admin.security*') || request()->routeIs('admin.admins*') ? 'settings' : 'main' }}'
}">

        {{-- ========== SIDEBAR ========== --}}
        <aside class="w-64 min-h-screen bg-gradient-to-b from-[#0f2f1f] to-[#071d13] text-white flex flex-col overflow-hidden">

            {{-- ================= MAIN MENU ================= --}}
            <div x-show="menu === 'main'"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="-translate-x-full opacity-0"
                x-transition:enter-end="translate-x-0 opacity-100"
                class="flex flex-col h-full">

                {{-- LOGO --}}
                <div class="p-5 flex items-center gap-3 border-b border-white/10">
                    <i data-lucide="leaf" class="w-6 h-6 text-green-400"></i>
                    <h1 class="font-semibold text-lg tracking-wide">Bharata Herbal</h1>
                </div>

                {{-- MENU --}}
                <nav class="flex-1 px-3 py-4 space-y-1 text-sm">

                    <a href="{{ route('admin.dashboard') }}"
                        class="flex items-center gap-3 px-3 py-2.5 transition
               {{ request()->routeIs('admin.dashboard') ? 'bg-green-800/60 border-l-4 border-green-400' : 'hover:bg-white/5' }}">
                        <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                        Dashboard
                    </a>

                    <a href="{{ route('admin.products.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 transition
               {{ request()->routeIs('admin.products*') ? 'bg-green-800/60 border-l-4 border-green-400' : 'hover:bg-white/5' }}">
                        <i data-lucide="box" class="w-5 h-5"></i>
                        Manajemen Produk
                    </a>

                    <a href="{{ route('admin.categories.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 transition
               {{ request()->routeIs('admin.categories*') ? 'bg-green-800/60 border-l-4 border-green-400' : 'hover:bg-white/5' }}">
                        <i data-lucide="tag" class="w-5 h-5"></i>
                        Manajemen Kategori
                    </a>

                    <a href="{{ route('admin.orders.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 transition
               {{ request()->routeIs('admin.orders*') ? 'bg-green-800/60 border-l-4 border-green-400' : 'hover:bg-white/5' }}">
                        <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                        Manajemen Pesanan
                    </a>

                    <a href="{{ route('admin.customers.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 transition
               {{ request()->routeIs('admin.customers*') ? 'bg-green-800/60 border-l-4 border-green-400' : 'hover:bg-white/5' }}">
                        <i data-lucide="users" class="w-5 h-5"></i>
                        Manajemen Pelanggan
                    </a>

                    <a href="{{ route('admin.reports.index') }}"
                        class="flex items-center gap-3 px-3 py-2.5 transition
               {{ request()->routeIs('admin.reports*') ? 'bg-green-800/60 border-l-4 border-green-400' : 'hover:bg-white/5' }}">
                        <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
                        Laporan Penjualan
                    </a>

                </nav>

                {{-- BOTTOM --}}
                <div class="p-3 border-t border-white/10 space-y-1 text-sm">

                    {{-- PENGATURAN (SWITCH MENU) --}}
                    <a href="#" @click.prevent="menu = 'settings'"
                        class="flex items-center gap-3 px-3 py-2 hover:bg-white/5">
                        <i data-lucide="settings" class="w-5 h-5"></i>
                        Pengaturan
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="w-full flex items-center gap-3 px-3 py-2 hover:bg-red-500/20 text-red-300">
                            <i data-lucide="log-out" class="w-5 h-5"></i>
                            Logout
                        </button>
                    </form>

                </div>

            </div>

            {{-- ================= SETTINGS MENU (COMPONENT) ================= --}}
            <x-settings-sidebar />

        </aside>
        {{-- Overlay for mobile --}}
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 z-40 md:hidden"></div>

        {{-- ========== MAIN CONTENT ========== --}}
        <div class="flex-1 flex flex-col overflow-hidden">

            {{-- HEADER --}}
            <div class="px-6 pt-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">
                            {{ $title ?? 'Dashboard' }}
                        </h1>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ $subtitle ?? '' }}
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <div class="relative" x-data="{ open: false }" @click.away="open = false">
                            <button @click="open = !open"
                                class="flex items-center gap-2 bg-white border border-gray-200 rounded-full px-4 py-2 shadow-sm hover:bg-gray-50 transition">
                                <div class="w-8 h-8 rounded-full bg-green-800 flex items-center justify-center text-white text-sm font-bold">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                                </div>
                                <span class="text-sm font-medium text-gray-700">Admin</span>
                                <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400"></i>
                            </button>

                            <div x-show="open" x-transition
                                class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1 text-sm z-50">
                                <a href="{{ route('home') }}"
                                    class="flex items-center gap-2 px-4 py-2 hover:bg-herbal-50 text-gray-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 7h18M5 7l1 11a2 2 0 002 2h8a2 2 0 002-2l1-11M9 7V5a3 3 0 016 0v2" />
                                    </svg>
                                    Toko
                                </a>
                                <div class="border-t border-gray-100 mt-1">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="w-full flex items-center gap-2 px-4 py-2 hover:bg-red-50 text-red-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                            </svg>
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- FLASH --}}
            @if (session('success'))
            <div class="mx-6 mt-4 bg-green-50 border border-green-200 text-green-800 text-sm px-4 py-3 rounded-xl">
                {{ session('success') }}
            </div>
            @endif

            {{-- CONTENT --}}
            <main class="flex-1 overflow-y-auto px-6 pb-6">
                {{ $slot }}
            </main>

        </div>




    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof lucide !== 'undefined') lucide.createIcons();
        });
    </script>
    @stack('scripts')
</body>

</html>
