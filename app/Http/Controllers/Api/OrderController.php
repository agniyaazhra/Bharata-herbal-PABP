<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        $query = auth()->user()->orders()->with('items.product', 'address');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->orderByDesc('created_at')->paginate($request->get('per_page', 10));

        return $this->success([
            'data' => OrderResource::collection($orders),
            'pagination' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
                'from' => $orders->firstItem(),
                'to' => $orders->lastItem(),
            ],
        ]);
    }

    public function show(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            return $this->error('Unauthorized', 403);
        }

        $order->load('items.product', 'address', 'payment', 'reviews');

        return $this->success(new OrderResource($order));
    }

    public function cancel(Request $request, Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            return $this->error('Unauthorized', 403);
        }

        if (!$order->canBeCancelled()) {
            return $this->error('Pesanan ini tidak dapat dibatalkan.', 400);
        }

        $request->validate([
            'cancel_reason' => 'nullable|string|max:255',
        ]);

        $order->update([
            'status' => 'cancelled',
            'cancel_reason' => $request->cancel_reason,
        ]);

        return $this->success(new OrderResource($order->refresh()), 'Pesanan berhasil dibatalkan.');
    }

    public function payNow(Request $request, Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            return $this->error('Unauthorized', 403);
        }

        if ($order->status !== 'unpaid') {
            return $this->error('Pesanan ini tidak perlu pembayaran.', 400);
        }

        $order->load('payment');
        if (!$order->payment) {
            return $this->error('Data pembayaran tidak ditemukan.', 404);
        }

        return $this->success([
            'payment' => [
                'id' => $order->payment->id,
                'method' => $order->payment->method,
                'amount' => (int) $order->payment->amount,
                'status' => $order->payment->status,
                'bank_transfer_details' => $order->payment->method === 'bank_transfer' ? [
                    'account_number' => \App\Models\BankAccount::where('is_active', true)->first()?->account_number,
                    'account_holder' => \App\Models\BankAccount::where('is_active', true)->first()?->account_holder,
                    'amount' => (int) $order->payment->amount,
                ] : null,
            ],
        ]);
    }

    public function buyAgain(Request $request, Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            return $this->error('Unauthorized', 403);
        }

        $cart = auth()->user()->cart()->firstOrCreate(['user_id' => auth()->id()]);

        foreach ($order->items as $item) {
            $product = $item->product;

            if ($product->status === 'inactive' || $product->stock <= 0) {
                continue;
            }

            $existingItem = $cart->items()->where('product_id', $product->id)->first();

            if ($existingItem) {
                $newQty = $existingItem->quantity + $item->quantity;
                if ($newQty <= $product->stock) {
                    $existingItem->update(['quantity' => $newQty]);
                }
            } else {
                $cart->items()->create([
                    'product_id'  => $product->id,
                    'quantity'    => min($item->quantity, $product->stock),
                    'is_selected' => true,
                ]);
            }
        }

        $cart->load('items.product.categories');

        return $this->success([
            'cart' => new \App\Http\Resources\CartResource($cart),
            'message' => 'Produk dari pesanan berhasil ditambahkan ke keranjang.',
        ]);
    }
}
