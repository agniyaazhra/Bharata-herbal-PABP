<x-layouts.admin>
<x-slot name="title">Dashboard</x-slot>
<x-slot name="subtitle">Selamat datang kembali, {{ auth()->user()->name }}!</x-slot>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {

    const barCtx = document.getElementById('salesChart').getContext('2d');
    new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: @json($salesLabels),
            datasets: [{
                label: 'Penjualan (Rp)',
                data: @json($salesData),
                backgroundColor: 'rgba(20,83,45,0.85)',
                borderRadius: 8,
                borderSkipped: false,
                hoverBackgroundColor: '#16a34a',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ' Rp ' + ctx.parsed.y.toLocaleString('id-ID')
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.04)' },
                    ticks: {
                        color: '#9ca3af',
                        font: { size: 11 },
                        callback: v => 'Rp' + (v >= 1000000 ? (v/1000000)+'jt' : (v/1000)+'rb')
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#9ca3af', font: { size: 12 } }
                }
            }
        }
    });

    const donutCtx = document.getElementById('categoryChart').getContext('2d');
    const donutData = @json($categoryData);
    new Chart(donutCtx, {
        type: 'doughnut',
        data: {
            labels: donutData.map(d => d.name),
            datasets: [{
                data: donutData.map(d => d.value),
                backgroundColor: [
                    '#14532d','#16a34a','#4ade80',
                    '#86efac','#bbf7d0','#f0fdf4'
                ],
                borderWidth: 2,
                borderColor: '#fff',
                hoverOffset: 6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ' ' + ctx.label + ': ' + ctx.parsed + ' pesanan'
                    }
                }
            }
        }
    });
});
</script>
@endpush

