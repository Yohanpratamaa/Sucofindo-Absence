<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\UserRoleService;
use Symfony\Component\HttpFoundation\Response;

class RedirectBasedOnRole
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
            $currentPath = $request->getPathInfo();
            
            // Get expected panel path for user role
            $expectedPanel = UserRoleService::getPanelIdByRole($user->role_user);
            $expectedPath = '/' . $expectedPanel;
            
            // If user is not on their correct panel
            if (!str_starts_with($currentPath, $expectedPath) && !str_starts_with($currentPath, '/logout')) {
                $redirectUrl = UserRoleService::getRedirectUrlByRole($user->role_user);
                return redirect($redirectUrl);
            }
        }

        return $next($request);
    }
}
