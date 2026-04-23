{{-- Settings Sidebar - Reusable Component --}}
<div x-show="menu === 'settings'"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="translate-x-full opacity-0"
    x-transition:enter-end="translate-x-0 opacity-100"
    class="flex flex-col h-full">

    {{-- Header --}}
    <div class="p-5 flex items-center gap-3 border-b border-white/10">
        <i data-lucide="settings" class="w-6 h-6"></i>
        <h1 class="font-semibold text-lg">Pengaturan</h1>
    </div>

    {{-- Kembali --}}
    <a href="#" @click.prevent="menu = 'main'"
        class="flex items-center gap-3 px-3 py-2 hover:bg-white/5 border-b border-white/10 text-sm">
        <i data-lucide="arrow-left" class="w-5 h-5"></i>
        Kembali
    </a>

    {{-- Menu Items --}}
    <nav class="px-3 py-4 space-y-1 text-sm flex-1 overflow-y-auto">

        {{-- Profil Admin --}}
        <a href="{{ route('admin.profile') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
           {{ request()->routeIs('admin.profile') ? 'bg-green-800/60 border-l-4 border-green-400 pl-2' : 'hover:bg-white/5' }}">
            <i data-lucide="user" class="w-5 h-5 shrink-0"></i>
            <span>Profil Admin</span>
        </a>

        {{-- Pengaturan Toko --}}
        <a href="{{ route('admin.settings.store') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
           {{ request()->routeIs('admin.settings.store*') ? 'bg-green-800/60 border-l-4 border-green-400 pl-2' : 'hover:bg-white/5' }}">
            <i data-lucide="store" class="w-5 h-5 shrink-0"></i>
            <span>Pengaturan Toko</span>
        </a>

        {{-- Pengaturan Pembayaran --}}
        <a href="{{ route('admin.settings.payment') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
           {{ request()->routeIs('admin.settings.payment*') || request()->routeIs('admin.settings.bank*') ? 'bg-green-800/60 border-l-4 border-green-400 pl-2' : 'hover:bg-white/5' }}">
            <i data-lucide="credit-card" class="w-5 h-5 shrink-0"></i>
            <span>Pengaturan Pembayaran</span>
        </a>

        {{-- Pengaturan Pengiriman --}}
        <a href="{{ route('admin.settings.shipping') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
           {{ request()->routeIs('admin.settings.shipping*') ? 'bg-green-800/60 border-l-4 border-green-400 pl-2' : 'hover:bg-white/5' }}">
            <i data-lucide="truck" class="w-5 h-5 shrink-0"></i>
            <span>Pengaturan Pengiriman</span>
        </a>

        {{-- Pengaturan Notifikasi --}}
        <a href="{{ route('admin.settings.notification') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
           {{ request()->routeIs('admin.settings.notification*') ? 'bg-green-800/60 border-l-4 border-green-400 pl-2' : 'hover:bg-white/5' }}">
            <i data-lucide="bell" class="w-5 h-5 shrink-0"></i>
            <span>Pengaturan Notifikasi</span>
        </a>

        {{-- Pengaturan Produk --}}
        <a href="{{ route('admin.settings.product') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
           {{ request()->routeIs('admin.settings.product*') ? 'bg-green-800/60 border-l-4 border-green-400 pl-2' : 'hover:bg-white/5' }}">
            <i data-lucide="package" class="w-5 h-5 shrink-0"></i>
            <span>Pengaturan Produk</span>
        </a>

        {{-- Pengaturan Keamanan (Admin Management) --}}
        @if(auth()->user()->isSuperAdmin())
        <a href="{{ route('admin.security.index') }}"
            class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition
           {{ request()->routeIs('admin.security*') || request()->routeIs('admin.admins*') || request()->routeIs('admin.sessions*') ? 'bg-green-800/60 border-l-4 border-green-400 pl-2' : 'hover:bg-white/5' }}">
            <i data-lucide="shield" class="w-5 h-5 shrink-0"></i>
            <span>Pengaturan Keamanan</span>
        </a>
        @endif

    </nav>

</div>