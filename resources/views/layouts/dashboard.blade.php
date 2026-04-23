{{-- User dashboard sidebar layout wrapper --}}
<x-app-layout>
    <x-slot name="title">{{ $title ?? 'Dashboard' }}</x-slot>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex flex-col md:flex-row gap-8">

            {{-- Sidebar --}}
           <aside class="w-64 min-h-screen bg-gradient-to-b from-green-900 to-green-800 text-white flex flex-col">

    <div class="p-5 flex items-center gap-3 border-b border-green-700">
        <i data-lucide="leaf"></i>
        <h1 class="font-semibold text-lg">Bharata Herbal</h1>
    </div>

    <nav class="flex-1 px-3 py-4 space-y-1 text-sm">

        <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg bg-green-700">
            <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
            <span>Dashboard</span>
        </a>

        <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-green-700/60 transition">
            <i data-lucide="box" class="w-5 h-5"></i>
            <span>Manajemen Produk</span>
        </a>

        <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-green-700/60 transition">
            <i data-lucide="tag" class="w-5 h-5"></i>
            <span>Manajemen Kategori</span>
        </a>

        <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-green-700/60 transition">
            <i data-lucide="shopping-cart" class="w-5 h-5"></i>
            <span>Manajemen Pesanan</span>
        </a>

        <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-green-700/60 transition">
            <i data-lucide="users" class="w-5 h-5"></i>
            <span>Manajemen Pelanggan</span>
        </a>

        <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-green-700/60 transition">
            <i data-lucide="bar-chart-3" class="w-5 h-5"></i>
            <span>Laporan Penjualan</span>
        </a>

    </nav>

    <div class="p-3 border-t border-green-700 space-y-1 text-sm">

        <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-green-700/60">
            <i data-lucide="settings" class="w-5 h-5"></i>
            <span>Pengaturan</span>
        </a>

        <a href="#" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-red-500/20 text-red-300">
            <i data-lucide="log-out" class="w-5 h-5"></i>
            <span>Logout</span>
        </a>

    </div>

</aside>

            {{-- Content --}}
            <main class="flex-1">
                {{ $slot }}
            </main>
        </div>
    </div>
</x-app-layout>
