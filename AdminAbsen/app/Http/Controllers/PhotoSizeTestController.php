<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PhotoSizeTestController extends Controller
{
    public function checkPhotoSize(Request $request)
    {
        $photoData = $request->input('photo_data');
        
        if (!$photoData) {
            return response()->json([
                'error' => 'No photo data provided'
            ]);
        }
        
        // Check original size
        $originalSize = strlen($photoData);
        
        // Remove base64 prefix
        $cleanedData = preg_replace('#^data:image/[^;]+;base64,#', '', $photoData);
        $cleanedSize = strlen($cleanedData);
        
        // Decode to get actual image size
        $imageData = base64_decode($cleanedData);
        $imageSize = $imageData ? strlen($imageData) : 0;
        
        return response()->json([
            'original_size' => $originalSize,
            'original_size_mb' => round($originalSize / 1024 / 1024, 2),
            'cleaned_size' => $cleanedSize,
            'cleaned_size_mb' => round($cleanedSize / 1024 / 1024, 2),
            'image_size' => $imageSize,
            'image_size_mb' => round($imageSize / 1024 / 1024, 2),
            'php_upload_limit' => ini_get('upload_max_filesize'),
            'php_post_limit' => ini_get('post_max_size'),
            'within_limits' => $imageSize < (2 * 1024 * 1024), // 2MB check
        ]);
    }
}
