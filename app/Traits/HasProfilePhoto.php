<?php

namespace App\Traits;

use App\Helpers\FileUploadHelper;
use Illuminate\Support\Facades\Storage;

trait HasProfilePhoto
{
    /**
     * Get the user's profile photo URL.
     *
     * @return string
     */
    public function getProfilePhotoUrlAttribute(): string
    {
        if ($this->profile_photo_path) {
            return Storage::disk(env('FILESYSTEM_DISK', 's3'))->url($this->profile_photo_path);
        }

        return $this->generateDefaultProfileImage();
    }

    /**
     * Generate a default profile image URL.
     *
     * @return string
     */
    private function generateDefaultProfileImage()
    {
        $name = trim(collect(explode(' ', $this->name))->map(function ($segment) {
            return mb_substr($segment, 0, 1);
        })->join(' '));
        return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&color=7F9CF5&background=EBF4FF';
        //For 2 letters ans dandom colors
        // $name = urlencode($this->name ?? 'User');
        // return "https://ui-avatars.com/api/?name={$name}&background=random";
    }

    /**
     * Delete the user's profile photo.
     *
     * @return void
     */
    public function deleteProfilePhoto()
    {

        if (is_null($this->profile_photo_path)) {
            return;
        }
        FileUploadHelper::deleteImage($this->profile_photo_path);

        $this->forceFill([
            'profile_photo_path' => null,
        ])->save();
    }
}
