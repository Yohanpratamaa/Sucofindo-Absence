<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class HandleFileUploadErrors
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            return $next($request);
        } catch (\Exception $e) {
            // Check if it's a file upload related error
            if (str_contains($e->getMessage(), 'file_size') ||
                str_contains($e->getMessage(), 'livewire-tmp')) {

                Log::warning('File upload error caught by middleware', [
                    'error' => $e->getMessage(),
                    'url' => $request->url(),
                    'method' => $request->method()
                ]);

                // Clean problematic files
                $this->cleanupProblematicFiles();

                // Return a user-friendly response
                if ($request->wantsJson()) {
                    return response()->json([
                        'message' => 'Terjadi masalah dengan upload file. Silakan coba lagi.'
                    ], 400);
                }

                return back()->with('error', 'Terjadi masalah dengan upload file. Silakan coba lagi.');
            }

            throw $e;
        }
    }

    private function cleanupProblematicFiles(): void
    {
        try {
            $tempPath = storage_path('app/livewire-tmp');
            if (is_dir($tempPath)) {
                $files = glob($tempPath . '/*');
                foreach ($files as $file) {
                    if (is_file($file) && time() - filemtime($file) > 300) { // 5 minutes old
                        unlink($file);
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Error cleaning up problematic files', ['error' => $e->getMessage()]);
        }
    }
}
