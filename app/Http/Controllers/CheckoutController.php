<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\BankAccount;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Setting;
use App\Services\OrderEventNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = auth()->user()->cart()->with('items.product')->first();

        if (!$cart || $cart->items->where('is_selected', true)->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Pilih minimal satu produk untuk checkout.');
        }

        $selectedItems  = $cart->items->where('is_selected', true);
        $addresses      = auth()->user()->addresses()->get();
        $defaultAddress = auth()->user()->defaultAddress;
        $shippingPreview = $this->resolveShippingPreview($selectedItems, $defaultAddress);
        $minimumOrderAmount = (int) Setting::get('shipping', 'minimum_order_amount', 0);
        $selectedSubtotal = (int) $selectedItems->sum(fn($i) => $i->quantity * $i->product->effective_price);

        $activeMethods = $this->activePaymentMethods();
        $bankAccounts = BankAccount::query()
            ->where('is_active', true)
            ->orderBy('bank_name')
            ->get();

        return view('checkout', compact(
            'cart',
            'selectedItems',
            'addresses',
            'defaultAddress',
            'activeMethods',
            'bankAccounts',
            'shippingPreview',
            'minimumOrderAmount',
            'selectedSubtotal'
        ));
    }

    public function store(Request $request)
    {
        $activeMethods = $this->activePaymentMethods();

        $request->validate([
            'address_id'     => 'required|exists:addresses,id',
            'payment_method' => ['required', 'in:' . implode(',', $activeMethods)],
            'payment_action' => 'nullable|in:pay_now,pay_later',
        ]);

        $cart = auth()->user()->cart()->with('items.product')->first();

        if (!$cart || $cart->items->where('is_selected', true)->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Pilih minimal satu produk untuk checkout.');
        }

        $selectedItems = $cart->items->where('is_selected', true);
        $subtotal = $selectedItems->sum(fn($i) => $i->quantity * $i->product->effective_price);
        $minimumOrderAmount = (int) Setting::get('shipping', 'minimum_order_amount', 0);

        if ($minimumOrderAmount > 0 && $subtotal < $minimumOrderAmount) {
            return back()->with('error', 'Minimum checkout adalah Rp ' . number_format($minimumOrderAmount, 0, ',', '.') . '.');
        }

        foreach ($selectedItems as $item) {
            if ($item->product->stock < $item->quantity) {
                return back()->with('error', "Stok \"{$item->product->name}\" tidak mencukupi.");
            }

            if ($item->product->status === 'inactive') {
                return back()->with('error', "Produk \"{$item->product->name}\" sedang dinonaktifkan dan belum bisa dipesan.");
            }
        }

        $isCod  = $request->payment_method === 'cod';

        $payNow = !$isCod && $request->payment_action === 'pay_now';

        $codFee      = (int) Setting::get('payment', 'cod_fee', 15000);
        DB::transaction(function () use ($cart, $request, $selectedItems, $subtotal, $isCod, $payNow, $codFee) {
            $address = auth()->user()->addresses()->findOrFail($request->address_id);
            $shippingPreview = $this->resolveShippingPreview($selectedItems, $address);

            $baseShippingCost = (int) ($shippingPreview['shipping_cost'] ?? 0);
            $shippingCost = $baseShippingCost + ($isCod ? $codFee : 0);

            $initialStatus   = ($isCod || $payNow) ? 'processing' : 'pending';
            $paymentDeadline = (!$isCod && !$payNow) ? now()->addHours(2) : null;

            $order = Order::create([
                'user_id'          => auth()->id(),
                'address_id'       => $request->address_id,
                'subtotal'         => $subtotal,
                'shipping_cost'    => $shippingCost,
                'total_price'      => $subtotal + $shippingCost,
                'status'           => $initialStatus,
                'notes'            => $request->notes,
                'payment_deadline' => $paymentDeadline,
                'estimated_delivery_at' => $shippingPreview['estimated_delivery_at'],
            ]);

            foreach ($selectedItems as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'quantity'   => $item->quantity,
                    'price'      => $item->product->effective_price,
                ]);

                $item->product->decrement('stock', $item->quantity);
            }

            $paymentStatus = $isCod ? 'pending' : ($payNow ? 'verified' : 'pending');
            $bankAccount = $request->payment_method === 'bank_transfer'
                ? BankAccount::query()->where('is_active', true)->orderBy('bank_name')->first()
                : null;

            Payment::create([
                'order_id'         => $order->id,
                'method'           => $request->payment_method,
                'status'           => $paymentStatus,
                'account_name'     => $bankAccount ? $bankAccount->bank_name . ' - ' . $bankAccount->account_holder : null,
                'account_number'   => $bankAccount?->account_number,
                'paid_at'          => $payNow ? now() : null,
                'payment_deadline' => $paymentDeadline,
            ]);

            $cart->items()->whereIn('id', $selectedItems->pluck('id'))->delete();

            session(['last_order_id' => $order->id, 'pay_now_success' => $payNow]);

            app(OrderEventNotificationService::class)->notify('order_created', $order->load('user'));

            if ($paymentStatus === 'verified') {
                app(OrderEventNotificationService::class)->notify('payment_confirmed', $order->load('user'));
            }
        });

        $message = $isCod
            ? 'Pesanan berhasil dibuat! Bayar saat produk tiba.'
            : ($payNow
                ? 'Pembayaran berhasil! Pesanan Anda sedang dikemas.'
                : 'Pesanan dibuat! Silakan lakukan pembayaran dalam 2 jam.');

        return redirect()->route('orders.show', session('last_order_id'))
            ->with('success', $message);
    }

    private function activePaymentMethods(): array
    {
        $allMethods = ['cod', 'dana', 'gopay', 'qris', 'bank_transfer'];

        $activeMethods = array_values(array_filter(
            $allMethods,
            function ($method) {
                if (!Setting::get('payment', "method_{$method}", true)) {
                    return false;
                }

                if ($method === 'bank_transfer') {
                    return BankAccount::query()->where('is_active', true)->exists();
                }

                return true;
            }
        ));

        return $activeMethods !== [] ? $activeMethods : ['cod'];
    }

    private function resolveShippingPreview($selectedItems, ?Address $address): array
    {
        $subtotal = $selectedItems->sum(fn($item) => $item->quantity * $item->product->effective_price);
        $shippingMethod = Setting::get('shipping', 'shipping_method', 'flat_rate');
        $fallbackDays = max((int) Setting::get('shipping', 'fallback_estimated_days', 3), 1);
        $flatRate = (int) Setting::get('shipping', 'flat_rate_cost', 10000);
        $freeMinimum = (int) Setting::get('shipping', 'free_shipping_minimum', 0);
        $isFreeShipping = $freeMinimum > 0 && $subtotal >= $freeMinimum;

        $courierMap = [
            'jne' => 'JNE',
            'jnt' => 'J&T Express',
            'sicepat' => 'SiCepat',
        ];

        $activeCouriers = collect($courierMap)
            ->map(fn($label, $code) => [
                'code' => $code,
                'label' => $label,
                'active' => (bool) Setting::get('shipping', "courier_{$code}_active", true),
                'days' => max((int) Setting::get('shipping', "courier_{$code}_days", $fallbackDays), 1),
            ])
            ->where('active')
            ->values();

        $selectedCourier = $activeCouriers->sortBy('days')->first();
        $estimateDays = $shippingMethod === 'automatic'
            ? ($selectedCourier['days'] ?? $fallbackDays)
            : $fallbackDays;

        return [
            'shipping_method' => $shippingMethod,
            'shipping_cost' => $isFreeShipping ? 0 : $flatRate,
            'is_free_shipping' => $isFreeShipping,
            'free_shipping_minimum' => $freeMinimum,
            'estimated_days' => $estimateDays,
            'estimated_delivery_at' => $address
                ? Carbon::now()->addDays($estimateDays)->setTime(18, 0)
                : null,
            'courier_name' => $selectedCourier['label'] ?? 'Kurir Tersedia',
            'destination_city' => $address?->city,
            'available_couriers' => $activeCouriers,
        ];
    }
}
