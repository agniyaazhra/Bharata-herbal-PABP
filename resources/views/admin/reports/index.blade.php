<x-layouts.admin>
<x-slot name="title">Laporan Penjualan</x-slot>
<x-slot name="subtitle">Analitik dan ringkasan performa penjualan real-time</x-slot>

@push('scripts')
{{-- ─── Chart.js ─────────────────────────────────────────────────── --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {

    const labels  = @json($chartLabels);
    const data    = @json($chartData);

    const gradient = (ctx) => {
        const g = ctx.createLinearGradient(0, 0, 0, 300);
        g.addColorStop(0, 'rgba(20,83,45,0.25)');
        g.addColorStop(1, 'rgba(20,83,45,0)');
        return g;
    };

    const salesCtx = document.getElementById('salesLineChart').getContext('2d');
    new Chart(salesCtx, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Penjualan (Rp)',
                data,
                fill: true,
                backgroundColor: gradient(salesCtx),
                borderColor: '#15803d',
                borderWidth: 2.5,
                pointRadius: 4,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#15803d',
                pointBorderWidth: 2,
                tension: 0.45,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1f2937',
                    titleFont: { size: 11, family: 'Inter' },
                    bodyFont:  { size: 13, family: 'Inter', weight: '600' },
                    cornerRadius: 8,
                    padding: 10,
                    callbacks: {
                        label: ctx => ' Rp ' + ctx.parsed.y.toLocaleString('id-ID')
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.04)', drawBorder: false },
                    ticks: {
                        color: '#9ca3af',
                        font: { size: 11, family: 'Inter' },
                        callback: v => v >= 1000000
                            ? 'Rp ' + (v / 1000000).toFixed(1) + 'jt'
                            : v >= 1000
                                ? 'Rp ' + (v / 1000).toFixed(0) + 'rb'
                                : 'Rp ' + v
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#6b7280', font: { size: 11, family: 'Inter' } }
                }
            }
        }
    });
});
</script>
@endpush

{{-- ═══════════════════════════════════════════════════════════════
  TOP ACTION ROW  (date range + export)
═══════════════════════════════════════════════════════════════ --}}
<form method="GET" action="{{ route('admin.reports.index') }}" id="mainForm">
    {{-- hidden fields so chart-range tabs preserve date filters --}}
    <input type="hidden" name="range" id="rangeInput" value="{{ $range }}">
    <input type="hidden" name="start_date" id="startInput" value="{{ $startDate }}">
    <input type="hidden" name="end_date"   id="endInput"   value="{{ $endDate }}">
    <input type="hidden" name="tx_status"  id="txStatusInput" value="{{ $txStatus }}">
    <input type="hidden" name="product_search" id="prodSearchInput" value="{{ $topProductSearch }}">
</form>

<div class="flex flex-col sm:flex-row gap-3 mb-7">
    {{-- Date pickers --}}
    <div class="flex items-center gap-2 bg-white border border-gray-200 rounded-xl shadow-sm px-4 py-2.5 flex-1">
        <i data-lucide="calendar" class="w-4 h-4 text-gray-400 shrink-0"></i>
        <span class="text-xs text-gray-500 shrink-0">Dari</span>
        <input type="date" id="startDate" value="{{ $startDate }}"
               class="text-sm text-gray-700 bg-transparent focus:outline-none w-full cursor-pointer">
        <span class="text-gray-300 mx-1">—</span>
        <span class="text-xs text-gray-500 shrink-0">Sampai</span>
        <input type="date" id="endDate" value="{{ $endDate }}"
               class="text-sm text-gray-700 bg-transparent focus:outline-none w-full cursor-pointer">
        <button type="button" id="applyDate"
                class="ml-2 px-3 py-1 bg-green-800 text-white text-xs font-semibold rounded-lg hover:bg-green-700 transition shrink-0">
            Terapkan
        </button>
    </div>

    {{-- Export button --}}
    <a id="exportBtn"
       href="{{ route('admin.reports.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
       class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-200
              text-sm font-semibold text-gray-700 rounded-xl shadow-sm hover:bg-gray-50 transition shrink-0">
        <i data-lucide="download" class="w-4 h-4"></i>
        Export CSV
    </a>
