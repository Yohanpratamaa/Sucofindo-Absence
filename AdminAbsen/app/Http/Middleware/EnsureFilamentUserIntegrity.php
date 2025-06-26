<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureFilamentUserIntegrity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Validate user object integrity
            if (!$this->validateUserIntegrity($user)) {
                Auth::logout();
                return redirect()->route('filament.admin.auth.login')
                    ->with('error', 'Session expired. Please login again.');
            }
        }

        return $next($request);
    }

    protected function validateUserIntegrity($user): bool
    {
        if (!$user) {
            return false;
        }

        // Check if user has required methods
        if (!method_exists($user, 'getUserName')) {
            return false;
        }

        // Validate getUserName returns string
        try {
            $userName = $user->getUserName();
            if (!is_string($userName)) {
                return false;
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('User integrity check failed: ' . $e->getMessage());
            return false;
        }

        // Check if user has required attributes
        if (!isset($user->role_user) || empty($user->role_user)) {
            return false;
        }

        return true;
    }
}
