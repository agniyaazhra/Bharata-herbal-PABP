<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ReviewResource;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    use ApiResponseTrait;

    public function store(Request $request, Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            return $this->error('Unauthorized', 403);
        }

        if ($order->status !== 'completed') {
            return $this->error('Hanya pesanan yang sudah selesai yang dapat diberi review.', 400);
        }

        $validated = $request->validate([
            'product_id'    => ['required', 'exists:products,id'],
            'rating'        => ['required', 'integer', 'min:1', 'max:5'],
            'comment'       => ['nullable', 'string', 'max:1000'],
            'reviewer_name' => ['nullable', 'string', 'max:255'],
            'reviewer_title' => ['nullable', 'string', 'max:255'],
        ]);

        // Verify that the product is in this order
        $orderItem = $order->items()->where('product_id', $validated['product_id'])->first();
        if (!$orderItem) {
            return $this->error('Produk ini tidak ada di pesanan Anda.', 404);
        }

        // Check if review already exists
        $existingReview = Review::where('order_id', $order->id)
            ->where('product_id', $validated['product_id'])
            ->first();

        if ($existingReview) {
            return $this->error('Anda sudah memberikan review untuk produk ini.', 400);
        }

        $review = $order->reviews()->create([
            'user_id'        => auth()->id(),
            'product_id'     => $validated['product_id'],
            'rating'         => $validated['rating'],
            'comment'        => $validated['comment'],
            'reviewer_name'  => $validated['reviewer_name'] ?? auth()->user()->name,
            'reviewer_title' => $validated['reviewer_title'],
        ]);

        // Update product rating
        $product = $orderItem->product;
        $allReviews = $product->reviews()->get();
        $avgRating = $allReviews->avg('rating');
        $product->update([
            'rating' => round($avgRating, 2),
            'rating_count' => $allReviews->count(),
        ]);

        return $this->success(new ReviewResource($review), 'Review berhasil ditambahkan.', 201);
    }

    public function destroy(Review $review)
    {
        if ($review->user_id !== auth()->id()) {
            return $this->error('Unauthorized', 403);
        }

        $product = $review->product;
        $review->delete();

        // Update product rating
        $allReviews = $product->reviews()->get();
        if ($allReviews->count() > 0) {
            $avgRating = $allReviews->avg('rating');
            $product->update([
                'rating' => round($avgRating, 2),
                'rating_count' => $allReviews->count(),
            ]);
        } else {
            $product->update([
                'rating' => 0,
                'rating_count' => 0,
            ]);
        }

        return $this->success(null, 'Review berhasil dihapus.');
    }
}
