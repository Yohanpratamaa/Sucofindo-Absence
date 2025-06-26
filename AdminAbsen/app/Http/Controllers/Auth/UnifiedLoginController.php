<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Pegawai;
use App\Services\UserRoleService;

class UnifiedLoginController extends Controller
{
    /**
     * Show the unified login form
     */
    public function showLoginForm()
    {
        // If already authenticated, redirect to appropriate dashboard
        if (Auth::check()) {
            return $this->redirectToDashboard(Auth::user());
        }

        return view('auth.unified-login');
    }

    /**
     * Handle unified login attempt
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Find user by email
        $user = Pegawai::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'Email tidak ditemukan.',
            ])->withInput($request->only('email'));
        }

        // Check if user is active
        if ($user->status !== 'active') {
            return back()->withErrors([
                'email' => 'Akun Anda tidak aktif. Silakan hubungi administrator.',
            ])->withInput($request->only('email'));
        }

        // Verify password
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'password' => 'Password salah.',
            ])->withInput($request->only('email'));
        }

        // Login the user
        Auth::login($user, $request->boolean('remember'));

        // Regenerate session for security
        $request->session()->regenerate();
        
        // Clear any previous panel-specific session data
        $request->session()->forget([
            'filament',
            'livewire',
            'url.intended',
            '_previous'
        ]);

        // Redirect to appropriate dashboard based on role
        return $this->redirectToDashboard($user);
    }

    /**
     * Redirect user to appropriate dashboard based on role
     */
    protected function redirectToDashboard($user)
    {
        $redirectUrl = UserRoleService::getRedirectUrlByRole($user->role_user);

        return redirect()->intended($redirectUrl)->with('success', 'Login berhasil! Selamat datang, ' . $user->nama);
    }

    /**
     * Handle logout from any panel
     */
    public function logout(Request $request)
    {
        // Store user info for logging before logout
        $user = Auth::user();
        $userName = $user ? ($user->nama ?? 'Unknown') : 'Unknown';
        
        // Clear Filament specific session data before logout
        $filamentKeys = [];
        foreach ($request->session()->all() as $key => $value) {
            if (strpos($key, 'filament') !== false || 
                strpos($key, 'livewire') !== false || 
                strpos($key, 'wire:') !== false) {
                $filamentKeys[] = $key;
            }
        }
        
        foreach ($filamentKeys as $key) {
            $request->session()->forget($key);
        }
        
        // Logout user
        Auth::logout();

        // Invalidate the session
        $request->session()->invalidate();
        
        // Regenerate CSRF token
        $request->session()->regenerateToken();

        \Illuminate\Support\Facades\Log::info("User {$userName} logged out successfully");
        
        return redirect('/login')
            ->with('success', 'Logout berhasil. Silakan login kembali.');
    }
}
