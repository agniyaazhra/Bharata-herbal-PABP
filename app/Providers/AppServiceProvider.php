<?php

namespace App\Providers;

use App\Models\CartItem;
use App\Models\OrderItem;
use App\Models\Review;
use App\Observers\OrderItemObserver;
use App\Observers\ReviewObserver;
use App\Policies\CartItemPolicy;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    
    public function register(): void
    {
       
    }

    public function boot(): void
    {
       
        Gate::policy(CartItem::class, CartItemPolicy::class);

        Review::observe(ReviewObserver::class);
        OrderItem::observe(OrderItemObserver::class);

        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            $schedule->command('app:cancel-expired-orders')->everyMinute();
        });
    }
}
