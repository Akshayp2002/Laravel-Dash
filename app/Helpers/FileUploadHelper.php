<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class FileUploadHelper
{
    /**
     * $imageUrl = FileUploadHelper::uploadToS3($file, "JDHG2G4BNNJ4445KJ5",'Test/Images/Profile', true);
     * $fileUrl = FileUploadHelper::uploadToS3($request->file('document'), 'Documents');
     * $fileUrl = FileUploadHelper::deleteImage($imageUrl);
     * $uploadedPaths = FileUploadHelper::uploadMultipleToS3($files, 'Test/Uploads', true, 'png');
     */
    /**
     * Upload an image or file to AWS S3.
     *
     * @param \Illuminate\Http\UploadedFile|null $file
     * @param string $path Storage path (default: 'uploads')
     * @param bool $isImage Whether the file is an image
     * @param string|null $format Image format ('webp', 'jpg', 'png', 'gif')
     * @return string|null The uploaded file URL or null on failure
     */
    public static function uploadToS3($file, ?string $user_id = null, ?string $path = null, bool $isImage = false, ?string $format = 'webp'): ?string
    {
        if (!$file) {
            return null;
        }

        // Set default values if parameters are not provided
        $user_id = $user_id ?? 'app';
        $path    = $path ?? 'uploads';


        // Generate a unique filename
        $extension = $isImage ? $format : $file->getClientOriginalExtension();
        $fileName = time() . '_' . uniqid() . '.' . $extension;
        $filePath = \strtoupper($user_id) .  '/' . $path . '/' . $fileName;

        if ($isImage) {
            // Read the image using Laravel's Intervention Image facade
            $image = Image::read($file);

            // Convert image to the desired format
            if ($format === 'jpg' || $format === 'jpeg') {
                $image = $image->toJpeg(80);
            } elseif ($format === 'png') {
                $image = $image->toPng();
            } elseif ($format === 'gif') {
                $image = $image->toGif();
            } else {
                // Default: Convert to WebP with 80% quality
                $image = $image->toWebp(80);
            }

            // Upload image to S3
            Storage::disk('s3')->put($filePath, (string) $image, 'public');
        } else {
            // Upload non-image files directly
            Storage::disk('s3')->put($filePath, file_get_contents($file), 'public');
        }

        return $filePath;
    }


    /**
     * Upload multiple files to AWS S3.
     *
     * @param array $files Array of \Illuminate\Http\UploadedFile
     * @param string $path Storage path (default: 'uploads')
     * @param bool $isImage Whether files are images
     * @param string|null $format Image format ('webp', 'jpg', 'png', 'gif')
     * @return array List of uploaded file paths
     */
    public static function uploadMultipleToS3(array $files, ?string $user_id = null, ?string $path = null, bool $isImage = false, ?string $format = 'webp'): array
    {
        $uploadedPaths = [];

        foreach ($files as $file) {
            if (!$file) {
                continue;
            }

            // Set default values if parameters are not provided
            $user_id = $user_id ?? 'app';
            $path    = $path ?? 'uploads';


            // Generate a unique filename
            $extension = $isImage ? $format : $file->getClientOriginalExtension();
            $fileName = time() . '_' . uniqid() . '.' . $extension;
            $filePath = \strtoupper($user_id) .  '/' . $path . '/' . $fileName;

            if ($isImage) {
                try {
                    $image = Image::read($file);

                    // Convert image to the desired format
                    if ($format === 'jpg' || $format === 'jpeg') {
                        $image = $image->toJpeg(80);
                    } elseif ($format === 'png') {
                        $image = $image->toPng();
                    } elseif ($format === 'gif') {
                        $image = $image->toGif();
                    } else {
                        $image = $image->toWebp(80);
                    }

                    // Upload to S3
                    Storage::disk('s3')->put($filePath, (string) $image, 'public');
                } catch (\Exception $e) {
                    continue; // Skip file if any error occurs
                }
            } else {
                // Upload non-image files directly
                Storage::disk('s3')->put($filePath, file_get_contents($file), 'public');
            }

            $uploadedPaths[] = $filePath;
        }

        return $uploadedPaths;
    }



    /**
     * Delete an image or file to AWS S3.
     *
     * @param \Illuminate\Http\UploadedFile|null $filePath
     */

    public static function deleteImage($filePath)
    {
        if ($filePath) {
            if (Storage::disk('s3')->exists($filePath)) {
                Storage::disk('s3')->delete($filePath);
                return true;
            }
        }
    }

    /**
     * Delete multiple images from AWS S3.
     *
     * @param array $filePaths Array of file paths to delete
     * @return array List of successfully deleted files
     */

    public static function deleteImages(array $filePaths): array
    {
        $deletedFiles = [];

        foreach ($filePaths as $filePath) {
            if (Storage::disk('s3')->exists($filePath)) {
                Storage::disk('s3')->delete($filePath);
                $deletedFiles[] = $filePath;
            }
        }

        return $deletedFiles; // Return list of deleted file paths
    }
}
