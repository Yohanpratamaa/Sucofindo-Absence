<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ClearFilamentSessionData
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Clear any Filament-specific session data that might cause conflicts
        if (Auth::check()) {
            $this->clearPanelSpecificSession($request);
        }

        return $next($request);
    }

    /**
     * Clear panel-specific session data to prevent conflicts
     */
    protected function clearPanelSpecificSession(Request $request): void
    {
        // Only clear if session is active and has data
        if (!$request->hasSession() || !$request->session()->isStarted()) {
            return;
        }

        // Clear Filament-specific session keys that might cause conflicts
        $sessionKeysToForget = [
            'filament.admin',
            'filament.pegawai',
            'filament.kepala-bidang',
            'livewire',
        ];

        foreach ($sessionKeysToForget as $key) {
            if ($request->session()->has($key)) {
                $request->session()->forget($key);
            }
        }

        // Clear session keys that start with 'wire:' safely
        try {
            $sessionData = $request->session()->all();
            $keysToForget = [];

            foreach ($sessionData as $key => $value) {
                if (strpos($key, 'wire:') === 0) {
                    $keysToForget[] = $key;
                }
            }

            foreach ($keysToForget as $key) {
                $request->session()->forget($key);
            }
        } catch (\Exception $e) {
            // If session access fails, just continue
            \Illuminate\Support\Facades\Log::warning('Session clearing failed: ' . $e->getMessage());
        }
    }
}
