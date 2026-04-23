<?php

namespace App\Observers;

use App\Models\Review;

class ReviewObserver
{
    public function created(Review $review)
    {
        $this->updateProductRating($review);
    }

    public function updated(Review $review)
    {
        $this->updateProductRating($review);
    }

    public function deleted(Review $review)
    {
        $this->updateProductRating($review);
    }

    private function updateProductRating(Review $review)
    {
        if ($review->product) {
            $review->product->updateRatingStats();
        }
    }
}
