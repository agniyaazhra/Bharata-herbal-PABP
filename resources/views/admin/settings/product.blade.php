<x-layouts.admin>
    <x-slot name="title">Pengaturan Produk</x-slot>
    <x-slot name="subtitle">Kelola aturan stok, status otomatis, dan notifikasi produk</x-slot>

    <div class="max-w-5xl">



        <form action="{{ route('admin.settings.product.update') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- LEFT --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Stock Minimum --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-bold mb-4">Batas Stok Minimum</h3>

                        <input type="number"
                               name="stock_minimum"
                               id="stock_minimum"
                               value="{{ $settings->minimum_stock_alert ?? 10 }}"
                               min="0"
                               max="9999"
                               class="w-full border rounded-xl px-4 py-2">

                        @error('stock_minimum')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror

                        <p class="text-xs text-gray-500 mt-2">
                            Produk dengan stok ≤ <span id="preview-min">{{ $settings->minimum_stock_alert ?? 10 }}</span>
                            akan mendapat status peringatan.
                        </p>
                    </div>

                    {{-- Status Otomatis --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-4">

                        <label class="flex justify-between items-center">
                            <span>Nonaktifkan otomatis saat stok habis</span>
                            <input type="checkbox"
                                   name="auto_nonaktif_stok_habis"
                                   {{ $settings->auto_disable_when_out_of_stock ? 'checked' : '' }}>
                        </label>

                        <label class="flex justify-between items-center">
                            <span>Tandai warning saat stok minimum</span>
                            <input type="checkbox"
                                   name="auto_warning_stok_minimum"
                                   {{ $settings->alert_when_below_minimum ? 'checked' : '' }}>
                        </label>

                    </div>

                    {{-- Notification --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-bold mb-4">Jenis Notifikasi</h3>

                        @php
                            $notifType = $settings->notify_email_admin ? 'email' : 'dashboard';
                        @endphp

                        <label class="block mb-2">
                            <input type="radio"
                                   name="notification_type"
                                   value="dashboard"
                                   {{ $notifType === 'dashboard' ? 'checked' : '' }}>
                            Dashboard
                        </label>

                        <label class="block">
                            <input type="radio"
                                   name="notification_type"
                                   value="email"
                                   {{ $notifType === 'email' ? 'checked' : '' }}>
                            Email
                        </label>
                    </div>

                </div>

                {{-- RIGHT --}}
                <div class="space-y-4">

                    {{-- Save --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <button type="submit"
                                class="w-full bg-green-700 hover:bg-green-800 text-white py-3 rounded-xl font-semibold">
                            Simpan Pengaturan
                        </button>
                    </div>
                            </form>

                    {{-- Reset --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        <form action="{{ route('admin.settings.product.reset') }}"
                              method="POST"
                              onsubmit="return confirm('Reset semua pengaturan produk ke default?')">
                            @csrf

                            <button type="submit"
                                    class="w-full border border-gray-300 hover:bg-gray-50 py-3 rounded-xl">
                                Reset Default
                            </button>
                        </form>
                    </div>

                    {{-- Summary --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                        @php
                            $summary = app(\App\Services\ProductStockService::class)->getStockSummary();
                        @endphp

                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span>Total Produk</span>
                                <span>{{ $summary['total'] }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Aktif</span>
                                <span>{{ $summary['active'] }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Peringatan</span>
                                <span>{{ $summary['warning'] }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Nonaktif</span>
                                <span>{{ $summary['inactive'] }}</span>
                            </div>
                        </div>
                    </div>

                </div>

            </div>


    </div>

    <script>
        document.getElementById('stock_minimum').addEventListener('input', function () {
            document.getElementById('preview-min').textContent = this.value || 10;
        });
    </script>
</x-layouts.admin>