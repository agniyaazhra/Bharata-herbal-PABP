{{-- User dashboard sidebar layout wrapper --}}
<x-app-layout>
    <x-slot name="title">{{ $title ?? 'Dashboard' }}</x-slot>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex flex-col md:flex-row gap-8">

            {{-- Sidebar --}}
            <aside class="md:w-64 shrink-0">
                <div class="card p-5 sticky top-24">
                    <div class="flex items-center gap-3 mb-5 pb-5 border-b border-gray-100">
                        <div class="w-12 h-12 bg-herbal-100 rounded-full flex items-center justify-center">
                            <span class="font-bold text-herbal-700 text-lg">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</span>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                    <nav class="space-y-1">
                        @php
                            $navItems = [
                                ['route' => 'user.profile',    'label' => 'Profil Saya',    'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                                ['route' => 'user.addresses',  'label' => 'Alamat',         'icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z'],
                                ['route' => 'orders.index',    'label' => 'Pesanan Saya',   'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                            ];
                        @endphp
                        @foreach ($navItems as $nav)
                        <a href="{{ route($nav['route']) }}"
                           class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors
                                  {{ request()->routeIs($nav['route']) ? 'bg-herbal-100 text-herbal-800' : 'text-gray-600 hover:bg-gray-100' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $nav['icon'] }}"/>
                            </svg>
                            {{ $nav['label'] }}
                        </a>
                        @endforeach
                    </nav>
                </div>
            </aside>

            {{-- Content --}}
            <main class="flex-1">
                {{ $slot }}
            </main>
        </div>
    </div>
</x-app-layout>
