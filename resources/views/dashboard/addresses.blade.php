<x-layouts.dashboard>
    <x-slot name="title">Alamat Saya</x-slot>
    <x-slot name="slot">

    <div class="space-y-6">
        {{-- Add New Address --}}
        <div class="card p-6" x-data="{ open: false }">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-bold text-gray-800 text-lg">Daftar Alamat</h2>
                <button @click="open = !open" class="btn-primary text-sm py-2 px-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Tambah Alamat
                </button>
            </div>

            {{-- Add form --}}
            <div x-show="open" x-transition class="border-t border-gray-100 pt-5 mt-2">
                <form action="{{ route('user.addresses.store') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @csrf
                    <div>
                        <label class="form-label">Label Alamat</label>
                        <select name="label" class="form-input">
                            <option value="Rumah">Rumah</option>
                            <option value="Kantor">Kantor</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Nama Penerima</label>
                        <input type="text" name="recipient_name" class="form-input" required>
                        @error('recipient_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Nomor HP Penerima</label>
                        <input type="text" name="phone" class="form-input" required>
                        @error('phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label class="form-label">Alamat Lengkap</label>
                        <textarea name="street" rows="2" class="form-input resize-none" required></textarea>
                        @error('street')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Kota</label>
                        <input type="text" name="city" class="form-input" required>
                        @error('city')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Provinsi</label>
                        <input type="text" name="province" class="form-input" required>
                        @error('province')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Kode Pos</label>
                        <input type="text" name="postal_code" class="form-input" required maxlength="10">
                        @error('postal_code')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex items-center gap-2 mt-4">
                        <input type="checkbox" name="is_default" id="is_default" value="1" class="rounded text-herbal-600 focus:ring-herbal-500">
                        <label for="is_default" class="text-sm text-gray-700">Jadikan alamat utama</label>
                    </div>
                    <div class="sm:col-span-2 flex gap-3">
                        <button type="submit" class="btn-primary text-sm py-2.5">Simpan Alamat</button>
                        <button type="button" @click="open = false" class="btn-secondary text-sm py-2.5">Batal</button>
                    </div>
                </form>
            </div>

            {{-- Address list --}}
            @if ($addresses->isEmpty())
                <div class="py-10 text-center text-gray-400">
                    <p class="text-3xl mb-3">📍</p>
                    <p class="font-semibold text-gray-600">Belum ada alamat tersimpan</p>
                    <p class="text-sm mt-1">Klik "Tambah Alamat" untuk menambahkan.</p>
                </div>
            @else
                <div class="space-y-4 mt-4">
                    @foreach ($addresses as $address)
                    <div class="border-2 rounded-xl p-4 {{ $address->is_default ? 'border-herbal-500 bg-herbal-50' : 'border-gray-200' }} transition-colors">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-semibold text-gray-800">{{ $address->recipient_name }}</span>
                                    <span class="badge bg-gray-100 text-gray-600 text-xs">{{ $address->label }}</span>
                                    @if ($address->is_default)
                                        <span class="badge bg-herbal-100 text-herbal-700 text-xs font-bold">✓ Utama</span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-600 mt-1">{{ $address->phone }}</p>
                                <p class="text-sm text-gray-500">{{ $address->full_address }}</p>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                @if (!$address->is_default)
                                <form action="{{ route('user.addresses.default', $address) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-xs text-herbal-700 hover:text-herbal-900 font-medium border border-herbal-300 px-3 py-1.5 rounded-lg hover:bg-herbal-50 transition-colors">
                                        Jadikan Utama
                                    </button>
                                </form>
                                @endif
                                <form action="{{ route('user.addresses.destroy', $address) }}" method="POST" onsubmit="return confirm('Hapus alamat ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-red-500 hover:text-red-700 border border-red-200 px-3 py-1.5 rounded-lg hover:bg-red-50 transition-colors">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    </x-slot>
</x-layouts.dashboard>
