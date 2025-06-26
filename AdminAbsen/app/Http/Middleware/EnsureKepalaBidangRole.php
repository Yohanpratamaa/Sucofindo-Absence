<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureKepalaBidangRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        if ($user->role_user !== 'Kepala Bidang') {
            // Don't logout, just redirect to avoid session conflicts
            return redirect()->route('login')->with('error', 'Akses ditolak. Anda tidak memiliki izin sebagai kepala bidang.');
        }

        return $next($request);
    }
}
