<x-layouts.admin>
<x-slot name="title">Pengaturan Notifikasi</x-slot>
<x-slot name="subtitle">Kelola notifikasi email dan WhatsApp untuk setiap event pesanan</x-slot>

<div class="max-w-5xl">



    <form action="{{ route('admin.settings.notification.update') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- ── Left: Events + Email + WA ─────────────────────── --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Event Toggles --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-bold text-gray-800 mb-5 flex items-center gap-2">
                        <span class="w-7 h-7 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="bell" class="w-4 h-4 text-purple-600"></i>
                        </span>
                        Event Notifikasi
                    </h3>

                    @php
                    $eventMeta = [
                        'order_created'     => ['label' => 'Pesanan Baru',         'icon' => '🛒', 'desc' => 'Saat pelanggan membuat pesanan baru'],
                        'payment_confirmed' => ['label' => 'Pembayaran Dikonfirmasi','icon'=> '✅', 'desc' => 'Saat pembayaran berhasil diverifikasi'],
                        'order_shipped'     => ['label' => 'Pesanan Dikirim',       'icon' => '🚚', 'desc' => 'Saat status pesanan berubah ke Dikirim'],
                        'order_completed'   => ['label' => 'Pesanan Selesai',       'icon' => '🎉', 'desc' => 'Saat pesanan telah diterima pelanggan'],
                    ];
                    @endphp

                    <div class="space-y-3">
                        @foreach ($events as $event)
                        @php
                        $isActive = $settings["event_{$event}"] ?? true;
                        $meta = $eventMeta[$event];
                        @endphp
                        <div class="flex items-center justify-between p-4 rounded-xl border {{ $isActive ? 'border-purple-200 bg-purple-50' : 'border-gray-200 bg-gray-50' }} transition"
                             id="event-card-{{ $event }}">
                            <div class="flex items-center gap-3">
                                <span class="text-xl">{{ $meta['icon'] }}</span>
                                <div>
                                    <p class="font-semibold text-sm text-gray-800">{{ $meta['label'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $meta['desc'] }}</p>
                                </div>
                            </div>
                            <label class="relative cursor-pointer shrink-0" onclick="toggleEvent('{{ $event }}', this)">
                                <input type="checkbox" name="event_{{ $event }}"
                                       id="event-{{ $event }}"
                                       class="sr-only" {{ $isActive ? 'checked' : '' }}>
                                <div class="w-11 h-6 rounded-full transition {{ $isActive ? 'bg-purple-600' : 'bg-gray-300' }}"
                                     id="event-bg-{{ $event }}">
                                    <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform {{ $isActive ? 'translate-x-5' : '' }}"
                                         id="event-dot-{{ $event }}"></div>
                                </div>
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Email Notification --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-bold text-gray-800 mb-5 flex items-center gap-2">
                        <span class="w-7 h-7 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="mail" class="w-4 h-4 text-blue-600"></i>
                        </span>
                        Notifikasi Email
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Utama</label>
                            <input type="email" name="email_primary"
                                   value="{{ old('email_primary', $settings['email_primary'] ?? '') }}"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/30 outline-none"
                                   placeholder="info@toko.id">
                            @error('email_primary')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Cadangan</label>
                            <input type="email" name="email_backup"
                                   value="{{ old('email_backup', $settings['email_backup'] ?? '') }}"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500/30 outline-none"
                                   placeholder="backup@toko.id">
                            @error('email_backup')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <button type="button" onclick="sendTestEmail()"
                            class="flex items-center gap-2 border border-blue-300 text-blue-700 bg-blue-50 hover:bg-blue-100 font-medium text-sm px-4 py-2.5 rounded-xl transition">
                        <i data-lucide="send" class="w-4 h-4"></i>
                        <span id="test-email-text">Kirim Email Uji Coba</span>
                    </button>
                    <div id="test-email-result" class="mt-3 hidden"></div>
                </div>

                {{-- WhatsApp Notification --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-bold text-gray-800 mb-5 flex items-center gap-2">
                        <span class="w-7 h-7 bg-green-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="message-circle" class="w-4 h-4 text-green-600"></i>
                        </span>
                        Notifikasi WhatsApp
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nomor Utama</label>
                            <input type="text" name="whatsapp_primary"
                                   value="{{ old('whatsapp_primary', $settings['whatsapp_primary'] ?? '') }}"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500/30 outline-none"
                                   placeholder="+6281234567890">
                            @error('whatsapp_primary')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nomor Cadangan</label>
                            <input type="text" name="whatsapp_backup"
                                   value="{{ old('whatsapp_backup', $settings['whatsapp_backup'] ?? '') }}"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500/30 outline-none"
                                   placeholder="+6289876543210">
                            @error('whatsapp_backup')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <button type="button" onclick="sendTestWhatsapp()"
                            class="flex items-center gap-2 border border-green-300 text-green-700 bg-green-50 hover:bg-green-100 font-medium text-sm px-4 py-2.5 rounded-xl transition">
                        <i data-lucide="message-circle" class="w-4 h-4"></i>
                        <span id="test-wa-text">Kirim WA Uji Coba</span>
                    </button>
                    <div id="test-wa-result" class="mt-3 hidden"></div>
                </div>

            </div>

            {{-- ── Right: Save + Info ─────────────────────────────── --}}
            <div class="space-y-4">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-24">
                    <h3 class="font-bold text-gray-800 mb-4">Aksi</h3>
                    <button type="submit"
                            class="w-full bg-green-700 hover:bg-green-800 text-white font-semibold py-3 rounded-xl text-sm transition flex items-center justify-center gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i> Simpan Pengaturan
                    </button>

                    <div class="mt-5 pt-5 border-t border-gray-100">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Info</p>
                        <div class="space-y-2 text-xs text-gray-500">
                            <p>📧 Email dikirim via log (simulasi)</p>
                            <p>📱 WhatsApp dikirim via log (simulasi)</p>
                            <p>🔔 Toggle = perubahan visual langsung</p>
                            <p>💾 Simpan = simpan ke database</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

function toggleEvent(event, label) {
    const cb  = document.getElementById('event-' + event);
    const bg  = document.getElementById('event-bg-' + event);
    const dot = document.getElementById('event-dot-' + event);
    const card = document.getElementById('event-card-' + event);

    cb.checked = !cb.checked;

    if (cb.checked) {
        bg.classList.replace('bg-gray-300', 'bg-purple-600');
        dot.classList.add('translate-x-5');
        card.classList.replace('border-gray-200', 'border-purple-200');
        card.classList.replace('bg-gray-50', 'bg-purple-50');
    } else {
        bg.classList.replace('bg-purple-600', 'bg-gray-300');
        dot.classList.remove('translate-x-5');
        card.classList.replace('border-purple-200', 'border-gray-200');
        card.classList.replace('bg-purple-50', 'bg-gray-50');
    }
}

function showResult(containerId, textId, success, message) {
    const result = document.getElementById(containerId);
    result.innerHTML = `<p class="text-xs ${success ? 'text-green-700 bg-green-50 border-green-200' : 'text-red-700 bg-red-50 border-red-200'} border rounded-lg px-3 py-2">${success ? '✅' : '❌'} ${message}</p>`;
    result.classList.remove('hidden');
    document.getElementById(textId).textContent = success ? (textId === 'test-email-text' ? 'Kirim Email Uji Coba' : 'Kirim WA Uji Coba') : (textId === 'test-email-text' ? 'Kirim Email Uji Coba' : 'Kirim WA Uji Coba');
}

function sendTestEmail() {
    document.getElementById('test-email-text').textContent = 'Mengirim...';
    fetch('{{ route("admin.settings.notification.test-email") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => showResult('test-email-result', 'test-email-text', data.success, data.message))
    .catch(() => showResult('test-email-result', 'test-email-text', false, 'Terjadi kesalahan.'));
}

function sendTestWhatsapp() {
    document.getElementById('test-wa-text').textContent = 'Mengirim...';
    fetch('{{ route("admin.settings.notification.test-whatsapp") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => showResult('test-wa-result', 'test-wa-text', data.success, data.message))
    .catch(() => showResult('test-wa-result', 'test-wa-text', false, 'Terjadi kesalahan.'));
}
</script>

</x-layouts.admin>
