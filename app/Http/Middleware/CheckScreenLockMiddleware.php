<?php

namespace App\Http\Middleware;

use App\Models\TwoFactorStatus;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class CheckScreenLockMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if ($user) {
            $status = TwoFactorStatus::where('user_id', $user->id)
                ->select('screen_lock_status', 'otp')
                ->first();
            // If the status exist, or otp is not null, or screen lock is enabled
            if ($status && $status->otp && $status->screen_lock_status) {
                return response()->json([
                    'message'     => 'Screen is locked. Please provide a PIN to unlock or set up a PIN first.',
                    'screen_lock' => true,
                ], 403);
            }
        }
        return $next($request);
    }
}
