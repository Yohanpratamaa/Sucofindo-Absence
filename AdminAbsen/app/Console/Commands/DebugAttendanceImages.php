<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Models\Attendance;
use App\Helpers\ImageHelper;

class DebugAttendanceImages extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'attendance:debug-images {--fix : Fix missing placeholder images}';

    /**
     * The console command description.
     */
    protected $description = 'Debug attendance images and check storage configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Debugging Attendance Images...');

        // Check storage configuration
        $this->checkStorageConfiguration();

        // Check storage link
        $this->checkStorageLink();

        // Check attendance directory
        $this->checkAttendanceDirectory();

        // Check attendance images
        $this->checkAttendanceImages();

        // Fix issues if requested
        if ($this->option('fix')) {
            $this->fixIssues();
        }

        $this->info('✅ Debug complete!');
    }

    private function checkStorageConfiguration()
    {
        $this->info('📋 Storage Configuration:');

        $config = config('filesystems.disks.public');
        $this->line("  Root: {$config['root']}");
        $this->line("  URL: {$config['url']}");
        $this->line("  Visibility: {$config['visibility']}");

        // Check if root directory exists
        if (is_dir($config['root'])) {
            $this->line("  ✅ Root directory exists");
        } else {
            $this->error("  ❌ Root directory does not exist!");
        }
    }

    private function checkStorageLink()
    {
        $this->info('🔗 Storage Link:');

        $publicStorage = public_path('storage');
        $storagePublic = storage_path('app/public');

        if (file_exists($publicStorage)) {
            if (is_dir($publicStorage)) {
                // Check if it's actually pointing to the correct location
                $realPath = realpath($publicStorage);
                $expectedPath = realpath($storagePublic);

                $this->line("  ✅ Storage directory/link exists");
                $this->line("  Path: {$publicStorage}");
                $this->line("  Resolves to: {$realPath}");
                $this->line("  Expected: {$expectedPath}");

                if ($realPath === $expectedPath) {
                    $this->line("  ✅ Link target is correct");
                } else {
                    $this->line("  ⚠️  Different path (may still work)");
                }

                // Test file access
                $testFiles = Storage::disk('public')->files('attendance');
                if (count($testFiles) > 0) {
                    $testFile = $testFiles[0];
                    $fullPath = $publicStorage . DIRECTORY_SEPARATOR . $testFile;
                    if (file_exists($fullPath)) {
                        $this->line("  ✅ File access test passed");
                    } else {
                        $this->error("  ❌ File access test failed");
                        $this->line("  Expected file: {$fullPath}");
                    }
                } else {
                    $this->line("  ℹ️  No test files available");
                }
            } else {
                $this->error("  ❌ Storage path exists but is not a directory!");
            }
        } else {
            $this->error("  ❌ Storage path does not exist!");
            $this->line("  Run: php artisan storage:link");
        }
    }

    private function checkAttendanceDirectory()
    {
        $this->info('📁 Attendance Directory:');

        $attendanceDir = 'attendance';

        if (Storage::disk('public')->exists($attendanceDir)) {
            $this->line("  ✅ Attendance directory exists");

            $files = Storage::disk('public')->files($attendanceDir);
            $this->line("  📄 Files count: " . count($files));

            if (count($files) > 0) {
                $this->line("  Sample files:");
                foreach (array_slice($files, 0, 5) as $file) {
                    $size = Storage::disk('public')->size($file);
                    $this->line("    - {$file} ({$size} bytes)");
                }

                if (count($files) > 5) {
                    $this->line("    ... and " . (count($files) - 5) . " more files");
                }
            }
        } else {
            $this->error("  ❌ Attendance directory does not exist!");
        }
    }

    private function checkAttendanceImages()
    {
        $this->info('🖼️  Attendance Images:');

        $attendances = Attendance::whereNotNull('picture_absen_masuk')
            ->orWhereNotNull('picture_absen_pulang')
            ->get();

        $this->line("  📊 Total attendance records with images: " . $attendances->count());

        $issues = ImageHelper::verifyAttendanceImages();

        if (empty($issues)) {
            $this->line("  ✅ All images found successfully");
        } else {
            $this->error("  ❌ Found " . count($issues) . " image issues:");

            foreach ($issues as $issue) {
                $this->line("    - Attendance ID {$issue['attendance_id']} ({$issue['type']}): {$issue['issue']}");
                $this->line("      Path: {$issue['path']}");
            }
        }
    }

    private function fixIssues()
    {
        $this->info('🛠️  Fixing Issues...');

        // Create placeholder image
        if (ImageHelper::createPlaceholderImage()) {
            $this->line("  ✅ Placeholder image created/verified");
        } else {
            $this->error("  ❌ Failed to create placeholder image");
        }

        // Create attendance directory if not exists
        if (!Storage::disk('public')->exists('attendance')) {
            Storage::disk('public')->makeDirectory('attendance');
            $this->line("  ✅ Attendance directory created");
        }

        // Create storage link if not exists
        $publicStorage = public_path('storage');
        if (!is_link($publicStorage)) {
            $this->call('storage:link');
            $this->line("  ✅ Storage link created");
        }
    }
}
