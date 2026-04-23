<x-layouts.admin>
<x-slot name="title">Tambah Admin</x-slot>
<x-slot name="subtitle">Buat akun admin baru untuk mengakses panel admin</x-slot>

<div class="max-w-2xl">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
        <form action="{{ route('admin.admins.store') }}" method="POST" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500/30 outline-none @error('name') border-red-400 @enderror"
                           placeholder="contoh: Budi Santoso">
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500/30 outline-none @error('email') border-red-400 @enderror"
                           placeholder="admin@example.com">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Password</label>
                    <input type="password" name="password"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500/30 outline-none @error('password') border-red-400 @enderror"
                           placeholder="Min. 8 karakter">
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500/30 outline-none"
                           placeholder="Ulangi password">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">No. Telepon <span class="text-gray-400">(opsional)</span></label>
                    <input type="text" name="phone" value="{{ old('phone') }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500/30 outline-none"
                           placeholder="08xx-xxxx-xxxx">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Role</label>
                    <select name="role"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500/30 outline-none @error('role') border-red-400 @enderror">
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="super_admin" {{ old('role') === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                    </select>
                    @error('role') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    <p class="text-xs text-gray-400 mt-1">Super Admin memiliki akses penuh termasuk manajemen admin lain.</p>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit"
                        class="bg-green-700 hover:bg-green-800 text-white font-semibold px-6 py-2.5 rounded-xl text-sm transition flex items-center gap-2">
                    <i data-lucide="user-plus" class="w-4 h-4"></i> Tambah Admin
                </button>
                <a href="{{ route('admin.admins.index') }}"
                   class="border border-gray-200 hover:bg-gray-50 text-gray-600 font-medium px-6 py-2.5 rounded-xl text-sm transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

</x-layouts.admin>
