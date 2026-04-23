<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->isSuperAdmin()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak. Fitur ini hanya untuk Super Admin.',
                ], 403);
            }

            abort(403, 'Akses ditolak. Hanya Super Admin yang bisa mengakses fitur ini.');
        }

        return $next($request);
    }
}
