<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 *
 */
class IsVerifiedMiddleware
{

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->user()->email_verified_at != null) {
            return $next($request);
        } else {
            return response()->json([
                'error' => 'User not verified'
            ], 401);
        }
    }
}
