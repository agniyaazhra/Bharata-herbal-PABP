<x-layouts.dashboard>
    <x-slot name="title">Riwayat Pesanan</x-slot>
    <x-slot name="slot">

        <div x-data="orderModals()" @keydown.escape="closeAll()">

            {{-- Page Header --}}
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="font-bold text-gray-800 text-xl">Riwayat Pesanan</h2>
                    <p class="text-sm text-gray-500 mt-0.5">Kelola dan pantau semua pesanan Anda</p>
                </div>
            </div>

            {{-- Status Tabs --}}
            @php
            $tabs = [
            'all' => ['label' => 'Semua', 'status' => null],
            'pending' => ['label' => 'Belum Bayar', 'status' => 'pending'],
            'processing' => ['label' => 'Sedang Dikemas','status' => 'processing'],
            'shipped' => ['label' => 'Dikirim', 'status' => 'shipped'],
            'completed' => ['label' => 'Selesai', 'status' => 'completed'],
            'cancelled' => ['label' => 'Dibatalkan', 'status' => 'cancelled'],
            ];
            $totalAll = array_sum($counts ?? []);
            @endphp
            <div class="flex overflow-x-auto border-b border-gray-200 mb-6 gap-0">
                @foreach ($tabs as $key => $tabInfo)
                <a href="{{ route('orders.index', ['tab' => $key]) }}"
                    class="flex items-center gap-1.5 px-4 py-3 text-sm font-medium whitespace-nowrap border-b-2 transition-colors
                    {{ $tab === $key
                        ? 'border-herbal-600 text-herbal-700'
                        : 'border-transparent text-gray-600 hover:text-herbal-700 hover:border-herbal-300' }}">
                    {{ $tabInfo['label'] }}
                    @php
                    $cnt = $key === 'all' ? $totalAll : ($counts[$tabInfo['status']] ?? 0);
                    @endphp
                    @if ($cnt > 0)
                    <span class="text-xs px-1.5 py-0.5 rounded-full font-semibold
                        {{ $tab === $key ? 'bg-herbal-100 text-herbal-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $cnt }}
                    </span>
                    @endif
                </a>
                @endforeach
            </div>

            {{-- Orders List --}}
            @if ($orders->isEmpty())
            <div class="py-16 text-center text-gray-400 card">
                <p class="text-5xl mb-4">📦</p>
                <p class="font-semibold text-gray-600 text-lg">Belum ada pesanan</p>
                <p class="text-sm text-gray-400 mt-1 mb-5">
                    @if ($tab !== 'all')
                    Tidak ada pesanan dengan status "{{ $tabs[$tab]['label'] ?? $tab }}"
                    @else
                    Anda belum pernah melakukan pemesanan
                    @endif
                </p>
                <a href="{{ route('shop') }}" class="btn-primary text-sm py-2.5 px-6">Mulai Belanja</a>
            </div>
            @else
            <div class="space-y-4">
                @foreach ($orders as $order)
                @foreach ($order->items as $item)
                @php $c = $order->status_color; @endphp
                <div class="card overflow-hidden">
                    {{-- Order Header --}}
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-4 bg-gray-50 border-b border-gray-100">
                        <div class="flex items-center gap-3 flex-wrap">
                            <span class="font-mono font-bold text-herbal-700 text-sm">
                                {{ $order->order_number }}
                            </span>
                            <span class="text-gray-400 text-xs">•</span>
                            <span class="text-gray-500 text-xs">{{ $order->created_at->format('d M Y, H:i') }}</span>
                            <span class="badge bg-{{ $c }}-100 text-{{ $c }}-700 font-semibold text-xs">
                                {{ $order->status_label }}
                            </span>
                        </div>
                        @if ($order->status === 'pending' && $order->payment_deadline)
                        @php $deadline = $order->payment_deadline; @endphp
                        @if ($deadline->isFuture())
                        <div class="flex items-center gap-1.5 text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-3 py-1.5"
                            x-data="countdownTimer('{{ $deadline->toIso8601String() }}')">
                            ⏳ Bayar sebelum {{ $deadline->format('H:i') }} WIB
                            <span x-text="remaining" class="ml-1 font-semibold" x-show="remaining !== ''"></span>
                        </div>
                        @endif
                        @endif
                    </div>

                    {{-- Single Order Item Card --}}
                    <div class="px-5 py-4">
                        <div class="flex items-start gap-4">
                            <div class="w-16 h-16 rounded-lg overflow-hidden bg-herbal-50 shrink-0 border border-gray-100">
                                @if ($item->product->image)
                                <img src="{{ Storage::url($item->product->image) }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                @else
                                <div class="w-full h-full flex items-center justify-center text-2xl">🌿</div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-gray-800">{{ $item->product->name }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">Rp {{ number_format($item->price, 0, ',', '.') }} × {{ $item->quantity }}<span class="mx-1.5">•</span>Total: <span class="font-semibold text-gray-700">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span></p>
                                @if ($item->status !== 'active')
                                <span class="inline-block mt-1.5 text-xs px-2 py-0.5 bg-red-100 text-red-700 rounded-md font-medium">❌ Dibatalkan</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- ✅ ACTION BUTTONS - DIPERBAIKI --}}
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-5 py-3 border-t border-gray-100 bg-gray-50/50">
                        <div class="flex flex-wrap gap-2">
                            @if ($order->status === 'pending' && $item->status === 'active')
                            @if ($item->canBeCancelled())
                            <button type="button"
                                @click="openCancelItem({{ $order->id }}, {{ $item->id }}, '{{ addslashes($item->product->name) }}')"
                                class="text-xs font-medium border border-red-300 text-red-600 hover:bg-red-50 px-3 py-2 rounded-lg transition">
                                Batalkan Produk
                            </button>
                            @endif
                            <form action="{{ route('orders.pay-now', $order) }}" method="POST" onsubmit="return confirmPayNow(this)">
                                @csrf
                                <button type="submit" class="text-xs font-semibold bg-herbal-700 hover:bg-herbal-800 text-white px-4 py-2 rounded-lg transition">
                                    ⚡ Bayar Sekarang
                                </button>
                            </form>
                            @if ($order->payment_deadline && $order->payment_deadline->isFuture())
                            <span class="text-xs text-amber-700 bg-amber-50 border border-amber-200 px-3 py-2 rounded-lg">
                                🕐 Bayar Nanti — batas {{ $order->payment_deadline->format('H:i') }}
                            </span>
                            @endif

                            @elseif ($order->status === 'paid')
                            <span class="text-xs text-blue-600 font-medium px-3 py-2 bg-blue-50 rounded-lg">
                                ✅ Pembayaran Dikonfirmasi — menunggu dikemas
                            </span>

                            @elseif ($order->status === 'processing' && $item->status === 'active')
                            @if ($item->canBeCancelled())
                            <button type="button"
                                @click="openCancelItem({{ $order->id }}, {{ $item->id }}, '{{ addslashes($item->product->name) }}')"
                                class="text-xs font-medium border border-red-300 text-red-600 hover:bg-red-50 px-3 py-2 rounded-lg transition">
                                Batalkan Produk
                            </button>
                            @endif
                            <a href="https://wa.me/" target="_blank"
                                class="text-xs font-medium border border-gray-300 text-gray-700 hover:bg-gray-50 px-3 py-2 rounded-lg transition inline-flex items-center gap-1.5">
                                💬 Chat Penjual
                            </a>

                            @elseif ($order->status === 'shipped')
                            <a href="{{ route('orders.show', $order) }}"
                                class="text-xs font-medium border border-gray-300 text-gray-700 hover:bg-gray-50 px-3 py-2 rounded-lg transition">
                                Detail Pesanan
                            </a>
                            <a href="https://wa.me/" target="_blank"
                                class="text-xs font-medium border border-gray-300 text-gray-700 hover:bg-gray-50 px-3 py-2 rounded-lg transition inline-flex items-center gap-1.5">
                                💬 Chat Penjual
                            </a>

                            @elseif ($order->status === 'completed')
                            @php
                            $myReview = $order->reviews->firstWhere('product_id', $item->product_id);
                            $canReorder = $item->product && $item->product->stock > 0 && $item->product->status !== 'inactive';
                            @endphp

                            @if ($myReview)
                            @php
                            $reviewData = [
                            'id' => $myReview->id,
                            'product_name' => $item->product->name,
                            'rating' => (int) $myReview->rating,
                            'comment' => (string) ($myReview->comment ?? ''),
                            'image' => $myReview->image ? Storage::url($myReview->image) : null,
                            'created_at' => optional($myReview->created_at)->format('d M Y, H:i'),
                            ];
                            @endphp
                            <button type="button"
                                @click="openMyReview({{ Js::from($reviewData) }})"
                                class="text-xs font-medium border border-gray-300 text-gray-700 hover:bg-gray-50 px-3 py-2 rounded-lg transition">
                                Lihat Ulasan Saya
                            </button>
                            @else
                            <div class="inline-flex items-center gap-2">
                                <span class="text-xs font-medium text-gray-600">Nilai</span>
                                <button type="button"
                                    @click='openReviewSingle({{ $order->id }}, @json(["id" => $item->product_id, "name" => $item->product->name]))'
                                    class="text-xs border border-amber-300 text-amber-600 hover:bg-amber-50 px-3 py-2 rounded-lg transition">
                                    ☆ ☆ ☆ ☆ ☆
                                </button>
                            </div>
                            @endif

                            <form action="{{ route('orders.buy-again-item', $order) }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                <input type="hidden" name="quantity" value="{{ max(1, (int) $item->quantity) }}">
                                <button type="submit"
                                    class="text-xs font-semibold bg-herbal-700 hover:bg-herbal-800 text-white px-4 py-2 rounded-lg transition {{ $canReorder ? '' : 'opacity-50 cursor-not-allowed' }}"
                                    {{ $canReorder ? '' : 'disabled' }}>
                                    Beli Lagi
                                </button>
                            </form>

                            @elseif ($order->status === 'cancelled' && $item->status === 'cancelled')
                            @php
                            $canReorder = $item->product && $item->product->stock > 0 && $item->product->status !== 'inactive';
                            @endphp

                            @if ($item->cancel_reason)
                            <button type="button"
                                @click="openCancelDetail({{ Js::from([
                                            'order_id' => $order->order_number,
                                            'product_name' => $item->product->name,
                                            'quantity' => $item->quantity,
                                            'price' => $item->price,
                                            'reason' => $item->cancel_reason,
                                            'cancelled_at' => optional($item->cancelled_at)->format('d M Y, H:i') ?? optional($order->updated_at)->format('d M Y, H:i'),
                                        ]) }})"
                                class="text-xs font-medium border border-gray-300 text-gray-700 hover:bg-gray-50 px-3 py-2 rounded-lg transition">
                                Rincian Pembatalan
                            </button>
                            @endif

                            <form action="{{ route('orders.buy-again-item', $order) }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                <input type="hidden" name="quantity" value="{{ max(1, (int) $item->quantity) }}">
                                <button type="submit"
                                    class="text-xs font-semibold bg-herbal-700 hover:bg-herbal-800 text-white px-4 py-2 rounded-lg transition {{ $canReorder ? '' : 'opacity-50 cursor-not-allowed' }}"
                                    {{ $canReorder ? '' : 'disabled' }}>
                                    Beli Lagi
                                </button>
                            </form>

                            @endif
                        </div>

                        <a href="{{ route('orders.show', $order) }}"
                            class="text-herbal-700 hover:text-herbal-900 font-medium text-xs whitespace-nowrap">
                            Lihat Detail →
                        </a>
                    </div>
                </div>
                @endforeach
                @endforeach
            </div>

            <div class="mt-6">{{ $orders->links() }}</div>
            @endif

            {{-- ================================================================ --}}
            {{-- MODALS SECTION --}}
            {{-- ================================================================ --}}

            {{-- CANCEL ORDER MODAL --}}
            <div x-show="showCancel" x-transition.opacity
                class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
                style="display: none;">
                <div @click.stop
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900">Batalkan Pesanan</h3>
                                <p class="text-sm text-gray-500">Pilih alasan pembatalan</p>
                            </div>
                        </div>

                        <form :action="`/pesanan/${cancelOrderId}/batal`" method="POST">
                            @csrf
                            <div class="space-y-2 mb-5">
                                @php
                                $cancelReasons = [
                                'Salah memilih produk atau varian',
                                'Ingin mengubah alamat pengiriman',
                                'Harga terlalu mahal / menemukan harga lebih murah',
                                'Estimasi pengiriman terlalu lama',
                                'Kendala dengan metode pembayaran',
                                'Lainnya',
                                ];
                                @endphp
                                @foreach ($cancelReasons as $reason)
                                <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition">
                                    <input type="radio" name="cancel_reason" value="{{ $reason }}"
                                        class="text-red-500 focus:ring-red-400" required>
                                    <span class="text-sm text-gray-700">{{ $reason }}</span>
                                </label>
                                @endforeach
                            </div>

                            <div class="flex gap-3">
                                <button type="button" @click="closeAll()"
                                    class="flex-1 border border-gray-300 text-gray-700 py-2.5 rounded-xl text-sm font-medium hover:bg-gray-50 transition">
                                    Batal
                                </button>
                                <button type="submit"
                                    class="flex-1 bg-red-600 hover:bg-red-700 text-white py-2.5 rounded-xl text-sm font-semibold transition">
                                    Konfirmasi Batalkan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- CANCEL ITEM MODAL --}}
            <div x-show="showCancelItem" x-transition.opacity
                class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
                style="display: none;">
                <div @click.stop
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900">Batalkan Produk</h3>
                                <p class="text-sm text-gray-500" x-text="`${cancelItemName}`"></p>
                            </div>
                        </div>

                        <form :action="`/pesanan/${cancelOrderIdForItem}/item/${cancelItemId}/batal`" method="POST">
                            @csrf
                            <div class="space-y-2 mb-5">
                                @php
                                $cancelReasons = [
                                'Salah memilih',
                                'Harga terlalu mahal',
                                'Ingin batal sebagian',
                                'Stok terbatas',
                                'Lainnya',
                                ];
                                @endphp
                                @foreach ($cancelReasons as $reason)
                                <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-50 transition">
                                    <input type="radio" name="cancel_reason" value="{{ $reason }}"
                                        class="text-red-500 focus:ring-red-400" required>
                                    <span class="text-sm text-gray-700">{{ $reason }}</span>
                                </label>
                                @endforeach
                            </div>

                            <div class="flex gap-3">
                                <button type="button" @click="closeAll()"
                                    class="flex-1 border border-gray-300 text-gray-700 py-2.5 rounded-xl text-sm font-medium hover:bg-gray-50 transition">
                                    Batal
                                </button>
                                <button type="submit"
                                    class="flex-1 bg-red-600 hover:bg-red-700 text-white py-2.5 rounded-xl text-sm font-semibold transition">
                                    Batalkan Produk
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- REVIEW SINGLE ITEM MODAL --}}
            <div x-show="showReviewSingle" x-transition.opacity
                class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
                style="display: none;">
                <div @click.stop
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center">⭐</div>
                                <div>
                                    <h3 class="font-bold text-gray-900">Beri Penilaian</h3>
                                    <p class="text-sm text-gray-500" x-text="`${reviewProductName}`"></p>
                                </div>
                            </div>
                            <button @click="closeAll()" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <form :action="`/pesanan/${reviewOrderId}/ulasan`" method="POST" enctype="multipart/form-data">
                            @csrf

                            <input type="hidden" name="product_id" :value="reviewProductId">

                            <div class="mb-4" x-data="{ rating: 0, hover: 0 }">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Rating</label>
                                <div class="flex gap-1">
                                    <template x-for="star in [1, 2, 3, 4, 5]" :key="star">
                                        <button type="button"
                                            @mouseenter="hover = star"
                                            @mouseleave="hover = 0"
                                            @click="rating = star"
                                            class="text-3xl transition-transform hover:scale-110 focus:outline-none">
                                            <span :class="(hover || rating) >= star ? 'text-amber-400' : 'text-gray-200'">★</span>
                                        </button>
                                    </template>
                                </div>
                                <input type="hidden" name="rating" :value="rating" required>
                                <p class="text-xs text-gray-500 mt-1" x-text="['', 'Sangat Buruk', 'Buruk', 'Cukup', 'Bagus', 'Sangat Bagus'][rating]"></p>
                            </div>

                            <div class="mb-4">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Ulasan (opsional)</label>
                                <textarea name="comment" rows="3"
                                    class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:border-herbal-500 focus:ring-2 focus:ring-herbal-200 outline-none resize-none"
                                    placeholder="Ceritakan pengalaman Anda dengan produk ini..."></textarea>
                            </div>

                            <div class="mb-6">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Foto (opsional)</label>
                                <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-gray-200 rounded-xl cursor-pointer hover:border-herbal-400 hover:bg-herbal-50 transition">
                                    <svg class="w-6 h-6 text-gray-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-xs text-gray-500">Unggah foto produk (max 2MB)</span>
                                    <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="hidden">
                                </label>
                            </div>

                            <div class="flex gap-3">
                                <button type="button" @click="closeAll()"
                                    class="flex-1 border border-gray-300 text-gray-700 py-2.5 rounded-xl text-sm font-medium hover:bg-gray-50 transition">
                                    Batal
                                </button>
                                <button type="submit"
                                    class="flex-1 bg-amber-500 hover:bg-amber-600 text-white py-2.5 rounded-xl text-sm font-semibold transition">
                                    Kirim Ulasan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- CANCELLATION DETAIL MODAL --}}
            <div x-show="showCancelDetail" x-transition.opacity
                class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
                style="display: none;">
                <div @click.stop
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-5">
                            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900">Rincian Pembatalan</h3>
                                <p class="text-sm text-gray-500">Detail pesanan yang dibatalkan</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">No. Pesanan</label>
                                <p class="text-sm font-mono font-bold text-gray-800 mt-1" x-text="`#${cancelDetail?.order_id || '-'}`"></p>
                            </div>

                            <div>
                                <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Produk</label>
                                <p class="text-sm font-semibold text-gray-800 mt-1" x-text="cancelDetail?.product_name || '-'"></p>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    <span x-text="`Qty: ${cancelDetail?.quantity || '-'}`"></span>
                                    <span x-text="`• Rp ${new Intl.NumberFormat('id-ID').format(cancelDetail?.price || 0)}`"></span>
                                </p>
                            </div>

                            <div>
                                <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Waktu Pembatalan</label>
                                <p class="text-sm text-gray-700 mt-1" x-text="cancelDetail?.cancelled_at || '-'"></p>
                            </div>

                            <div>
                                <label class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Alasan Pembatalan</label>
                                <p class="text-sm text-gray-700 mt-1 leading-relaxed" x-text="cancelDetail?.reason || '-'"></p>
                            </div>
                        </div>

                        <div class="mt-6">
                            <button type="button" @click="closeAll()"
                                class="w-full border border-gray-300 text-gray-700 py-2.5 rounded-xl text-sm font-medium hover:bg-gray-50 transition">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- MY REVIEW MODAL --}}
            <div x-show="showMyReview" x-transition.opacity
                class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
                style="display: none;">
                <div @click.stop
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 scale-95"
                    x-transition:enter-end="opacity-100 scale-100"
                    class="bg-white rounded-2xl shadow-2xl w-full max-w-md">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-bold text-gray-900">Ulasan Saya</h3>
                            <button @click="closeAll()" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <p class="text-sm font-semibold text-gray-800" x-text="myReview?.product_name"></p>
                        <div class="mt-3 text-amber-500 text-lg" x-text="'★'.repeat(myReview?.rating || 0) + '☆'.repeat(5 - (myReview?.rating || 0))"></div>

                        <template x-if="myReview?.comment">
                            <p class="mt-4 text-sm text-gray-600 leading-relaxed" x-text="myReview.comment"></p>
                        </template>

                        <template x-if="myReview?.image">
                            <img :src="myReview.image" alt="Foto ulasan" class="mt-4 w-full h-44 object-cover rounded-xl border border-gray-100">
                        </template>

                        <p class="mt-4 text-xs text-gray-400" x-text="myReview?.created_at ? `Dikirim: ${myReview.created_at}` : ''"></p>

                        <div class="mt-6 flex gap-3">
                            <button type="button" @click="closeAll()"
                                class="flex-1 border border-gray-300 text-gray-700 py-2.5 rounded-xl text-sm font-medium hover:bg-gray-50 transition">
                                Tutup
                            </button>
                            <form :action="`/ulasan/${myReview?.id}/hapus`" method="POST" class="flex-1">
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menghapus ulasan ini?')"
                                    class="w-full bg-red-600 hover:bg-red-700 text-white py-2.5 rounded-xl text-sm font-semibold transition">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>{{-- end x-data --}}

    </x-slot>
