<x-layouts.admin>
<x-slot name="title">Manajemen Kategori</x-slot>
<x-slot name="subtitle">Kelola kategori produk herbal Anda</x-slot>

{{-- ═══════════════════════════════════════
  ACTION BAR
═══════════════════════════════════════ --}}
<div class="flex items-center justify-between mb-6">

    {{-- Tab Navigation --}}
    <div class="flex items-center gap-1 bg-white border border-gray-200 rounded-xl p-1">
        <button onclick="showTab('kategori')" id="tab-kategori"
                class="tab-btn px-4 py-2 rounded-lg text-sm font-semibold transition-all
                       bg-green-900 text-white shadow-sm">
            <span class="flex items-center gap-2">
                <i data-lucide="tag" class="w-4 h-4"></i>
                Kategori
            </span>
        </button>
        <a href="{{ route('admin.products.index') }}"
           class="px-4 py-2 rounded-lg text-sm font-semibold text-gray-500 hover:text-gray-700
                  hover:bg-gray-100 transition-all flex items-center gap-2">
            <i data-lucide="package" class="w-4 h-4"></i>
            Daftar Produk
        </a>
    </div>

    {{-- Add Button --}}
    <button onclick="openModal()"
            class="flex items-center gap-2 px-5 py-2.5 bg-green-900 hover:bg-green-800
                   text-white text-sm font-semibold rounded-xl shadow-sm
                   transition-all hover:shadow-md active:scale-95">
        <i data-lucide="plus" class="w-4 h-4"></i>
        Tambah Kategori
    </button>
</div>

{{-- ═══════════════════════════════════════
  STATS ROW
═══════════════════════════════════════ --}}
<div class="grid grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-3">
        <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center shrink-0">
            <i data-lucide="layers" class="w-5 h-5 text-green-700"></i>
        </div>
        <div>
            <p class="text-xs text-gray-400 font-medium">Total Kategori</p>
            <p class="text-xl font-extrabold text-gray-900">{{ $categories->count() }}</p>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-3">
        <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center shrink-0">
            <i data-lucide="package" class="w-5 h-5 text-blue-600"></i>
        </div>
        <div>
            <p class="text-xs text-gray-400 font-medium">Total Produk</p>
            <p class="text-xl font-extrabold text-gray-900">{{ $categories->sum('products_count') }}</p>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-3">
        <div class="w-10 h-10 bg-amber-50 rounded-lg flex items-center justify-center shrink-0">
            <i data-lucide="bar-chart-2" class="w-5 h-5 text-amber-600"></i>
        </div>
        <div>
            <p class="text-xs text-gray-400 font-medium">Rata-rata Produk</p>
            <p class="text-xl font-extrabold text-gray-900">
                {{ $categories->count() > 0 ? number_format($categories->avg('products_count'), 1) : 0 }}
            </p>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════
  CATEGORY GRID
