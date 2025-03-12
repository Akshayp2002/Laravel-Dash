<?php

namespace App\Services\Api\Auth;

use App\Services\BaseService;
use App\Models\TwoFactorStatus;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Writer;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use App\Helpers\RateLimiterHelper;
use Illuminate\Support\Str;
use App\Models\Otp;
use App\Models\User;
use App\Helpers\OtpHelper;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailOtpVerifyMail;

class MfaService extends BaseService
{
    /**
     * Create a new class instance.
     */
    // check if the user is enabled
    public function status()
    {
        // Fetch only the required fields from the TwoFactorStatus table
        $status = TwoFactorStatus::where('user_id', Auth::user()->id)
            ->select('otp', 'screen_lock_status', 'two_factor_all_status', 'qr_code_status', 'email_otp_status', 'mobile_otp_status')
            ->first();
        // If no status record is found, return an error response
        if (!$status) {
            return response()->json([
                'status'             => false,
                'screen_lock_status' => false, // As there's no status, lock status will be false
                'message'            => 'No Two-Factor Authentication record found for this user.',
            ], 404);
        }
        // Determine if the screen lock is set up but not in use
        $screenLockStatus = $status->otp && $status->screen_lock_status === 0;  
        // If 2FA is found, return the status data
        return response()->json([
            'two_factor_all_status' => (bool) $status->two_factor_all_status,
            'qr_code_status'        => (bool) $status->qr_code_status,
            'email_otp_status'      => (bool) $status->email_otp_status,
            'mobile_otp_status'     => (bool) $status->mobile_otp_status,
            'screen_lock_status'    => $screenLockStatus,
            'message'               => '2FA Status retrieved successfully.',
        ], 200);
    }

    //enable/disable 2fa
    public function toggle2FA($request)
    {
        // Get the authenticated user from token
        $user = Auth::user();
        // Verify password before enabling 2FA
        if (!Hash::check($request['password'], $user->password)) {
            return response()->json(['error' => 'Incorrect password.'], 403);
        }
        // Get the authenticated user
        $status = TwoFactorStatus::firstOrNew(['user_id' =>  $user->id]);

        // Check if the current route is for enabling or disabling 2FA
        $enable = request()->route()->getName() === '2fa.enable';

        // Set the 2FA status based on the route (true for enabling, false for disabling)
        $status->two_factor_all_status = $enable;
        $status->save();

        return response()->json([
            'status'  => true,
            'message' => $enable ? '2FA is Enabled.' : '2FA is Disabled.'
        ], 200);
    }


