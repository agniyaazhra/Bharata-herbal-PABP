<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class CartController extends Controller
{
    public function index()
    {
        $cart = Auth::user()->cart()->with('items.product.categories')->first();
        $minimumOrderAmount = (int) Setting::get('shipping', 'minimum_order_amount', 0);
        return view('cart', compact('cart', 'minimumOrderAmount'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1|max:99',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->status === 'inactive' || $product->stock <= 0) {
            return back()->with('error', 'Produk ini sedang tidak tersedia.');
        }

        if ($product->stock < $request->quantity) {
            return back()->with('error', 'Stok produk tidak mencukupi.');
        }

        $cart = Auth::user()->cart()->firstOrCreate(['user_id' => Auth::id()]);

        $item = $cart->items()->where('product_id', $product->id)->first();

        if ($item) {
            $newQty = $item->quantity + $request->quantity;
            if ($newQty > $product->stock) {
                return back()->with('error', 'Total kuantitas melebihi stok yang tersedia.');
            }
            $item->update(['quantity' => $newQty]);
        } else {
            $cart->items()->create([
                'product_id'  => $product->id,
                'quantity'    => $request->quantity,
                'is_selected' => true,
            ]);
        }

        return back()->with('success', "Produk \"{$product->name}\" berhasil ditambahkan ke keranjang.");
    }

    public function update(Request $request, CartItem $cartItem)
    {
        $this->authorize('update', $cartItem);

        $request->validate([
            'quantity' => 'required|integer|min:1|max:99',
        ]);

        if ($request->quantity > $cartItem->product->stock) {
            return response()->json(['error' => 'Stok tidak mencukupi.'], 422);
        }

        $cartItem->update(['quantity' => $request->quantity]);

        $cart = $cartItem->cart->load('items.product');

        $subtotal      = $cartItem->quantity * $cartItem->product->effective_price;
        $selectedTotal = $cart->total;

  return response()->json([
    'success'        => true,
    'subtotal'       => 'Rp ' . number_format($subtotal, 0, ',', '.'),
    'total'          => 'Rp ' . number_format($selectedTotal, 0, ',', '.'),
    'total_amount'   => (int) $selectedTotal,
    'selected_count' => $cart->selected_count,
    'item_name'      => $cartItem->product->name,
]);
    }

    public function remove(CartItem $cartItem)
    {
        $this->authorize('delete', $cartItem);
        $cartItem->delete();
        return back()->with('success', 'Produk berhasil dihapus dari keranjang.');
    }

    public function toggleSelect(Request $request, CartItem $cartItem)
    {
        $this->authorize('update', $cartItem);

        $cartItem->update([
            'is_selected' => !$cartItem->is_selected
        ]);

        $cart = $cartItem->cart->load('items.product');
        $selectedTotal = (int) $cart->total;

        $items = $cart->items->map(function ($item) {
            return [
                'id'          => $item->id,
                'name'        => $item->product->name,
                'quantity'    => $item->quantity,
                'subtotal'    => $item->subtotal,
                'is_selected' => (bool) $item->is_selected,
            ];
        });

        return response()->json([
            'success'              => true,
            'items'                => $items,
            'selected_total'       => 'Rp ' . number_format($selectedTotal, 0, ',', '.'),
            'selected_total_amount' => $selectedTotal,
            'selected_count'       => $cart->selected_count,
        ]);
    }

    public function toggleSelectAll(Request $request)
    {
        $request->validate([
            'select_all' => 'required|boolean',
        ]);

        $cart = auth()->user()->cart()->with('items.product')->first();

        if (!$cart) {
            return response()->json(['error' => 'Keranjang tidak ditemukan.'], 404);
        }

        $cart->items()->update([
            'is_selected' => $request->boolean('select_all')
        ]);

        $cart->load('items.product');
        $selectedTotal = (int) $cart->total;

        $items = $cart->items->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->product->name,
                'quantity' => $item->quantity,
                'subtotal' => $item->subtotal,
                'is_selected' => $item->is_selected,
            ];
        });

        return response()->json([
            'success'              => true,
            'items'                => $items,
            'selected_total'       => 'Rp ' . number_format($selectedTotal, 0, ',', '.'),
            'selected_total_amount' => $selectedTotal,
            'selected_count'       => $cart->selected_count,
        ]);
    }

    public function clearAll()
    {
        $cart = auth()->user()->cart()->first();

        if (!$cart) {
            return back()->with('error', 'Keranjang tidak ditemukan.');
        }

        $cart->items()->delete();

        return back()->with('success', 'Semua item keranjang berhasil dihapus.');
    }
}