═══════════════════════════════════════ --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
    @forelse ($categories as $category)
    @php
        $iconBgs = [
            'bg-green-100 text-green-700',
            'bg-blue-100 text-blue-700',
            'bg-purple-100 text-purple-700',
            'bg-amber-100 text-amber-700',
            'bg-rose-100 text-rose-700',
            'bg-teal-100 text-teal-700',
            'bg-indigo-100 text-indigo-700',
            'bg-orange-100 text-orange-700',
        ];
        $colorClass = $iconBgs[$category->id % count($iconBgs)];
    @endphp

    <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-5 py-4
                hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group
                flex items-center gap-4"
         x-data="{ editMode: false }">

        {{-- Icon --}}
        <div class="w-12 h-12 rounded-xl {{ $colorClass }} flex items-center justify-center shrink-0 text-xl">
            {{ $category->icon ?: '🌿' }}
        </div>

        {{-- Info --}}
        <div class="flex-1 min-w-0">
            <template x-if="!editMode">
                <div>
                    <p class="font-bold text-gray-900 truncate">{{ $category->name }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        <span class="font-semibold text-green-700">{{ $category->products_count }}</span> produk
                        · <span class="font-mono text-gray-300">{{ $category->slug }}</span>
                    </p>
                </div>
            </template>

            {{-- Inline Edit Form --}}
            <template x-if="editMode">
                <form action="{{ route('admin.categories.update', $category) }}" method="POST"
                      class="flex flex-col gap-1.5">
                    @csrf @method('PUT')
                    <input type="text" name="name" value="{{ $category->name }}"
                           class="text-sm font-semibold border border-gray-200 rounded-lg px-2.5 py-1.5
                                  focus:outline-none focus:ring-2 focus:ring-green-500/30 w-full bg-gray-50">
                    <div class="flex items-center gap-1.5">
                        <input type="text" name="icon" value="{{ $category->icon }}"
                               class="text-sm border border-gray-200 rounded-lg px-2.5 py-1.5
                                      focus:outline-none focus:ring-2 focus:ring-green-500/30 w-16 bg-gray-50"
                               placeholder="🌿">
                        <button type="submit"
                                class="flex-1 bg-green-900 text-white text-xs font-semibold
                                       py-1.5 rounded-lg hover:bg-green-800 transition-colors">
                            Simpan
                        </button>
                        <button type="button" @click="editMode = false"
                                class="text-xs text-gray-400 hover:text-gray-600 px-2 py-1.5 rounded-lg
                                       border border-gray-200 hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                    </div>
                </form>
            </template>
        </div>

        {{-- Actions --}}
        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity shrink-0"
             x-show="!editMode">
            <button @click="editMode = true"
                    title="Edit"
                    class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-blue-100 flex items-center justify-center
                           transition-colors">
                <i data-lucide="pencil" class="w-3.5 h-3.5 text-gray-500 hover:text-blue-600"></i>
            </button>

            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                  onsubmit="return confirm('Hapus kategori \'{{ addslashes($category->name) }}\'?\nProduk dalam kategori ini tidak akan terhapus.')">
                @csrf @method('DELETE')
                <button type="submit"
                        title="Hapus"
                        class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-red-100 flex items-center justify-center
                               transition-colors">
                    <i data-lucide="trash" class="w-3.5 h-3.5 text-gray-500 hover:text-red-600"></i>
                </button>
            </form>
        </div>
    </div>

    @empty
    <div class="col-span-3">
        <div class="bg-white rounded-2xl border border-gray-100 p-16 text-center">
            <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i data-lucide="tag" class="w-8 h-8 text-gray-300"></i>
            </div>
            <p class="font-semibold text-gray-500">Belum ada kategori</p>
            <p class="text-sm text-gray-400 mt-1">Klik "+ Tambah Kategori" untuk memulai</p>
        </div>
    </div>
    @endforelse
</div>

{{-- ═══════════════════════════════════════
  MODAL: TAMBAH KATEGORI
═══════════════════════════════════════ --}}
<div id="addModal"
     class="fixed inset-0 z-50 flex items-center justify-center hidden"
     x-data>

    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" onclick="closeModal()"></div>

    {{-- Modal Card --}}
    <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 p-6 z-10
                animate-fade-in">

        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Tambah Kategori Baru</h3>
                <p class="text-sm text-gray-400 mt-0.5">Isi data kategori produk</p>
            </div>
            <button onclick="closeModal()"
                    class="w-9 h-9 rounded-xl bg-gray-100 hover:bg-gray-200 flex items-center justify-center transition-colors">
                <i data-lucide="x" class="w-4 h-4 text-gray-500"></i>
            </button>
        </div>

        <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Nama Kategori <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm
                              focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500
                              bg-gray-50 transition"
                       placeholder="cth: Imunitas & Daya Tahan Tubuh">
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Deskripsi
                </label>
                <textarea name="description" rows="3"
                          class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm
                                 focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500
                                 bg-gray-50 resize-none transition"
                          placeholder="Deskripsi singkat kategori ini...">{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Ikon (emoji)
                </label>
                <div class="flex items-center gap-3">
                    <input type="text" name="icon" value="{{ old('icon') }}" maxlength="10"
                           id="iconInput"
                           class="w-24 border border-gray-200 rounded-xl px-4 py-2.5 text-lg
                                  focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500
                                  bg-gray-50 text-center transition"
                           placeholder="🌿">
                    <div class="flex gap-2 flex-wrap">
                        @foreach (['🌿','🫁','💧','💪','🛡️','⚡','🌱','🍃','🔬','💊','🫀','🧠'] as $emoji)
                        <button type="button" onclick="setIcon('{{ $emoji }}')"
                                class="w-9 h-9 rounded-lg border border-gray-200 hover:border-green-400
                                       hover:bg-green-50 flex items-center justify-center text-lg
                                       transition-all">
                            {{ $emoji }}
                        </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal()"
                        class="flex-1 border border-gray-200 text-gray-600 font-semibold text-sm
                               py-2.5 rounded-xl hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="flex-1 bg-green-900 hover:bg-green-800 text-white font-semibold text-sm
                               py-2.5 rounded-xl transition-colors shadow-sm flex items-center justify-center gap-2">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Tambah Kategori
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openModal() {
        document.getElementById('addModal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeModal() {
        document.getElementById('addModal').classList.add('hidden');
        document.body.style.overflow = '';
    }
    function setIcon(emoji) {
        document.getElementById('iconInput').value = emoji;
    }
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeModal();
    });

    @if ($errors->any())
        openModal();
    @endif
</script>
@endpush

</x-layouts.admin>
