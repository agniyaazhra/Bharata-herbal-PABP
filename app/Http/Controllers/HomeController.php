<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use App\Models\Setting;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProducts = Product::with('categories')
            ->featured()
            ->availableForSale()
            ->take(8)
            ->get();

        $bestsellerProducts = Product::with('categories')
            ->bestseller()
            ->availableForSale()
            ->take(8)
            ->get();

        $categories = Category::withCount([
            'products as products_count' => fn($query) => $query->availableForSale(),
        ])->get();

        $testimonials = Review::with('user')
            ->where('is_featured', true)
            ->latest()
            ->take(6)
            ->get();

        return view('home', compact(
            'featuredProducts',
            'bestsellerProducts',
            'categories',
            'testimonials'
        ));
    }

    public function about()
    {
        $storeSettings = Setting::getGroup('store');
        return view('about', compact('storeSettings'));
    }

    public function contact()
    {
        $storeSettings = Setting::getGroup('store');
        return view('contact', compact('storeSettings'));
    }
}
