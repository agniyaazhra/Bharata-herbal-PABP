<x-guest-layout>
    <x-slot name="title">Daftar Akun</x-slot>

    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-900">Buat Akun Baru</h2>
        <p class="text-gray-500 mt-2">Bergabung dengan Bharata Herbal hari ini</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        {{-- Nama Lengkap --}}
        <div>
            <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Lengkap</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                   autocomplete="name"
                   class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-herbal-500 focus:ring-2 focus:ring-herbal-200 outline-none transition text-sm"
                   placeholder="Masukkan nama lengkap Anda">
            @error('name')
                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                   autocomplete="username"
                   class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-herbal-500 focus:ring-2 focus:ring-herbal-200 outline-none transition text-sm"
                   placeholder="nama@email.com">
            @error('email')
                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- No Telepon --}}
        <div>
            <label for="phone" class="block text-sm font-semibold text-gray-700 mb-1.5">Nomor Telepon</label>
            <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">+62</span>
                <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" required
                       class="w-full pl-12 pr-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-herbal-500 focus:ring-2 focus:ring-herbal-200 outline-none transition text-sm"
                       placeholder="08xxxxxxxxxx">
            </div>
            @error('phone')
                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Password</label>
            <input id="password" type="password" name="password" required
                   autocomplete="new-password"
                   class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-herbal-500 focus:ring-2 focus:ring-herbal-200 outline-none transition text-sm"
                   placeholder="Minimal 6 karakter">
            @error('password')
                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Confirm Password --}}
        <div>
            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1.5">Konfirmasi Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required
                   autocomplete="new-password"
                   class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-herbal-500 focus:ring-2 focus:ring-herbal-200 outline-none transition text-sm"
                   placeholder="Ulangi password Anda">
            @error('password_confirmation')
                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Terms --}}
        <p class="text-xs text-gray-500 leading-relaxed">
            Dengan mendaftar, Anda menyetujui
            <a href="#" class="text-herbal-700 font-medium">Syarat &amp; Ketentuan</a>
            dan <a href="#" class="text-herbal-700 font-medium">Kebijakan Privasi</a> kami.
        </p>

        {{-- Submit --}}
        <button type="submit"
                class="w-full bg-herbal-800 hover:bg-herbal-900 text-white font-semibold py-3.5 px-6 rounded-xl transition-all duration-200 shadow-sm hover:shadow-md active:scale-[0.98] text-sm mt-2">
            Buat Akun
        </button>
    </form>

    {{-- Login Link --}}
    <div class="mt-8 pt-6 border-t border-gray-100 text-center">
        <p class="text-sm text-gray-600">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="font-semibold text-herbal-700 hover:text-herbal-900 transition-colors ml-1">
                Masuk di Sini
            </a>
        </p>
    </div>
</x-guest-layout>
