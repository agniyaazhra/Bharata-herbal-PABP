<x-layouts.admin>
<x-slot name="title">Notifikasi</x-slot>
<x-slot name="subtitle">Peringatan stok produk dan informasi penting lainnya</x-slot>

<div class="max-w-4xl">

    @if (session('success'))
    <div class="mb-6 flex items-center gap-3 bg-green-50 border border-green-200 rounded-xl p-4 text-green-800 text-sm">
        <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        {{ session('success') }}
    </div>
    @endif

    {{-- Header Actions --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <h2 class="text-sm font-semibold text-gray-700">
                {{ $unreadCount > 0 ? "$unreadCount belum dibaca" : 'Semua sudah dibaca' }}
            </h2>
        </div>
        @if ($unreadCount > 0)
        <form action="{{ route('admin.notifications.read-all') }}" method="POST">
            @csrf
            <button type="submit"
                    class="flex items-center gap-2 border border-gray-200 hover:bg-gray-50 text-gray-600 px-4 py-2 rounded-xl text-sm font-medium transition">
                <i data-lucide="check-check" class="w-4 h-4"></i> Tandai Semua Dibaca
            </button>
        </form>
        @endif
    </div>

    {{-- Filters --}}
    <form action="{{ route('admin.notifications.index') }}" method="GET" class="flex gap-3 mb-6">
        <select name="type" class="border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-green-500/30 outline-none">
            <option value="">Semua Tipe</option>
            <option value="warning" {{ request('type') === 'warning' ? 'selected' : '' }}>⚠️ Peringatan</option>
            <option value="danger"  {{ request('type') === 'danger'  ? 'selected' : '' }}>🔴 Stok Habis</option>
            <option value="info"    {{ request('type') === 'info'    ? 'selected' : '' }}>ℹ️ Informasi</option>
        </select>
        <select name="is_read" class="border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-green-500/30 outline-none">
            <option value="">Semua Status</option>
            <option value="0" {{ request('is_read') === '0' ? 'selected' : '' }}>Belum Dibaca</option>
            <option value="1" {{ request('is_read') === '1' ? 'selected' : '' }}>Sudah Dibaca</option>
        </select>
        <button type="submit" class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-xl text-sm text-gray-700 transition">
            Filter
        </button>
        @if (request()->hasAny(['type', 'is_read']))
        <a href="{{ route('admin.notifications.index') }}" class="text-gray-400 hover:text-gray-600 px-4 py-2 text-sm">Reset</a>
        @endif
    </form>

    {{-- Notifications List --}}
    <div class="space-y-3">
        @forelse ($notifications as $notif)
        @php
            $typeConfig = match($notif->type) {
                'danger'  => ['bg' => 'bg-red-50 border-red-200',   'dot' => 'bg-red-500',    'icon' => '🔴'],
                'warning' => ['bg' => 'bg-yellow-50 border-yellow-200', 'dot' => 'bg-yellow-500', 'icon' => '⚠️'],
                'success' => ['bg' => 'bg-green-50 border-green-200',  'dot' => 'bg-green-500',  'icon' => '✅'],
                default   => ['bg' => 'bg-blue-50 border-blue-200',    'dot' => 'bg-blue-500',   'icon' => 'ℹ️'],
            };
        @endphp
        <div class="flex items-start gap-4 p-5 rounded-2xl border {{ !$notif->is_read ? $typeConfig['bg'] : 'bg-white border-gray-100' }} transition">
            <span class="text-xl shrink-0 pt-0.5">{{ $typeConfig['icon'] }}</span>
            <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="font-semibold text-sm text-gray-800 flex items-center gap-2">
                            {{ $notif->title }}
                            @if (!$notif->is_read)
                                <span class="w-2 h-2 rounded-full {{ $typeConfig['dot'] }} shrink-0"></span>
                            @endif
                        </p>
                        <p class="text-sm text-gray-600 mt-1">{{ $notif->message }}</p>
                        <p class="text-xs text-gray-400 mt-2">{{ $notif->created_at->diffForHumans() }} · {{ $notif->created_at->format('d M Y, H:i') }}</p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        @if (!$notif->is_read)
                        <form action="{{ route('admin.notifications.read', $notif) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-xs text-blue-600 hover:text-blue-800 transition" title="Tandai dibaca">
                                <i data-lucide="check" class="w-4 h-4"></i>
                            </button>
                        </form>
                        @endif
                        <form action="{{ route('admin.notifications.destroy', $notif) }}" method="POST"
                              onsubmit="return confirm('Hapus notifikasi ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs text-gray-400 hover:text-red-500 transition" title="Hapus">
                                <i data-lucide="x" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-2xl border border-gray-100 p-16 text-center text-gray-400">
            <i data-lucide="bell-off" class="w-10 h-10 mx-auto mb-3 opacity-30"></i>
            <p class="font-medium">Tidak ada notifikasi.</p>
            <p class="text-sm mt-1">Notifikasi akan muncul saat ada peringatan stok produk.</p>
        </div>
        @endforelse
    </div>

    @if ($notifications->hasPages())
    <div class="mt-6">
        {{ $notifications->links() }}
    </div>
    @endif

</div>

</x-layouts.admin>
