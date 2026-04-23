<x-app-layout>
    <x-slot name="title">Tentang Kami</x-slot>
    @php
        $storeName = $storeSettings['store_name'] ?? 'Bharata Herbal';
        $storeDescription = $storeSettings['store_description'] ?? 'Kami hadir untuk membawa kebaikan alam nusantara ke kehidupan Anda sehari-hari.';
    @endphp

    {{-- Hero --}}
    <section class="bg-gradient-to-br from-herbal-900 to-herbal-700 text-white py-20">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 text-center">
            <h1 class="text-4xl font-extrabold">Tentang {{ $storeName }}</h1>
            <p class="mt-4 text-herbal-200 text-lg">{{ $storeDescription }}</p>
        </div>
    </section>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 space-y-16">

        {{-- Mission --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Misi Kami</h2>
                <p class="text-gray-600 leading-relaxed">
                    Bharata Herbal didirikan dengan misi mulia: menyediakan produk herbal alami berkualitas tinggi yang dapat diakses oleh semua lapisan masyarakat Indonesia.
                    Kami percaya bahwa kesehatan adalah hak setiap orang, dan alam nusantara menyimpan kekayaan yang luar biasa untuk mendukungnya.
                </p>
                <p class="text-gray-600 leading-relaxed mt-4">
                    Setiap produk kami dipilih dengan teliti, diformulasikan dari bahan-bahan alami pilihan, dan diproses dengan standar kualitas tertinggi untuk memastikan keamanan dan efektivitasnya.
                </p>
            </div>
            <div class="bg-herbal-50 rounded-2xl p-8 text-center">
                <div class="text-6xl mb-4">🌿</div>
                <h3 class="font-bold text-herbal-800 text-xl">100% Alami</h3>
                <p class="text-herbal-700 mt-2">Semua bahan dipilih dari sumber alami terpercaya</p>
            </div>
        </div>

        {{-- Values --}}
        <div>
            <h2 class="text-2xl font-bold text-gray-800 text-center mb-10">Nilai-Nilai Kami</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                @foreach ([
                    ['icon' => '🌱', 'title' => 'Alami', 'desc' => 'Bahan-bahan 100% alami tanpa bahan kimia berbahaya'],
                    ['icon' => '✅', 'title' => 'Terpercaya', 'desc' => 'Produk telah diuji dan mendapat kepercayaan ribuan pelanggan'],
                    ['icon' => '💚', 'title' => 'Peduli', 'desc' => 'Kami peduli pada kesehatan Anda dan kelestarian alam'],
                ] as $val)
                <div class="card p-6 text-center">
                    <div class="text-4xl mb-3">{{ $val['icon'] }}</div>
                    <h3 class="font-bold text-gray-800 mb-2">{{ $val['title'] }}</h3>
                    <p class="text-gray-500 text-sm">{{ $val['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>

    </div>
</x-app-layout>
