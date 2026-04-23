<x-layouts.admin>
<x-slot name="title">Pengaturan Toko</x-slot>
<x-slot name="subtitle">Kelola informasi dan identitas toko Anda</x-slot>

<div class="max-w-5xl">



    <form id="store-form" action="{{ route('admin.settings.store.update') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- ── Left: Main Info ────────────────────────────────── --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Store Identity --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-bold text-gray-800 mb-5 flex items-center gap-2">
                        <span class="w-7 h-7 bg-green-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="store" class="w-4 h-4 text-green-700"></i>
                        </span>
                        Identitas Toko
                    </h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Toko <span class="text-red-500">*</span></label>
                            <input type="text" name="store_name"
                                   value="{{ old('store_name', $settings['store_name'] ?? '') }}"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500/30 focus:border-green-500 outline-none transition"
                                   placeholder="Bharata Herbal" required>
                            @error('store_name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Deskripsi Toko</label>
                            <textarea name="store_description" rows="3"
                                      class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500/30 focus:border-green-500 outline-none transition resize-none"
                                      placeholder="Produk herbal alami berkualitas tinggi...">{{ old('store_description', $settings['store_description'] ?? '') }}</textarea>
                            @error('store_description')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Alamat Toko</label>
                            <textarea name="store_address" rows="2"
                                      class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500/30 focus:border-green-500 outline-none transition resize-none"
                                      placeholder="Jl. Nusantara No. 1, Jakarta">{{ old('store_address', $settings['store_address'] ?? '') }}</textarea>
                            @error('store_address')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- Contact Info --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-bold text-gray-800 mb-5 flex items-center gap-2">
                        <span class="w-7 h-7 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="phone" class="w-4 h-4 text-blue-600"></i>
                        </span>
                        Kontak & Media Sosial
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Nomor WhatsApp</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">📱</span>
                                <input type="text" name="whatsapp_number"
                                       value="{{ old('whatsapp_number', $settings['whatsapp_number'] ?? '') }}"
                                       class="w-full pl-9 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500/30 focus:border-green-500 outline-none transition"
                                       placeholder="+6281234567890">
                            </div>
                            @error('whatsapp_number')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Bisnis</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">✉️</span>
                                <input type="email" name="business_email"
                                       value="{{ old('business_email', $settings['business_email'] ?? '') }}"
                                       class="w-full pl-9 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500/30 focus:border-green-500 outline-none transition"
                                       placeholder="info@toko.id">
                            </div>
                            @error('business_email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Instagram</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">📸</span>
                                <input type="text" name="instagram"
                                       value="{{ old('instagram', $settings['instagram'] ?? '') }}"
                                       class="w-full pl-9 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-green-500/30 focus:border-green-500 outline-none transition"
                                       placeholder="@namatoko">
                            </div>
                            @error('instagram')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Right: Actions & Preview ────────────────────────── --}}
            <div class="space-y-4">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-24">
                    <h3 class="font-bold text-gray-800 mb-4">Aksi</h3>
                    <div class="space-y-3">
                        <button type="submit"
                                class="w-full bg-green-700 hover:bg-green-800 text-white font-semibold py-2.5 px-4 rounded-xl text-sm transition flex items-center justify-center gap-2">
                            <i data-lucide="save" class="w-4 h-4"></i> Simpan Perubahan
                        </button>
                        <button type="button" onclick="resetForm()"
                                class="w-full border border-gray-200 hover:bg-gray-50 text-gray-700 font-medium py-2.5 px-4 rounded-xl text-sm transition flex items-center justify-center gap-2">
                            <i data-lucide="rotate-ccw" class="w-4 h-4"></i> Reset
                        </button>
                    </div>

                    <div class="mt-5 pt-5 border-t border-gray-100">
                        <p class="text-xs text-gray-400 font-medium uppercase tracking-wider mb-3">Preview</p>
                        <div class="bg-green-50 rounded-xl p-4">
                            <p class="font-bold text-green-900 text-sm" id="preview-name">{{ $settings['store_name'] ?? 'Bharata Herbal' }}</p>
                            <p class="text-xs text-green-700 mt-1 line-clamp-2" id="preview-desc">{{ $settings['store_description'] ?? '' }}</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

<script>
    const originalValues = {};
    document.querySelectorAll('#store-form input,
        originalValues[el.name] = el.value;
    });

    function resetForm() {
        document.querySelectorAll('#store-form input,
            if (originalValues[el.name] !== undefined) el.value = originalValues[el.name];
        });
        updatePreview();
    }

    function updatePreview() {
        const name = document.querySelector('[name="store_name"]').value;
        const desc = document.querySelector('[name="store_description"]').value;
        document.getElementById('preview-name').textContent = name || 'Nama Toko';
        document.getElementById('preview-desc').textContent = desc || '';
    }

    document.querySelector('[name="store_name"]').addEventListener('input', updatePreview);
    document.querySelector('[name="store_description"]').addEventListener('input', updatePreview);
</script>

</x-layouts.admin>
