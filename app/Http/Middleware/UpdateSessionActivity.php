<?php

namespace App\Http\Middleware;

use App\Models\AdminSession;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateSessionActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->isAdmin()) {
           
            $cacheKey = 'session_touch_' . auth()->id() . '_' . session()->getId();

            if (!cache()->has($cacheKey)) {
                AdminSession::where('user_id', auth()->id())
                    ->where('token_id', session()->getId())
                    ->where('is_active', true)
                    ->update(['last_active' => now()]);

                cache()->put($cacheKey, true, now()->addMinutes(3));
            }
        }

        return $next($request);
    }
}
