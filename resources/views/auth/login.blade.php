<x-guest-layout>
    <x-slot name="title">Masuk</x-slot>

    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-900">Selamat Datang!</h2>
        <p class="text-gray-500 mt-2">Masuk ke akun Bharata Herbal Anda</p>
    </div>

    {{-- Session Status --}}
    @if (session('status'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm font-medium">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        {{-- Email --}}
        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                   autocomplete="username"
                   class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-herbal-500 focus:ring-2 focus:ring-herbal-200 outline-none transition text-sm"
                   placeholder="nama@email.com">
            @error('email')
                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-sm font-semibold text-gray-700">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-xs text-herbal-700 hover:text-herbal-900 font-medium">
                        Lupa password?
                    </a>
                @endif
            </div>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                   class="w-full px-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:border-herbal-500 focus:ring-2 focus:ring-herbal-200 outline-none transition text-sm"
                   placeholder="Masukkan password">
            @error('password')
                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Remember Me --}}
        <div class="flex items-center gap-2">
            <input id="remember_me" type="checkbox" name="remember"
                   class="w-4 h-4 rounded border-gray-300 text-herbal-600 focus:ring-herbal-500">
            <label for="remember_me" class="text-sm text-gray-600 select-none cursor-pointer">Ingat saya</label>
        </div>

        {{-- Submit --}}
        <button type="submit"
                class="w-full bg-herbal-800 hover:bg-herbal-900 text-white font-semibold py-3.5 px-6 rounded-xl transition-all duration-200 shadow-sm hover:shadow-md active:scale-[0.98] text-sm">
            Masuk ke Akun
        </button>
    </form>

    {{-- Register Link --}}
    <div class="mt-8 pt-6 border-t border-gray-100 text-center">
        <p class="text-sm text-gray-600">
            Belum punya akun?
            <a href="{{ route('register') }}" class="font-semibold text-herbal-700 hover:text-herbal-900 transition-colors ml-1">
                Daftar Sekarang
            </a>
        </p>
    </div>
</x-guest-layout>
