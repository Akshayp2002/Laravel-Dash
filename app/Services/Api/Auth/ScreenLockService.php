<?php

namespace App\Services\Api\Auth;

use App\Models\TwoFactorStatus;
use App\Services\BaseService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;


class ScreenLockService extends BaseService
{
    public function setupLock($request)
    {
        $request->validate([
            'new_pin'     => 'required|numeric|min:4',  // Ensure the PIN is numeric and has at least 4 digits
            'confirm_pin' => 'required|numeric|same:new_pin',  // Ensure the confirm_pin is numeric and matches new_pin
        ]);
        $user = Auth::user();
        // Check if the user already has a screen lock set up (check if OTP is set)
        $existingStatus = TwoFactorStatus::where('user_id', $user->id)->first();

        if ($existingStatus && $existingStatus->otp) {
            return response()->json([
                'message' => 'Screen lock is already set up.'
            ], 400);
        }

        // Update or create the TwoFactorStatus record with the encrypted PIN
        TwoFactorStatus::updateOrCreate(
            ['user_id' => $user->id],
            [
                'otp' => Crypt::encrypt($request->new_pin),
            ]
        );

        // Return a success response
        return response()->json([
            'message' => 'Screen lock setup successfully!'
        ]);
    }

    public function enableLock()
    {
        // Retrieve the user's TwoFactorStatus record
        $status = TwoFactorStatus::where('user_id', Auth::user()->id)->first();

        // Check if the TwoFactorStatus record exists and if the OTP (PIN) is set
        if (!$status || !$status->otp) {
            return response()->json([
                'message' => 'Screen lock is not set up. Please set up your PIN first.'
            ], 400);
        }

        // Set the screen lock status to true (enabled) if it isn't already
        if (!$status->screen_lock_status) {
            $status->screen_lock_status = true;
            $status->save();
            return response()->json([
                'message' => 'Screen lock enabled successfully!'
            ]);
        }

        // If the lock is already enabled, return a message
        return response()->json([
            'message' => 'Screen lock is already enabled.'
        ]);
    }

    public function disableLock()
    {
        // Retrieve the user's TwoFactorStatus record
        $status = TwoFactorStatus::where('user_id', Auth::user()->id)->first();
        // Check if the status record exists and if OTP (PIN) is set
        if (!$status || !$status->otp) {
            return response()->json([
                'message' => 'Screen lock not set up. Please set up your PIN first.'
            ], 400);
        }

        // Disable the screen lock by setting the status to false
        $status->screen_lock_status = false;
        $status->otp                = null;
        $status->save();
        return response()->json([
            'message' => 'Screen lock disabled successfully!'
        ]);
    }

    public function verifyLock($request)
    {
        $request->validate([
            'pin'     => 'required|numeric|min:4'
        ]);
        $status = TwoFactorStatus::where('user_id', Auth::user()->id)->first();

        if (!$status->screen_lock_status) {
            return response()->json(['message' => 'Screen lock is already disabled'], 400);
        }

        // If no status is found, return an error (meaning the PIN is not set for this user)
        if (!$status || !$status->otp) {
            return response()->json(['message' => 'PIN not set for this user.'], 400);
        }

        // Compare the provided PIN with the stored (decrypted) OTP
        if ($request->pin !== Crypt::decrypt($status->otp)) {
            return response()->json(['message' => 'Invalid PIN'], 403);
        }

        // If the PIN is correct, update the screen lock status to unlocked
        // Only update if it's currently locked, saving unnecessary database write operation
        if ($status->screen_lock_status) {
            $status->update(['screen_lock_status' => false]);
        }

        return response()->json(['message' => 'Screen unlocked successfully!']);
    }

    public function recoveryLock() {}
}
