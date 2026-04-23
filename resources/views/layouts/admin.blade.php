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

@php
    $onSettingsPage = request()->routeIs('admin.settings.*')
        || request()->routeIs('admin.admins.*')
        || request()->routeIs('admin.profile*');
@endphp

<div class="flex h-screen overflow-hidden"
     x-data="{ sidebarOpen: false, menu: '{{ $onSettingsPage ? 'settings' : 'main' }}' }">

    {{-- ===================== SIDEBAR ===================== --}}
    <aside class="w-64 min-h-screen bg-gradient-to-b from-[#0f2f1f] to-[#071d13] text-white flex flex-col overflow-hidden shrink-0">

        {{-- ───── MAIN MENU ───── --}}
        <div x-show="menu === 'main'"
             x-transition:enter="transition ease-out duration-250"
             x-transition:enter-start="-translate-x-4 opacity-0"
             x-transition:enter-end="translate-x-0 opacity-100"
             class="flex flex-col h-full">

            {{-- Logo --}}
            <div class="p-5 flex items-center gap-3 border-b border-white/10">
                <i data-lucide="leaf" class="w-6 h-6 text-green-400"></i>
                <h1 class="font-semibold text-lg tracking-wide">Bharata Herbal</h1>
            </div>

            {{-- Menu utama --}}
            <nav class="flex-1 px-3 py-4 space-y-0.5 text-sm overflow-y-auto">

                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                          {{ request()->routeIs('admin.dashboard') ? 'bg-green-800/60 border-l-4 border-green-400 pl-2' : 'hover:bg-white/5' }}">
                    <i data-lucide="layout-dashboard" class="w-5 h-5 shrink-0"></i> Dashboard
                </a>

                <a href="{{ route('admin.products.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                          {{ request()->routeIs('admin.products*') ? 'bg-green-800/60 border-l-4 border-green-400 pl-2' : 'hover:bg-white/5' }}">
                    <i data-lucide="box" class="w-5 h-5 shrink-0"></i> Manajemen Produk
                </a>

                <a href="{{ route('admin.categories.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                          {{ request()->routeIs('admin.categories*') ? 'bg-green-800/60 border-l-4 border-green-400 pl-2' : 'hover:bg-white/5' }}">
                    <i data-lucide="tag" class="w-5 h-5 shrink-0"></i> Manajemen Kategori
                </a>

                <a href="{{ route('admin.orders.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                          {{ request()->routeIs('admin.orders*') ? 'bg-green-800/60 border-l-4 border-green-400 pl-2' : 'hover:bg-white/5' }}">
                    <i data-lucide="shopping-cart" class="w-5 h-5 shrink-0"></i> Manajemen Pesanan
                </a>

                <a href="{{ route('admin.customers.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                          {{ request()->routeIs('admin.customers*') ? 'bg-green-800/60 border-l-4 border-green-400 pl-2' : 'hover:bg-white/5' }}">
                    <i data-lucide="users" class="w-5 h-5 shrink-0"></i> Manajemen Pelanggan
                </a>

                <a href="{{ route('admin.reports.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                          {{ request()->routeIs('admin.reports*') ? 'bg-green-800/60 border-l-4 border-green-400 pl-2' : 'hover:bg-white/5' }}">
                    <i data-lucide="bar-chart-3" class="w-5 h-5 shrink-0"></i> Laporan Penjualan
                </a>

                {{-- Divider: Keamanan --}}
                <div class="px-3 pt-4 pb-1">
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-white/30">Keamanan</p>
                </div>

                {{-- Notifikasi + badge --}}
                @php $unreadCount = auth()->user()->unreadNotifications()->count(); @endphp
                <a href="{{ route('admin.notifications.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                          {{ request()->routeIs('admin.notifications*') ? 'bg-green-800/60 border-l-4 border-green-400 pl-2' : 'hover:bg-white/5' }}">
                    <i data-lucide="bell" class="w-5 h-5 shrink-0"></i>
                    Notifikasi
                    @if($unreadCount > 0)
                    <span class="ml-auto bg-red-500 text-white text-[10px] font-bold px-1.5 py-0.5 rounded-full leading-none">
                        {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                    </span>
                    @endif
                </a>

                <a href="{{ route('admin.sessions.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                          {{ request()->routeIs('admin.sessions*') ? 'bg-green-800/60 border-l-4 border-green-400 pl-2' : 'hover:bg-white/5' }}">
                    <i data-lucide="monitor" class="w-5 h-5 shrink-0"></i> Sesi Aktif
                </a>

            </nav>

            {{-- Bottom: Settings + Logout --}}
            <div class="p-3 border-t border-white/10 space-y-0.5 text-sm">
                <a href="#" @click.prevent="menu = 'settings'"
                   class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-white/5 transition">
                    <i data-lucide="settings" class="w-5 h-5"></i>
                    Pengaturan
                    <i data-lucide="chevron-right" class="w-4 h-4 ml-auto opacity-40"></i>
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-red-500/20 text-red-300 transition">
                        <i data-lucide="log-out" class="w-5 h-5"></i> Logout
                    </button>
                </form>
            </div>
        </div>

        {{-- ───── SETTINGS MENU ───── --}}
        <div x-show="menu === 'settings'"
             x-transition:enter="transition ease-out duration-250"
             x-transition:enter-start="translate-x-4 opacity-0"
             x-transition:enter-end="translate-x-0 opacity-100"
             class="flex flex-col h-full">

            {{-- Header --}}
            <div class="p-5 flex items-center gap-3 border-b border-white/10">
                <i data-lucide="settings" class="w-6 h-6 text-green-400"></i>
                <h1 class="font-semibold text-lg">Pengaturan</h1>
            </div>

            {{-- Kembali --}}
            <a href="#" @click.prevent="menu = 'main'"
               class="flex items-center gap-2 px-4 py-3 text-sm hover:bg-white/5 border-b border-white/10 transition">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
            </a>

            {{-- Settings nav --}}
            <nav class="flex-1 px-3 py-4 space-y-0.5 text-sm overflow-y-auto">

                <a href="{{ route('admin.profile') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                          {{ request()->routeIs('admin.profile*') ? 'bg-green-800/60 border-l-4 border-green-400 pl-2' : 'hover:bg-white/5' }}">
                    <i data-lucide="user" class="w-5 h-5 shrink-0"></i> Profil Admin
                </a>

                <div class="px-3 pt-4 pb-1">
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-white/30">Toko & Produk</p>
                </div>

                <a href="{{ route('admin.settings.store') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                          {{ request()->routeIs('admin.settings.store*') ? 'bg-green-800/60 border-l-4 border-green-400 pl-2' : 'hover:bg-white/5' }}">
                    <i data-lucide="store" class="w-5 h-5 shrink-0"></i> Pengaturan Toko
                </a>

                <a href="{{ route('admin.settings.product') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                          {{ request()->routeIs('admin.settings.product*') ? 'bg-green-800/60 border-l-4 border-green-400 pl-2' : 'hover:bg-white/5' }}">
                    <i data-lucide="package" class="w-5 h-5 shrink-0"></i> Pengaturan Produk
                </a>

                <div class="px-3 pt-4 pb-1">
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-white/30">Transaksi</p>
                </div>

                <a href="{{ route('admin.settings.payment') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                          {{ request()->routeIs('admin.settings.payment*') ? 'bg-green-800/60 border-l-4 border-green-400 pl-2' : 'hover:bg-white/5' }}">
                    <i data-lucide="credit-card" class="w-5 h-5 shrink-0"></i> Pengaturan Pembayaran
                </a>

                <a href="{{ route('admin.settings.shipping') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                          {{ request()->routeIs('admin.settings.shipping*') ? 'bg-green-800/60 border-l-4 border-green-400 pl-2' : 'hover:bg-white/5' }}">
                    <i data-lucide="truck" class="w-5 h-5 shrink-0"></i> Pengaturan Pengiriman
                </a>

                <a href="{{ route('admin.settings.notification') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                          {{ request()->routeIs('admin.settings.notification*') ? 'bg-green-800/60 border-l-4 border-green-400 pl-2' : 'hover:bg-white/5' }}">
                    <i data-lucide="bell" class="w-5 h-5 shrink-0"></i> Pengaturan Notifikasi
                </a>

                {{-- Manajemen Admin — hanya super_admin --}}
                @if(auth()->user()->isSuperAdmin())
                <div class="px-3 pt-4 pb-1">
                    <p class="text-[10px] font-semibold uppercase tracking-widest text-white/30">Keamanan</p>
                </div>

                <a href="{{ route('admin.admins.index') }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
                          {{ request()->routeIs('admin.admins*') ? 'bg-green-800/60 border-l-4 border-green-400 pl-2' : 'hover:bg-white/5' }}">
                    <i data-lucide="shield" class="w-5 h-5 shrink-0"></i>
                    Manajemen Admin
                    <span class="ml-auto text-[10px] bg-purple-600/50 border border-purple-500/30 rounded-full px-1.5 py-0.5">SA</span>
                </a>
                @endif

            </nav>
        </div>

    </aside>

    {{-- Mobile overlay --}}
    <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 z-40 md:hidden"></div>

    {{-- ===================== MAIN CONTENT ===================== --}}
    <div class="flex-1 flex flex-col overflow-hidden">

        {{-- ── TOPBAR ── --}}
        <header class="bg-white border-b border-gray-100 px-6 py-3.5 flex items-center justify-between shrink-0">
            <div>
                <h1 class="text-xl font-bold text-gray-900 tracking-tight leading-none">
                    {{ $title ?? 'Dashboard' }}
                </h1>
                @if(!empty($subtitle))
                <p class="text-xs text-gray-400 mt-1">{{ $subtitle }}</p>
                @endif
            </div>

            {{-- Right side: Bell + User --}}
            <div class="flex items-center gap-2">

                {{-- 🔔 Notification Bell --}}
                @php $bellUnread = auth()->user()->unreadNotifications()->count(); @endphp
                <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                    <button @click="open = !open"
                            id="notif-bell-btn"
                            class="relative w-9 h-9 flex items-center justify-center rounded-xl hover:bg-gray-100 transition">
                        <i data-lucide="bell" class="w-5 h-5 text-gray-500"></i>
                        @if($bellUnread > 0)
                        <span id="bell-badge"
                              class="absolute -top-0.5 -right-0.5 min-w-[1rem] h-4 bg-red-500 rounded-full
                                     flex items-center justify-center text-[9px] font-bold text-white px-0.5">
                            {{ $bellUnread > 9 ? '9+' : $bellUnread }}
                        </span>
                        @endif
                    </button>

                    {{-- Notif Dropdown --}}
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         class="absolute right-0 top-11 w-80 bg-white rounded-2xl shadow-2xl border border-gray-100 z-50 overflow-hidden">

                        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                            <p class="font-semibold text-sm text-gray-800">Notifikasi Stok</p>
                            @if($bellUnread > 0)
                            <form action="{{ route('admin.notifications.read-all') }}" method="POST">
                                @csrf
                                <button type="submit" class="text-xs text-blue-600 hover:text-blue-800 transition">
                                    Tandai semua dibaca
                                </button>
                            </form>
                            @endif
                        </div>

                        <div class="divide-y divide-gray-50 max-h-72 overflow-y-auto">
                            @forelse(auth()->user()->notifications()->latest()->limit(8)->get() as $notif)
                            <div class="px-4 py-3 hover:bg-gray-50 transition {{ !$notif->is_read ? 'bg-amber-50/30' : '' }}">
                                <div class="flex items-start gap-2.5">
                                    <span class="text-sm mt-0.5 shrink-0">
                                        {{ $notif->type === 'danger' ? '🔴' : ($notif->type === 'warning' ? '⚠️' : 'ℹ️') }}
                                    </span>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-semibold text-gray-800 leading-none">{{ $notif->title }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5 line-clamp-2">{{ $notif->message }}</p>
                                        <p class="text-[10px] text-gray-400 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                                    </div>
                                    @if(!$notif->is_read)
                                    <span class="w-2 h-2 bg-blue-500 rounded-full shrink-0 mt-1"></span>
                                    @endif
                                </div>
                            </div>
                            @empty
                            <div class="px-4 py-10 text-center text-gray-400 text-sm">
                                <i data-lucide="bell-off" class="w-7 h-7 mx-auto mb-2 opacity-30"></i>
                                <p>Tidak ada notifikasi</p>
                            </div>
                            @endforelse
                        </div>

                        <div class="px-4 py-2.5 border-t border-gray-100 text-center bg-gray-50/50">
                            <a href="{{ route('admin.notifications.index') }}"
                               class="text-xs text-green-700 font-semibold hover:underline">
                                Lihat semua notifikasi →
                            </a>
                        </div>
                    </div>
                </div>

                {{-- 👤 User dropdown --}}
                <div class="relative ml-1" x-data="{ open: false }" @click.away="open = false">
                    <button @click="open = !open"
                        class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-full pl-1 pr-3 py-1 hover:bg-gray-100 transition">
                        <div class="w-7 h-7 rounded-full bg-green-800 flex items-center justify-center text-white text-xs font-bold shrink-0">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                        <div class="hidden sm:block leading-none text-left">
                            <p class="text-xs font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                            <p class="text-[10px] text-gray-400 mt-0.5">{{ auth()->user()->role_label }}</p>
                        </div>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400 hidden sm:block"></i>
                    </button>

                    <div x-show="open" x-transition
                         class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1 text-sm z-50">
                        <a href="{{ route('home') }}"
                           class="flex items-center gap-2 px-4 py-2 hover:bg-herbal-50 text-gray-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 7h18M5 7l1 11a2 2 0 002 2h8a2 2 0 002-2l1-11M9 7V5a3 3 0 016 0v2"/>
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
                                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </header>

        {{-- ── GLOBAL FLASH ── --}}
        @if(session('success'))
        <div class="mx-6 mt-4 flex items-center gap-2 bg-green-50 border border-green-200 text-green-800 text-sm px-4 py-3 rounded-xl">
            <svg class="w-4 h-4 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="mx-6 mt-4 flex items-center gap-2 bg-red-50 border border-red-200 text-red-800 text-sm px-4 py-3 rounded-xl">
            <svg class="w-4 h-4 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            {{ session('error') }}
        </div>
        @endif

        {{-- ── PAGE CONTENT ── --}}
        <main class="flex-1 overflow-y-auto px-6 py-6">
            @yield('content')
            {{ $slot ?? '' }}
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