</div>

{{-- ═══════════════════════════════════════════════════════════════
  STAT CARDS  (3 cards)
═══════════════════════════════════════════════════════════════ --}}
@php
    $fmt = fn($n) => $n >= 1_000_000
        ? number_format($n / 1_000_000, 2) . 'jt'
        : number_format($n / 1_000, 2) . 'rb';

    $cards = [
        [
            'label'   => 'Total Penjualan',
            'value'   => 'Rp ' . $fmt($revenueThis),
            'prev'    => 'Rp ' . $fmt($revenuePrev) . ' minggu lalu',
            'growth'  => $revenueGrowth,
            'icon'    => 'dollar-sign',
            'bg'      => 'bg-green-50',
            'iconClr' => 'text-green-700',
        ],
        [
            'label'   => 'Total Pelanggan',
            'value'   => number_format($customersTotal),
            'prev'    => $customersPrev . ' bergabung minggu lalu',
            'growth'  => $customersGrowth,
            'icon'    => 'users',
            'bg'      => 'bg-blue-50',
            'iconClr' => 'text-blue-600',
        ],
        [
            'label'   => 'Total Keuntungan',
            'value'   => 'Rp ' . $fmt($profitThis),
            'prev'    => 'Rp ' . $fmt($profitPrev) . ' minggu lalu',
            'growth'  => $profitGrowth,
            'icon'    => 'trending-up',
            'bg'      => 'bg-purple-50',
            'iconClr' => 'text-purple-600',
        ],
    ];
@endphp

<div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-7">
    @foreach ($cards as $card)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6
                hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group">
        <div class="flex items-start justify-between">
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest">
                    {{ $card['label'] }}
                </p>
                <p class="text-3xl font-extrabold text-gray-900 mt-2 leading-none">
                    {{ $card['value'] }}
                </p>
                <div class="flex items-center gap-1.5 mt-3">
                    @if ($card['growth'] >= 0)
                        <span class="inline-flex items-center gap-1 text-xs font-bold text-green-600 bg-green-50 px-2 py-0.5 rounded-full">
                            <i data-lucide="trending-up" class="w-3 h-3"></i>
                            +{{ $card['growth'] }}%
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 text-xs font-bold text-red-500 bg-red-50 px-2 py-0.5 rounded-full">
                            <i data-lucide="trending-down" class="w-3 h-3"></i>
                            {{ $card['growth'] }}%
                        </span>
                    @endif
                    <span class="text-xs text-gray-400">{{ $card['prev'] }}</span>
                </div>
            </div>
            <div class="w-12 h-12 {{ $card['bg'] }} rounded-xl flex items-center justify-center shrink-0 ml-3
                        group-hover:scale-110 transition-transform duration-200">
                <i data-lucide="{{ $card['icon'] }}" class="w-6 h-6 {{ $card['iconClr'] }}"></i>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- ═══════════════════════════════════════════════════════════════
  MAIN GRID:  Chart (left 2/3)  +  Top Products (right 1/3)
