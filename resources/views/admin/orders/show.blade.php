<x-layouts.admin>
    <x-slot name="title">Detail Pesanan

    @php
    $latestTracking = $order->trackingUpdates->sortByDesc('created_at')->first();
    $isShipped = old('status', $order->status) === 'shipped';
    @endphp

    <div class="max-w-4xl space-y-6">
        <div class="flex items-center gap-3 mb-2">
            <a href="{{ route('admin.orders.index') }}" class="text-gray-400 hover:text-gray-700 text-sm">← Kembali</a>
        </div>

        {{-- Status Update Form --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h3 class="font-bold text-gray-800 mb-5">Update Status Pesanan</h3>
            <form action="{{ route('admin.orders.update-status', $order) }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4 items-end">
                @csrf @method('PATCH')
                <div>
                    <label class="form-label">Status</label>
                    <select name="status" id="order-status-input" class="form-input py-2 text-sm">
                        @foreach (['pending' => 'Menunggu Pembayaran', 'paid' => 'Dibayar', 'processing' => 'Sedang Diproses', 'shipped' => 'Dikirim', 'completed' => 'Selesai', 'cancelled' => 'Dibatalkan'] as $val => $lbl)
                        <option value="{{ $val }}" {{ $order->status === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="shipping-only {{ $isShipped ? '' : 'hidden' }}">
                    <label class="form-label">No. Resi Pengiriman</label>
                    <input type="text" name="tracking_number" value="{{ old('tracking_number', $order->tracking_number) }}"
                        class="form-input py-2 text-sm" placeholder="Masukkan nomor resi...">
                </div>
                <div class="shipping-only {{ $isShipped ? '' : 'hidden' }}">
                    <label class="form-label">Nama Kurir</label>
                    <select name="courier_name" class="form-input py-2 text-sm">
                        <option value="">-- Pilih Kurir --</option>
                        @php
                       
                        $shippingSettings = \App\Models\Setting::getGroup('shipping');
                        $courierLabels = [
                        'jne' => 'JNE',
                        'jnt' => 'J&T',
                        'sicepat' => 'SiCepat',

                        ];
                       
                        $activeCouriers = array_filter($courierLabels, fn($key) =>
                        ($shippingSettings["courier_{$key}_active"] ?? false)
                        , ARRAY_FILTER_USE_KEY);
                        @endphp
                        @forelse ($activeCouriers as $key => $label)
                        <option value="{{ $key }}" {{ old('courier_name', $order->courier_name) === $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                        @empty
                        <option value="" disabled>Tidak ada kurir aktif</option>
                        @endforelse
                    </select>
                </div>
                <button type="submit" class="btn-primary text-sm py-2 px-5">Simpan Update</button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Order Info --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="font-bold text-gray-800 mb-4">Info Pesanan</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-gray-500">No. Pesanan</span><span class="font-mono font-bold text-herbal-700">{{ $order->order_number }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Tanggal</span><span>{{ $order->created_at->format('d M Y, H:i') }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Pelanggan</span><span class="font-medium">{{ $order->user->name }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Email</span><span>{{ $order->user->email }}</span></div>
                    @if ($order->courier_label)
                    <div class="flex justify-between"><span class="text-gray-500">Kurir</span><span class="font-medium text-gray-800">{{ $order->courier_label }}</span></div>
                    @endif
                    @if ($order->tracking_number)
                    <div class="flex justify-between"><span class="text-gray-500">No. Resi</span><span class="font-mono font-bold text-blue-700">{{ $order->tracking_number }}</span></div>
                    @endif
                    @if ($order->estimated_delivery_at)
                    <div class="flex justify-between"><span class="text-gray-500">Estimasi Tiba</span><span class="flex items-center gap-2"><span class="text-xs font-medium px-2 py-1 rounded-full bg-green-100 text-green-700">Otomatis</span><span>{{ $order->estimated_delivery_at->format('d M Y, H:i') }} WIB</span></span></div>
                    @endif
                    @if ($latestTracking)
                    <div class="pt-2 border-t border-gray-100"><span class="text-gray-500 block mb-1">Tracking Terakhir:</span>
                        <p class="text-gray-800 font-semibold">{{ $latestTracking->keterangan }}</p>
                        <p class="text-sm text-gray-500">{{ $latestTracking->lokasi }} • {{ $latestTracking->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    @endif
                    @if ($order->notes)
                    <div class="pt-2 border-t border-gray-100"><span class="text-gray-500 block mb-1">Catatan:</span>
                        <p class="text-gray-700">{{ $order->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Address + Payment --}}
            <div class="space-y-5">
                @if ($order->address)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <h4 class="font-semibold text-gray-700 mb-2 text-sm">Alamat Pengiriman</h4>
                    <p class="font-semibold text-gray-800">{{ $order->address->recipient_name }}</p>
                    <p class="text-sm text-gray-500 mt-0.5">{{ $order->address->phone }}</p>
                    <p class="text-sm text-gray-500">{{ $order->address->full_address }}</p>
                </div>
                @endif
                @if ($order->payment)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <h4 class="font-semibold text-gray-700 mb-2 text-sm">Pembayaran</h4>
                    <div class="text-sm space-y-1">
                        <div class="flex justify-between"><span class="text-gray-500">Metode</span><span class="font-medium">{{ $order->payment->method_label }}</span></div>
                        @php $ps = $order->payment->status === 'verified' ? 'green' : ($order->payment->status === 'failed' ? 'red' : 'yellow') @endphp
                        <div class="flex justify-between"><span class="text-gray-500">Status</span><span class="badge bg-{{ $ps }}-100 text-{{ $ps }}-700 font-semibold">{{ ucfirst($order->payment->status) }}</span></div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Order Items --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-100 font-bold text-gray-800">Item Pesanan</div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="px-5 py-3 text-gray-500 font-semibold">Produk</th>
                        <th class="px-5 py-3 text-gray-500 font-semibold">Harga Satuan</th>
                        <th class="px-5 py-3 text-gray-500 font-semibold">Qty</th>
                        <th class="px-5 py-3 text-gray-500 font-semibold">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach ($order->items as $item)
                    <tr>
                        <td class="px-5 py-3 font-medium text-gray-800">{{ $item->product->name }}</td>
                        <td class="px-5 py-3 text-gray-600">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                        <td class="px-5 py-3 text-gray-600">{{ $item->quantity }}</td>
                        <td class="px-5 py-3 font-semibold text-gray-800">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 border-t border-gray-200 text-sm">
                    <tr>
                        <td colspan="3" class="px-5 py-3 text-right text-gray-600">Subtotal</td>
                        <td class="px-5 py-3 font-semibold">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="px-5 py-2 text-right text-gray-600">{{ $order->payment?->method === 'cod' ? 'Pengiriman + Biaya COD' : 'Ongkos Kirim' }}</td>
                        <td class="px-5 py-2 font-semibold">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="px-5 py-3 text-right font-bold text-gray-800">TOTAL</td>
                        <td class="px-5 py-3 font-extrabold text-herbal-800 text-base">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-[1.1fr_0.9fr] gap-6">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-center justify-between gap-3 mb-5">
                    <div>
                        <h3 class="font-bold text-gray-800">Auto-Generate Tracking</h3>
                        <p class="text-sm text-gray-500 mt-1">Saat status menjadi Dikirim, sistem akan membuat 4 checkpoint simulasi secara otomatis.</p>
                    </div>
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-orange-50 text-orange-700 text-xs font-semibold ring-1 ring-orange-100">
                        {{ $order->trackingUpdates->count() }} update
                    </span>
                </div>

                <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 p-4 space-y-3 text-sm text-gray-600">
                    <div class="flex justify-between gap-3"><span>Paket di-pickup kurir</span><span class="text-gray-400">+ 0 menit</span></div>
                    <div class="flex justify-between gap-3"><span>Tiba di gudang asal</span><span class="text-gray-400">+ 2 jam</span></div>
                    <div class="flex justify-between gap-3"><span>Dalam perjalanan ke kota tujuan</span><span class="text-gray-400">+ 8 jam</span></div>
                    <div class="flex justify-between gap-3"><span>Paket tiba di gudang tujuan</span><span class="text-gray-400">+ 20 jam</span></div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="font-bold text-gray-800 mb-5">Preview Tracking</h3>

                @forelse ($order->trackingUpdates->sortByDesc('created_at') as $update)
                @php $isActive = $update->created_at->lte(now()); @endphp
                <div class="relative pl-6 pb-5 last:pb-0">
                    @unless ($loop->last)
                    <span class="absolute left-[7px] top-4 bottom-0 w-px {{ $isActive ? 'bg-emerald-200' : 'bg-gray-200' }}"></span>
                    @endunless
                    <span class="absolute left-0 top-1.5 w-4 h-4 rounded-full {{ $isActive ? 'bg-emerald-500 ring-4 ring-emerald-50' : 'bg-gray-300 ring-4 ring-gray-100' }}"></span>
                    <div class="rounded-2xl border {{ $isActive ? 'border-emerald-100 bg-emerald-50/60' : 'border-gray-200 bg-gray-50/90' }} p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold {{ $isActive ? 'text-gray-800' : 'text-gray-500' }}">{{ $update->keterangan }}</p>
                                <p class="text-sm {{ $isActive ? 'text-gray-500' : 'text-gray-400' }} mt-1">{{ $update->lokasi }}</p>
                            </div>
                            <span class="text-xs font-medium {{ $isActive ? 'text-emerald-600' : 'text-gray-400' }} whitespace-nowrap">{{ $update->created_at->format('d M Y, H:i') }}</span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 px-5 py-8 text-center">
                    <p class="font-medium text-gray-700">Belum ada tracking otomatis.</p>
                    <p class="text-sm text-gray-500 mt-1">Ubah status pesanan ke Dikirim untuk membuat checkpoint pengiriman.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusInput = document.getElementById('order-status-input');
            const shippingOnlyFields = document.querySelectorAll('.shipping-only');

            if (!statusInput) return;

            const syncShippingFields = () => {
                const show = statusInput.value === 'shipped';
                shippingOnlyFields.forEach((field) => field.classList.toggle('hidden', !show));
            };

            statusInput.addEventListener('change', syncShippingFields);
            syncShippingFields();
        });
    </script>
    @endpush
</x-layouts.admin>