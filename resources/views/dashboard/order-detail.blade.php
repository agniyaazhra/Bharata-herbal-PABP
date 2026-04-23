<x-layouts.dashboard>
    <x-slot name="title">Detail Pesanan
    <x-slot name="slot">

        @php
        $trackingUpdates = $order->trackingUpdates->sortByDesc('created_at')->values();
        $latestTracking = $trackingUpdates->first();
        $latestActiveTracking = $trackingUpdates->first(fn ($update) => $update->created_at->lte(now()));
        $showShipmentSection = in_array($order->status, ['shipped', 'completed'], true)
        && (
        $trackingUpdates->isNotEmpty()
        || $order->tracking_number
        || $order->courier_label
        || $order->estimated_delivery_at
        );

        $cityCoordinates = [
        'jakarta' => [-6.200000, 106.816666],
        'bandung' => [-6.917464, 107.619123],
        'semarang' => [-6.966667, 110.416664],
        'yogyakarta' => [-7.795580, 110.369492],
        'surabaya' => [-7.257472, 112.752090],
        'malang' => [-7.966620, 112.632629],
        'solo' => [-7.566620, 110.816666],
        'medan' => [3.595196, 98.672226],
        'palembang' => [-2.976074, 104.775429],
        'makassar' => [-5.147665, 119.432732],
        'denpasar' => [-8.670458, 115.212631],
        ];

        $resolveCoordinates = function (string $location, int $index) use ($cityCoordinates, $trackingUpdates) {
        foreach ($cityCoordinates as $city => [$lat, $lng]) {
        if (str_contains(strtolower($location), $city)) {
        return ['lat' => $lat, 'lng' => $lng];
        }
        }

        $count = max($trackingUpdates->count() - 1, 1);
        $start = $cityCoordinates['jakarta'];
        $end = $cityCoordinates['surabaya'];
        $progress = $index / $count;

        return [
        'lat' => $start[0] + (($end[0] - $start[0]) * $progress),
        'lng' => $start[1] + (($end[1] - $start[1]) * $progress),
        ];
        };

        $mapMarkers = $trackingUpdates->values()->map(function ($point, $index) use ($resolveCoordinates, $latestActiveTracking) {
        $coordinates = $resolveCoordinates($point->lokasi, $index);

        return [
        'id' => $point->id,
        'label' => $point->lokasi,
        'status' => $point->keterangan,
        'lat' => $coordinates['lat'],
        'lng' => $coordinates['lng'],
        'active' => $point->created_at->lte(now()),
        'is_current' => $latestActiveTracking && $point->id === $latestActiveTracking->id,
        ];
        });
        $currentMapMarker = $mapMarkers->firstWhere('is_current', true) ?? $mapMarkers->first();
        @endphp

        <div class="space-y-6">
            {{-- Order Header --}}
            <div class="card p-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="font-bold text-gray-800 text-xl">Pesanan
                        <p class="text-gray-500 text-sm mt-1">{{ $order->created_at->format('d F Y, H:i') }} WIB</p>
                    </div>
                    @php $c = $order->status_color @endphp
                    <span class="badge bg-{{ $c }}-100 text-{{ $c }}-700 font-semibold text-sm px-4 py-2">
                        {{ $order->status_label }}
                    </span>
                </div>

                {{-- Payment Deadline --}}
                @if ($order->status === 'pending' && $order->payment_deadline && $order->payment_deadline->isFuture())
                <div class="mt-5 p-4 bg-amber-50 border border-amber-200 rounded-xl flex items-center gap-3">
                    <span class="text-2xl">⏰</span>
                    <div>
                        <p class="text-sm font-semibold text-amber-800">Batas Waktu Pembayaran</p>
                        <p class="text-amber-700">{{ $order->payment_deadline->format('d M Y, H:i') }} WIB</p>
                    </div>
                </div>
                @endif

                {{-- Tracking --}}
                @if ($order->tracking_number)
                <div class="mt-5 p-4 bg-blue-50 border border-blue-200 rounded-xl flex items-center gap-3">
                    <span class="text-2xl">🚚</span>
                    <div>
                        <p class="text-sm font-semibold text-blue-800">No. Resi Pengiriman</p>
                        <p class="text-lg font-mono font-bold text-blue-700">{{ $order->tracking_number }}</p>
                    </div>
                </div>
                @endif

                {{-- Cancel Reason --}}
                @if ($order->status === 'cancelled' && $order->cancel_reason)
                <div class="mt-5 p-4 bg-red-50 border border-red-200 rounded-xl">
                    <p class="text-sm font-semibold text-red-800 mb-1">Alasan Pembatalan</p>
                    <p class="text-red-700 text-sm">{{ $order->cancel_reason }}</p>
                </div>
                @endif
            </div>

            @if ($showShipmentSection)
            <div class="card overflow-hidden">
                <div class="relative px-6 pt-6 pb-5 bg-gradient-to-r from-sky-50 via-white to-emerald-50 border-b border-gray-100">
                    <div class="absolute inset-y-0 right-0 w-40 bg-[radial-gradient(circle_at_top_right,_rgba(16,185,129,0.16),_transparent_58%)]"></div>
                    <div class="relative flex flex-col lg:flex-row lg:items-start lg:justify-between gap-5">
                        <div>
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-orange-100 text-orange-700 text-xs font-semibold">
                                Tracking Pengiriman
                            </div>
                            <h3 class="mt-3 text-xl font-bold text-gray-900">Pantau progres pengiriman paket Anda</h3>
                            <p class="mt-1 text-sm text-gray-600">Visual tracking ini bersifat simulasi internal untuk pengalaman pelacakan yang lebih nyaman.</p>
                        </div>

                        @if ($latestTracking)
                        <div class="rounded-2xl bg-white/90 backdrop-blur border border-white shadow-sm px-4 py-3 min-w-[240px]">
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-400">Update Terakhir</p>
                            <p class="mt-2 font-semibold text-gray-900">{{ $latestTracking->keterangan }}</p>
                            <p class="text-sm text-gray-500 mt-1">{{ $latestTracking->lokasi }}</p>
                            <p class="text-xs text-gray-400 mt-2">{{ $latestTracking->created_at->format('d M Y, H:i') }} WIB</p>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Kurir</p>
                            <p class="mt-2 text-base font-semibold text-slate-900">{{ $order->courier_label ?? 'Menunggu assignment' }}</p>
                        </div>
                        <div class="rounded-2xl border border-blue-200 bg-blue-50/80 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-blue-400">Nomor Resi</p>
                            <p class="mt-2 text-base font-semibold text-blue-900 font-mono">{{ $order->tracking_number ?? '-' }}</p>
                        </div>
                        <div class="rounded-2xl border border-emerald-200 bg-emerald-50/80 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-emerald-400">Estimasi Tiba</p>
                            @if ($order->estimated_delivery_at)
                            @php
                            $daysRemaining = now('Asia/Jakarta')->diffInDays($order->estimated_delivery_at, false);
                            @endphp
                            <p class="mt-2 text-base font-semibold text-emerald-900">
                                {{ $order->estimated_delivery_at->format('d M Y') }}
                                @if ($daysRemaining > 0)
                                <span class="text-sm text-emerald-700 block mt-1">(dalam {{ $daysRemaining }} hari)</span>
                                @endif
                            </p>
                            @else
                            <p class="mt-2 text-base font-semibold text-emerald-900">Sedang dihitung</p>
                            @endif
                        </div>
                        <div class="rounded-2xl border border-orange-200 bg-orange-50/80 p-4">
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-orange-400">Lokasi Terkini</p>
                            <p class="mt-2 text-base font-semibold text-orange-900">{{ $latestActiveTracking?->lokasi ?? 'Belum ada update aktif' }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 xl:grid-cols-[1.15fr_0.85fr] gap-6">
                        <div class="rounded-[28px] border border-slate-200 overflow-hidden bg-slate-950 text-white">
                            <div class="relative min-h-[320px] px-6 py-6 bg-[radial-gradient(circle_at_top_left,_rgba(56,189,248,0.18),_transparent_35%),radial-gradient(circle_at_bottom_right,_rgba(16,185,129,0.22),_transparent_38%),linear-gradient(160deg,_#082f49,_#0f172a_58%,_#111827)]">
                                <div class="absolute inset-0 opacity-20" style="background-image: linear-gradient(to right, rgba(255,255,255,0.14) 1px, transparent 1px), linear-gradient(to bottom, rgba(255,255,255,0.14) 1px, transparent 1px); background-size: 34px 34px;"></div>
                                <div class="relative z-10">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-sky-200/80">Shipment Map</p>
                                            <h4 class="mt-2 text-lg font-semibold">Simulasi posisi paket saat ini</h4>
                                            <p class="text-sm text-slate-300 mt-1">Marker mengikuti update tracking terbaru, bukan lokasi GPS real-time.</p>
                                        </div>
                                        @if ($latestTracking)
                                        <div class="rounded-2xl bg-white/10 border border-white/10 px-4 py-3 text-right">
                                            <p class="text-xs uppercase tracking-[0.18em] text-slate-300">Current Hub</p>
                                            <p class="mt-1 font-semibold">{{ $latestActiveTracking?->lokasi ?? $latestTracking->lokasi }}</p>
                                        </div>
                                        @endif
                                    </div>

                                    <div class="relative mt-8 h-[260px] rounded-[26px] border border-white/10 bg-white/5 overflow-hidden">
                                        <div id="shipment-leaflet-map"
                                            class="absolute inset-0"
                                            data-marker='@json($currentMapMarker)'
                                            data-current='@json($latestActiveTracking ? [' lokasi'=> $latestActiveTracking->lokasi, 'keterangan' => $latestActiveTracking->keterangan] : null)'></div>
                                        @if ($mapMarkers->isEmpty())
                                        <div class="absolute inset-0 flex items-center justify-center px-6 text-center z-[500] pointer-events-none">
                                            <div>
                                                <p class="font-semibold text-white">Lokasi simulasi belum tersedia</p>
                                                <p class="text-sm text-slate-300 mt-1">Tracking akan tampil di peta setelah status pengiriman dibuat.</p>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-[28px] border border-gray-100 bg-white p-5 shadow-sm">
                            <div class="flex items-center justify-between gap-3 mb-5">
                                <div>
                                    <h4 class="text-lg font-semibold text-gray-900">Timeline Pengiriman</h4>
                                    <p class="text-sm text-gray-500 mt-1">Riwayat update disusun kronologis dari awal hingga status terbaru.</p>
                                </div>
                                <span class="inline-flex items-center gap-2 rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">
                                    {{ $trackingUpdates->count() }} langkah
                                </span>
                            </div>

                            <div class="space-y-4">
                                @forelse ($trackingUpdates as $update)
                                @php
                                $isActive = $update->created_at->lte(now());
                                @endphp
                                <div class="relative pl-8">
                                    @unless ($loop->last)
                                    <span class="absolute left-[11px] top-8 bottom-[-22px] w-px {{ $isActive ? 'bg-emerald-300' : 'bg-gray-200' }}"></span>
                                    @endunless
                                    <span class="absolute left-0 top-1 flex h-6 w-6 items-center justify-center rounded-full {{ $isActive ? 'bg-emerald-500 text-white ring-4 ring-emerald-50' : 'bg-gray-300 text-white ring-4 ring-gray-100' }}">
                                        <span class="text-xs font-bold">{{ $isActive ? '✓' : '•' }}</span>
                                    </span>
                                    <div class="rounded-2xl border {{ $isActive ? 'border-emerald-100 bg-emerald-50/70' : 'border-gray-200 bg-gray-50/90' }} p-4">
                                        <div class="flex items-start justify-between gap-3">
                                            <div>
                                                <p class="font-semibold {{ $isActive ? 'text-gray-900' : 'text-gray-500' }}">{{ $update->keterangan }}</p>
                                                <p class="text-sm {{ $isActive ? 'text-gray-500' : 'text-gray-400' }} mt-1">{{ $update->lokasi }}</p>
                                            </div>
                                            <span class="text-xs font-medium {{ $isActive ? 'text-emerald-600' : 'text-gray-400' }}">
                                                {{ $isActive ? 'Aktif' : 'Akan Datang' }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-400 mt-3">{{ $update->created_at->format('d M Y, H:i') }} WIB</p>
                                    </div>
                                </div>
                                @empty
                                <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 px-5 py-8 text-center">
                                    <p class="font-semibold text-gray-800">Tracking belum tersedia.</p>
                                    <p class="text-sm text-gray-500 mt-1">Silakan cek kembali setelah admin menambahkan update pengiriman pertama.</p>
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Order Items --}}
                <div class="card p-6 md:col-span-2">
                    <h3 class="font-semibold text-gray-800 mb-4">Produk yang Dipesan</h3>
                    <div class="space-y-4">
                        @foreach ($order->items as $item)
                        <div class="flex items-start gap-4 py-4 border-b border-gray-50 last:border-0 group">
                            <div class="w-14 h-14 rounded-xl overflow-hidden bg-herbal-50 shrink-0">
                                @if ($item->product->image)
                                <img src="{{ Storage::url($item->product->image) }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                @else
                                <div class="w-full h-full flex items-center justify-center text-xl">🌿</div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2 mb-1">
                                    <p class="font-medium text-gray-800">{{ $item->product->name }}</p>
                                    <span class="badge text-xs px-2 py-1 whitespace-nowrap bg-{{ $item->status_color }}-100 text-{{ $item->status_color }}-700 font-semibold">
                                        {{ $item->status_label }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-500">Rp {{ number_format($item->price, 0, ',', '.') }} × {{ $item->quantity }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-herbal-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>

                                <div class="mt-2 flex flex-col gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                    {{-- Show review button if completed --}}
                                    @if ($order->status === 'completed')
                                    @php
                                    $reviewed = $order->reviews->where('product_id', $item->product_id)->isNotEmpty();
                                    @endphp
                                    @if ($reviewed)
                                    <span class="text-xs text-green-600 font-medium block">✅ Sudah diulas</span>
                                    @endif
                                    @endif

                                    {{-- Show cancel button if item can be cancelled --}}
                                    @if ($item->canBeCancelled())
                                    <button
                                        data-id="{{ $item->id }}"
                                        data-name="{{ $item->product->name }}"
                                        onclick="openCancelItemModal(this.dataset.id, this.dataset.name)"
                                        class="text-xs text-red-600 hover:text-red-700 font-medium hover:underline">
                                        Batalkan Item
                                    </button>
                                    @endif

                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="border-t border-gray-100 mt-5 pt-4 space-y-2 text-sm">
                        <div class="flex justify-between text-gray-600">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-gray-600">
                            <span>{{ $order->payment?->method === 'cod' ? 'Pengiriman + Biaya COD' : 'Ongkos Kirim' }}</span>
                            <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between font-bold text-gray-800 text-base pt-2 border-t border-gray-100">
                            <span>Total</span>
                            <span class="text-herbal-800">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Shipping Address --}}
                @if ($order->address)
                <div class="card p-6">
                    <h3 class="font-semibold text-gray-800 mb-3">Alamat Pengiriman</h3>
                    <p class="font-semibold text-gray-800">{{ $order->address->recipient_name }}</p>
                    <p class="text-sm text-gray-500 mt-1">{{ $order->address->phone }}</p>
                    <p class="text-sm text-gray-500">{{ $order->address->full_address }}</p>
                </div>
                @endif

                {{-- Payment Info --}}
                @if ($order->payment)
                <div class="card p-6">
                    <h3 class="font-semibold text-gray-800 mb-3">Informasi Pembayaran</h3>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-500">Metode</span>
                            <span class="font-medium text-gray-800">{{ $order->payment->method_label }}</span>
                        </div>
                        @if ($order->payment->method === 'bank_transfer' && $order->payment->account_number)
                        <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Tujuan Transfer</p>
                            <p class="mt-2 text-sm font-semibold text-slate-800">{{ $order->payment->account_name }}</p>
                            <p class="mt-1 text-sm font-mono text-slate-700">{{ $order->payment->account_number }}</p>
                        </div>
                        @endif
                        <div class="flex justify-between">
                            <span class="text-gray-500">Status</span>
                            @php $ps = $order->payment->status === 'verified' ? 'green' : ($order->payment->status === 'failed' ? 'red' : 'yellow') @endphp
                            <span class="badge bg-{{ $ps }}-100 text-{{ $ps }}-700 font-semibold">{{ $order->payment->status_label }}</span>
                        </div>
                        @if ($order->payment->paid_at)
                        <div class="flex justify-between">
                            <span class="text-gray-500">Dibayar pada</span>
                            <span class="font-medium text-gray-800">{{ $order->payment->paid_at->format('d M Y') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>

            {{-- Reviews Section (for completed orders) --}}
            @if ($order->status === 'completed')
            <div class="card p-6">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <span>⭐ Berikan Ulasan Produk</span>
                </h3>
                
                @if ($order->items->isEmpty())
                    <p class="text-gray-500 text-sm">Tidak ada produk dalam pesanan ini.</p>
                @else
                    <div class="space-y-4">
                        @foreach ($order->items as $item)
                            @php
                                $hasReview = $order->reviews()->where('product_id', $item->product_id)->where('user_id', auth()->id())->exists();
                            @endphp
                            <div class="border border-gray-200 rounded-xl p-4 hover:border-herbal-300 transition">
                                <div class="flex items-center gap-4">
                                    {{-- Product Image --}}
                                    <div class="w-16 h-16 flex-shrink-0 rounded-lg overflow-hidden bg-gray-100">
                                        @if ($item->product->image)
                                            <img src="{{ Storage::url($item->product->image) }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-herbal-50">
                                                <svg class="w-6 h-6 text-herbal-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m0 0l8 4m0 0l8-4m0 0v10l-8 4m0 0l-8-4m0 0v-10" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    {{-- Product Info & Review Status --}}
                                    <div class="flex-1 min-w-0">
                                        <a href="{{ route('product.show', $item->product->slug) }}" class="font-semibold text-gray-800 hover:text-herbal-700 truncate block">
                                            {{ $item->product->name }}
                                        </a>
                                        <p class="text-sm text-gray-500 mt-1">Qty: {{ $item->quantity }} × Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                        
                                        @if ($hasReview)
                                            <div class="mt-2 inline-flex items-center gap-1 px-2 py-1 bg-green-50 border border-green-200 rounded-lg">
                                                <span class="text-xs font-semibold text-green-700">✓ Sudah diulas</span>
                                            </div>
                                        @else
                                            <button type="button" 
                                                onclick="openReviewModal({{ $item->product->id }}, '{{ addslashes($item->product->name) }}')"
                                                class="mt-2 text-sm font-semibold text-herbal-700 hover:text-herbal-900 transition">
                                                → Tulis Ulasan
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
            @endif
            <div class="flex flex-wrap gap-3">
                @if ($order->status === 'pending')
                <form action="{{ route('orders.pay-now', $order) }}" method="POST"
                    onsubmit="this.querySelector('button').disabled=true; this.querySelector('button').textContent='⌛ Memproses...'">
                    @csrf
                    <button type="submit"
                        class="btn-primary py-2.5 px-6 text-sm">
                        ⚡ Bayar Sekarang
                    </button>
                </form>
                @if ($order->canBeCancelled())
                <button type="button" onclick="document.getElementById('cancel-modal').classList.remove('hidden')"
                    class="border border-red-300 text-red-600 hover:bg-red-50 py-2.5 px-5 rounded-xl text-sm font-medium transition">
                    Batalkan Pesanan
                </button>
                @endif
                @elseif ($order->status === 'processing' && $order->canBeCancelled())
                <button type="button" onclick="document.getElementById('cancel-modal').classList.remove('hidden')"
                    class="border border-red-300 text-red-600 hover:bg-red-50 py-2.5 px-5 rounded-xl text-sm font-medium transition">
                    Batalkan Pesanan
                </button>
                @elseif ($order->status === 'cancelled')
                <form action="{{ route('orders.buy-again', $order) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-primary py-2.5 px-6 text-sm">
                        🛒 Beli Lagi
                    </button>
                </form>
                @endif

                <a href="{{ route('orders.index') }}" class="inline-flex items-center gap-2 text-herbal-700 hover:text-herbal-900 text-sm font-medium py-2.5">
                    ← Kembali ke Riwayat Pesanan
                </a>
            </div>

            @include('dashboard.review-modal')

            {{-- Inline cancel modal --}}
            @if ($order->canBeCancelled())
            <div id="cancel-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
                    <h3 class="font-bold text-gray-900 text-lg mb-4">Batalkan Pesanan</h3>
                    <form action="{{ route('orders.cancel', $order) }}" method="POST">
                        @csrf
                        <div class="space-y-2 mb-5">
                            @foreach (['Salah memilih produk', 'Ingin mengubah alamat', 'Harga terlalu mahal', 'Estimasi pengiriman terlalu lama', 'Kendala pembayaran', 'Lainnya'] as $reason)
                            <label class="flex items-center gap-3 p-3 border rounded-xl cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="cancel_reason" value="{{ $reason }}" required class="text-red-500">
                                <span class="text-sm">{{ $reason }}</span>
                            </label>
                            @endforeach
                        </div>
                        <div class="flex gap-3">
                            <button type="button" onclick="document.getElementById('cancel-modal').classList.add('hidden')"
                                class="flex-1 border border-gray-300 text-gray-700 py-2.5 rounded-xl text-sm hover:bg-gray-50">
                                Batal
                            </button>
                            <button type="submit"
                                class="flex-1 bg-red-600 text-white py-2.5 rounded-xl text-sm font-semibold hover:bg-red-700">
                                Konfirmasi Batalkan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            {{-- Inline cancel item modal --}}
            <div id="cancel-item-modal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
                    <h3 class="font-bold text-gray-900 text-lg mb-2">Batalkan Item</h3>
                    <p id="cancel-item-name" class="text-gray-600 text-sm mb-4"></p>
                    <form id="cancel-item-form" method="POST">
                        @csrf
                        <div class="space-y-2 mb-5">
                            @foreach (['Salah memilih', 'Harga terlalu mahal', 'Ingin batal sebagian', 'Stok terbatas', 'Lainnya'] as $reason)
                            <label class="flex items-center gap-3 p-3 border rounded-xl cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="cancel_reason" value="{{ $reason }}" required class="text-red-500">
                                <span class="text-sm">{{ $reason }}</span>
                            </label>
                            @endforeach
                        </div>
                        <div class="flex gap-3">
                            <button type="button" onclick="closeCancelItemModal()"
                                class="flex-1 border border-gray-300 text-gray-700 py-2.5 rounded-xl text-sm hover:bg-gray-50">
                                Batal
                            </button>
                            <button type="submit"
                                class="flex-1 bg-red-600 text-white py-2.5 rounded-xl text-sm font-semibold hover:bg-red-700">
                                Konfirmasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <script>
                function openCancelItemModal(itemId, productName) {
                    const form = document.getElementById('cancel-item-form');
                    form.action = '{{ route("orders.cancel-item", [$order, ":itemId"]) }}'.replace(':itemId', itemId);
                    document.getElementById('cancel-item-name').textContent = 'Batalkan: ' + productName;
                    document.getElementById('cancel-item-modal').classList.remove('hidden');
                }

                function closeCancelItemModal() {
                    document.getElementById('cancel-item-modal').classList.add('hidden');
                    document.getElementById('cancel-item-form').reset();
                }
            </script>
        </div>

        @if ($showShipmentSection)
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const mapElement = document.getElementById('shipment-leaflet-map');

                if (!mapElement || typeof L === 'undefined') {
                    return;
                }

                const marker = JSON.parse(mapElement.dataset.marker || 'null');

                if (!marker) {
                    return;
                }

                const map = L.map(mapElement, {
                    zoomControl: true,
                    attributionControl: false,
                });

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 18,
                }).addTo(map);

                const icon = L.divIcon({
                    className: '',
                    html: `<div style="width:16px;height:16px;border-radius:9999px;background:${marker.is_current ? '#f97316' : marker.active ? '#10b981' : '#94a3b8'};border:3px solid rgba(255,255,255,.9);box-shadow:0 8px 18px rgba(15,23,42,.25)"></div>`,
                    iconSize: [22, 22],
                    iconAnchor: [11, 11],
                });

                L.marker([marker.lat, marker.lng], {
                        icon
                    })
                    .addTo(map)
                    .bindPopup(`<strong>${marker.label}</strong><br>${marker.status}`)
                    .openPopup();

                map.setView([marker.lat, marker.lng], 8);
            });
        </script>
        @endif

    </x-slot>
</x-layouts.dashboard>