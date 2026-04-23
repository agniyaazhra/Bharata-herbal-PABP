<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Services\ProductStockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function __construct(protected ProductStockService $stockService) {}

    public function index(Request $request)
    {
        $query = Product::with('categories');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->whereHas('categories', fn($q) => $q->where('categories.id', $request->category));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $products    = $query->latest()->paginate(15)->withQueryString();
        $categories  = Category::all();
        $stockSummary = $this->stockService->getStockSummary();

        return view('admin.products.index', compact('products', 'categories', 'stockSummary'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string',
            'usage'          => 'nullable|string',
            'benefits'       => 'nullable|string',
            'composition'    => 'nullable|string',
            'price'          => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'stock'          => 'required|integer|min:0',
            'image'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_featured'    => 'boolean',
            'is_bestseller'  => 'boolean',
            'categories'     => 'required|array|min:1',
            'categories.*'   => 'exists:categories,id',
        ]);

        $data['slug']         = Str::slug($data['name']);
        $data['is_featured']  = $request->boolean('is_featured');
        $data['is_bestseller']= $request->boolean('is_bestseller');
       
        $data['status']       = Product::resolveStatus($data['stock']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($data);
        $product->categories()->sync($request->categories);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        $product->load('categories');
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string',
            'usage'          => 'nullable|string',
            'benefits'       => 'nullable|string',
            'composition'    => 'nullable|string',
            'price'          => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0',
            'stock'          => 'required|integer|min:0',
            'image'          => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_featured'    => 'boolean',
            'is_bestseller'  => 'boolean',
            'categories'     => 'required|array|min:1',
            'categories.*'   => 'exists:categories,id',
        ]);

        $data['is_featured']  = $request->boolean('is_featured');
        $data['is_bestseller']= $request->boolean('is_bestseller');

        if ($request->hasFile('image')) {
            if ($product->image) Storage::disk('public')->delete($product->image);
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);
        $product->categories()->sync($request->categories);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        if ($product->image) Storage::disk('public')->delete($product->image);
        $product->delete();

        return back()->with('success', 'Produk berhasil dihapus.');
    }

    public function updateStock(Request $request, Product $product)
    {
        $request->validate([
            'stock' => 'required|integer|min:0',
        ]);

        $product = $this->stockService->updateStock($product, (int) $request->stock);

        return response()->json([
            'success' => true,
            'message' => 'Stok berhasil diperbarui.',
            'data'    => [
                'id'     => $product->id,
                'stock'  => $product->stock,
                'status' => $product->status,
            ],
        ]);
    }
}
