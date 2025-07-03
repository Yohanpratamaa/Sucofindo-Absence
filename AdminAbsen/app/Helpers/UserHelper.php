<?php

namespace App\Helpers;

use App\Models\Pegawai;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Filament\Facades\Filament;

class UserHelper
{
    /**
     * Safely get current authenticated user
     *
     * @return Pegawai|null
     */
    public static function getCurrentUser(): ?Pegawai
    {
        try {
            // Try Filament auth first
            $user = Filament::auth()->user();

            if ($user && $user instanceof Pegawai) {
                return $user;
            }

            // Fallback to default Auth
            $user = Auth::user();

            if ($user && $user instanceof Pegawai) {
                return $user;
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error getting current user: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Safely check if current user is super admin
     *
     * @return bool
     */
    public static function isSuperAdmin(): bool
    {
        $user = self::getCurrentUser();

        if (!$user) {
            return false;
        }

        try {
            return $user->isSuperAdmin();
        } catch (\Exception $e) {
            Log::error('Error checking super admin status: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Safely check if current user can approve
     *
     * @return bool
     */
    public static function canApprove(): bool
    {
        $user = self::getCurrentUser();

        if (!$user) {
            return false;
        }

        try {
            return $user->canApprove();
        } catch (\Exception $e) {
            Log::error('Error checking approval permission: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Safely check if current user has specific role
     *
     * @param string $role
     * @return bool
     */
    public static function hasRole(string $role): bool
    {
        $user = self::getCurrentUser();

        if (!$user) {
            return false;
        }

        try {
            return $user->hasRole($role);
        } catch (\Exception $e) {
            Log::error('Error checking user role: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Safely check if current user has any of the specified roles
     *
     * @param array $roles
     * @return bool
     */
    public static function hasAnyRole(array $roles): bool
    {
        $user = self::getCurrentUser();

        if (!$user) {
            return false;
        }

        try {
            return $user->hasAnyRole($roles);
        } catch (\Exception $e) {
            Log::error('Error checking user roles: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user display name safely
     *
     * @return string
     */
    public static function getDisplayName(): string
    {
        $user = self::getCurrentUser();

        if (!$user) {
            return 'Unknown User';
        }

        return $user->nama ?? $user->name ?? 'Unknown User';
    }

    /**
     * Get user role display name safely
     *
     * @return string
     */
    public static function getRoleDisplayName(): string
    {
        $user = self::getCurrentUser();

        if (!$user) {
            return 'Unknown Role';
        }

        try {
            return $user->getRoleDisplayName();
        } catch (\Exception $e) {
            Log::error('Error getting role display name: ' . $e->getMessage());
            return 'Unknown Role';
        }
    }

    /**
     * Validate user for approval actions
     *
     * @return array [success => bool, message => string, user => Pegawai|null]
     */
    public static function validateForApproval(): array
    {
        $user = self::getCurrentUser();

        if (!$user) {
            return [
                'success' => false,
                'message' => 'User tidak terautentikasi.',
                'user' => null
            ];
        }

        if (!method_exists($user, 'isSuperAdmin')) {
            return [
                'success' => false,
                'message' => 'Model user tidak memiliki method yang diperlukan.',
                'user' => $user
            ];
        }

        if ($user->isSuperAdmin()) {
            return [
                'success' => false,
                'message' => 'Super Admin tidak diperbolehkan melakukan approval/reject.',
                'user' => $user
            ];
        }

        return [
            'success' => true,
            'message' => 'User valid untuk melakukan approval.',
            'user' => $user
        ];
    }
}
