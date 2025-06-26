<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class FilamentUnifiedAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = Auth::user();

        // Validate user object integrity
        if (!$this->validateUserIntegrity($user)) {
            // Simple logout without complex session clearing
            Auth::logout();
            return redirect()->route('login')
                ->with('error', 'Session tidak valid. Silakan login kembali.');
        }

        return $next($request);
    }

    protected function validateUserIntegrity($user): bool
    {
        if (!$user) {
            return false;
        }

        // Check if user has required attributes
        if (!isset($user->role_user) || empty($user->role_user)) {
            return false;
        }

        // Check if user has basic required attributes
        if (!isset($user->nama) && !isset($user->email) && !isset($user->npp)) {
            return false;
        }

        // Check if user status is active
        if (isset($user->status) && $user->status !== 'active') {
            return false;
        }

        return true;
    }
}
