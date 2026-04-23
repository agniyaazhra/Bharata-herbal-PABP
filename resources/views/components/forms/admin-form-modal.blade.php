@props([
'title' => 'Form Admin',
'isEdit' => false,
'user' => null,
])

<div x-cloak x-show="showModal" x-transition.opacity class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @click.self="closeAdminModal()">
    <div @click.stop x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="bg-white rounded-2xl shadow-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">

        {{-- Header --}}
        <div class="sticky top-0 bg-white border-b border-gray-100 px-8 py-6 flex items-center justify-between">
            <div>
                <h3 class="font-bold text-gray-900 text-lg" x-text="isEditMode ? 'Edit Admin' : 'Tambah Admin Baru'"></h3>
            </div>
            <button @click="closeAdminModal()" class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        {{-- Form --}}
        <form :action="formAction" method="POST" @submit.prevent="submitForm" class="p-8">
            @csrf
            <template x-if="isEditMode">
                @method('PUT')
            </template>

            {{-- Error Alert --}}
            <div x-show="errors.length > 0" x-cloak class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl">
                <p class="text-red-700 text-sm font-semibold mb-2">Terjadi kesalahan:</p>
                <ul class="space-y-1">
                    <template x-for="error in errors" :key="error">
                        <li class="text-red-600 text-sm">• <span x-text="error"></span></li>
                    </template>
                </ul>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                {{-- Nama Lengkap --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" x-model="formData.name"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500/30 outline-none"
                        placeholder="contoh: Budi Santoso">
                    <template x-if="fieldErrors.name">
                        <p class="text-red-500 text-xs mt-1" x-text="fieldErrors.name[0]"></p>
                    </template>
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                    <input type="email" name="email" x-model="formData.email"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500/30 outline-none"
                        placeholder="admin@example.com">
                    <template x-if="fieldErrors.email">
                        <p class="text-red-500 text-xs mt-1" x-text="fieldErrors.email[0]"></p>
                    </template>
                </div>

                {{-- Password --}}
                <div class="relative">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        <span x-text="isEditMode ? 'Password Baru (kosongkan jika tidak diubah)' : 'Password'"></span>
                    </label>
                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'" name="password" x-model="formData.password"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 pr-10 text-sm focus:ring-2 focus:ring-green-500/30 outline-none"
                            placeholder="Min. 8 karakter">
                        <button type="button" @click="showPassword = !showPassword" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                            <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-4.803m5.596-3.856a3.375 3.375 0 11-4.753 4.753m4.753-4.753L3.596 3.039m10.318 10.318L3.039 3.596"></path>
                            </svg>
                        </button>
                    </div>
                    <template x-if="fieldErrors.password">
                        <p class="text-red-500 text-xs mt-1" x-text="fieldErrors.password[0]"></p>
                    </template>
                </div>

                {{-- Konfirmasi Password --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" x-model="formData.password_confirmation"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500/30 outline-none"
                        placeholder="Ulangi password">
                </div>

                {{-- Telepon --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">No. Telepon <span class="text-gray-400">(opsional)</span></label>
                    <input type="text" name="phone" x-model="formData.phone"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500/30 outline-none"
                        placeholder="08xx-xxxx-xxxx">
                </div>

                {{-- Role --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Role</label>
                    <select name="role" x-model="formData.role"
                        class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500/30 outline-none">
                        <option value="admin">Admin</option>
                        <option value="super_admin">Super Admin</option>
                    </select>
                    <template x-if="fieldErrors.role">
                        <p class="text-red-500 text-xs mt-1" x-text="fieldErrors.role[0]"></p>
                    </template>
                    <p class="text-xs text-gray-400 mt-1">Super Admin memiliki akses penuh termasuk manajemen admin lain.</p>
                </div>
            </div>

            {{-- Edit Info --}}
            <template x-if="isEditMode && editUser">
                <div class="mt-5 p-4 bg-gray-50 rounded-xl text-xs text-gray-500 space-y-1">
                    <p>📅 Bergabung: <strong x-text="editUser.created_at"></strong></p>
                    <p>🕐 Login Terakhir: <strong x-text="editUser.last_login"></strong></p>
                </div>
            </template>

            {{-- Buttons --}}
            <div class="flex items-center gap-3 pt-6 border-t border-gray-100 mt-6">
                <button type="submit" class="flex-1 bg-green-700 hover:bg-green-800 text-white font-semibold px-6 py-2.5 rounded-xl text-sm transition flex items-center justify-center gap-2">
                    <svg x-show="!isLoading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" x-text="isEditMode ? '💾' : '➕'"></path>
                    </svg>
                    <span x-text="isLoading ? 'Menyimpan...' : (isEditMode ? 'Simpan Perubahan' : 'Tambah Admin')"></span>
                </button>
                <button type="button" @click="closeAdminModal()" class="flex-1 border border-gray-200 hover:bg-gray-50 text-gray-600 font-medium px-6 py-2.5 rounded-xl text-sm transition">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>