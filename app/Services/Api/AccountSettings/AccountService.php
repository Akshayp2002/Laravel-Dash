<?php

namespace App\Services\Api\AccountSettings;

use App\Services\BaseService;
use Illuminate\Support\Facades\Hash;
use Auth;
use Illuminate\Support\Facades\Storage;
use App\Helpers\FileUploadHelper;

class AccountService extends BaseService
{
    public function changeProfile($request)
    {
        // Get the authenticated user
        $user = Auth::user();
        // Upload the new profile picture to S3
        if (!$filePath = FileUploadHelper::uploadToS3($request->file('profile'), $user->id, 'Profile', true)) {
            return response()->json(['error' => 'Failed to upload profile picture'], 500);
        }

        // Delete the old profile picture (if it exists)
        if (!empty($user->profile_photo_path)) {
            FileUploadHelper::deleteImage($user->profile_photo_path);
        }

        // Update user's profile photo path
        $user->update(['profile_photo_path' => $filePath]);

        return response()->json([
            'message'            => 'Profile picture updated successfully!',
            'profile_photo_path' => Storage::disk('s3')->url($filePath),
        ]);
    }

    public function changePassword($new_password)
    {
        Auth::user()->update([
            'password' => Hash::make($new_password),
        ]);
        return response()->json([
            'message' => 'Password changed successfully'
        ], 200);
    }
}
