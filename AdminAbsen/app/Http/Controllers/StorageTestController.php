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

    /**
     * Test khusus untuk gambar attendance di Railway
     */
    public function testAttendanceImages()
    {
        $results = [];

        // Test 1: Cek direktori attendance
        $attendanceDir = 'attendance';
        $results['attendance_dir_exists'] = Storage::disk('public')->exists($attendanceDir);

        if (!$results['attendance_dir_exists']) {
            try {
                Storage::disk('public')->makeDirectory($attendanceDir);
                $results['attendance_dir_created'] = Storage::disk('public')->exists($attendanceDir);
            } catch (\Exception $e) {
                $results['attendance_dir_error'] = $e->getMessage();
            }
        }

        // Test 2: Cek gambar attendance yang ada
        try {
            $existingFiles = Storage::disk('public')->allFiles($attendanceDir);
            $results['existing_files_count'] = count($existingFiles);
            $results['sample_files'] = array_slice($existingFiles, 0, 5); // Sample 5 files
        } catch (\Exception $e) {
            $results['list_files_error'] = $e->getMessage();
        }

        // Test 3: Test upload gambar attendance baru
        try {
            $testImageName = 'test-attendance-' . time() . '.jpg';
            $testImagePath = $attendanceDir . '/' . $testImageName;

            // Create dummy image data (1x1 pixel JPEG)
            $imageData = base64_decode('/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQH/2wBDAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQEBAQH/wAARCAABAAEDASIAAhEBAxEB/8QAFQABAQAAAAAAAAAAAAAAAAAAAAv/xAAUEAEAAAAAAAAAAAAAAAAAAAAA/8QAFQEBAQAAAAAAAAAAAAAAAAAAAAX/xAAUEQEAAAAAAAAAAAAAAAAAAAAA/9oADAMBAAIRAxEAPwA/wA==');

            Storage::disk('public')->put($testImagePath, $imageData);
            $results['test_image_uploaded'] = Storage::disk('public')->exists($testImagePath);

            if ($results['test_image_uploaded']) {
                // Test URL generation
                $results['test_image_url'] = asset('storage/' . $testImagePath);
                $results['test_image_size'] = Storage::disk('public')->size($testImagePath);

                // Test web accessibility
                $webPath = public_path('storage/' . $testImagePath);
                $results['test_image_web_accessible'] = file_exists($webPath);

                // Cleanup
                Storage::disk('public')->delete($testImagePath);
                $results['test_image_cleaned'] = !Storage::disk('public')->exists($testImagePath);
            }

        } catch (\Exception $e) {
            $results['test_upload_error'] = $e->getMessage();
        }

        // Test 4: Cek attendance records yang ada
        try {
            $attendanceRecords = \App\Models\Attendance::whereNotNull('picture_absen_masuk')
                ->orWhereNotNull('picture_absen_siang')
                ->orWhereNotNull('picture_absen_pulang')
                ->limit(5)
                ->get(['id', 'picture_absen_masuk', 'picture_absen_siang', 'picture_absen_pulang']);

            $results['attendance_records_count'] = $attendanceRecords->count();

            $imageStatus = [];
            foreach ($attendanceRecords as $record) {
                $recordStatus = ['id' => $record->id];

                if ($record->picture_absen_masuk) {
                    $recordStatus['masuk_exists'] = Storage::disk('public')->exists($record->picture_absen_masuk);
                    $recordStatus['masuk_url'] = $record->picture_absen_masuk_url;
                }

                if ($record->picture_absen_siang) {
                    $recordStatus['siang_exists'] = Storage::disk('public')->exists($record->picture_absen_siang);
                    $recordStatus['siang_url'] = $record->picture_absen_siang_url;
                }

                if ($record->picture_absen_pulang) {
                    $recordStatus['pulang_exists'] = Storage::disk('public')->exists($record->picture_absen_pulang);
                    $recordStatus['pulang_url'] = $record->picture_absen_pulang_url;
                }

                $imageStatus[] = $recordStatus;
            }

            $results['attendance_images_status'] = $imageStatus;

        } catch (\Exception $e) {
            $results['attendance_records_error'] = $e->getMessage();
        }

        // Test 5: Storage configuration
        $results['storage_config'] = [
            'default_disk' => config('filesystems.default'),
            'public_disk_root' => config('filesystems.disks.public.root'),
            'public_disk_url' => config('filesystems.disks.public.url'),
            'app_url' => config('app.url'),
            'asset_url' => config('app.asset_url'),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Attendance images test completed',
            'results' => $results,
            'timestamp' => now(),
            'environment' => app()->environment(),
        ]);
    }
}
