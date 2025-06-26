<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsurePegawaiRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect('/pegawai/login');
        }

        $user = Auth::user();

        if ($user->role_user !== 'employee') {
            Auth::logout();
            return redirect('/pegawai/login')->with('error', 'Akses ditolak. Anda tidak memiliki izin sebagai pegawai.');
        }

        return $next($request);
    }
}