{{-- ═══════════════════════════════════════════
  STAT CARDS
═══════════════════════════════════════════ --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">

    @php
    $stats = [
        [
            'label'  => 'Total Produk',
            'value'  => number_format($totalProducts),
            'growth' => '+' . $newProducts . ' produk baru',
            'icon'   => 'package',
            'bg'     => 'bg-green-50',
            'icon_color' => 'text-green-700',
        ],
        [
            'label'  => 'Total Pelanggan',
            'value'  => number_format($totalCustomers),
            'growth' => '+' . $newCustomers . ' bulan ini',
            'icon'   => 'users',
            'bg'     => 'bg-blue-50',
            'icon_color' => 'text-blue-600',
        ],
        [
            'label'  => 'Total Pesanan',
            'value'  => number_format($totalOrders),
            'growth' => '+' . $newOrders . ' hari ini',
            'icon'   => 'shopping-bag',
            'bg'     => 'bg-purple-50',
            'icon_color' => 'text-purple-600',
        ],
        [
            'label'  => 'Total Penjualan',
            'value'  => 'Rp ' . number_format($totalSales/1000000, 1) . 'jt',
            'growth' => '+12% dari bulan lalu',
            'icon'   => 'trending-up',
            'bg'     => 'bg-amber-50',
            'icon_color' => 'text-amber-600',
        ],
    ];
    @endphp

    @foreach ($stats as $stat)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6
                hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
        <div class="flex items-start justify-between">
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-2">
                    {{ $stat['label'] }}
                </p>
                <p class="text-3xl font-extrabold text-gray-900 leading-none">
                    {{ $stat['value'] }}
                </p>
                <div class="flex items-center gap-1 mt-3">
                    <i data-lucide="trending-up" class="w-3.5 h-3.5 text-green-500 shrink-0"></i>
                    <span class="text-xs font-semibold text-green-600">{{ $stat['growth'] }}</span>
                </div>
            </div>
            <div class="w-12 h-12 {{ $stat['bg'] }} rounded-xl flex items-center justify-center shrink-0 ml-3">
                <i data-lucide="{{ $stat['icon'] }}" class="w-6 h-6 {{ $stat['icon_color'] }}"></i>
            </div>
        </div>
    </div>
    @endforeach

</div>

{{-- ═══════════════════════════════════════════
  CHARTS ROW
═══════════════════════════════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

    {{-- Bar Chart: Grafik Penjualan ──────── --}}
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="font-bold text-gray-900">Grafik Penjualan</h2>
                <p class="text-xs text-gray-400 mt-0.5">Pendapatan per hari</p>
            </div>
            <select class="text-xs border border-gray-200 rounded-lg px-3 py-1.5 text-gray-600
                           focus:outline-none focus:ring-2 focus:ring-green-500/30 bg-gray-50">
                <option>7 Hari Terakhir</option>
                <option>30 Hari Terakhir</option>
                <option>Bulan Ini</option>
            </select>
        </div>
        <div class="h-64">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    {{-- Donut Chart: Top Kategori ───────── --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="font-bold text-gray-900">Top Kategori</h2>
                <p class="text-xs text-gray-400 mt-0.5">Berdasarkan pesanan</p>
            </div>
            <select class="text-xs border border-gray-200 rounded-lg px-3 py-1.5 text-gray-600
                           focus:outline-none focus:ring-2 focus:ring-green-500/30 bg-gray-50">
                <option>Bulan Ini</option>
                <option>3 Bulan</option>
            </select>
        </div>

        <div class="h-40 mb-5">
            <canvas id="categoryChart"></canvas>
        </div>

        {{-- Legend --}}
        @php
        $legendColors = ['bg-green-900','bg-green-600','bg-green-400','bg-green-300','bg-green-200','bg-green-100'];
        @endphp
        <div class="space-y-2">
            @foreach ($categoryData as $i => $cat)
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full {{ $legendColors[$i % count($legendColors)] }}"></div>
                    <span class="text-xs text-gray-600">{{ $cat['name'] }}</span>
                </div>
                <span class="text-xs font-bold text-gray-800">{{ $cat['value'] }}</span>
            </div>
            @endforeach
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════
  RECENT ORDERS TABLE
═══════════════════════════════════════════ --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

    <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
        <div>
            <h2 class="font-bold text-gray-900">Pesanan Terbaru</h2>
            <p class="text-xs text-gray-400 mt-0.5">10 transaksi terakhir</p>
        </div>
        <a href="{{ route('admin.orders.index') }}"
           class="text-xs font-semibold text-green-700 hover:text-green-900
                  flex items-center gap-1 transition-colors">
            Lihat Semua
            <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">No. Pesanan</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Pelanggan</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Total</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Tanggal</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($recentOrders as $order)
                @php
                    $statusConfig = [
                        'pending'    => ['bg-yellow-100 text-yellow-700',  'Menunggu'],
                        'paid'       => ['bg-blue-100 text-blue-700',      'Dibayar'],
                        'processing' => ['bg-indigo-100 text-indigo-700',  'Diproses'],
                        'shipped'    => ['bg-orange-100 text-orange-700',  'Dikirim'],
                        'completed'  => ['bg-green-100 text-green-700',    'Selesai'],
                        'cancelled'  => ['bg-red-100 text-red-700',        'Dibatalkan'],
                    ];
                    [$badge, $label] = $statusConfig[$order->status] ?? ['bg-gray-100 text-gray-600', ucfirst($order->status)];
                @endphp
                <tr class="hover:bg-gray-50/60 transition-colors group">
                    <td class="px-6 py-4 font-mono font-bold text-green-800 text-xs">
                       
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-full bg-green-100 flex items-center justify-center
                                        text-green-800 font-bold text-xs shrink-0">
                                {{ strtoupper(substr($order->user->name, 0, 2)) }}
                            </div>
                            <span class="font-medium text-gray-800 text-sm">{{ $order->user->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 font-semibold text-gray-900">
                        Rp {{ number_format($order->total_price, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $badge }}">
                            {{ $label }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-400 text-xs">
                        {{ $order->created_at->format('d M Y, H:i') }}
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.orders.show', $order) }}"
                           class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-green-100 flex items-center justify-center
                                  transition-colors opacity-0 group-hover:opacity-100">
                            <i data-lucide="eye" class="w-4 h-4 text-gray-500 hover:text-green-700"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center gap-2 text-gray-300">
                            <i data-lucide="inbox" class="w-12 h-12"></i>
                            <p class="text-sm text-gray-400 font-medium">Belum ada pesanan</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ═══════════════════════════════════════════
  TOP PRODUCTS (Rating & Sales)
═══════════════════════════════════════════ --}}
<div class="mt-8 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100">
        <div>
            <h2 class="font-bold text-gray-900">Produk Terlaris & Rating Terbaik</h2>
            <p class="text-xs text-gray-400 mt-0.5">Performa produk berdasarkan penjualan dan ulasan pelanggan</p>
        </div>
        <a href="{{ route('admin.products.index') }}"
           class="text-xs font-semibold text-green-700 hover:text-green-900
                  flex items-center gap-1 transition-colors">
            Kelola Produk
            <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Produk</th>
                    <th class="text-center px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Rating</th>
                    <th class="text-center px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Ulasan</th>
                    <th class="text-center px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Terjual</th>
                    <th class="text-center px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wide">Stok</th>
                    <th class="px-6 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($topProducts as $product)
                <tr class="hover:bg-gray-50/60 transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-gray-100 overflow-hidden shrink-0">
                                @if ($product->image)
                                    <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-herbal-50">
                                        <svg class="w-5 h-5 text-herbal-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m0 0l8 4m0 0l8-4m0 0v10l-8 4m0 0l-8-4m0 0v-10" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <a href="{{ route('product.show', $product->slug) }}" target="_blank" class="font-semibold text-gray-900 hover:text-herbal-700 block truncate text-sm">
                                    {{ $product->name }}
                                </a>
                                <p class="text-xs text-gray-400">{{ $product->categories->first()?->name ?? '-' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="inline-flex items-center gap-1.5 bg-amber-50 px-2.5 py-1 rounded-lg">
                            <span class="text-lg">★</span>
                            <span class="font-bold text-amber-900">{{ number_format($product->rating, 1) }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="inline-flex items-center gap-1.5 bg-blue-50 px-2.5 py-1 rounded-lg">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                            </svg>
                            <span class="font-bold text-blue-900">{{ $product->rating_count }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="inline-flex items-center gap-1.5 bg-green-50 px-2.5 py-1 rounded-lg">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            <span class="font-bold text-green-900">{{ $product->sales_count }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold
                                   {{ $product->stock > 10 ? 'bg-green-100 text-green-700' : ($product->stock > 0 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                            {{ $product->stock }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.products.edit', $product) }}"
                           class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-green-100 flex items-center justify-center
                                  transition-colors opacity-0 group-hover:opacity-100">
                            <svg class="w-4 h-4 text-gray-500 hover:text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center gap-2 text-gray-300">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                            <p class="text-sm text-gray-400 font-medium">Belum ada produk</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

</x-layouts.admin>