═══════════════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-7">

    {{-- ── CHART CARD ─────────────────────────────────────────── --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-6">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-5">
            <div>
                <h2 class="font-bold text-gray-900">Laporan Mingguan</h2>
                <p class="text-xs text-gray-400 mt-0.5">Pendapatan per hari selama 7 hari</p>
            </div>
            {{-- Range toggle --}}
            <div class="flex rounded-lg overflow-hidden border border-gray-200 text-xs font-semibold">
                <button type="button" data-range="this_week"
                        class="range-btn px-3 py-1.5 transition
                               {{ $range === 'this_week' ? 'bg-green-800 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' }}">
                    Minggu Ini
                </button>
                <button type="button" data-range="last_week"
                        class="range-btn px-3 py-1.5 border-l border-gray-200 transition
                               {{ $range === 'last_week' ? 'bg-green-800 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' }}">
                    Minggu Lalu
                </button>
            </div>
        </div>

        {{-- Canvas --}}
        <div class="h-64">
            <canvas id="salesLineChart"></canvas>
        </div>

        {{-- Summary row below chart --}}
        <div class="grid grid-cols-4 gap-4 mt-5 pt-5 border-t border-gray-100">
            @php
            $miniCards = [
                ['label' => 'Total Pembeli',  'value' => number_format($totalOrders),   'icon' => 'users'],
                ['label' => 'Total Produk',   'value' => number_format($totalProducts),  'icon' => 'package'],
                ['label' => 'Stok Produk',    'value' => number_format($totalStock),      'icon' => 'box'],
                ['label' => 'Total Pendapatan','value' => 'Rp ' . $fmt($totalRevenue),   'icon' => 'wallet'],
            ];
            @endphp
            @foreach ($miniCards as $m)
            <div class="text-center">
                <div class="w-9 h-9 bg-green-50 rounded-xl flex items-center justify-center mx-auto mb-2">
                    <i data-lucide="{{ $m['icon'] }}" class="w-4 h-4 text-green-700"></i>
                </div>
                <p class="text-[11px] text-gray-400 font-medium">{{ $m['label'] }}</p>
                <p class="text-sm font-bold text-gray-800 mt-0.5">{{ $m['value'] }}</p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── TOP PRODUCTS CARD ──────────────────────────────────── --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col">

        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="font-bold text-gray-900">Produk Terlaris</h2>
                <p class="text-xs text-gray-400 mt-0.5">Berdasarkan jumlah terjual</p>
            </div>
        </div>

        {{-- Search produk --}}
        <div class="relative mb-4">
            <i data-lucide="search" class="w-3.5 h-3.5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
            <input type="text" id="productSearch"
                   value="{{ $topProductSearch }}"
                   placeholder="Cari produk…"
                   class="w-full pl-9 pr-3 py-2 text-xs bg-gray-50 border border-gray-200 rounded-lg
                          focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-400 transition">
        </div>

        {{-- List --}}
        <div class="flex-1 space-y-2 overflow-y-auto max-h-72 pr-1">

            @forelse ($topProducts as $i => $product)
            @php
                $sold = $product->order_items_sum_quantity ?? 0;
                $maxSold = $topProducts->max('order_items_sum_quantity') ?: 1;
                $pct  = $maxSold > 0 ? round(($sold / $maxSold) * 100) : 0;
                $rankColors = ['bg-amber-400','bg-gray-300','bg-amber-700','bg-gray-200'];
                $rankClr = $rankColors[$i] ?? 'bg-gray-100';
            @endphp
            <div class="flex items-center gap-3 p-2.5 rounded-xl hover:bg-gray-50 transition group">

                {{-- Rank badge --}}
                <div class="w-6 h-6 rounded-full {{ $rankClr }} flex items-center justify-center shrink-0">
                    <span class="text-[10px] font-bold text-white">{{ $i + 1 }}</span>
                </div>

                {{-- Image --}}
                @if ($product->image)
                    <img src="{{ asset('storage/' . $product->image) }}"
                         alt="{{ $product->name }}"
                         class="w-9 h-9 rounded-lg object-cover shrink-0 border border-gray-100">
                @else
                    <div class="w-9 h-9 rounded-lg bg-green-50 flex items-center justify-center shrink-0 border border-gray-100">
                        <i data-lucide="package" class="w-4 h-4 text-green-600"></i>
                    </div>
                @endif

                {{-- Info --}}
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-gray-800 truncate">{{ $product->name }}</p>
                    <p class="text-[10px] text-gray-400 mt-0.5">
                        Rp {{ number_format($product->effective_price, 0, ',', '.') }}
                    </p>
                    {{-- Progress bar --}}
                    <div class="mt-1.5 h-1 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-green-600 rounded-full transition-all duration-700"
                             style="width: {{ $pct }}%"></div>
                    </div>
                </div>

                {{-- Sold count --}}
                <div class="text-right shrink-0">
                    <p class="text-sm font-bold text-gray-900">{{ number_format($sold) }}</p>
                    <p class="text-[10px] text-gray-400">terjual</p>
                </div>
            </div>
            @empty
            <div class="flex flex-col items-center justify-center py-10 text-gray-300">
                <i data-lucide="package-x" class="w-10 h-10 mb-2"></i>
                <p class="text-sm text-gray-400">Belum ada data produk</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════
  RIWAYAT TRANSAKSI
═══════════════════════════════════════════════════════════════ --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

    {{-- Table header + filter --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 px-6 py-5 border-b border-gray-100">
        <div>
            <h2 class="font-bold text-gray-900">Riwayat Transaksi</h2>
            <p class="text-xs text-gray-400 mt-0.5">
                {{ $transactions->total() }} transaksi ditemukan
            </p>
        </div>

        {{-- Status filter tabs --}}
        <div class="flex flex-wrap gap-2">
            @php
            $txTabs = [
                ''           => 'Semua',
                'pending'    => 'Menunggu',
                'paid'       => 'Dibayar',
                'processing' => 'Diproses',
                'shipped'    => 'Dikirim',
                'completed'  => 'Selesai',
                'cancelled'  => 'Dibatalkan',
            ];
            @endphp
            @foreach ($txTabs as $val => $lbl)
            <button type="button"
                    data-txstatus="{{ $val }}"
                    class="tx-filter-btn text-xs font-semibold px-3 py-1.5 rounded-full border transition
                           {{ $txStatus === $val || ($val === '' && $txStatus === '')
                               ? 'bg-green-800 text-white border-green-800'
                               : 'bg-white text-gray-600 border-gray-200 hover:border-green-300 hover:text-green-800' }}">
                {{ $lbl }}
            </button>
            @endforeach
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide w-8">No</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Nama Customer</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Produk</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Tanggal</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Status</th>
                    <th class="px-5 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Total</th>
                    <th class="px-5 py-4 w-10"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($transactions as $i => $order)
                @php
                    $statusMap = [
                        'pending'    => ['pill' => 'bg-yellow-50 text-yellow-700 ring-yellow-200', 'dot' => 'bg-yellow-400', 'label' => 'Menunggu'],
                        'paid'       => ['pill' => 'bg-blue-50 text-blue-700 ring-blue-200',       'dot' => 'bg-blue-400',   'label' => 'Dibayar'],
                        'processing' => ['pill' => 'bg-indigo-50 text-indigo-700 ring-indigo-200', 'dot' => 'bg-indigo-400','label' => 'Diproses'],
                        'shipped'    => ['pill' => 'bg-orange-50 text-orange-700 ring-orange-200', 'dot' => 'bg-orange-400','label' => 'Dikirim'],
                        'completed'  => ['pill' => 'bg-green-50 text-green-700 ring-green-200',    'dot' => 'bg-green-500',  'label' => 'Selesai'],
                        'cancelled'  => ['pill' => 'bg-red-50 text-red-700 ring-red-200',          'dot' => 'bg-red-400',   'label' => 'Dibatalkan'],
                    ];
                    $s = $statusMap[$order->status] ?? ['pill' => 'bg-gray-100 text-gray-600 ring-gray-200', 'dot' => 'bg-gray-400', 'label' => $order->status];
                    $products = $order->items->take(2)->map(fn($item) => ($item->product->name ?? 'Produk') . ' ×' . $item->quantity)->implode(', ');
                    $extraItems = max(0, $order->items->count() - 2);
                @endphp
                <tr class="hover:bg-green-50/20 transition-colors cursor-pointer group"
                    onclick="window.location='{{ route('admin.orders.show', $order) }}'">
                    <td class="px-5 py-4 text-gray-400 text-xs font-medium">
                        {{ ($transactions->currentPage() - 1) * $transactions->perPage() + $i + 1 }}
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-green-100 text-green-800 font-bold text-xs
                                        flex items-center justify-center shrink-0">
                                {{ strtoupper(substr($order->user->name ?? '?', 0, 2)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-800 text-sm leading-tight">{{ $order->user->name ?? '-' }}</p>
                                <p class="text-[11px] text-gray-400 font-mono">{{ $order->order_number }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4 max-w-[220px]">
                        <p class="text-gray-700 text-xs truncate">{{ $products }}</p>
                        @if ($extraItems > 0)
                            <p class="text-[11px] text-gray-400">+{{ $extraItems }} lainnya</p>
                        @endif
                    </td>
                    <td class="px-5 py-4 whitespace-nowrap">
                        <p class="text-gray-800 text-xs font-medium">{{ $order->created_at->format('d M Y') }}</p>
                        <p class="text-gray-400 text-[11px]">{{ $order->created_at->format('H:i') }} WIB</p>
                    </td>
                    <td class="px-5 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold ring-1 {{ $s['pill'] }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $s['dot'] }}"></span>
                            {{ $s['label'] }}
                        </span>
                    </td>
                    <td class="px-5 py-4 whitespace-nowrap">
                        <p class="font-bold text-gray-900">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                        @if ($order->payment)
                            <p class="text-[11px] text-gray-400">{{ $order->payment->method_label }}</p>
                        @endif
                    </td>
                    <td class="px-5 py-4" onclick="event.stopPropagation()">
                        <a href="{{ route('admin.orders.show', $order) }}"
                           class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-green-100 flex items-center justify-center
                                  opacity-0 group-hover:opacity-100 transition ml-auto">
                            <i data-lucide="eye" class="w-4 h-4 text-gray-500 hover:text-green-700"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-16 text-center">
                        <div class="flex flex-col items-center gap-3 text-gray-300">
                            <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center">
                                <i data-lucide="receipt" class="w-8 h-8 text-gray-300"></i>
                            </div>
                            <p class="text-base font-semibold text-gray-400">Belum ada transaksi</p>
                            <p class="text-sm text-gray-400">Coba ubah rentang tanggal atau filter status</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if ($transactions->total() > 0)
    <div class="px-6 py-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3">
        <p class="text-xs text-gray-400 shrink-0">
            Menampilkan
            <span class="font-semibold text-gray-700">{{ $transactions->firstItem() }}–{{ $transactions->lastItem() }}</span>
            dari <span class="font-semibold text-gray-700">{{ $transactions->total() }}</span> transaksi
        </p>
        <div class="text-sm">{{ $transactions->links() }}</div>
    </div>
    @endif
</div>

{{-- ═══════════════════════════════════════════════════════════════
  JAVASCRIPT: interactions (range toggle, date apply, tx filter, product search)
═══════════════════════════════════════════════════════════════ --}}
<script>
document.addEventListener('DOMContentLoaded', () => {

    const form         = document.getElementById('mainForm');
    const rangeInput   = document.getElementById('rangeInput');
    const startInput   = document.getElementById('startInput');
    const endInput     = document.getElementById('endInput');
    const txStatusInp  = document.getElementById('txStatusInput');
    const prodSearchInp= document.getElementById('prodSearchInput');

    document.querySelectorAll('.range-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            rangeInput.value = btn.dataset.range;
            form.submit();
        });
    });

    document.getElementById('applyDate').addEventListener('click', () => {
        startInput.value = document.getElementById('startDate').value;
        endInput.value   = document.getElementById('endDate').value;

        const exp = document.getElementById('exportBtn');
        if (exp) {
            exp.href = exp.href
                .replace(/start_date=[^&]*/, 'start_date=' + startInput.value)
                .replace(/end_date=[^&]*/,   'end_date='   + endInput.value);
        }
        form.submit();
    });

    document.querySelectorAll('.tx-filter-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            txStatusInp.value = btn.dataset.txstatus;
            form.submit();
        });
    });

    let searchTimer;
    document.getElementById('productSearch').addEventListener('input', function () {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            prodSearchInp.value = this.value;
            form.submit();
        }, 500);
    });
});
</script>

{{-- ═══════════════════════════════════════════════════════════════
  PAGINATION STYLE
═══════════════════════════════════════════════════════════════ --}}
<style>
    nav[aria-label] { display: flex; align-items: center; gap: 0.25rem; }
    nav[aria-label] svg { width: 1rem; height: 1rem; }
    nav[aria-label] span[aria-current] {
        background:
        border-color:
    }
    nav[aria-label] a, nav[aria-label] button { border-radius: 0.5rem !important; }
</style>

</x-layouts.admin>
