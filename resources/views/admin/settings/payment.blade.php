<x-layouts.admin>
<x-slot name="title">Pengaturan Pembayaran</x-slot>
<x-slot name="subtitle">Kelola metode pembayaran, rekening bank, dan biaya COD</x-slot>

<div class="max-w-5xl">



    <div class="space-y-6">

        {{-- ═══ Payment Methods + COD Fee ═══ --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-800 mb-5 flex items-center gap-2">
                <span class="w-7 h-7 bg-indigo-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="credit-card" class="w-4 h-4 text-indigo-600"></i>
                </span>
                Metode Pembayaran
            </h3>

            <form action="{{ route('admin.settings.payment.update') }}" method="POST">
                @csrf

                @php
                $methodLabels = [
                    'cod'           => ['label' => 'Bayar di Tempat (COD)',   'icon' => '🏠', 'desc' => 'Bayar saat produk tiba'],
                    'dana'          => ['label' => 'Dana',                     'icon' => '💙', 'desc' => 'Transfer via aplikasi Dana'],
                    'gopay'         => ['label' => 'GoPay',                    'icon' => '💚', 'desc' => 'Transfer via GoPay'],
                    'qris'          => ['label' => 'QRIS',                     'icon' => '🔲', 'desc' => 'Scan kode QR universal'],
                    'bank_transfer' => ['label' => 'Bank Transfer',            'icon' => '🏦', 'desc' => 'Transfer ATM / Mobile Banking'],
                ];
                @endphp

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-6">
                    @foreach ($methodLabels as $key => $meta)
                    @php $isActive = $settings["method_{$key}"] ?? true; @endphp
                    <label class="flex items-center justify-between p-4 rounded-xl border-2 cursor-pointer transition
                                  {{ $isActive ? 'border-green-300 bg-green-50' : 'border-gray-200 bg-gray-50' }}"
                           id="method-card-{{ $key }}"
                           onclick="toggleMethod('{{ $key }}', this)">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl">{{ $meta['icon'] }}</span>
                            <div>
                                <p class="font-semibold text-sm text-gray-800">{{ $meta['label'] }}</p>
                                <p class="text-xs text-gray-500">{{ $meta['desc'] }}</p>
                            </div>
                        </div>
                        <div class="relative shrink-0">
                            <input type="checkbox" name="method_{{ $key }}"
                                   id="method-{{ $key }}"
                                   class="sr-only peer"
                                   {{ $isActive ? 'checked' : '' }}>
                            <div class="w-11 h-6 rounded-full transition
                                        {{ $isActive ? 'bg-green-600' : 'bg-gray-300' }}"
                                 id="toggle-bg-{{ $key }}">
                                <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform
                                            {{ $isActive ? 'translate-x-5' : '' }}"
                                     id="toggle-dot-{{ $key }}"></div>
                            </div>
                        </div>
                    </label>
                    @endforeach
                </div>

                {{-- COD Fee --}}
                <div class="border-t border-gray-100 pt-5">
                    <h4 class="font-semibold text-gray-700 text-sm mb-3 flex items-center gap-2">
                        <i data-lucide="package" class="w-4 h-4 text-amber-500"></i>
                        Biaya COD (Cash on Delivery)
                    </h4>
                    <div class="flex items-center gap-3 max-w-xs">
                        <span class="text-sm font-bold text-gray-500 shrink-0">Rp</span>
                        <input type="number" name="cod_fee" min="0" step="500"
                               value="{{ $settings['cod_fee'] ?? 15000 }}"
                               class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500/30 focus:border-green-500 outline-none">
                        <span class="text-xs text-gray-400">/ pesanan</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-2">Biaya ini otomatis ditambahkan ke total pesanan COD.</p>
                </div>

                <div class="mt-5 flex justify-end">
                    <button type="submit"
                            class="bg-green-700 hover:bg-green-800 text-white font-semibold py-2.5 px-6 rounded-xl text-sm transition flex items-center gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i> Simpan Pengaturan Pembayaran
                    </button>
                </div>
            </form>
        </div>

        {{-- ═══ Bank Accounts ═══ --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-5">
                <h3 class="font-bold text-gray-800 flex items-center gap-2">
                    <span class="w-7 h-7 bg-amber-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="landmark" class="w-4 h-4 text-amber-600"></i>
                    </span>
                    Rekening Bank
                </h3>
                <button onclick="document.getElementById('add-bank-modal').classList.remove('hidden')"
                        class="bg-green-700 hover:bg-green-800 text-white text-sm font-medium px-4 py-2 rounded-lg transition flex items-center gap-2">
                    <i data-lucide="plus" class="w-4 h-4"></i> Tambah Rekening
                </button>
            </div>

            @if ($bankAccounts->isEmpty())
            <div class="text-center py-10 text-gray-300">
                <i data-lucide="landmark" class="w-12 h-12 mx-auto mb-2"></i>
                <p class="text-sm text-gray-400">Belum ada rekening bank. Tambahkan rekening untuk menerima transfer.</p>
            </div>
            @else
            <div class="space-y-3">
                @foreach ($bankAccounts as $account)
                <div class="flex items-center justify-between p-4 rounded-xl border border-gray-100 bg-gray-50 group">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white rounded-xl border border-gray-200 flex items-center justify-center font-bold text-sm text-green-800 shrink-0">
                            {{ strtoupper(substr($account->bank_name, 0, 3)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-sm text-gray-800">{{ $account->bank_name }}</p>
                            <p class="text-xs text-gray-500">{{ $account->account_number }} · {{ $account->account_holder }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition">
                        <button onclick="openEditModal({{ $account->id }}, '{{ addslashes($account->bank_name) }}', '{{ $account->account_number }}', '{{ addslashes($account->account_holder) }}')"
                                class="w-8 h-8 rounded-lg bg-blue-100 hover:bg-blue-200 flex items-center justify-center text-blue-600 transition">
                            <i data-lucide="pencil" class="w-3.5 h-3.5"></i>
                        </button>
                        <form action="{{ route('admin.settings.bank.destroy', $account) }}" method="POST"
                              onsubmit="return confirm('Hapus rekening ini?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="w-8 h-8 rounded-lg bg-red-100 hover:bg-red-200 flex items-center justify-center text-red-600 transition">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

    </div>{{-- end space-y-6 --}}
</div>

{{-- ══════════════ ADD BANK MODAL ══════════════ --}}
<div id="add-bank-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="document.getElementById('add-bank-modal').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md z-10">
        <h3 class="font-bold text-gray-800 mb-5">Tambah Rekening Bank</h3>
        <form action="{{ route('admin.settings.bank.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Bank</label>
               <select name="bank_name"
    class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500/30 outline-none"
    required>

    <option value="">-- Pilih Bank --</option>
    <option value="BCA">BCA</option>
    <option value="Mandiri">Mandiri</option>
    <option value="BRI">BRI</option>
    <option value="BNI">BNI</option>
    <option value="CIMB Niaga">CIMB Niaga</option>
    <option value="Permata">Permata</option>
</select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nomor Rekening</label>
                <input type="text" name="account_number" placeholder="1234567890"
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500/30 outline-none" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Atas Nama</label>
                <input type="text" name="account_holder" placeholder="Nama pemilik rekening"
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500/30 outline-none" required>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('add-bank-modal').classList.add('hidden')"
                        class="flex-1 border border-gray-200 text-gray-700 py-2.5 rounded-xl text-sm font-medium hover:bg-gray-50 transition">Batal</button>
                <button type="submit"
                        class="flex-1 bg-green-700 text-white py-2.5 rounded-xl text-sm font-semibold hover:bg-green-800 transition">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════ EDIT BANK MODAL ══════════════ --}}
<div id="edit-bank-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="document.getElementById('edit-bank-modal').classList.add('hidden')"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md z-10">
        <h3 class="font-bold text-gray-800 mb-5">Edit Rekening Bank</h3>
        <form id="edit-bank-form" method="POST" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Bank</label>
                <input type="text" id="edit-bank-name" name="bank_name"
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500/30 outline-none" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nomor Rekening</label>
                <input type="text" id="edit-account-number" name="account_number"
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500/30 outline-none" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Atas Nama</label>
                <input type="text" id="edit-account-holder" name="account_holder"
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500/30 outline-none" required>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('edit-bank-modal').classList.add('hidden')"
                        class="flex-1 border border-gray-200 text-gray-700 py-2.5 rounded-xl text-sm font-medium hover:bg-gray-50 transition">Batal</button>
                <button type="submit"
                        class="flex-1 bg-green-700 text-white py-2.5 rounded-xl text-sm font-semibold hover:bg-green-800 transition">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleMethod(key, card) {
    const cb  = document.getElementById('method-' + key);
    const bg  = document.getElementById('toggle-bg-' + key);
    const dot = document.getElementById('toggle-dot-' + key);

    cb.checked = !cb.checked;

    if (cb.checked) {
        card.classList.replace('border-gray-200','border-green-300');
        card.classList.replace('bg-gray-50','bg-green-50');
        bg.classList.replace('bg-gray-300','bg-green-600');
        dot.classList.add('translate-x-5');
    } else {
        card.classList.replace('border-green-300','border-gray-200');
        card.classList.replace('bg-green-50','bg-gray-50');
        bg.classList.replace('bg-green-600','bg-gray-300');
        dot.classList.remove('translate-x-5');
    }
}

function openEditModal(id, bankName, accountNumber, accountHolder) {
    document.getElementById('edit-bank-form').action =
        '{{ url("admin/settings/payment/bank") }}/' + id;
    document.getElementById('edit-bank-name').value       = bankName;
    document.getElementById('edit-account-number').value  = accountNumber;
    document.getElementById('edit-account-holder').value  = accountHolder;
    document.getElementById('edit-bank-modal').classList.remove('hidden');
}
</script>

</x-layouts.admin>
