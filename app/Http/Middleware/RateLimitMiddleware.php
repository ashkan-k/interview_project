<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;

class RateLimitMiddleware
{
    public function handle($request, Closure $next)
    {
        $userId = $request->user()->id;
        $cacheKey = 'calculate_distance_requests_' . $userId;
        $maxAttempts = 10; // حداکثر تعداد درخواست‌ها
        $decayMinutes = 1; // زمان در دقیقه

        // بررسی تعداد درخواست‌های کاربر
        if (Cache::has($cacheKey)) {
            $requestCount = Cache::get($cacheKey);

            if ($requestCount >= $maxAttempts) {
                return Response::json([
                    'message' => 'شما بیش از حد مجاز درخواست ارسال کرده‌اید. لطفاً بعداً تلاش کنید.'
                ], 429); // کد 429: Too Many Requests
            }

            Cache::increment($cacheKey);
        } else {
            // اگر کاربر برای اولین بار در این بازه زمانی درخواست ارسال کرده باشد
            Cache::put($cacheKey, 1, $decayMinutes * 60); // ذخیره تعداد درخواست‌ها برای یک دقیقه
        }

        return $next($request);
    }
}

