<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StorageTestController extends Controller
{
    /**
     * Test storage link dan file upload untuk Railway
     */
    public function testStorage()
    {
        $results = [];

        // Test 1: Cek storage link
        $results['storage_link_exists'] = is_link(public_path('storage'));
        $results['storage_link_target'] = $results['storage_link_exists'] ? readlink(public_path('storage')) : null;

        // Test 2: Cek writable storage
        $results['storage_writable'] = is_writable(storage_path('app/public'));

        // Test 3: Test create file
        try {
            $testFileName = 'test-' . Str::random(10) . '.txt';
            $testContent = 'Railway storage test - ' . now();

            Storage::disk('public')->put($testFileName, $testContent);
            $results['file_created'] = Storage::disk('public')->exists($testFileName);

            // Test 4: Test file URL
            if ($results['file_created']) {
                $results['file_url'] = asset('storage/' . $testFileName);
                $results['file_path'] = Storage::disk('public')->path($testFileName);

                // Test 5: Test file accessible via web
                $results['file_web_accessible'] = file_exists(public_path('storage/' . $testFileName));

                // Cleanup test file
                Storage::disk('public')->delete($testFileName);
                $results['file_cleaned'] = !Storage::disk('public')->exists($testFileName);
            }

        } catch (\Exception $e) {
            $results['file_creation_error'] = $e->getMessage();
        }

        // Test 6: Disk configuration
        $results['default_disk'] = config('filesystems.default');
        $results['public_disk_config'] = config('filesystems.disks.public');

        // Test 7: Environment variables
        $results['app_url'] = config('app.url');
        $results['filesystem_disk'] = config('filesystems.default');

        // Test 8: Directory structure
        $results['storage_directories'] = [
            'storage/app/public' => is_dir(storage_path('app/public')),
            'storage/app/public/uploads' => is_dir(storage_path('app/public/uploads')),
            'storage/app/public/images' => is_dir(storage_path('app/public/images')),
            'storage/app/public/avatars' => is_dir(storage_path('app/public/avatars')),
            'public/storage' => file_exists(public_path('storage')),
        ];

        return response()->json([
            'status' => 'Storage Test Results',
            'timestamp' => now(),
            'environment' => app()->environment(),
            'results' => $results
        ], 200, [], JSON_PRETTY_PRINT);
    }

    /**
     * Test upload file sederhana
     */
    public function testUpload(Request $request)
    {
        if (!$request->hasFile('file')) {
            return response()->json([
                'error' => 'No file uploaded. Use POST with file field.'
            ], 400);
        }

        try {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();

            // Store file
            $path = $file->storeAs('uploads', $fileName, 'public');

            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'file_name' => $fileName,
                'stored_path' => $path,
                'file_url' => asset('storage/' . $path),
                'file_size' => $file->getSize(),
                'file_type' => $file->getMimeType(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
