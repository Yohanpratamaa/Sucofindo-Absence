<?php

namespace App\Services;

use App\Models\Pegawai;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserRoleService
{
    /**
     * Create user account based on role
     */
    public static function createUserBasedOnRole(Pegawai $pegawai): void
    {
        // Ensure user is active and has proper authentication details
        if ($pegawai->status === 'active') {

            // Set default password if not provided
            if (empty($pegawai->password)) {
                $pegawai->password = Hash::make('password123');
            }

            // Ensure email is present and unique
            if (empty($pegawai->email)) {
                $pegawai->email = strtolower($pegawai->npp) . '@sucofindo.com';
            }

            // Log the user creation based on role
            Log::info("User account created for {$pegawai->nama} with role: {$pegawai->role_user}");

            // Additional role-specific setup can be added here
            switch ($pegawai->role_user) {
                case 'employee':
                    self::setupEmployeeAccount($pegawai);
                    break;
                case 'Kepala Bidang':
                    self::setupKepalaBidangAccount($pegawai);
                    break;
                case 'super admin':
                    self::setupAdminAccount($pegawai);
                    break;
            }
        }
    }

    /**
     * Setup employee account specific configurations
     */
    protected static function setupEmployeeAccount(Pegawai $pegawai): void
    {
        Log::info("Employee account setup completed for {$pegawai->nama}");
        // Add employee-specific setup here
        // For example: assign default permissions, create profile, etc.
    }

    /**
     * Setup kepala bidang account specific configurations
     */
    protected static function setupKepalaBidangAccount(Pegawai $pegawai): void
    {
        Log::info("Kepala Bidang account setup completed for {$pegawai->nama}");
        // Add kepala bidang-specific setup here
        // For example: assign approval permissions, team management, etc.
    }

    /**
     * Setup admin account specific configurations
     */
    protected static function setupAdminAccount(Pegawai $pegawai): void
    {
        Log::info("Admin account setup completed for {$pegawai->nama}");
        // Add admin-specific setup here
        // For example: assign all permissions, system management, etc.
    }

    /**
     * Get redirect URL based on user role
     */
    public static function getRedirectUrlByRole(string $role): string
    {
        return match ($role) {
            'employee' => '/pegawai',
            'Kepala Bidang' => '/kepala-bidang',
            'super admin' => '/admin',
            default => '/admin',
        };
    }

    /**
     * Get panel ID based on user role
     */
    public static function getPanelIdByRole(string $role): string
    {
        return match ($role) {
            'employee' => 'pegawai',
            'Kepala Bidang' => 'kepala-bidang',
            'super admin' => 'admin',
            default => 'admin',
        };
    }
}
