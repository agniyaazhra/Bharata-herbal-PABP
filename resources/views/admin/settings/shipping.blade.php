<x-layouts.admin>
    <x-slot name="title">Pengaturan Pengiriman</x-slot>
    <x-slot name="subtitle">Kelola kurir, tarif, dan estimasi pengiriman</x-slot>

    <div class="max-w-5xl">


        <form action="{{ route('admin.settings.shipping.update') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- ── Left: Couriers + Shipping Method ──────────────── --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Courier Services --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-bold text-gray-800 mb-5 flex items-center gap-2">
                            <span class="w-7 h-7 bg-orange-100 rounded-lg flex items-center justify-center">
                                <i data-lucide="truck" class="w-4 h-4 text-orange-600"></i>
                            </span>
                            Layanan Kurir
                        </h3>

                        @php
                        $courierMeta = [
                        'jne' => ['name' => 'JNE', 'logo' => '📦'],
                        'jnt' => ['name' => 'J&T Express', 'logo' => '🚚'],
                        'sicepat' => ['name' => 'SiCepat', 'logo' => '⚡'],
                        ];
                        @endphp

                        <div class="space-y-3">
                            @foreach ($couriers as $courier)
                            @php
                            $isActive = $settings["courier_{$courier}_active"] ?? true;
                            $days = $settings["courier_{$courier}_days"] ?? 3;
                            $meta = $courierMeta[$courier];
                            @endphp
                            <div class="flex items-center gap-4 p-4 rounded-xl border {{ $isActive ? 'border-green-200 bg-green-50' : 'border-gray-200 bg-gray-50' }} transition"
                                id="courier-card-{{ $courier }}">
                                <span class="text-2xl shrink-0">{{ $meta['logo'] }}</span>
                                <div class="flex-1">
                                    <p class="font-semibold text-sm text-gray-800">{{ $meta['name'] }}</p>
                                    <div class="flex items-center gap-2 mt-2">
                                        <label class="text-xs text-gray-500">Estimasi:</label>
                                        <input type="number" name="courier_{{ $courier }}_days"
                                            value="{{ $days }}" min="1" max="30"
                                            class="w-16 border border-gray-200 rounded-lg px-2 py-1 text-xs text-center focus:ring-1 focus:ring-green-500 outline-none">
                                        <span class="text-xs text-gray-400">hari</span>
                                    </div>
                                </div>
                                <label class="relative cursor-pointer shrink-0" onclick="toggleCourier('{{ $courier }}', this)">
                                    <input type="checkbox" name="courier_{{ $courier }}_active"
                                        id="courier-{{ $courier }}"
                                        class="sr-only" {{ $isActive ? 'checked' : '' }}>
                                    <div class="w-11 h-6 rounded-full transition {{ $isActive ? 'bg-green-600' : 'bg-gray-300' }}"
                                        id="courier-bg-{{ $courier }}">
                                        <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform {{ $isActive ? 'translate-x-5' : '' }}"
                                            id="courier-dot-{{ $courier }}"></div>
                                    </div>
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Shipping Cost Configuration --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-bold text-gray-800 mb-5 flex items-center gap-2">
                            <span class="w-7 h-7 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i data-lucide="settings-2" class="w-4 h-4 text-blue-600"></i>
                            </span>
                            Konfigurasi Ongkos Kirim
                        </h3>

                        {{-- Method Selector --}}
                        <div class="mb-5">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Metode Kalkulasi</label>
                            <div class="flex gap-3">
                                @php $method = $settings['shipping_method'] ?? 'flat_rate'; @endphp
                                <label class="flex-1 flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition
                                          {{ $method === 'flat_rate' ? 'border-green-400 bg-green-50' : 'border-gray-200' }}">
                                    <input type="radio" name="shipping_method" value="flat_rate"
                                        {{ $method === 'flat_rate' ? 'checked' : '' }}
                                        class="text-green-600" onchange="toggleShippingMethod()">
                                    <div>
                                        <p class="font-semibold text-sm">Flat Rate</p>
                                        <p class="text-xs text-gray-500">Tarif tetap untuk semua pesanan</p>
                                    </div>
                                </label>

                            </div>
                        </div>

                        {{-- Flat Rate Cost --}}
                        <div id="flat-rate-section">
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Ongkos Kirim Flat</label>
                            <div class="flex items-center gap-3 max-w-xs">
                                <span class="text-sm font-bold text-gray-500 shrink-0">Rp</span>
                                <input type="number" name="flat_rate_cost" min="0" step="500"
                                    value="{{ $settings['flat_rate_cost'] ?? 10000 }}"
                                    class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500/30 outline-none">
                            </div>
                        </div>
                    </div>

                </div>

                {{-- ── Right: Free Shipping + ETA + Save ─────────────── --}}
                <div class="space-y-4">

                    {{-- Free Shipping --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <span class="text-xl">🆓</span> Gratis Ongkir
                        </h3>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Minimum Pembelian</label>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold text-gray-500 shrink-0">Rp</span>
                            <input type="number" name="free_shipping_minimum" min="0" step="1000"
                                value="{{ $settings['free_shipping_minimum'] ?? 0 }}"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500/30 outline-none">
                        </div>
                        <p class="text-xs text-gray-400 mt-2">Set ke 0 untuk menonaktifkan gratis ongkir.</p>
                    </div>

                    {{-- Minimum Order --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <span class="text-xl">🧾</span> Minimum Belanja
                        </h3>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Minimum Checkout</label>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-bold text-gray-500 shrink-0">Rp</span>
                            <input type="number" name="minimum_order_amount" min="0" step="1000"
                                value="{{ $settings['minimum_order_amount'] ?? 0 }}"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500/30 outline-none">
                        </div>
                        <p class="text-xs text-gray-400 mt-2">Pelanggan hanya bisa checkout jika total produk dipilih mencapai nilai ini.</p>
                    </div>

                    {{-- Fallback ETA --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                            <span class="text-xl">⏱</span> Estimasi Global
                        </h3>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Fallback (Hari)</label>
                        <input type="number" name="fallback_estimated_days" min="1" max="30"
                            value="{{ $settings['fallback_estimated_days'] ?? 3 }}"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500/30 outline-none">
                        <p class="text-xs text-gray-400 mt-2">Digunakan jika kurir tidak memiliki estimasi spesifik.</p>
                    </div>

                    {{-- Save --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <button type="submit"
                            class="w-full bg-green-700 hover:bg-green-800 text-white font-semibold py-3 rounded-xl text-sm transition flex items-center justify-center gap-2">
                            <i data-lucide="save" class="w-4 h-4"></i> Simpan Pengaturan Pengiriman
                        </button>
                    </div>
                </div>

            </div>
        </form>
    </div>

    <script>
        function toggleCourier(code, label) {
            const cb = document.getElementById('courier-' + code);
            const bg = document.getElementById('courier-bg-' + code);
            const dot = document.getElementById('courier-dot-' + code);
            const card = document.getElementById('courier-card-' + code);

            cb.checked = !cb.checked;

            if (cb.checked) {
                bg.classList.replace('bg-gray-300', 'bg-green-600');
                dot.classList.add('translate-x-5');
                card.classList.replace('border-gray-200', 'border-green-200');
                card.classList.replace('bg-gray-50', 'bg-green-50');
            } else {
                bg.classList.replace('bg-green-600', 'bg-gray-300');
                dot.classList.remove('translate-x-5');
                card.classList.replace('border-green-200', 'border-gray-200');
                card.classList.replace('bg-green-50', 'bg-gray-50');
            }
        }

        function toggleShippingMethod() {
            const flat = document.querySelector('[name="shipping_method"][value="flat_rate"]').checked;
            document.getElementById('flat-rate-section').style.opacity = flat ? '1' : '0.4';
            document.getElementById('flat-rate-section').style.pointerEvents = flat ? '' : 'none';
        }

        toggleShippingMethod();
    </script>

</x-layouts.admin>