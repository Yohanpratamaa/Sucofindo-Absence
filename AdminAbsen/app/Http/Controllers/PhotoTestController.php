<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PhotoTestController extends Controller
{
    public function testPhoto(Request $request)
    {
        try {
            // Create a test image (simple base64 encoded image)
            $testImageData = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';
            
            // Decode and save
            $imageData = base64_decode($testImageData);
            $filename = 'attendance/test_' . Carbon::now()->format('Y-m-d_H-i-s') . '.png';
            
            // Ensure directory exists
            if (!Storage::disk('public')->exists('attendance')) {
                Storage::disk('public')->makeDirectory('attendance');
            }
            
            $saved = Storage::disk('public')->put($filename, $imageData);
            
            if ($saved) {
                $url = asset('storage/' . $filename);
                return response()->json([
                    'success' => true,
                    'message' => 'Test photo saved successfully',
                    'filename' => $filename,
                    'url' => $url,
                    'full_path' => storage_path('app/public/' . $filename)
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to save test photo'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Test photo error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }
}
