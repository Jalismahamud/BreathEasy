<?php

namespace App\Helper;

use getID3;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

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

        $relativePath = $filePath;

        if (Str::startsWith($filePath, ['http://', 'https://'])) {
            $parsed = parse_url($filePath, PHP_URL_PATH);
            $relativePath = ltrim($parsed, '/');
        } else {

            try {
                $assetBase = asset('/');
                if (strpos($filePath, $assetBase) === 0) {
                    $relativePath = substr($filePath, strlen($assetBase));
                }
            } catch (\Throwable $e) {
            }
        }

        $relativePath = ltrim($relativePath, '/');


        $fullPath = public_path($relativePath);
        if (file_exists($fullPath)) {
            unlink($fullPath);
            return true;
        }

        $storagePath = storage_path('app/public/' . $relativePath);
        if (file_exists($storagePath)) {
            unlink($storagePath);
            return true;
        }

        return false;
    }



    public static function getVideoDurationFormatted($relativePath)
    {
        if (! $relativePath) {
            return null;
        }

        $path = $relativePath;

        // If given a full URL, extract the path
        if (Str::startsWith($relativePath, ['http://', 'https://'])) {
            $parsed = parse_url($relativePath, PHP_URL_PATH);
            $path = ltrim($parsed, '/');
        }

        // Check public_path first
        $absolutePath = public_path($path);
        if (! file_exists($absolutePath)) {
            // Fallback to storage/app/public
            $absolutePath = storage_path('app/public/' . ltrim($path, '/'));
            if (! file_exists($absolutePath)) {
                return null;
            }
        }

        $getID3 = new getID3();
        $info = $getID3->analyze($absolutePath);

        if (!isset($info['playtime_seconds'])) {
            return null;
        }

        return gmdate("H:i:s", (int)$info['playtime_seconds']);
    }


    public static function uploadVideo($file, $folder)
    {
        if (!$file->isValid()) {
            return null;
        }

        $uniqueId  = uniqid();
        $videoName = Str::slug(time() . '-' . $uniqueId);
        $extension = $file->getClientOriginalExtension();

        $basePath = public_path("uploads/{$folder}/{$videoName}");

        if (!file_exists($basePath)) {
            mkdir($basePath, 0755, true);
        }

        $originalPath = "{$basePath}/original.{$extension}";
        $file->move($basePath, "original.{$extension}");

        $outputPlaylist = "{$basePath}/{$videoName}.m3u8";

        $cmd = "ffmpeg -i {$originalPath} -profile:v baseline -level 3.0 -start_number 0 -hls_time 5 -hls_list_size 0 -f hls {$outputPlaylist} -hide_banner -loglevel error";

        exec($cmd, $output, $returnCode);

        if ($returnCode !== 0) {
            Log::error('FFmpeg failed: ' . implode("\n", $output));
            return null;
        }

        return "uploads/{$folder}/{$videoName}/{$videoName}.m3u8";
    }
}
