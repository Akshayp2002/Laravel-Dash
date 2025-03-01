<?php

namespace App\Helpers;

use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Http\JsonResponse;

class RateLimiterHelper
{
    /**
     * Check rate limiting for login attempts.
     *
     * @param string $email
     * @return JsonResponse|null
     */
    public static function checkLoginRateLimit(string $email): ?JsonResponse
    {
        $key = 'login-attempts:' . $email;

        if (RateLimiter::tooManyAttempts($key, 5)) {
            return response()->json([
                'message' => 'Too many login attempts. Please try again in ' . RateLimiter::availableIn($key) . ' seconds.'
            ], 429);
        }

        RateLimiter::hit($key, 60); // Store the attempt for 60 seconds
        return null; // No rate limiting issue
    }
}
