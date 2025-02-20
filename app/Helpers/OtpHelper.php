<?php

namespace App\Helpers;

use App\Models\Otp;
use Carbon\Carbon;

class OtpHelper
{
    /**
     * Generate a 6-digit OTP and save it to the database.
     *
     * @param int $userId
     * @return string $otp
     */
    public static function generateOtp($userId)
    {
        // Generate a 6-digit OTP
        $otp = rand(100000, 999999);

        // Save OTP to the database with an expiration time (e.g., 5 minutes)
        $otpRecord  = Otp::create([
            'user_id'    => $userId,
            'otp'        => $otp,
            'expires_at' => Carbon::now()->addMinutes(5),
        ]);

        // Return both the ID of the OTP record and the OTP
        return [
            'user_token' => $otpRecord->id,   // The ID of the OTP record
            'otp'        => $otp,             // The OTP itself
        ];
    }

    /**
     * Verify OTP based on the given user ID and OTP value.
     *
     * @param int $userId
     * @param string $otp
     * @return bool
     */
    public static function verifyOtp($user_id, $otp)
    {
        // Check if OTP exists for the user, module, and is not expired, and get the most recent OTP
        $otpRecord = Otp::where('id', $user_id)
            ->where('otp', $otp)
            ->where('expires_at', '>', Carbon::now())
            ->orderBy('created_at', 'desc')
            ->first();

        if ($otpRecord) {

            return true;
        }

        return false;
    }

    /**
     * Verify OTP based on the given user ID and OTP value.
     *
     * @param int $userId
     * @param string $otp
     * @return bool
     */
    public static function verifyOtpUser($user_id, $otp)
    {
        // Check if OTP exists for the user, module, and is not expired, and get the most recent OTP
        $otpRecord = Otp::where('id', $user_id)
            ->where('otp', $otp)
            ->where('expires_at', '>', Carbon::now())
            ->orderBy('created_at', 'desc')
            ->first();

        if ($otpRecord) {
            $otpuser = $otpRecord->user_id;
            $otpRecord->delete();
            return $otpuser;
        }
        return false;
    }
}
