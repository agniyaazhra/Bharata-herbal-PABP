<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::withCount([
            'products as products_count' => fn($query) => $query->availableForSale(),
        ])->get();
        $query = Product::with('categories')->availableForSale();

        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        match ($request->sort) {
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'rating'     => $query->orderBy('rating', 'desc'),
            default      => $query->latest(),
        };

        $products = $query->paginate(12)->withQueryString();

        $selectedCategory = null;
        if ($request->filled('category')) {
            $selectedCategory = Category::where('slug', $request->category)->first();
        }

        return view('shop', compact('products', 'categories', 'selectedCategory'));
    }

    public function show(Product $product)
    {
        $product->load('categories', 'reviews.user');

        $relatedProducts = Product::with('categories')
            ->whereHas('categories', function ($q) use ($product) {
                $q->whereIn('categories.id', $product->categories->pluck('id'));
            })
            ->where('id', '!=', $product->id)
            ->availableForSale()
            ->take(4)
            ->get();

        return view('product-detail', compact('product', 'relatedProducts'));
    }
}
