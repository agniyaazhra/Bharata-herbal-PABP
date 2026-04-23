<x-layouts.admin>
<x-slot name="title">Manajemen Pelanggan</x-slot>
<x-slot name="subtitle">Kelola data pelanggan, riwayat pembelian, dan aktivitas pengguna</x-slot>   

{{-- ===== STAT CARDS ===== --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">

    {{-- Card 1 --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-2">Total Pelanggan</p>
                <p class="text-3xl font-extrabold text-gray-900">{{ number_format($totalCustomers) }}</p>
                <div class="flex items-center gap-1 mt-2">
                    <i data-lucide="trending-up" class="w-3.5 h-3.5 text-green-500"></i>
                    <span class="text-xs font-semibold text-green-600">+{{ $newThisMonth }} bulan ini</span>
                </div>
            </div>
            <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center shrink-0">
                <i data-lucide="users" class="w-6 h-6 text-green-700"></i>
            </div>
        </div>
    </div>

    {{-- Card 2 --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-2">Pelanggan Aktif</p>
                <p class="text-3xl font-extrabold text-gray-900">{{ number_format($activeCustomers) }}</p>
                <div class="flex items-center gap-1 mt-2">
                    <i data-lucide="trending-up" class="w-3.5 h-3.5 text-green-500"></i>
                    <span class="text-xs font-semibold text-green-600">+27% dari bulan lalu</span>
                </div>
            </div>
            <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center shrink-0">
                <i data-lucide="user-check" class="w-6 h-6 text-blue-600"></i>
            </div>
        </div>
    </div>

    {{-- Card 3 --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-2">Total Pesanan</p>
                <p class="text-3xl font-extrabold text-gray-900">{{ number_format($totalOrders) }}</p>
                <div class="flex items-center gap-1 mt-2">
                    <i data-lucide="trending-up" class="w-3.5 h-3.5 text-green-500"></i>
                    <span class="text-xs font-semibold text-green-600">+18% dari bulan lalu</span>
                </div>
            </div>
            <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center shrink-0">
                <i data-lucide="shopping-bag" class="w-6 h-6 text-purple-600"></i>
            </div>
        </div>
    </div>

    {{-- Card 4 --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-2">Total Pendapatan</p>
                <p class="text-3xl font-extrabold text-gray-900">{{ 'Rp ' . number_format($totalRevenue / 1000000, 1) . 'M' }}</p>
                <div class="flex items-center gap-1 mt-2">
                    <i data-lucide="trending-up" class="w-3.5 h-3.5 text-green-500"></i>
                    <span class="text-xs font-semibold text-green-600">+12% dari bulan lalu</span>
                </div>
            </div>
            <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center shrink-0">
                <i data-lucide="wallet" class="w-6 h-6 text-amber-600"></i>
            </div>
        </div>
    </div>
</div>

{{-- ===== TABLE CONTAINER ===== --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

    {{-- Filter Bar --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 p-5 border-b border-gray-100">
        <form method="GET" action="{{ route('admin.customers.index') }}" class="flex items-center gap-3 flex-1 flex-wrap">

            {{-- Search --}}
            <div class="relative flex-1 min-w-[200px]">
                <i data-lucide="search" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Cari nama atau email..."
                       class="w-full pl-10 pr-4 py-2.5 rounded-full border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-green-600/30 focus:border-green-600 bg-gray-50 transition">
            </div>

            {{-- Status Filter --}}
            <select name="status"
                    class="px-4 py-2.5 rounded-full border border-gray-200 text-sm text-gray-600 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-600/30 focus:border-green-600 transition">
                <option value="">Semua Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
            </select>

            {{-- Sort --}}
            <select name="sort"
                    class="px-4 py-2.5 rounded-full border border-gray-200 text-sm text-gray-600 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-600/30 focus:border-green-600 transition">
                <option value="newest" {{ request('sort', 'newest') === 'newest' ? 'selected' : '' }}>Urutkan Terbaru</option>
                <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Terlama</option>
                <option value="most_orders" {{ request('sort') === 'most_orders' ? 'selected' : '' }}>Pesanan Terbanyak</option>
            </select>

            <button type="submit"
                    class="px-4 py-2.5 rounded-full bg-green-900 text-white text-sm font-semibold hover:bg-green-800 transition-colors flex items-center gap-2">
                <i data-lucide="filter" class="w-4 h-4"></i>
                Filter
            </button>

            @if(request('search') || request('status') || request('sort'))
            <a href="{{ route('admin.customers.index') }}"
               class="px-4 py-2.5 rounded-full border border-gray-200 text-gray-500 text-sm hover:bg-gray-50 transition-colors">
                Reset
            </a>
            @endif
        </form>

        {{-- Export Button --}}
        <a href="{{ route('admin.customers.index') }}?export=1"
           class="flex items-center gap-2 px-5 py-2.5 bg-green-900 text-white text-sm font-semibold rounded-full hover:bg-green-800 transition-colors shadow-sm shrink-0">
            <i data-lucide="download" class="w-4 h-4"></i>
            Export Data
        </a>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-green-50 border-b border-green-100">
                    <th class="text-left px-6 py-4 text-xs font-bold text-green-800 uppercase tracking-wider">Pelanggan</th>
                    <th class="text-left px-6 py-4 text-xs font-bold text-green-800 uppercase tracking-wider">Kontak</th>
                    <th class="text-left px-6 py-4 text-xs font-bold text-green-800 uppercase tracking-wider">Total Pesanan</th>
                    <th class="text-left px-6 py-4 text-xs font-bold text-green-800 uppercase tracking-wider">Total Belanja</th>
                    <th class="text-left px-6 py-4 text-xs font-bold text-green-800 uppercase tracking-wider">Terakhir Transaksi</th>
                    <th class="text-left px-6 py-4 text-xs font-bold text-green-800 uppercase tracking-wider">Status</th>
                    <th class="text-left px-6 py-4 text-xs font-bold text-green-800 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($customers as $customer)
                @php
                    $initials   = strtoupper(substr($customer->name, 0, 2));
                    $colors     = ['bg-green-100 text-green-700','bg-blue-100 text-blue-700','bg-purple-100 text-purple-700','bg-amber-100 text-amber-700','bg-rose-100 text-rose-700'];
                    $color      = $colors[$customer->id % count($colors)];
                    $isActive   = $customer->orders_count > 0;
                    $lastOrder  = $customer->latestOrder;
                @endphp
                <tr class="hover:bg-gray-50/80 transition-colors group">

                    {{-- Pelanggan --}}
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full {{ $color }} flex items-center justify-center font-bold text-sm shrink-0">
                                {{ $initials }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm leading-tight">{{ $customer->name }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">ID
                            </div>
                        </div>
                    </td>

                    {{-- Kontak --}}
                    <td class="px-6 py-4">
                        <p class="text-sm text-gray-700">{{ $customer->email }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $customer->phone ?? '-' }}</p>
                    </td>

                    {{-- Total Pesanan --}}
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center justify-center w-8 h-8 bg-green-50 rounded-lg">
                                <i data-lucide="package" class="w-4 h-4 text-green-700"></i>
                            </span>
                            <span class="font-bold text-gray-900">{{ $customer->orders_count }}</span>
                            <span class="text-xs text-gray-400">pesanan</span>
                        </div>
                    </td>

                    {{-- Total Belanja --}}
                    <td class="px-6 py-4">
                        <p class="font-bold text-gray-900 text-sm">
                            Rp {{ number_format($customer->orders_sum_total_price ?? 0, 0, ',', '.') }}
                        </p>
                    </td>

                    {{-- Terakhir Transaksi --}}
                    <td class="px-6 py-4">
                        @if ($lastOrder)
                            <p class="text-sm text-gray-700">{{ $lastOrder->created_at->format('d M Y') }}</p>
                            <p class="text-xs text-gray-400">{{ $lastOrder->created_at->diffForHumans() }}</p>
                        @else
                            <p class="text-sm text-gray-400">—</p>
                        @endif
                    </td>

                    {{-- Status --}}
                    <td class="px-6 py-4">
                        @if ($isActive)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block animate-pulse"></span>
                                Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-500">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400 inline-block"></span>
                                Belum Order
                            </span>
                        @endif
                    </td>

                    {{-- Aksi --}}
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-1">
                            <a href="{{ route('admin.customers.show', $customer) }}"
                               title="Lihat Detail"
                               class="w-8 h-8 rounded-lg bg-green-50 hover:bg-green-100 flex items-center justify-center transition-colors group-hover:shadow-sm">
                                <i data-lucide="eye" class="w-4 h-4 text-green-700"></i>
                            </a>
                            <a href="mailto:{{ $customer->email }}"
                               title="Kirim Email"
                               class="w-8 h-8 rounded-lg bg-blue-50 hover:bg-blue-100 flex items-center justify-center transition-colors group-hover:shadow-sm">
                                <i data-lucide="mail" class="w-4 h-4 text-blue-600"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-20 text-center">
                        <div class="flex flex-col items-center gap-3 text-gray-400">
                            <i data-lucide="users" class="w-12 h-12 text-gray-200"></i>
                            <p class="font-semibold text-gray-500">Tidak ada pelanggan ditemukan</p>
                            <p class="text-sm">Coba ubah kata kunci atau filter pencarian</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if ($customers->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
        <p class="text-sm text-gray-500">
            Menampilkan <span class="font-semibold text-gray-800">{{ $customers->firstItem() }}–{{ $customers->lastItem() }}</span>
            dari <span class="font-semibold text-gray-800">{{ $customers->total() }}</span> pelanggan
        </p>
        <div class="[&_.pagination]:flex [&_.pagination]:gap-1 [&_.page-link]:px-3 [&_.page-link]:py-1.5 [&_.page-link]:rounded-lg [&_.page-link]:text-sm [&_.page-link]:border [&_.page-link]:border-gray-200 [&_.page-link]:text-gray-600 [&_.page-item.active_.page-link]:bg-green-900 [&_.page-item.active_.page-link]:text-white [&_.page-item.active_.page-link]:border-green-900">
            {{ $customers->withQueryString()->links() }}
        </div>
    </div>
    @else
    <div class="px-6 py-4 border-t border-gray-100">
        <p class="text-sm text-gray-500">Total <span class="font-semibold text-gray-800">{{ $customers->total() }}</span> pelanggan</p>
    </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof lucide !== 'undefined') lucide.createIcons();
    });
</script>
@endpush

</x-layouts.admin>
