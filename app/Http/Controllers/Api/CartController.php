<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        $cart = auth()->user()->cart()->with('items.product.categories')->first();

        if (!$cart) {
            return $this->success([
                'items' => [],
                'total' => 0,
                'selected_count' => 0,
                'total_items' => 0,
                'minimum_order_amount' => (int) \App\Models\Setting::get('shipping', 'minimum_order_amount', 0),
                'is_minimum_met' => false,
            ]);
        }

        return $this->success(new CartResource($cart));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1|max:99',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->status === 'inactive' || $product->stock <= 0) {
            return $this->error('Produk ini sedang tidak tersedia.', 400);
        }

        if ($product->stock < $request->quantity) {
            return $this->error('Stok produk tidak mencukupi.', 400);
        }

        $cart = auth()->user()->cart()->firstOrCreate(['user_id' => auth()->id()]);

        $item = $cart->items()->where('product_id', $product->id)->first();

        if ($item) {
            $newQty = $item->quantity + $request->quantity;
            if ($newQty > $product->stock) {
                return $this->error('Total kuantitas melebihi stok yang tersedia.', 400);
            }
            $item->update(['quantity' => $newQty]);
        } else {
            $cart->items()->create([
                'product_id'  => $product->id,
                'quantity'    => $request->quantity,
                'is_selected' => true,
            ]);
        }

        $cart->load('items.product.categories');

        return $this->success(new CartResource($cart), "Produk \"{$product->name}\" berhasil ditambahkan ke keranjang.", 201);
    }

    public function update(Request $request, CartItem $cartItem)
    {
        if ($cartItem->cart->user_id !== auth()->id()) {
            return $this->error('Unauthorized', 403);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1|max:99',
        ]);

        if ($request->quantity > $cartItem->product->stock) {
            return $this->error('Stok tidak mencukupi.', 400);
        }

        $cartItem->update(['quantity' => $request->quantity]);

        $cart = $cartItem->cart->load('items.product.categories');

        return $this->success(new CartResource($cart));
    }

    public function remove(CartItem $cartItem)
    {
        if ($cartItem->cart->user_id !== auth()->id()) {
            return $this->error('Unauthorized', 403);
        }

        $product_name = $cartItem->product->name;
        $cartItem->delete();

        $cart = $cartItem->cart->load('items.product.categories');

        return $this->success(new CartResource($cart), "Produk \"{$product_name}\" berhasil dihapus dari keranjang.");
    }

    public function toggleSelect(Request $request, CartItem $cartItem)
    {
        if ($cartItem->cart->user_id !== auth()->id()) {
            return $this->error('Unauthorized', 403);
        }

        $cartItem->update([
            'is_selected' => !$cartItem->is_selected,
        ]);

        $cart = $cartItem->cart->load('items.product.categories');

        return $this->success(new CartResource($cart));
    }

    public function toggleSelectAll(Request $request)
    {
        $request->validate([
            'select_all' => 'required|boolean',
        ]);

        $cart = auth()->user()->cart()->first();
        if ($cart) {
            $cart->items()->update(['is_selected' => $request->select_all]);
            $cart->load('items.product.categories');
            return $this->success(new CartResource($cart));
        }

        return $this->success([
            'items' => [],
            'total' => 0,
            'selected_count' => 0,
            'total_items' => 0,
            'minimum_order_amount' => (int) \App\Models\Setting::get('shipping', 'minimum_order_amount', 0),
            'is_minimum_met' => false,
        ]);
    }

    public function clearAll(Request $request)
    {
        $cart = auth()->user()->cart()->first();
        if ($cart) {
            $cart->items()->delete();
        }

        return $this->success(null, 'Keranjang berhasil dikosongkan.');
    }
}
