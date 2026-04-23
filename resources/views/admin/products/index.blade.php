<x-layouts.admin>
<x-slot name="title">Manajemen Produk</x-slot>
<x-slot name="subtitle">Kelola seluruh produk herbal yang tersedia di toko</x-slot>

{{-- ═══════════════════════════════════════
  SEARCH + STATS ROW
═══════════════════════════════════════ --}}
<div class="flex flex-col sm:flex-row gap-4 mb-6">

    {{-- Search --}}
    <form method="GET" action="{{ route('admin.products.index') }}" class="flex-1">
        <div class="relative">
            <i data-lucide="search" class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none"></i>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari produk berdasarkan nama..."
                   class="w-full pl-12 pr-4 py-3 rounded-xl border border-gray-200 bg-white text-sm
                          focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500
                          shadow-sm transition">
        </div>
    </form>

    {{-- Filter Kategori --}}
    <form method="GET" action="{{ route('admin.products.index') }}" id="filterForm">
        @if(request('search'))
            <input type="hidden" name="search" value="{{ request('search') }}">
        @endif
        @if(request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
        @endif
        <select name="category" onchange="document.getElementById('filterForm').submit()"
                class="h-full px-4 py-3 rounded-xl border border-gray-200 bg-white text-sm text-gray-600
                       focus:outline-none focus:ring-2 focus:ring-green-500/30 shadow-sm transition">
            <option value="">Semua Kategori</option>
            @foreach(\App\Models\Category::orderBy('name')->get() as $cat)
                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
            @endforeach
        </select>
    </form>

    {{-- Filter Status --}}
    <form method="GET" action="{{ route('admin.products.index') }}" id="statusFilterForm">
        @if(request('search'))   <input type="hidden" name="search"   value="{{ request('search') }}"> @endif
        @if(request('category')) <input type="hidden" name="category" value="{{ request('category') }}"> @endif
        <select name="status" onchange="document.getElementById('statusFilterForm').submit()"
                class="h-full px-4 py-3 rounded-xl border border-gray-200 bg-white text-sm text-gray-600
                       focus:outline-none focus:ring-2 focus:ring-green-500/30 shadow-sm transition">
            <option value="">Semua Status</option>
            <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>✅ Aktif</option>
            <option value="warning"  {{ request('status') === 'warning'  ? 'selected' : '' }}>⚠️ Peringatan</option>
            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>🔴 Nonaktif</option>
        </select>
    </form>

    {{-- Add Button --}}
    <a href="{{ route('admin.products.create') }}"
       class="flex items-center gap-2 px-5 py-3 bg-green-900 hover:bg-green-800 text-white
              text-sm font-semibold rounded-xl shadow-sm transition-all hover:shadow-md
              active:scale-95 whitespace-nowrap">
        <i data-lucide="plus" class="w-4 h-4"></i>
        Tambah Produk
    </a>
</div>

{{-- ═══════════════════════════════════════
  STAT CARDS
═══════════════════════════════════════ --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    @foreach ([
        ['label' => 'Total Produk',     'value' => number_format($stockSummary['total']),    'icon' => 'package',       'bg' => 'bg-green-50',  'color' => 'text-green-700'],
        ['label' => 'Produk Aktif',     'value' => number_format($stockSummary['active']),   'icon' => 'check-circle',  'bg' => 'bg-teal-50',   'color' => 'text-teal-600'],
        ['label' => 'Peringatan Stok',  'value' => number_format($stockSummary['warning']),  'icon' => 'alert-triangle','bg' => 'bg-yellow-50', 'color' => 'text-yellow-600'],
        ['label' => 'Stok Habis',       'value' => number_format($stockSummary['inactive']), 'icon' => 'package-x',     'bg' => 'bg-red-50',    'color' => 'text-red-600'],
    ] as $s)
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-3">
        <div class="w-10 h-10 {{ $s['bg'] }} rounded-lg flex items-center justify-center shrink-0">
            <i data-lucide="{{ $s['icon'] }}" class="w-5 h-5 {{ $s['color'] }}"></i>
        </div>
        <div>
            <p class="text-xs text-gray-400 font-medium leading-tight">{{ $s['label'] }}</p>
            <p class="text-xl font-extrabold text-gray-900 leading-tight">{{ $s['value'] }}</p>
        </div>
    </div>
    @endforeach
</div>

{{-- ═══════════════════════════════════════
  TABLE
═══════════════════════════════════════ --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

    {{-- Table Header --}}
    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
        <div>
            <h2 class="font-bold text-gray-900">Daftar Produk</h2>
            <p class="text-xs text-gray-400 mt-0.5">
                Menampilkan {{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }}
                dari {{ $products->total() }} produk
                @if(request('search'))
                    · hasil pencarian "<span class="font-semibold text-green-700">{{ request('search') }}</span>"
                @endif
            </p>
        </div>
        @if(request('search') || request('category'))
        <a href="{{ route('admin.products.index') }}"
           class="text-xs text-gray-400 hover:text-gray-600 border border-gray-200 px-3 py-1.5
                  rounded-lg hover:bg-gray-50 transition-colors flex items-center gap-1">
            <i data-lucide="x" class="w-3 h-3"></i> Reset filter
        </a>
        @endif
    </div>

    {{-- Scrollable Table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="text-left px-6 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="text-left px-6 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Produk</th>
                    <th class="text-left px-6 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Kategori</th>
                    <th class="text-left px-6 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Stok</th>
                    <th class="text-left px-6 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Harga</th>
                    <th class="text-left px-6 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse ($products as $product)
                <tr class="hover:bg-gray-50/70 transition-colors group">

                    {{-- ID --}}
                    <td class="px-6 py-4">
                        <span class="font-mono text-xs text-gray-400">
                           
                        </span>
                    </td>

                    {{-- Produk --}}
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            {{-- Thumbnail --}}
                            @if($product->image)
                                <img src="{{ Storage::url($product->image) }}"
                                     alt="{{ $product->name }}"
                                     class="w-11 h-11 rounded-xl object-cover border border-gray-100 shrink-0">
                            @else
                                <div class="w-11 h-11 rounded-xl bg-green-50 border border-green-100
                                            flex items-center justify-center shrink-0">
                                    <i data-lucide="leaf" class="w-5 h-5 text-green-400"></i>
                                </div>
                            @endif
                            {{-- Name --}}
                            <div class="min-w-0">
                                <p class="font-semibold text-gray-900 truncate max-w-[200px]">
                                    {{ $product->name }}
                                </p>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    @if($product->is_featured)
                                    <span class="inline-flex items-center gap-1 text-xs text-amber-600 font-medium">
                                        <i data-lucide="star" class="w-3 h-3"></i>Unggulan
                                    </span>
                                    @endif
                                    @if($product->is_bestseller)
                                    <span class="inline-flex items-center gap-1 text-xs text-green-600 font-medium">
                                        <i data-lucide="trending-up" class="w-3 h-3"></i>Terlaris
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </td>

                    {{-- Kategori --}}
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-1 max-w-[160px]">
                            @forelse($product->categories as $cat)
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium
                                             bg-green-100 text-green-800">
                                    {{ $cat->name }}
                                </span>
                            @empty
                                <span class="text-gray-300 text-xs">—</span>
                            @endforelse
                        </div>
                    </td>

                    {{-- Stok --}}
                    <td class="px-6 py-4">
                        @if($product->stock > 50)
                            <div class="flex items-center gap-1.5">
                                <div class="w-1.5 h-1.5 rounded-full bg-green-500"></div>
                                <span class="font-semibold text-gray-800">{{ number_format($product->stock) }}</span>
                                <span class="text-xs text-gray-400">pcs</span>
                            </div>
                        @elseif($product->stock > 0)
                            <div class="flex items-center gap-1.5">
                                <div class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></div>
                                <span class="font-semibold text-amber-700">{{ number_format($product->stock) }}</span>
                                <span class="text-xs text-amber-500">pcs</span>
                            </div>
                        @else
                            <div class="flex items-center gap-1.5">
                                <div class="w-1.5 h-1.5 rounded-full bg-red-500"></div>
                                <span class="font-semibold text-red-600">Habis</span>
                            </div>
                        @endif
                    </td>

                    {{-- Harga --}}
                    <td class="px-6 py-4">
                        @if($product->discount_price)
                            <p class="font-bold text-gray-900">
                                Rp {{ number_format($product->discount_price, 0, ',', '.') }}
                            </p>
                            <p class="text-xs text-gray-400 line-through">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </p>
                        @else
                            <p class="font-bold text-gray-900">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </p>
                        @endif
                    </td>

                    {{-- Status --}}
                    <td class="px-6 py-4">
                        @if($product->status === 'active')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full inline-block"></span> Aktif
                            </span>
                        @elseif($product->status === 'warning')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                <span class="w-1.5 h-1.5 bg-yellow-500 rounded-full inline-block animate-pulse"></span> Peringatan Stok
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700">
                                <span class="w-1.5 h-1.5 bg-red-500 rounded-full inline-block"></span> Nonaktif
                            </span>
                        @endif
                    </td>

                    {{-- Aksi --}}
                    <td class="px-6 py-4">
                        <div class="flex items-center justify-center gap-1">
                            <a href="{{ route('admin.products.edit', $product) }}"
                               title="Edit Produk"
                               class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-blue-100 flex items-center justify-center
                                      transition-all hover:scale-105">
                                <i data-lucide="pencil" class="w-3.5 h-3.5 text-gray-500 group-hover:text-blue-600"></i>
                            </a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                  onsubmit="return confirm('Hapus produk \'{{ addslashes($product->name) }}\'?\nTindakan ini tidak dapat dibatalkan.')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        title="Hapus Produk"
                                        class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-red-100 flex items-center justify-center
                                               transition-all hover:scale-105">
                                    <i data-lucide="trash" class="w-3.5 h-3.5 text-gray-500 hover:text-red-600"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-20 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center">
                                <i data-lucide="package" class="w-8 h-8 text-gray-300"></i>
                            </div>
                            <p class="font-semibold text-gray-500">Tidak ada produk ditemukan</p>
                            @if(request('search'))
                                <p class="text-sm text-gray-400">
                                    Tidak ada hasil untuk "<span class="font-medium">{{ request('search') }}</span>"
                                </p>
                                <a href="{{ route('admin.products.index') }}"
                                   class="mt-1 text-sm text-green-700 hover:underline">Hapus filter</a>
                            @else
                                <a href="{{ route('admin.products.create') }}"
                                   class="mt-1 flex items-center gap-2 px-4 py-2 bg-green-900 text-white text-sm
                                          font-semibold rounded-xl hover:bg-green-800 transition-colors">
                                    <i data-lucide="plus" class="w-4 h-4"></i>
                                    Tambah Produk Pertama
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($products->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
        <p class="text-sm text-gray-500">
            Halaman <span class="font-semibold text-gray-800">{{ $products->currentPage() }}</span>
            dari <span class="font-semibold text-gray-800">{{ $products->lastPage() }}</span>
        </p>
        <div class="[&_nav]:flex [&_nav]:items-center [&_nav]:gap-1
                    [&_.page-link]:flex [&_.page-link]:items-center [&_.page-link]:justify-center
                    [&_.page-link]:min-w-[2rem] [&_.page-link]:h-8 [&_.page-link]:px-3
                    [&_.page-link]:rounded-lg [&_.page-link]:text-xs [&_.page-link]:font-semibold
                    [&_.page-link]:border [&_.page-link]:border-gray-200 [&_.page-link]:text-gray-600
                    [&_.page-link:hover]:bg-gray-100 [&_.page-link]:transition-colors
                    [&_.page-item.active_.page-link]:bg-green-900 [&_.page-item.active_.page-link]:text-white
                    [&_.page-item.active_.page-link]:border-green-900
                    [&_.page-item.disabled_.page-link]:opacity-40">
            {{ $products->withQueryString()->links() }}
        </div>
    </div>
    @endif
</div>

</x-layouts.admin>
