<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Services\OrderEventNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'all');

        $query = auth()->user()->orders()
            ->with('items.product', 'payment', 'reviews')
            ->latest();

        $statusMap = [
            'pending'    => 'pending',
            'paid'       => 'paid',
            'processing' => 'processing',
            'shipped'    => 'shipped',
            'completed'  => 'completed',
            'cancelled'  => 'cancelled',
        ];

        if ($tab !== 'all' && isset($statusMap[$tab])) {
            $query->where('status', $statusMap[$tab]);
        }

        $orders = $query->paginate(10)->withQueryString();

        $counts = auth()->user()->orders()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return view('dashboard.orders', compact('orders', 'tab', 'counts'));
    }

    public function show(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }
        $order->load([
            'items.product',
            'payment',
            'address',
            'reviews',
            'trackingUpdates',
        ]);
        return view('dashboard.order-detail', compact('order'));
    }

    public function cancel(Request $request, Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if (!$order->canBeCancelled()) {
            return back()->with('error', 'Pesanan ini tidak dapat dibatalkan.');
        }

        $request->validate([
            'cancel_reason' => 'required|string|max:500',
        ]);

        DB::transaction(function () use ($order, $request) {
           
            foreach ($order->items as $item) {
                $item->product->increment('stock', $item->quantity);
            }

            $order->update([
                'status'        => 'cancelled',
                'cancel_reason' => $request->cancel_reason,
            ]);

            if ($order->payment) {
                $order->payment->update(['status' => 'failed']);
            }
        });

        return redirect()->route('orders.index')->with('success', 'Pesanan berhasil dibatalkan.');
    }

    public function cancelItem(Request $request, Order $order, $itemId)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $item = $order->items()->findOrFail($itemId);

        if (!$item->canBeCancelled()) {
            return back()->with('error', 'Item ini tidak dapat dibatalkan.');
        }

        $request->validate([
            'cancel_reason' => 'required|string|max:500',
        ]);

        DB::transaction(function () use ($item, $request) {
           
            $item->product->increment('stock', $item->quantity);

            $item->update([
                'status'         => 'cancelled',
                'cancel_reason'  => $request->cancel_reason,
                'cancelled_at'   => now(),
            ]);

            $order = $item->order;
            $activeItems = $order->items()->where('status', 'active')->count();

            if ($activeItems === 0) {
               
                $order->update(['status' => 'cancelled']);

                if ($order->payment) {
                    $order->payment->update(['status' => 'failed']);
                }
            }
        });

        return back()->with('success', 'Item berhasil dibatalkan!');
    }

    public function buyAgain(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load('items.product');

        $cart = auth()->user()->cart()->firstOrCreate(['user_id' => auth()->id()]);

        $errors = [];
        foreach ($order->items as $item) {
            $product = $item->product;

            if (!$product || $product->stock < 1 || $product->status === 'inactive') {
                $errors[] = ($product->name ?? 'Produk') . ' tidak tersedia.';
                continue;
            }

            $existingItem = $cart->items()->where('product_id', $product->id)->first();

            if ($existingItem) {
                $newQty = min($existingItem->quantity + $item->quantity, $product->stock);
                $existingItem->update(['quantity' => $newQty, 'is_selected' => true]);
            } else {
                $cart->items()->create([
                    'product_id'  => $product->id,
                    'quantity'    => min($item->quantity, $product->stock),
                    'is_selected' => true,
                ]);
            }
        }

        if (!empty($errors)) {
            return redirect()->route('cart.index')
                ->with('warning', 'Beberapa produk tidak bisa ditambahkan: ' . implode(', ', $errors));
        }

        return redirect()->route('cart.index')->with('success', 'Produk berhasil ditambahkan kembali ke keranjang.');
    }

    public function buyAgainItem(Request $request, Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1|max:99',
        ]);

        $product = Product::findOrFail($request->product_id);

        if (!$product || $product->stock < 1 || $product->status === 'inactive') {
            return back()->with('error', $product->name . ' tidak tersedia.');
        }

        if ($product->stock < $request->quantity) {
            return back()->with('error', 'Stok produk tidak mencukupi.');
        }

        $cart = auth()->user()->cart()->firstOrCreate(['user_id' => auth()->id()]);

        $existingItem = $cart->items()->where('product_id', $product->id)->first();

        if ($existingItem) {
            $newQty = min($existingItem->quantity + $request->quantity, $product->stock);
            $existingItem->update(['quantity' => $newQty, 'is_selected' => true]);
        } else {
            $cart->items()->create([
                'product_id'  => $product->id,
                'quantity'    => $request->quantity,
                'is_selected' => true,
            ]);
        }

        return redirect()->route('checkout.index')->with('success', 'Produk berhasil ditambahkan. Lanjutkan checkout.');
    }

    public function payNow(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if ($order->status !== 'pending') {
            return back()->with('error', 'Pesanan ini tidak bisa dibayar (status: ' . $order->status_label . ').');
        }

        DB::transaction(function () use ($order) {
            $order->update(['status' => 'processing']);

            if ($order->payment) {
                $order->payment->update([
                    'status'  => 'verified',
                    'paid_at' => now(),
                ]);
            }

            app(OrderEventNotificationService::class)->notify('payment_confirmed', $order->load('user'));
        });

        return redirect()->route('orders.show', $order)
            ->with('success', '✅ Pembayaran berhasil! Pesanan Anda sedang diproses.');
    }

    public function storeReview(Request $request, Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        if ($order->status !== 'completed') {
            return back()->with('error', 'Ulasan hanya bisa diberikan untuk pesanan yang sudah selesai.');
        }

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating'     => 'required|integer|min:1|max:5',
            'comment'    => 'nullable|string|max:1000',
            'image'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $productInOrder = $order->items()
            ->where('product_id', $request->product_id)
            ->exists();

        if (!$productInOrder) {
            return back()->with('error', 'Produk ini tidak termasuk dalam pesanan yang dipilih.');
        }

        $existing = Review::where('order_id', $order->id)
            ->where('product_id', $request->product_id)
            ->where('user_id', auth()->id())
            ->first();

        if ($existing) {
            return back()->with('error', 'Anda sudah memberikan ulasan untuk produk ini.');
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('reviews', 'public');
        }

        Review::create([
            'user_id'       => auth()->id(),
            'product_id'    => $request->product_id,
            'order_id'      => $order->id,
            'rating'        => $request->rating,
            'comment'       => $request->comment,
            'image'         => $imagePath,
            'reviewer_name' => auth()->user()->name,
        ]);

        $this->syncProductRating((int) $request->product_id);

        return back()->with('success', 'Ulasan berhasil dikirim. Terima kasih!');
    }

    private function syncProductRating(int $productId): void
    {
        $stats = Review::query()
            ->where('product_id', $productId)
            ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as total_reviews')
            ->first();

        Product::query()
            ->whereKey($productId)
            ->update([
                'rating' => round((float) ($stats?->avg_rating ?? 0), 1),
                'rating_count' => (int) ($stats?->total_reviews ?? 0),
            ]);
    }

    public function deleteReview(Review $review)
    {
        if ($review->user_id !== auth()->id()) {
            abort(403);
        }

        $productId = $review->product_id;
        $review->delete();

        $this->syncProductRating($productId);

        return back()->with('success', 'Ulasan berhasil dihapus.');
    }
}
