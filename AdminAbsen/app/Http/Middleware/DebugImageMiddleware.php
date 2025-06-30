<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DebugImageMiddleware
{
    /**
     * Handle an incoming request for debugging image access
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if this is a storage image request
        if ($request->is('storage/attendance/*')) {
            $path = str_replace('storage/', '', $request->path());

            Log::info('Image access attempt', [
                'path' => $path,
                'full_url' => $request->fullUrl(),
                'file_exists' => Storage::disk('public')->exists($path),
                'file_size' => Storage::disk('public')->exists($path) ? Storage::disk('public')->size($path) : 0,
                'user_agent' => $request->userAgent(),
            ]);

            // Check if file exists
            if (!Storage::disk('public')->exists($path)) {
                Log::warning('Image not found', ['path' => $path]);

                // Return a default image or 404
                return response()->file(public_path('images/no-image.png'));
            }
        }

        return $next($request);
    }
}
