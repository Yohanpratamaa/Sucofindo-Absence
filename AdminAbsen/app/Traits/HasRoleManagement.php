<?php

namespace App\Traits;

trait HasRoleManagement
{
    /**
     * Check if the user is a super admin
     *
     * @return bool
     */
    public function isSuperAdmin(): bool
    {
        // Ensure we have a role_user field and it's not null
        if (!isset($this->role_user) || $this->role_user === null) {
            return false;
        }

        return strtolower(trim($this->role_user)) === 'super admin';
    }

    /**
     * Check if the user can approve izin/attendance
     *
     * @return bool
     */
    public function canApprove(): bool
    {
        return !$this->isSuperAdmin();
    }

    /**
     * Check if the user is an admin (any kind)
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        if (!isset($this->role_user) || $this->role_user === null) {
            return false;
        }

        $role = strtolower(trim($this->role_user));
        return in_array($role, ['super admin', 'admin', 'kepala bidang']);
    }

    /**
     * Check if the user is a regular employee
     *
     * @return bool
     */
    public function isPegawai(): bool
    {
        if (!isset($this->role_user) || $this->role_user === null) {
            return false;
        }

        $role = strtolower(trim($this->role_user));
        return $role === 'pegawai';
    }

    /**
     * Check if the user is a kepala bidang
     *
     * @return bool
     */
    public function isKepalaBidang(): bool
    {
        if (!isset($this->role_user) || $this->role_user === null) {
            return false;
        }

        $role = strtolower(trim($this->role_user));
        return $role === 'kepala bidang';
    }

    /**
     * Get user role display name
     *
     * @return string
     */
    public function getRoleDisplayName(): string
    {
        if (!isset($this->role_user) || $this->role_user === null) {
            return 'Unknown';
        }

        return match (strtolower(trim($this->role_user))) {
            'super admin' => 'Super Admin',
            'admin' => 'Admin',
            'kepala bidang' => 'Kepala Bidang',
            'pegawai' => 'Pegawai',
            default => ucfirst($this->role_user)
        };
    }

    /**
     * Check if user has specific role
     *
     * @param string $role
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        if (!isset($this->role_user) || $this->role_user === null) {
            return false;
        }

        return strtolower(trim($this->role_user)) === strtolower(trim($role));
    }

    /**
     * Check if user has any of the specified roles
     *
     * @param array $roles
     * @return bool
     */
    public function hasAnyRole(array $roles): bool
    {
        if (!isset($this->role_user) || $this->role_user === null) {
            return false;
        }

        $userRole = strtolower(trim($this->role_user));
        $normalizedRoles = array_map(fn($role) => strtolower(trim($role)), $roles);

        return in_array($userRole, $normalizedRoles);
    }
}
