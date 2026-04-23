<x-layouts.admin>
<x-slot name="title">Sesi Aktif</x-slot>
<x-slot name="subtitle">Kelola semua perangkat yang sedang login ke akun Anda</x-slot>

<div class="max-w-3xl">

    @if (session('success'))
    <div class="mb-6 flex items-center gap-3 bg-green-50 border border-green-200 rounded-xl p-4 text-green-800 text-sm">
        <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif

    @if (session('error'))
    <div class="mb-6 flex items-center gap-3 bg-red-50 border border-red-200 rounded-xl p-4 text-red-800 text-sm">
        <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        {{ session('error') }}
    </div>
    @endif

    {{-- Logout Semua --}}
    <div class="bg-orange-50 border border-orange-200 rounded-2xl p-5 mb-6 flex items-center justify-between gap-4">
        <div>
            <p class="font-semibold text-orange-800 text-sm">Logout dari Semua Perangkat Lain</p>
            <p class="text-orange-700 text-xs mt-1">Sesi saat ini akan tetap aktif. Semua perangkat lain akan dikeluarkan.</p>
        </div>
        <form action="{{ route('admin.sessions.destroy-all') }}" method="POST"
              onsubmit="return confirm('Logout dari semua perangkat lain?')">
            @csrf @method('DELETE')
            <button type="submit"
                    class="shrink-0 bg-orange-600 hover:bg-orange-700 text-white px-5 py-2.5 rounded-xl text-sm font-medium transition flex items-center gap-2">
                <i data-lucide="log-out" class="w-4 h-4"></i> Logout Semua
            </button>
        </form>
    </div>

    {{-- Session List --}}
    <div class="space-y-4">
        @forelse ($sessions as $session)
        @php
            $icon = match(true) {
                str_contains(strtolower($session->device_name ?? ''), 'mobile') => '📱',
                str_contains(strtolower($session->device_name ?? ''), 'tablet') => '📟',
                default => '💻'
            };
        @endphp
        <div class="bg-white rounded-2xl border {{ $session->is_current ? 'border-green-300 shadow-green-100 shadow-md' : 'border-gray-100 shadow-sm' }} p-6">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-start gap-4">
                    <div class="text-3xl shrink-0 pt-1">{{ $icon }}</div>
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <p class="font-semibold text-gray-800">{{ $session->device_name ?? 'Perangkat Tidak Diketahui' }}</p>
                            @if($session->is_current)
                                <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium">Sesi Saat Ini</span>
                            @endif
                        </div>
                        <div class="text-xs text-gray-500 space-y-0.5">
                            <p>🌐 Browser: <strong>{{ $session->browser ?? '-' }}</strong></p>
                            <p>📍 IP: <strong>{{ $session->ip_address ?? '-' }}</strong></p>
                            @if($session->location)
                            <p>🗺 Lokasi: <strong>{{ $session->location }}</strong></p>
                            @endif
                            <p>🕐 Terakhir aktif: <strong>{{ $session->last_active_for_humans }}</strong></p>
                        </div>
                    </div>
                </div>

                @if (!$session->is_current)
                <form action="{{ route('admin.sessions.destroy', $session) }}" method="POST"
                      onsubmit="return confirm('Akhiri sesi pada perangkat ini?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="shrink-0 border border-red-200 hover:bg-red-50 text-red-600 px-4 py-2 rounded-xl text-sm font-medium transition flex items-center gap-1.5">
                        <i data-lucide="x" class="w-4 h-4"></i> Akhiri Sesi
                    </button>
                </form>
                @else
                <span class="shrink-0 text-green-600 text-xs font-medium flex items-center gap-1">
                    <i data-lucide="check-circle" class="w-4 h-4"></i> Aktif Sekarang
                </span>
                @endif
            </div>
        </div>
        @empty
        <div class="bg-white rounded-2xl border border-gray-100 p-12 text-center text-gray-400">
            <i data-lucide="monitor" class="w-10 h-10 mx-auto mb-3 opacity-30"></i>
            <p class="font-medium">Tidak ada sesi aktif yang tercatat.</p>
            <p class="text-sm mt-1">Sesi akan otomatis tercatat saat Anda login.</p>
        </div>
        @endforelse
    </div>

</div>

</x-layouts.admin>
