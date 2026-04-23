<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AddressResource;
use App\Http\Resources\CartResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Setting;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    use ApiResponseTrait;

    public function index(Request $request)
    {
        $cart = auth()->user()->cart()->with('items.product')->first();

        if (!$cart || $cart->items()->where('is_selected', true)->count() === 0) {
            return $this->error('Keranjang kosong atau tidak ada item yang dipilih.', 400);
        }

        $addresses = auth()->user()->addresses()->get();
        $defaultAddress = auth()->user()->defaultAddress()->first();
        $minimumOrderAmount = (int) Setting::get('shipping', 'minimum_order_amount', 0);
        $shippingCost = (int) Setting::get('shipping', 'cost', 0);

        $selectedTotal = $cart->items
            ->where('is_selected', true)
            ->sum(function ($item) {
                return $item->quantity * $item->product->effective_price;
            });

        $paymentMethods = \App\Models\Setting::get('payment', 'methods', []) ?? [];
        $bankAccounts = \App\Models\BankAccount::where('is_active', true)->get(['id', 'bank_name', 'account_number', 'account_holder', 'notes']);

        return $this->success([
            'cart' => new CartResource($cart),
            'selected_items' => $cart->items()->where('is_selected', true)->with('product')->get()->map(fn($item) => [
                'product_id' => $item->product_id,
                'product_name' => $item->product->name,
                'quantity' => $item->quantity,
                'unit_price' => (int) $item->product->effective_price,
                'subtotal' => (int) ($item->quantity * $item->product->effective_price),
            ]),
            'addresses' => AddressResource::collection($addresses),
            'default_address' => $defaultAddress ? new AddressResource($defaultAddress) : null,
            'payment_methods' => $paymentMethods,
            'bank_accounts' => $bankAccounts,
            'subtotal' => (int) $selectedTotal,
            'shipping_cost' => $shippingCost,
            'total' => (int) ($selectedTotal + $shippingCost),
            'minimum_order_amount' => $minimumOrderAmount,
            'is_minimum_met' => $selectedTotal >= $minimumOrderAmount,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'address_id'      => 'required|exists:addresses,id',
            'payment_method'  => 'required|in:transfer,cash_on_delivery',
            'notes'           => 'nullable|string|max:255',
        ]);

        $user = auth()->user();
        $cart = $user->cart()->with('items.product')->first();

        if (!$cart || $cart->items()->where('is_selected', true)->count() === 0) {
            return $this->error('Keranjang kosong atau tidak ada item yang dipilih.', 400);
        }

        // Verify address belongs to user
        $address = $user->addresses()->findOrFail($request->address_id);

        // Calculate totals
        $subtotal = $cart->items
            ->where('is_selected', true)
            ->sum(function ($item) {
                return $item->quantity * $item->product->effective_price;
            });

        $minimumOrderAmount = (int) Setting::get('shipping', 'minimum_order_amount', 0);

        if ($subtotal < $minimumOrderAmount) {
            return $this->error("Minimum pembelian Rp " . number_format($minimumOrderAmount, 0, ',', '.') . " belum tercapai.", 400);
        }

        $shippingCost = (int) Setting::get('shipping', 'cost', 0);
        $total = $subtotal + $shippingCost;

        // Create order
        $order = $user->orders()->create([
            'address_id'    => $address->id,
            'subtotal'      => $subtotal,
            'shipping_cost' => $shippingCost,
            'total_price'   => $total,
            'status'        => $request->payment_method === 'cash_on_delivery' ? 'pending' : 'unpaid',
            'notes'         => $request->notes,
        ]);

        // Create order items
        foreach ($cart->items()->where('is_selected', true)->get() as $item) {
            $order->items()->create([
                'product_id' => $item->product_id,
                'quantity'   => $item->quantity,
                'unit_price' => $item->product->effective_price,
            ]);
        }

        // Create payment record
        if ($request->payment_method === 'transfer') {
            $order->payment()->create([
                'method'        => 'bank_transfer',
                'status'        => 'pending',
                'amount'        => $total,
            ]);

            // Set payment deadline (e.g., 1 day from now)
            $order->update(['payment_deadline' => now()->addDay()]);
        }

        // Clear selected items from cart
        $cart->items()->where('is_selected', true)->delete();

        return $this->success([
            'order_id' => $order->id,
            'order_number' => 'ORD-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
            'total_price' => (int) $total,
            'status' => $order->status,
        ], 'Pesanan berhasil dibuat.', 201);
    }
}
