<x-app-layout>
    <x-slot name="title">Checkout</x-slot>

    @php
        $subtotalDisplay = $selectedItems->sum(fn($i) => $i->quantity * $i->product->effective_price);
        $shippingCostDisplay = (int) ($shippingPreview['shipping_cost'] ?? 0);
        $isFreeShipping = $shippingPreview['is_free_shipping'] ?? false;
        $flatRateDisplay = $shippingCostDisplay;
        $codFeeDisplay = (int) \App\Models\Setting::get('payment', 'cod_fee', 15000);
        $minimumOrderAmount = (int) ($minimumOrderAmount ?? 0);
        $belowMinimum = $minimumOrderAmount > 0 && $subtotalDisplay < $minimumOrderAmount;
        $initialTotal = $subtotalDisplay + $shippingCostDisplay + $codFeeDisplay;
    @endphp

    <x-breadcrumb :crumbs="[
        ['label' => 'Beranda', 'url' => route('home')],
        ['label' => 'Pesanan Saya', 'url' => route('orders.index')],
        ['label' => 'Bayar Sekarang'],
    ]" />

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Checkout</h1>

        <form action="{{ route('checkout.store') }}" method="POST">
            @csrf
            @if ($belowMinimum)
                <div class="mb-5 p-4 bg-blue-50 border border-blue-200 rounded-xl text-blue-800 text-sm">
                    Minimum checkout adalah <strong>Rp {{ number_format($minimumOrderAmount, 0, ',', '.') }}</strong>.
                    Total produk dipilih saat ini <strong>Rp {{ number_format($subtotalDisplay, 0, ',', '.') }}</strong>.
                </div>
            @endif
            <div class="flex flex-col lg:flex-row gap-8">

                {{-- ===== LEFT: Form ===== --}}
                <div class="flex-1 space-y-6">

                    {{-- Shipping Address --}}
                    <div class="card p-6">
                        <h2 class="font-bold text-gray-800 text-lg mb-5 flex items-center gap-2">
                            <span class="w-7 h-7 bg-herbal-100 text-herbal-700 rounded-full flex items-center justify-center text-sm font-bold">1</span>
                            Alamat Pengiriman
                        </h2>

                        @if ($addresses->isEmpty())
                            <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-xl text-yellow-800 text-sm mb-4">
                                Anda belum memiliki alamat tersimpan.
                                <a href="{{ route('user.addresses') }}" class="underline font-semibold">Tambah alamat</a>
                            </div>
                        @else
                            <div class="space-y-3">
                                @foreach ($addresses as $address)
                                <label class="flex items-start gap-4 p-4 border-2 rounded-xl cursor-pointer transition-colors {{ $address->is_default ? 'border-herbal-600 bg-herbal-50' : 'border-gray-200 hover:border-herbal-300' }}">
                                    <input type="radio" name="address_id" value="{{ $address->id }}"
                                           {{ $address->is_default || old('address_id') == $address->id ? 'checked' : '' }}
                                           class="mt-1 text-herbal-600 focus:ring-herbal-500">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="font-semibold text-gray-800">{{ $address->recipient_name }}</span>
                                            <span class="badge bg-gray-100 text-gray-600">{{ $address->label }}</span>
                                            @if ($address->is_default)
                                                <span class="badge bg-herbal-100 text-herbal-700">Utama</span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">{{ $address->phone }}</p>
                                        <p class="text-sm text-gray-500">{{ $address->full_address }}</p>
                                    </div>
                                </label>
                                @endforeach
                            </div>
                        @endif

                        <a href="{{ route('user.addresses') }}" class="inline-flex items-center gap-1.5 text-sm text-herbal-700 hover:text-herbal-900 font-medium mt-4">
                            + Tambah Alamat Baru
                        </a>

                        @error('address_id')<p class="text-red-500 text-sm mt-2">{{ $message }}</p>@enderror
                    </div>

                    {{-- Payment Method --}}
                    <div class="card p-6">
                        <h2 class="font-bold text-gray-800 text-lg mb-5 flex items-center gap-2">
                            <span class="w-7 h-7 bg-herbal-100 text-herbal-700 rounded-full flex items-center justify-center text-sm font-bold">2</span>
                            Metode Pembayaran
                        </h2>

                        @php
                        $paymentMethods = [
                            'cod'          => ['label' => 'Bayar di Tempat (COD)', 'icon' => '🏠', 'desc' => 'Bayar saat produk tiba', 'color' => 'amber'],
                            'dana'         => ['label' => 'Dana', 'icon' => '💙', 'desc' => 'Transfer via aplikasi Dana', 'color' => 'blue'],
                            'gopay'        => ['label' => 'GoPay', 'icon' => '💚', 'desc' => 'Transfer via GoPay', 'color' => 'green'],
                            'qris'         => ['label' => 'QRIS', 'icon' => '📱', 'desc' => 'Scan kode QR universal', 'color' => 'purple'],
                            'bank_transfer'=> ['label' => 'Virtual Account / Bank Transfer', 'icon' => '🏦', 'desc' => 'Transfer via ATM atau m-Banking', 'color' => 'gray'],
                        ];
                        @endphp

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" id="payment-methods-grid">
                        @foreach ($paymentMethods as $val => $pm)
                        @if (!isset($activeMethods) || in_array($val, $activeMethods))
                            <label id="pm-label-{{ $val }}"
                                   class="flex items-center gap-3 p-4 border-2 rounded-xl cursor-pointer transition-all hover:border-herbal-400
                                          {{ old('payment_method', 'cod') == $val ? 'border-herbal-600 bg-herbal-50' : 'border-gray-200' }}
                                          {{ $val === 'bank_transfer' ? 'sm:col-span-2' : '' }}">
                                <input type="radio" name="payment_method" value="{{ $val }}"
                                       id="pm-{{ $val }}"
                                       {{ old('payment_method', 'cod') == $val ? 'checked' : '' }}
                                       class="text-herbal-600 focus:ring-herbal-500"
                                       onchange="highlightPaymentMethod('{{ $val }}')">
                                <span class="text-2xl">{{ $pm['icon'] }}</span>
                                <div class="flex-1">
                                    <span class="text-sm font-semibold text-gray-800 block">{{ $pm['label'] }}</span>
                                    <span class="text-xs text-gray-500">{{ $pm['desc'] }}</span>
                                </div>
                                @if ($val !== 'cod')
                                    <span class="text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full font-medium">⏱ 2 jam</span>
                                @endif
                            </label>
                        @endif
                        @endforeach
                        </div>

                        <div id="payment-deadline-notice" class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-lg text-xs text-amber-700 {{ old('payment_method', 'cod') === 'cod' ? 'hidden' : '' }}">
                            ⏳ Untuk metode ini, Anda memiliki <strong>2 jam</strong> untuk menyelesaikan pembayaran setelah pesanan dibuat. Pesanan akan otomatis dibatalkan jika melewati batas waktu.
                        </div>

                        @if (in_array('bank_transfer', $activeMethods ?? [], true))
                        <div id="bank-transfer-panel" class="mt-4 rounded-xl border border-slate-200 bg-slate-50 p-4 {{ old('payment_method', 'cod') === 'bank_transfer' ? '' : 'hidden' }}">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-slate-800">Rekening Transfer Aktif</p>
                                    <p class="text-xs text-slate-500 mt-1">Tampilan ini mengikuti rekening yang diatur admin.</p>
                                </div>
                                <span class="text-xs font-semibold text-slate-500">{{ $bankAccounts->count() }} rekening</span>
                            </div>

                            <div class="mt-4 space-y-3">
                                @forelse ($bankAccounts as $account)
                                <div class="rounded-xl border border-slate-200 bg-white px-4 py-3">
                                    <p class="text-sm font-semibold text-slate-800">{{ $account->bank_name }}</p>
                                    <p class="mt-1 text-sm text-slate-600 font-mono">{{ $account->account_number }}</p>
                                    <p class="text-xs text-slate-500 mt-1">a.n. {{ $account->account_holder }}</p>
                                </div>
                                @empty
                                <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-700">
                                    Belum ada rekening aktif yang dikonfigurasi admin.
                                </div>
                                @endforelse
                            </div>
                        </div>
                        @endif

                        @error('payment_method')<p class="text-red-500 text-sm mt-2">{{ $message }}</p>@enderror
                    </div>

                    <div class="card p-6">
                        <h2 class="font-bold text-gray-800 text-lg mb-5 flex items-center gap-2">
                            <span class="w-7 h-7 bg-herbal-100 text-herbal-700 rounded-full flex items-center justify-center text-sm font-bold">3</span>
                            Ringkasan Pengiriman
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
                            <div class="rounded-xl border border-blue-100 bg-blue-50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-wide text-blue-500">Metode</p>
                                <p class="mt-2 font-semibold text-blue-900">
                                    {{ ($shippingPreview['shipping_method'] ?? 'flat_rate') === 'automatic' ? 'Otomatis' : 'Flat Rate' }}
                                </p>
                            </div>
                            <div class="rounded-xl border border-emerald-100 bg-emerald-50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-wide text-emerald-500">Estimasi</p>
                                <p class="mt-2 font-semibold text-emerald-900">{{ $shippingPreview['estimated_days'] ?? 3 }} hari</p>
                                @if (!empty($shippingPreview['estimated_delivery_at']))
                                <p class="mt-1 text-xs text-emerald-700">Sekitar {{ $shippingPreview['estimated_delivery_at']->format('d M Y, H:i') }} WIB</p>
                                @endif
                            </div>
                            <div class="rounded-xl border border-orange-100 bg-orange-50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-wide text-orange-500">Kurir Preview</p>
                                <p class="mt-2 font-semibold text-orange-900">{{ $shippingPreview['courier_name'] ?? 'Kurir Tersedia' }}</p>
                                <p class="mt-1 text-xs text-orange-700">{{ $shippingPreview['destination_city'] ?? 'Tujuan belum dipilih' }}</p>
                            </div>
                        </div>

                        @if (($shippingPreview['available_couriers'] ?? collect())->isNotEmpty())
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach ($shippingPreview['available_couriers'] as $courier)
                            <span class="inline-flex items-center gap-2 rounded-full bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600">
                                {{ $courier['label'] }} · {{ $courier['days'] }} hari
                            </span>
                            @endforeach
                        </div>
                        @endif
                    </div>

                    {{-- Notes --}}
                    <div class="card p-6">
                        <label class="form-label">Catatan (opsional)</label>
                        <textarea name="notes" rows="3" placeholder="Instruksi khusus untuk pesanan Anda..."
                                  class="form-input resize-none">{{ old('notes') }}</textarea>
                    </div>
                </div>

                {{-- ===== RIGHT: Order Summary ===== --}}
                <div class="lg:w-80 shrink-0">
                    <div class="card p-6 sticky top-24">
                        <h2 class="font-bold text-gray-800 text-lg mb-5 flex items-center gap-2">
                            <span class="w-7 h-7 bg-herbal-100 text-herbal-700 rounded-full flex items-center justify-center text-sm font-bold">4</span>
                            Rincian Pesanan
                        </h2>

                        <div class="space-y-3 max-h-60 overflow-y-auto pr-1">
                            @foreach ($selectedItems as $item)
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-lg overflow-hidden bg-herbal-50 shrink-0">
                                    @if ($item->product->image)
                                        <img src="{{ Storage::url($item->product->image) }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-sm">🌿</div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-800 truncate">{{ $item->product->name }}</p>
                                    <p class="text-xs text-gray-500">×{{ $item->quantity }}</p>
                                </div>
                                <p class="text-sm font-semibold text-gray-800 shrink-0">
                                    Rp {{ number_format($item->quantity * $item->product->effective_price, 0, ',', '.') }}
                                </p>
                            </div>
                            @endforeach
                        </div>

                        <div class="border-t border-gray-100 mt-4 pt-4 space-y-2 text-sm">
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal</span>
                                <span>Rp {{ number_format($subtotalDisplay, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Ongkos Kirim</span>
                                <span id="shipping-cost-display">
                                    @if ($isFreeShipping)
                                        <span class="text-green-600 font-semibold">Gratis ✓</span>
                                    @else
                                        Rp {{ number_format($flatRateDisplay, 0, ',', '.') }}
                                    @endif
                                </span>
                            </div>
                        </div>

                        <div class="flex justify-between text-gray-600 {{ old('payment_method', 'cod') === 'cod' ? '' : 'hidden' }}" id="cod-fee-row">
                            <span>Biaya COD</span>
                            <span id="cod-fee-display">Rp {{ number_format($codFeeDisplay, 0, ',', '.') }}</span>
                        </div>

                        <div class="border-t border-gray-100 mt-3 pt-3 flex justify-between">
                            <span class="font-bold text-gray-800">Total Estimasi</span>
                            <span class="font-extrabold text-herbal-800 text-lg" id="total-estimate-display">
                                Rp {{ number_format($initialTotal, 0, ',', '.') }}
                            </span>
                        </div>

                        {{-- Submit Buttons --}}
                        <div id="checkout-actions" class="mt-6">
                            <input type="hidden" name="payment_action" id="payment_action_field" value="pay_later">

                            {{-- COD: single button --}}
                            <div id="cod-actions">
                                <button type="submit"
                                        class="btn-primary w-full justify-center py-4 text-base {{ $belowMinimum ? 'opacity-50 pointer-events-none' : '' }}" {{ $belowMinimum ? 'disabled' : '' }}>
                                    ✓ Buat Pesanan (Bayar di Tempat)
                                </button>
                            </div>

                            {{-- Non-COD: two buttons --}}
                            <div id="non-cod-actions" class="space-y-3 hidden">
                                <button type="submit"
                                        id="btn-pay-now"
                                        class="btn-primary w-full justify-center py-4 text-base {{ $belowMinimum ? 'opacity-50 pointer-events-none' : '' }}" {{ $belowMinimum ? 'disabled' : '' }}
                                        onclick="document.getElementById('payment_action_field').value = 'pay_now'">
                                    ⚡ Bayar Sekarang
                                </button>
                                <button type="submit"
                                        id="btn-pay-later"
                                        class="w-full justify-center py-3.5 text-base border-2 border-herbal-600 text-herbal-700 hover:bg-herbal-50 rounded-xl font-semibold transition-colors {{ $belowMinimum ? 'opacity-50 pointer-events-none' : '' }}" {{ $belowMinimum ? 'disabled' : '' }}
                                        onclick="document.getElementById('payment_action_field').value = 'pay_later'">
                                    🕐 Bayar Nanti (2 jam)
                                </button>
                                <p class="text-xs text-center text-gray-400 mt-1">
                                    Bayar Nanti: pesanan aktif selama 2 jam, otomatis dibatalkan jika belum dibayar.
                                </p>
                            </div>
                        </div>
                        <a href="{{ route('cart.index') }}" class="block text-center text-sm text-gray-500 hover:text-herbal-700 mt-3">
                            ← Kembali ke Keranjang
                        </a>
                    </div>
                </div>

            </div>
        </form>
    </div>

    <script>
        function highlightPaymentMethod(selected) {
            document.querySelectorAll('[id^="pm-label-"]').forEach(function(label) {
                label.classList.remove('border-herbal-600', 'bg-herbal-50');
                label.classList.add('border-gray-200');
            });
            const activeLabel = document.getElementById('pm-label-' + selected);
            if (activeLabel) {
                activeLabel.classList.remove('border-gray-200');
                activeLabel.classList.add('border-herbal-600', 'bg-herbal-50');
            }
            const notice = document.getElementById('payment-deadline-notice');
            const bankTransferPanel = document.getElementById('bank-transfer-panel');
            if (notice) {
                notice.classList.toggle('hidden', selected === 'cod');
            }
            if (bankTransferPanel) {
                bankTransferPanel.classList.toggle('hidden', selected !== 'bank_transfer');
            }

            const shippingDisplay = document.getElementById('shipping-cost-display');
            const codFeeRow = document.getElementById('cod-fee-row');
            const codFeeDisplay = document.getElementById('cod-fee-display');
            const totalDisplay    = document.getElementById('total-estimate-display');
            @if (isset($isFreeShipping) && $isFreeShipping)
                if (shippingDisplay) shippingDisplay.innerHTML = '<span class="text-green-600 font-semibold">Gratis ✓</span>';
                const codFee = selected === 'cod' ? {{ $codFeeDisplay ?? 15000 }} : 0;
                if (codFeeDisplay) codFeeDisplay.textContent = 'Rp ' + codFee.toLocaleString('id-ID');
                if (codFeeRow) codFeeRow.classList.toggle('hidden', selected !== 'cod');
                if (totalDisplay) totalDisplay.textContent = 'Rp ' + ({{ $subtotalDisplay ?? 0 }} + codFee).toLocaleString('id-ID');
            @else
                const codCost     = {{ $codFeeDisplay ?? 15000 }};
                const flatCost    = {{ $flatRateDisplay ?? 10000 }};
                const subtotal    = {{ $subtotalDisplay ?? 0 }};
                const shippingFee = flatCost;
                const codFee      = selected === 'cod' ? codCost : 0;
                const fmtShipping = 'Rp ' + shippingFee.toLocaleString('id-ID');
                const fmtTotal    = 'Rp ' + (subtotal + shippingFee + codFee).toLocaleString('id-ID');
                if (shippingDisplay) shippingDisplay.textContent = fmtShipping;
                if (codFeeDisplay) codFeeDisplay.textContent = 'Rp ' + codFee.toLocaleString('id-ID');
                if (codFeeRow) codFeeRow.classList.toggle('hidden', selected !== 'cod');
                if (totalDisplay) totalDisplay.textContent = fmtTotal;
            @endif

            const codActions    = document.getElementById('cod-actions');
            const nonCodActions = document.getElementById('non-cod-actions');
            if (selected === 'cod') {
                codActions.classList.remove('hidden');
                nonCodActions.classList.add('hidden');
            } else {
                codActions.classList.add('hidden');
                nonCodActions.classList.remove('hidden');
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const checkedMethod = document.querySelector('input[name="payment_method"]:checked');
            if (checkedMethod) {
                highlightPaymentMethod(checkedMethod.value);
            }
        });
    </script>
</x-app-layout>