    // Enable 2fa
    public function enableAuthenticator($request)
    {
        // Get the authenticated user from token
        $user = Auth::user();
        // Verify password before enabling 2FA
        if (!Hash::check($request['password'], $user->password)) {
            return response()->json(['error' => 'Incorrect password.'], 403);
        }

        // If 2FA is already enabled and confirmed, prevent regenerating QR code
        if ($user->two_factor_secret && $user->two_factor_confirmed_at) {
            return response()->json(['message' => '2FA is already enabled and verified.'], 200);
        }

        // If 2FA is enabled but not confirmed, return existing QR code & secret
        if ($user->two_factor_secret && !$user->two_factor_confirmed_at) {
            return $this->generateQr($user, false); // Pass `false` to prevent regenerating the secret
        }

        // Otherwise, generate new QR code and secret
        return $this->generateQr($user, true);
    }
    // QR Code generate here
    public function generateQr($user, $regenerate = true)
    {
        $google2fa = new Google2FA();

        // If 2FA secret already exists and we don't need to regenerate, return existing details
        if (!$regenerate && $user->two_factor_secret) {
            $secret = decrypt($user->two_factor_secret);
        } else {
            // Generate new secret if not aready set
            $secret = $google2fa->generateSecretKey();
            $user->forceFill([
                'two_factor_secret' => encrypt($secret),
            ])->save();
        }

        // Generate QR Code URL
        $qrCodeData = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );
        // Generate QR Code as SVG
        $renderer = new ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle(200),
            new SvgImageBackEnd()
        );
        $writer       = new Writer($renderer);
        $qrCodeSvg    = $writer->writeString($qrCodeData);
        $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);

        return response()->json([
            'qr_code_svg' => $qrCodeBase64,
            'message'     => $regenerate ? '2FA enabled' : '2FA already enabled but not verified',
            'secret'      => $secret
        ]);
    }

    //Verify the 2fa initally
    public function verifyAuthenticator($request)
    {
        $user      = Auth::user();
        $google2fa = new Google2FA();
        // Rate Limiting for 5 request in a minute
        if ($response = RateLimiterHelper::checkLoginRateLimit($user->email)) return $response;

        // Generate secret if not already set
        if (!$user->two_factor_secret) {
            $secret = $google2fa->generateSecretKey();
            $user->forceFill([
                'two_factor_secret'       => encrypt($secret),
                'two_factor_confirmed_at' => null,               // Ensure 2FA is not confirmed yet
            ])->save();
        } else {
            $secret = decrypt($user->two_factor_secret);
        }

        // Verify OTP before confirming 2FA
        $isValid = $google2fa->verifyKey($secret, $request->code, 2);

        if (!$isValid) {
            return response()->json(['error' => 'Invalid OTP, Please try again.'], 400);
        }

        // Generate Recovery Codes (Array of 5 Random Strings)
        $recoveryCodes = collect(range(1, 5))->map(function () {
            return Str::random(10); // Generate 10-character alphanumeric codes
        })->toArray();

        // Store encrypted recovery codes in the database
        $user->forceFill([
            'two_factor_confirmed_at'   => now(),
            'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes)),
        ])->save();
        // Change the status to enabled
        $status = TwoFactorStatus::where('user_id', $user->id)->first();
        $status->qr_code_status = true;
        $status->save();
        return response()->json([
            'message'        => '2FA has been successfully enabled.',
            'secret'         => $secret,
            'recovery_codes' => $recoveryCodes,
        ]);
    }

    // Verify MFA Code for while /login
    public function verifyAuthenticatorLogin($request)
    {
        // Rate Limiting for 5 request in a minute
        $google2fa = new Google2FA();
        $otpRecord = Otp::where('otp', $request->mfa_token)->first();
        // Find OTP record
        if (!$otpRecord) {
            return response()->json(['message' => 'Invalid or expired OTP token!'], 400);
        }
        $user = User::find($otpRecord->user_id);

        if ($response = RateLimiterHelper::checkLoginRateLimit($user->email)) return $response;

        if (!$user->two_factor_secret) {
            return response()->json(['error' => '2FA is not enabled'], 400);
        }
        // Verify the code
        $isValid = $google2fa->verifyKey(decrypt($user->two_factor_secret), $request->code, 2);

        if (!$isValid) {
            return response()->json(['error' => 'Invalid code'], 400);
        }
        // Mark 2FA as confirmed
        if (!$user->two_factor_confirmed_at) {
            $user->two_factor_confirmed_at = now();
            $user->save();
        }

        $data['name']     = $user->name;
        $data['token']    = $user->createToken($request->userAgent())->accessToken;
        $data['email']    = $user->email;
        $otpRecord->delete(); // Ensures OTP cannot be reused

        return response()->json([
            'data'    => $data,
            'message' => "2FA verification successful!"
        ]);
    }


    //Recovery Code Generate
    public function getRecoveryCodes()
    {
        $user = Auth::user();

        if (!$user->two_factor_secret) {
            return response()->json(['error' => '2FA is not enabled.'], 400);
        }

        // return Decrypt recovery codes
        return response()->json([
            'recovery_codes' => json_decode(decrypt($user->two_factor_recovery_codes), true),
        ]);
    }

    // Regenerate Recovery Code
    public function regenerateRecoveryCodes()
    {
        $user = Auth::user();

        if (!$user->two_factor_secret) {
            return response()->json(['error' => '2FA is not enabled.'], 400);
        }

        // Generate a new set of 5 recovery codes
        $newRecoveryCodes = collect(range(1, 5))->map(function () {
            return Str::random(10);
        })->toArray();

        // Encrypt and save them
        $user->forceFill([
            'two_factor_recovery_codes' => encrypt(json_encode($newRecoveryCodes)),
        ])->save();

        return response()->json([
            'message'        => 'New recovery codes have been generated.',
            'recovery_codes' => $newRecoveryCodes,
        ]);
    }

    // Verify recovery code during login
    public function verifyRecoveryCode($request)
    {
        $otpRecord = Otp::where('otp', $request->mfa_token)->first();
        // Find OTP record
        if (!$otpRecord) {
            return response()->json(['message' => 'Invalid or expired OTP token!'], 400);
        }
        $user = User::find($otpRecord->user_id);

        // Rate Limiting for 5 request in a minute
        if ($response = RateLimiterHelper::checkLoginRateLimit($user->email)) return $response;

        if (!$user || !$user->two_factor_secret) {
            return response()->json(['error' => 'User not found or 2FA is not enabled'], 400);
        }
        $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true);

        if (!in_array($request->recovery_code, $recoveryCodes)) {
            return response()->json(['error' => 'Invalid recovery code'], 400);
        }
        // Remove Used Recovery Code
        $newRecoveryCodes = array_values(array_diff($recoveryCodes, [$request->recovery_code]));

        // Encrypt and save new recovery codes
        $user->forceFill([
            'two_factor_recovery_codes' => encrypt(json_encode($newRecoveryCodes)),
        ])->save();

        $data['name']     = $user->name;
        $data['token']    = $user->createToken($request->userAgent())->accessToken;
        $data['email']    = $user->email;
        $otpRecord->delete(); // Ensures OTP cannot be reused
        // Issue a new token
        return response()->json([
            'data'    => $data,
            'message' => "Login successful using a recovery code!"
        ]);
    }

    // Disable 2FA
    public function disableAuthenticator($request)
    {
        // Get the authenticated user from token
        $user = Auth::user();
        //Verify password before disabling 2FA
        if (!Hash::check($request['password'], $user->password)) {
            return response()->json(['error' => 'Incorrect password.'], 403);
        }
        // Change the status to enabled
        $status = TwoFactorStatus::firstOrNew(['user_id' => $user->id]);
        $status->qr_code_status = false;
        $status->save();
        // Disable 2FA
        $user->forceFill([
            'two_factor_secret'         => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at'   => null,
        ])->save();

        return response()->json(['message' => '2FA has been successfully disabled.']);
    }

    // enable/disable mfa email otp
    public function toggleEmailOtp($request)
    {
        // Get the authenticated user from token
        $user   = Auth::user();
        $status = TwoFactorStatus::firstOrNew(['user_id' =>  $user->id]);
        $enable = $request->route()->getName() === 'emailOtp.enable';
        if ($enable && $status->email_otp_status) {
            return response()->json([
                'status'  => true,
                'message' => 'Email OTP is already enabled.',
            ], 200);
        }
        // Generate OTP and send via email
        $result = OtpHelper::generateOtp($user->id);
        Mail::to($user->email)->send(new EmailOtpVerifyMail($result));
        return response()->json([
            'status'     => true,
            'message'    => 'OTP has been sent to your email. Please verify to ' . ($enable ? 'enable' : 'disable') . ' email OTP.',
            'user_token' => $result['user_token']
        ], 200);
    }

    // enable/disable email otp verify
    public function toggleEmailOtpVerify($request)
    {
        $user = Auth::user();
        $isValid = OtpHelper::verifyOtpUser($request['user_token'], $request['otp']);
        if (!$isValid) {
            return response()->json([
                'status'  => false,
                'message' => 'Invalid or expired OTP.',
            ], 400);
        }
        $enable = request()->route()->getName() === 'emailOtpVerify.enable';
        $status = TwoFactorStatus::firstOrNew(['user_id' => $user->id]);
        $status->email_otp_status = $enable;
        $status->save();
        return response()->json([
            'status'  => true,
            'message' => 'Email OTP has been ' . ($enable ? 'enabled' : 'disabled') . ' successfully.',
        ], 200);
    }

}
