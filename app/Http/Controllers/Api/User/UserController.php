<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\TwoFactorStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function userProfile(){
        $user = Auth::user()->only(['id','name','email', 'profile_photo_url']);
        // Fetch only the relevant fields (screen_lock_status and otp) from the TwoFactorStatus table
        $status = TwoFactorStatus::where('user_id', $user['id'])
            ->select('otp', 'screen_lock_status') // Only select the columns needed
            ->first();
            
        // Determine if the screen lock is set up but not in use (otp exists and screen_lock_status is 0)
        $screenLockStatus = $status && $status->otp && $status->screen_lock_status === 0;
        return response()->json([
            'data' => array_merge($user, ['screen_lock_status' => $screenLockStatus]),
            'message' => 'User Profile'
        ]);
    }
}
