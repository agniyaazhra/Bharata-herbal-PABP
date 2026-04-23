<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        $query = Product::with('categories')->availableForSale();

        if ($request->filled('category')) {
            $query->whereHas('categories', fn($q) => $q->where('slug', $request->category));
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        match ($request->sort) {
            'price_asc'  => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'rating'     => $query->orderBy('rating', 'desc'),
            default      => $query->latest(),
        };

        $products = $query->paginate($request->get('per_page', 12));

        return $this->success(new ProductCollection($products));
    }

    public function show(string $slug)
    {
        $product = Product::with('categories', 'reviews.user')
            ->where('slug', $slug)
            ->availableForSale()
            ->firstOrFail();

        return $this->success(new ProductResource($product));
    }

    public function categories()
    {
        $categories = Category::withCount([
            'products as products_count' => fn($q) => $q->availableForSale(),
        ])->get();

        return $this->success(CategoryResource::collection($categories));
    }
}
