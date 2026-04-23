<x-app-layout>
    <x-slot name="title">Kontak</x-slot>
    @php
        $storeName = $storeSettings['store_name'] ?? 'Bharata Herbal';
        $storeAddress = $storeSettings['store_address'] ?? 'Jl. Nusantara No. 1, Jakarta Selatan, DKI Jakarta 12345';
        $businessEmail = $storeSettings['business_email'] ?? 'info@bharataherbal.id';
        $whatsappNumber = $storeSettings['whatsapp_number'] ?? '+62 812-3456-7890';
    @endphp

    <section class="bg-gradient-to-br from-herbal-900 to-herbal-700 text-white py-16">
        <div class="max-w-3xl mx-auto px-4 text-center">
            <h1 class="text-4xl font-extrabold">Hubungi Kami</h1>
            <p class="mt-3 text-herbal-200">Kami siap membantu Anda. Tim {{ $storeName }} akan merespons secepatnya.</p>
        </div>
    </section>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10">

            {{-- Contact Form --}}
            <div class="card p-8">
                <h2 class="font-bold text-gray-800 text-xl mb-6">Kirim Pesan</h2>
                <form class="space-y-4">
                    @csrf
                    <div>
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-input" placeholder="Nama Anda">
                    </div>
                    <div>
                        <label class="form-label">Email</label>
                        <input type="email" class="form-input" placeholder="email@anda.com">
                    </div>
                    <div>
                        <label class="form-label">Subjek</label>
                        <input type="text" class="form-input" placeholder="Perihal pesan...">
                    </div>
                    <div>
                        <label class="form-label">Pesan</label>
                        <textarea rows="5" class="form-input resize-none" placeholder="Tulis pesan Anda di sini..."></textarea>
                    </div>
                    <button type="submit" class="btn-primary w-full justify-center">
                        Kirim Pesan
                    </button>
                </form>
            </div>

            {{-- Contact Info --}}
            <div class="space-y-6">
                <h2 class="font-bold text-gray-800 text-xl">Informasi Kontak</h2>
                @foreach ([
                    ['icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z|M15 11a3 3 0 11-6 0 3 3 0 016 0z', 'label' => 'Alamat', 'value' => $storeAddress],
                    ['icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z', 'label' => 'Email', 'value' => $businessEmail],
                    ['icon' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z', 'label' => 'Telepon', 'value' => $whatsappNumber],
                ] as $info)
                <div class="card p-5 flex items-start gap-4">
                    <div class="w-10 h-10 bg-herbal-100 rounded-xl flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-herbal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            @foreach (explode('|', $info['icon']) as $d)
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $d }}"/>
                            @endforeach
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wide">{{ $info['label'] }}</p>
                        <p class="text-gray-800 font-medium mt-0.5">{{ $info['value'] }}</p>
                    </div>
                </div>
                @endforeach

                <div class="card p-5">
                    <p class="text-xs text-gray-500 font-medium uppercase tracking-wide mb-3">Jam Operasional</p>
                    <p class="text-gray-700 text-sm">Senin – Jumat: 09.00 – 17.00 WIB</p>
                    <p class="text-gray-700 text-sm">Sabtu: 09.00 – 14.00 WIB</p>
                    <p class="text-gray-400 text-sm">Minggu & Hari Libur: Tutup</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
