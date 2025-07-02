<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class CleanupUploadFiles extends Command
{
    protected $signature = 'uploads:cleanup';
    protected $description = 'Clean up problematic upload files and setup directories';

    public function handle()
    {
        $this->info('Starting upload cleanup...');

        // Clear livewire temporary files
        $livewireTmpPath = storage_path('app/livewire-tmp');
        if (File::exists($livewireTmpPath)) {
            File::cleanDirectory($livewireTmpPath);
            $this->info('Cleared livewire-tmp directory');
        }

        // Ensure required directories exist
        $directories = [
            'storage/app/public/izin-documents',
            'storage/app/livewire-tmp',
            'storage/app/public/attendance'
        ];

        foreach ($directories as $dir) {
            if (!File::exists($dir)) {
                File::makeDirectory($dir, 0755, true);
                $this->info("Created directory: {$dir}");
            }
        }

        // Fix permissions
        File::chmod(storage_path('app'), 0755);
        File::chmod(storage_path('app/public'), 0755);

        $this->info('Upload cleanup completed successfully!');
        return 0;
    }
}
