<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ImageHelper
{
    /**
     * Check if image exists and return proper URL
     */
    public static function getImageUrl(?string $path, string $disk = 'public'): ?string
    {
        if (!$path) {
            return null;
        }

        // Check if file exists
        if (Storage::disk($disk)->exists($path)) {
            if ($disk === 'public') {
                return asset('storage/' . $path);
            }
            return asset('storage/' . $path);
        }

        // Log missing image
        Log::warning('Image file not found', [
            'path' => $path,
            'disk' => $disk,
            'storage_path' => Storage::disk($disk)->path($path)
        ]);

        return null;
    }

    /**
     * Get image URL with fallback
     */
    public static function getImageUrlWithFallback(?string $path, string $fallback = '/images/no-image.png', string $disk = 'public'): string
    {
        $url = self::getImageUrl($path, $disk);
        return $url ?? url($fallback);
    }

    /**
     * Verify and fix attendance images
     */
    public static function verifyAttendanceImages(): array
    {
        $issues = [];
        $attendance = \App\Models\Attendance::whereNotNull('picture_absen_masuk')
            ->orWhereNotNull('picture_absen_pulang')
            ->get();

        foreach ($attendance as $record) {
            // Check check-in image
            if ($record->picture_absen_masuk && !Storage::disk('public')->exists($record->picture_absen_masuk)) {
                $issues[] = [
                    'attendance_id' => $record->id,
                    'type' => 'check_in',
                    'path' => $record->picture_absen_masuk,
                    'issue' => 'File not found'
                ];
            }

            // Check check-out image
            if ($record->picture_absen_pulang && !Storage::disk('public')->exists($record->picture_absen_pulang)) {
                $issues[] = [
                    'attendance_id' => $record->id,
                    'type' => 'check_out',
                    'path' => $record->picture_absen_pulang,
                    'issue' => 'File not found'
                ];
            }
        }

        return $issues;
    }

    /**
     * Create placeholder image if not exists
     */
    public static function createPlaceholderImage(): bool
    {
        $placeholderPath = public_path('images');
        
        if (!is_dir($placeholderPath)) {
            mkdir($placeholderPath, 0755, true);
        }

        $placeholderFile = $placeholderPath . '/no-image.png';
        
        if (!file_exists($placeholderFile)) {
            // Create a simple placeholder image
            $image = imagecreate(200, 200);
            $background = imagecolorallocate($image, 240, 240, 240);
            $textColor = imagecolorallocate($image, 100, 100, 100);
            
            imagefill($image, 0, 0, $background);
            imagestring($image, 5, 50, 90, 'No Image', $textColor);
            
            $result = imagepng($image, $placeholderFile);
            imagedestroy($image);
            
            return $result;
        }

        return true;
    }
}
