<x-layouts.admin>
    <x-slot name="title">{{ isset($product) ? 'Edit Produk' : 'Tambah Produk' }}</x-slot>

    <div class="max-w-3xl">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('admin.products.index') }}" class="text-gray-400 hover:text-gray-700">← Kembali</a>
            <span class="text-gray-300">/</span>
            <h2 class="font-bold text-gray-800">{{ isset($product) ? 'Edit Produk' : 'Produk Baru' }}</h2>
        </div>

        <form action="{{ isset($product) ? route('admin.products.update', $product) : route('admin.products.store') }}"
              method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @if (isset($product)) @method('PUT') @endif

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-5">
                <h3 class="font-semibold text-gray-700 border-b border-gray-100 pb-3">Informasi Dasar</h3>

                <div>
                    <label class="form-label">Nama Produk <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $product->name ?? '') }}" class="form-input" required>
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="form-label">Harga (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" name="price" value="{{ old('price', $product->price ?? '') }}" class="form-input" required min="0">
                        @error('price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Harga Diskon (Rp)</label>
                        <input type="number" name="discount_price" value="{{ old('discount_price', $product->discount_price ?? '') }}" class="form-input" min="0">
                        @error('discount_price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Stok <span class="text-red-500">*</span></label>
                        <input type="number" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" class="form-input" required min="0">
                        @error('stock')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="form-label">Kategori <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        @foreach ($categories as $cat)
                        <label class="flex items-center gap-2 cursor-pointer p-2.5 rounded-lg border border-gray-200 hover:border-herbal-400 transition-colors">
                            <input type="checkbox" name="categories[]" value="{{ $cat->id }}"
                                   class="rounded text-herbal-600 focus:ring-herbal-500"
                                   {{ in_array($cat->id, old('categories', isset($product) ? $product->categories->pluck('id')->toArray() : [])) ? 'checked' : '' }}>
                            <span class="text-sm text-gray-700">{{ $cat->name }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('categories')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="form-label">Gambar Produk</label>
                    @if (isset($product) && $product->image)
                        <div class="mb-3">
                            <img src="{{ Storage::url($product->image) }}" alt="Current" class="w-32 h-32 object-cover rounded-xl border border-gray-200">
                            <p class="text-xs text-gray-400 mt-1">Gambar saat ini. Upload baru untuk mengganti.</p>
                        </div>
                    @endif
                    <input type="file" name="image" accept="image/*" class="form-input py-2">
                    @error('image')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="flex gap-6">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_featured" value="1" class="rounded text-herbal-600 focus:ring-herbal-500"
                               {{ old('is_featured', $product->is_featured ?? false) ? 'checked' : '' }}>
                        <span class="text-sm font-medium text-gray-700">Produk Unggulan</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_bestseller" value="1" class="rounded text-herbal-600 focus:ring-herbal-500"
                               {{ old('is_bestseller', $product->is_bestseller ?? false) ? 'checked' : '' }}>
                        <span class="text-sm font-medium text-gray-700">Produk Terlaris</span>
                    </label>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-5">
                <h3 class="font-semibold text-gray-700 border-b border-gray-100 pb-3">Deskripsi & Detail</h3>
                @foreach (['description' => 'Deskripsi Produk', 'benefits' => 'Manfaat', 'usage' => 'Cara Penggunaan', 'composition' => 'Komposisi'] as $field => $label)
                <div>
                    <label class="form-label">{{ $label }}</label>
                    <textarea name="{{ $field }}" rows="4" class="form-input resize-none">{{ old($field, $product->{$field} ?? '') }}</textarea>
                </div>
                @endforeach
            </div>

            <div class="flex gap-3">
                <button type="submit" class="btn-primary">
                    {{ isset($product) ? 'Simpan Perubahan' : 'Tambah Produk' }}
                </button>
                <a href="{{ route('admin.products.index') }}" class="btn-secondary">Batal</a>
            </div>
        </form>
    </div>
</x-layouts.admin>
