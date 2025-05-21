<?php

namespace App\Helper;

use Exception;
use Firebase\JWT\JWT;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class Helper
{

    public static function uploadImage($file, $folder)
    {
        if (! $file->isValid()) {
            return null;
        }

        $uniqueId  = uniqid();
        $extension = $file->getClientOriginalExtension();
        $imageName = Str::slug(time() . '-' . $uniqueId) . '.' . $extension;
        $path      = public_path('uploads/' . $folder);

        if (! file_exists($path)) {
            mkdir($path, 0755, true);
        }

        $file->move($path, $imageName);

        return 'uploads/' . $folder . '/' . $imageName;
    }

    public static function deleteImage($imageUrl)
    {
        if (! $imageUrl) {
            return false;
        }
        $filePath = public_path($imageUrl);
        if (file_exists($filePath)) {
            return unlink($filePath);
        }
        return false;
    }

    public static function deleteAvatar($filePath)
    {
        if (! $filePath) {
            return false;
        }

        $relativePath = str_replace(asset('/'), '', $filePath);
        $fullPath     = public_path($relativePath);

        if (file_exists($fullPath)) {
            unlink($fullPath);
            return true;
        }

        return false;
    }
}
