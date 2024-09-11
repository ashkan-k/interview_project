<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;

class CustomThrottleMiddleware
{
    public function handle(Request $request, Closure $next, $maxAttempts = 10, $decayMinutes = 1)
    {
        try {
            return $next($request);
        } catch (ThrottleRequestsException $e) {
            return response()->json([
                'message' => 'شما بیش از حد مجاز درخواست ارسال کرده‌اید. لطفاً بعداً تلاش کنید.',
            ], 429);
        }
    }
}

