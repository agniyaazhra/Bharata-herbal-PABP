<x-app-layout>
    <x-slot name="title">Keranjang Belanja</x-slot>
    @php
    $minimumOrderAmount = (int) ($minimumOrderAmount ?? 0);
    $selectedTotalAmount = (int) ($cart?->total ?? 0);
    $belowMinimum = $minimumOrderAmount > 0 && $selectedTotalAmount
    < $minimumOrderAmount;
        @endphp

        <x-breadcrumb :crumbs="[
        ['label' => 'Beranda', 'url' => route('home')],
        ['label' => 'Keranjang'],
    ]" />

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Keranjang Belanja</h1>

        @if (!$cart || $cart->items->isEmpty())
        <div class="text-center py-24 card">
            <svg class="w-20 h-20 mx-auto text-gray-200 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <h3 class="text-xl font-semibold text-gray-600">Keranjang Anda Kosong</h3>
            <p class="text-gray-400 mt-2">Belum ada produk yang ditambahkan ke keranjang.</p>
            <a href="{{ route('shop') }}" class="btn-primary mt-6 inline-flex">Mulai Belanja</a>
        </div>
        @else
        <div class="flex flex-col lg:flex-row gap-8">

            {{-- ===== CART ITEMS ===== --}}
            <div class="flex-1 space-y-4">

                {{-- Select All Row --}}
                <div class="card px-5 py-3 flex items-center gap-3 bg-herbal-50 border border-herbal-100"
                    id="select-all-row">
                    <input type="checkbox" id="select-all-checkbox"
                        class="w-4 h-4 rounded text-herbal-600 border-gray-300 focus:ring-herbal-500 cursor-pointer"
                        {{ $cart->items->every(fn($i) => $i->is_selected) ? 'checked' : '' }}>
                    <label for="select-all-checkbox" class="text-sm font-semibold text-herbal-800 cursor-pointer select-none">
                        Pilih Semua ({{ $cart->items->count() }} produk)
                    </label>
                    <span class="ml-auto text-xs text-gray-400" id="selected-count-label">
                        {{ $cart->items->where('is_selected', true)->count() }} dipilih
                    </span>
                    <form action="{{ route('cart.clear-all') }}" method="POST"
                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus semua item dari keranjang?')">
                        @csrf
                        <button type="submit" class="text-xs font-medium text-red-600 hover:text-red-700 ml-4 px-2 py-1 hover:bg-red-50 rounded transition">
                            🗑 Hapus Semua
                        </button>
                    </form>
                </div>

                @foreach ($cart->items as $item)
                <div class="card p-5 flex items-start gap-4 transition-opacity duration-200"
                    id="cart-item-{{ $item->id }}"
                    data-item-id="{{ $item->id }}">

                    {{-- Checkbox --}}
                    <div class="flex items-center pt-1 shrink-0">
                        <input type="checkbox"
                            id="item-check-{{ $item->id }}"
                            class="item-checkbox w-4 h-4 rounded text-herbal-600 border-gray-300 focus:ring-herbal-500 cursor-pointer"
                            data-item-id="{{ $item->id }}"
                            {{ $item->is_selected ? 'checked' : '' }}>
                    </div>

                    {{-- Image --}}
                    <a href="{{ route('product.show', $item->product->slug) }}" class="shrink-0 w-20 h-20 rounded-xl overflow-hidden bg-herbal-50">
                        @if ($item->product->image)
                        <img src="{{ Storage::url($item->product->image) }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                        @else
                        <div class="w-full h-full flex items-center justify-center">
                            <span class="text-2xl">🌿</span>
                        </div>
                        @endif
                    </a>

                    {{-- Info --}}
                    <div class="flex-1">
                        <a href="{{ route('product.show', $item->product->slug) }}"
                            class="font-semibold text-gray-800 hover:text-herbal-700 transition-colors leading-snug">
                            {{ $item->product->name }}
                        </a>
                        @if ($item->product->categories->isNotEmpty())
                        <p class="text-xs text-herbal-600 mt-0.5">{{ $item->product->categories->first()->name }}</p>
                        @endif
                        <p class="text-herbal-800 font-bold mt-2">
                            Rp {{ number_format($item->product->effective_price, 0, ',', '.') }}
                        </p>
                        @if ($item->product->discount_price)
                        <p class="text-xs text-gray-400 line-through">Rp {{ number_format($item->product->price, 0, ',', '.') }}</p>
                        @endif
                    </div>

                    {{-- Qty + Remove --}}
                    <div class="flex flex-col items-end gap-3 shrink-0">
                        {{-- Quantity --}}
                        <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden"
                            x-data="{ qty: {{ $item->quantity }} }">
                            <button @click="if(qty > 1) { qty--; updateCart({{ $item->id }}, qty) }"
                                class="px-3 py-2 text-gray-600 hover:bg-gray-100 transition font-bold">−</button>
                            <input x-model="qty" type="number" min="1" max="{{ $item->product->stock }}"
                                @change="updateCart({{ $item->id }}, qty)"
                                class="w-12 text-center border-0 border-x border-gray-200 py-2 text-sm font-semibold focus:ring-0">
                            <button @click="if(qty < {{ $item->product->stock }}) { qty++; updateCart({{ $item->id }}, qty) }"
                                class="px-3 py-2 text-gray-600 hover:bg-gray-100 transition font-bold">+</button>
                        </div>
                        {{-- Subtotal --}}
                        <p class="text-sm font-bold text-herbal-800" id="subtotal-{{ $item->id }}">
                            Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                        </p>
                        {{-- Remove --}}
                        <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-600 transition-colors text-xs flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach

                <a href="{{ route('shop') }}" class="inline-flex items-center gap-2 text-herbal-700 hover:text-herbal-900 text-sm font-medium mt-2">
                    ← Lanjutkan Belanja
                </a>
            </div>

            {{-- ===== STICKY ORDER SUMMARY ===== --}}
            <div class="lg:w-80 shrink-0">
                <div class="card p-6 sticky top-24">
                    <h3 class="font-bold text-gray-800 text-lg mb-5">Ringkasan Pesanan</h3>

                    <div class="space-y-3 text-sm" id="summary-items">
                        @foreach ($cart->items as $item)
                        <div class="flex justify-between text-gray-600 summary-row {{ $item->is_selected ? '' : 'opacity-40' }}"
                            data-item-id="{{ $item->id }}">
                            <span class="truncate max-w-[180px]">{{ $item->product->name }} ×{{ $item->quantity }}</span>
                            <span class="font-medium">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                    </div>

                    <div class="border-t border-gray-100 mt-4 pt-4">
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-gray-800">Total Dipilih</span>
                            <span class="text-xl font-extrabold text-herbal-800" id="cart-total">
                                Rp {{ number_format($cart->total, 0, ',', '.') }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Belum termasuk ongkos kirim</p>
                    </div>

                    @if ($cart->items->where('is_selected', true)->isEmpty())
                    <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg text-xs text-yellow-700" id="no-selection-warning">
                        ⚠ Pilih minimal satu produk untuk melanjutkan checkout.
                    </div>
                    @endif

                    <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg text-xs text-blue-700 {{ $belowMinimum ? '' : 'hidden' }}" id="minimum-order-warning">
                        Minimum checkout Rp {{ number_format($minimumOrderAmount, 0, ',', '.') }}.
                    </div>

                    <a href="{{ route('checkout.index') }}"
                        id="checkout-btn"
                        class="btn-primary w-full justify-center mt-6 py-3.5 {{ $cart->items->where('is_selected', true)->isEmpty() || $belowMinimum ? 'opacity-50 pointer-events-none' : '' }}">
                        Lanjutkan ke Checkout →
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>

    <script>
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;

        const CART_UPDATE_URL = '{{ route("cart.update", "__ID__") }}';
        const CART_TOGGLE_URL = '{{ route("cart.toggle-select", "__ID__") }}';
        const CART_TOGGLE_ALL = '{{ route("cart.toggle-select-all") }}';
const MINIMUM_ORDER_AMOUNT = {{ $minimumOrderAmount }};
function updateCart(itemId, qty) {
    fetch(CART_UPDATE_URL.replace('__ID__', itemId), {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': CSRF
        },
        body: JSON.stringify({
            quantity: qty
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            document.getElementById(`subtotal-${itemId}`).textContent = data.subtotal;
            document.getElementById('cart-total').textContent = data.total;

            const summaryRow = document.querySelector(
                `.summary-row[data-item-id="${itemId}"]`
            );

            if (summaryRow) {
                summaryRow.innerHTML = `
                    <span>${data.item_name} ×${qty}</span>
                    <span class="font-medium">${data.subtotal}</span>
                `;
            }

            updateCheckoutButton(data.selected_count, data.total_amount);
        }
    })
    .catch(console.error);
}
        document.querySelectorAll('.item-checkbox').forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                const itemId = this.dataset.itemId;
                toggleItemSelect(itemId, this.checked);
            });
        });

        function toggleItemSelect(itemId, checked) {
            fetch(CART_TOGGLE_URL.replace('__ID__', itemId), {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF
                    },
                    body: JSON.stringify({})
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {

                        if (data.items) {
                            const thisItem = data.items.find(i => i.id == itemId);
                            const summaryRow = document.querySelector(`.summary-row[data-item-id="${itemId}"]`);
                            if (summaryRow && thisItem) {
                                summaryRow.classList.toggle('opacity-40', !thisItem.is_selected);
                            }
                        }

                        document.getElementById('cart-total').textContent = data.selected_total;

                        document.getElementById('selected-count-label').textContent = data.selected_count + ' dipilih';

                        syncSelectAllCheckbox();
                        updateCheckoutButton(data.selected_count, data.selected_total_amount);
                    }
                })
                .catch(console.error);
        }

        const selectAllCheckbox = document.getElementById('select-all-checkbox');
        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                const selectAll = this.checked;
                fetch(CART_TOGGLE_ALL, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': CSRF
                        },
                        body: JSON.stringify({
                            select_all: selectAll
                        })
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {

                            document.querySelectorAll('.item-checkbox').forEach(cb => {
                                const item = data.items.find(i => i.id == cb.dataset.itemId);
                                if (item) cb.checked = item.is_selected;
                            });

                            const summaryContainer = document.getElementById('summary-items');
                            summaryContainer.innerHTML = '';

                            data.items.forEach(item => {
                                const row = document.createElement('div');
                                row.className = 'flex justify-between text-gray-600 summary-row ' + (item.is_selected ? '' : 'opacity-40');
                                row.innerHTML = `
                <span>${item.name} ×${item.quantity}</span>
                <span>Rp ${new Intl.NumberFormat('id-ID').format(item.subtotal)}</span>
            `;
                                summaryContainer.appendChild(row);
                            });

                            document.getElementById('cart-total').textContent = data.selected_total;

                            document.getElementById('selected-count-label').textContent = data.selected_count + ' dipilih';

                            updateCheckoutButton(data.selected_count, data.selected_total_amount);
                        }
                    })
                    .catch(console.error);
            });
        }

        function syncSelectAllCheckbox() {
            const all = document.querySelectorAll('.item-checkbox');
            const checked = document.querySelectorAll('.item-checkbox:checked');
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = all.length === checked.length;
                selectAllCheckbox.indeterminate = checked.length > 0 && checked.length < all.length;
            }
        }

        function updateCheckoutButton(selectedCount, selectedTotalAmount) {
            const btn = document.getElementById('checkout-btn');
            const warning = document.getElementById('no-selection-warning');
            const minimumWarning = document.getElementById('minimum-order-warning');
            if (!btn) return;

            const count = parseInt(selectedCount, 10);
            const total = parseInt(selectedTotalAmount, 10) || 0;
            const belowMinimumNow = MINIMUM_ORDER_AMOUNT > 0 && total < MINIMUM_ORDER_AMOUNT;

            if (isNaN(count) || count <= 0 || belowMinimumNow) {
                btn.classList.add('opacity-50', 'pointer-events-none');
                if (warning) warning.style.display = isNaN(count) || count <= 0 ? 'block' : 'none';
                if (minimumWarning) minimumWarning.classList.toggle('hidden', !belowMinimumNow);
            } else {
                btn.classList.remove('opacity-50', 'pointer-events-none');
                if (warning) warning.style.display = 'none';
                if (minimumWarning) minimumWarning.classList.add('hidden');
            }
        }

        syncSelectAllCheckbox();
        syncSelectAllCheckbox();

      updateCheckoutButton(
    {{ $cart->selected_count }},
    {{ (int) $cart->total }}
);
    </script>
</x-app-layout>