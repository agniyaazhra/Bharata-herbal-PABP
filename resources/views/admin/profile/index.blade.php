<x-layouts.admin>
    <x-slot name="title">Pengaturan Profil Admin</x-slot>
    <x-slot name="subtitle">Kelola informasi pribadi dan keamanan akun Anda</x-slot>

    <div class="max-w-5xl">

        {{-- Success Alert --}}
        @if (session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-xl text-green-800 text-sm">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5 text-green-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Profile Card --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Edit Profil --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-bold text-gray-800 mb-5 flex items-center gap-2">
                        <span class="w-7 h-7 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="user" class="w-4 h-4 text-blue-600"></i>
                        </span>
                        Data Pribadi Admin
                    </h3>

                    <form method="POST" action="{{ route('admin.profile.update') }}" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500/30 focus:border-green-500 outline-none transition @error('name') border-red-400 @enderror"
                                required>
                            @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Email</label>
                            <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500/30 focus:border-green-500 outline-none transition @error('email') border-red-400 @enderror"
                                required>
                            @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nomor Telepon</label>
                            <input type="text" name="phone" value="{{ old('phone', auth()->user()->phone) }}"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500/30 focus:border-green-500 outline-none transition"
                                placeholder="081234567890">
                            @error('phone')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div class="pt-2 flex gap-3">
                            <button type="submit"
                                class="flex-1 bg-green-700 hover:bg-green-800 text-white font-semibold py-2.5 px-4 rounded-xl text-sm transition flex items-center justify-center gap-2">
                                <i data-lucide="save" class="w-4 h-4"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Change Password --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-bold text-gray-800 mb-5 flex items-center gap-2">
                        <span class="w-7 h-7 bg-red-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="lock" class="w-4 h-4 text-red-600"></i>
                        </span>
                        Ubah Password
                    </h3>

                    <form method="POST" action="{{ route('admin.profile.password') }}" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Password Lama</label>
                            <input type="password" name="old_password"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-red-500/30 focus:border-red-500 outline-none transition @error('old_password') border-red-400 @enderror"
                                required>
                            @error('old_password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Password Baru</label>
                            <input type="password" name="password"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-red-500/30 focus:border-red-500 outline-none transition @error('password') border-red-400 @enderror"
                                required>
                            @error('password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation"
                                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-red-500/30 focus:border-red-500 outline-none transition"
                                required>
                        </div>

                        <p class="text-xs text-gray-500">Password minimal 8 karakter, kombinasi huruf dan angka lebih aman.</p>

                        <div class="pt-2">
                            <button type="submit"
                                class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold py-2.5 px-4 rounded-xl text-sm transition flex items-center justify-center gap-2">
                                <i data-lucide="key" class="w-4 h-4"></i> Ubah Password
                            </button>
                        </div>
                    </form>
                </div>

            </div>

            {{-- Info Card --}}
            <div class="space-y-4">

                {{-- Profile Summary --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-24">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <i data-lucide="user-check" class="w-5 h-5 text-green-600"></i>
                        Profil Anda
                    </h3>

                    <div class="space-y-3 text-sm">
                        <div>
                            <p class="text-gray-500 font-medium">Nama</p>
                            <p class="text-gray-800 font-semibold">{{ auth()->user()->name }}</p>
                        </div>

                        <div>
                            <p class="text-gray-500 font-medium">Email</p>
                            <p class="text-gray-800 text-xs break-all">{{ auth()->user()->email }}</p>
                        </div>

                        <div>
                            <p class="text-gray-500 font-medium">Role</p>
                            <p class="text-gray-800 font-semibold">
                                @if(auth()->user()->role === 'admin')
                                <span class="inline-block bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-semibold">Admin</span>
                                @endif
                            </p>
                        </div>

                        @if(auth()->user()->last_login)
                        <div>
                            <p class="text-gray-500 font-medium">Login Terakhir</p>
                            <p class="text-gray-700 text-xs">{{ auth()->user()->last_login->diffForHumans() }}</p>
                        </div>
                        @endif
                    </div>
                </div>

            </div>

        </div>

</x-layouts.admin>