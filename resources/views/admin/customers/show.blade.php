<x-layouts.admin>
    <x-slot name="title">Detail Pelanggan: {{ $user->name }}</x-slot>

    <div class="max-w-5xl space-y-6">
        <a href="{{ route('admin.customers.index') }}" class="text-gray-400 hover:text-gray-700 text-sm">← Kembali ke Pelanggan</a>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-herbal-100 rounded-full flex items-center justify-center">
                    <span class="text-herbal-700 font-bold text-2xl">{{ strtoupper(substr($user->name, 0, 2)) }}</span>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-800">{{ $user->name }}</h2>
                    <p class="text-gray-500">{{ $user->email }}</p>
                    @if ($user->phone) <p class="text-sm text-gray-400">{{ $user->phone }}</p> @endif
                    <p class="text-xs text-gray-400 mt-1">Bergabung {{ $user->created_at->format('d F Y') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-100 font-bold text-gray-800">Riwayat Pesanan ({{ $orders->total() }})</div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="px-5 py-3 text-gray-500 font-semibold">No. Pesanan</th>
                        <th class="px-5 py-3 text-gray-500 font-semibold">Total</th>
                        <th class="px-5 py-3 text-gray-500 font-semibold">Status</th>
                        <th class="px-5 py-3 text-gray-500 font-semibold">Tanggal</th>
                        <th class="px-5 py-3 text-gray-500 font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($orders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-5 py-3 font-mono font-semibold text-herbal-700">{{ $order->order_number }}</td>
                        <td class="px-5 py-3 font-semibold">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                        <td class="px-5 py-3">
                            @php $c = $order->status_color @endphp
                            <span class="badge bg-{{ $c }}-100 text-{{ $c }}-700 font-semibold">{{ $order->status_label }}</span>
                        </td>
                        <td class="px-5 py-3 text-gray-500">{{ $order->created_at->format('d M Y') }}</td>
                        <td class="px-5 py-3">
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-xs text-herbal-700 border border-herbal-200 px-3 py-1.5 rounded-lg hover:bg-herbal-50">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-5 py-10 text-center text-gray-400">Belum ada pesanan.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="p-5">{{ $orders->links() }}</div>
        </div>
    </div>
</x-layouts.admin>
