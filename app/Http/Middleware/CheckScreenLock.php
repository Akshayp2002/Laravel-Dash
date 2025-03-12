<?php

namespace App\Http\Middleware;

use App\Models\TwoFactorStatus;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class CheckScreenLock
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the authenticated user
        $user = Auth::user();
        $screenLockStatus = null;
        if($user){
            $screenLockStatus = TwoFactorStatus::where('user_id', $user->id)->value('screen_lock_status');
        }
        // Check if the user has enabled screen lock
      

        if ($screenLockStatus) {
            // Ensure the user has provided the correct PIN (OTP)
            $request->validate([
                'pin' => 'required', // Assuming 'pin' is the field in the request
            ]);

            $otp = TwoFactorStatus::where('user_id', $user->id)->value('otp'); // Get stored hashed PIN

            if (!Hash::check($request->pin, $otp)) {
                return response()->json(['message' => 'Invalid PIN'], 403);
            }
        }
        return $next($request);
    }
}