</x-layouts.dashboard>

<script>
    function orderModals() {
        return {
            showCancel: false,
            cancelOrderId: null,
            showCancelItem: false,
            cancelItemId: null,
            cancelItemName: '',
            cancelOrderIdForItem: null,
            showReviewSingle: false,
            reviewOrderId: null,
            reviewProductId: null,
            reviewProductName: '',
            showCancelDetail: false,
            cancelDetail: null,
            showMyReview: false,
            myReview: null,

            openCancel(orderId) {
                this.cancelOrderId = orderId;
                this.showCancel = true;
            },
            openCancelItem(orderId, itemId, itemName) {
                this.cancelOrderIdForItem = orderId;
                this.cancelItemId = itemId;
                this.cancelItemName = itemName;
                this.showCancelItem = true;
            },
            openReviewSingle(orderId, product) {
                this.reviewOrderId = orderId;
                this.reviewProductId = product.id;
                this.reviewProductName = product.name;
                this.showReviewSingle = true;
            },
            openCancelDetail(data) {
                this.cancelDetail = data;
                this.showCancelDetail = true;
            },
            openMyReview(review) {
                this.myReview = review;
                this.showMyReview = true;
            },
            closeAll() {
                this.showCancel = false;
                this.showCancelItem = false;
                this.showReviewSingle = false;
                this.showCancelDetail = false;
                this.showMyReview = false;
            }
        }
    }

    function countdownTimer(deadlineStr) {
        return {
            remaining: '',
            init() {
                const updateCountdown = () => {
                    const now = new Date();
                    const deadline = new Date(deadlineStr);
                    const diff = deadline - now;

                    if (diff <= 0) {
                        this.remaining = '(Waktu habis)';
                        return;
                    }

                    const hours = Math.floor(diff / (1000 * 60 * 60));
                    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((diff % (1000 * 60)) / 1000);

                    this.remaining = `(${hours}j ${minutes}m ${seconds}d)`;
                };

                updateCountdown();
                setInterval(updateCountdown, 1000);
            }
        }
    }
</script>