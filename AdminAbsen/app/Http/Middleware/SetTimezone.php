<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Date;

class SetTimezone
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Set default timezone untuk aplikasi
        date_default_timezone_set('Asia/Jakarta');

        // Set timezone untuk Carbon
        config(['app.timezone' => 'Asia/Jakarta']);

        return $next($request);
    }
}
