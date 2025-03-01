<?php

namespace App\Http\Controllers\Api\AccountSettings;

use App\Helpers\FileUploadHelper;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Auth;
use Illuminate\Support\Facades\Storage;

class AccountController extends Controller
{

    public function changeProfile(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'profile' => 'required|file|mimes:jpg,jpeg,png,webp|max:2048',   // Ensure it's a valid image
        ]);

        // Return validation errors if the request is invalid
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Get the authenticated user
        $user = Auth::user();
          // Get the uploaded file
        $file = $request->file('profile');

        // Upload the new profile picture
        $filePath = FileUploadHelper::uploadToS3($file, $user->id, 'Profile', true);

        if ($filePath) {
            // Delete the old profile picture (if it exists)
            FileUploadHelper::deleteImage($user->profile_photo_path);

            // Update the user's profile photo path in the database
            $user->profile_photo_path = $filePath;
            $user->save();

            return response()->json([
                'message'            => 'Profile picture updated successfully!',
                'profile_photo_path' => Storage::disk('s3')->url($filePath),
            ]);
        }

        return response()->json(['error' => 'Failed to upload profile picture'], 500);
    }



    public function changePassword(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password'     => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }

        $user = auth()->user();

        // Check if current password matches
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Current password is incorrect'], 400);
        }

        // Update the password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'message' => 'Password changed successfully'
        ], 200);
    }
}
