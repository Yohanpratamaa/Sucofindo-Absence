<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pegawai;
use App\Helpers\UserHelper;

class TestRoleManagement extends Command
{
    protected $signature = 'test:roles';
    protected $description = 'Test role management functionality';

    public function handle()
    {
        $this->info('Testing Role Management Functionality...');

        // Test with different roles
        $roles = [
            'super admin',
            'admin',
            'kepala bidang',
            'pegawai',
            null
        ];

        foreach ($roles as $role) {
            $this->testRole($role);
        }

        $this->info('Testing UserHelper methods...');
        $this->testUserHelper();

        $this->info('All tests completed!');
    }

    private function testRole($role)
    {
        $this->line("\n--- Testing Role: " . ($role ?? 'NULL') . " ---");

        $user = new Pegawai(['role_user' => $role, 'nama' => 'Test User']);

        try {
            $this->line("isSuperAdmin(): " . ($user->isSuperAdmin() ? 'true' : 'false'));
            $this->line("canApprove(): " . ($user->canApprove() ? 'true' : 'false'));
            $this->line("isAdmin(): " . ($user->isAdmin() ? 'true' : 'false'));
            $this->line("isPegawai(): " . ($user->isPegawai() ? 'true' : 'false'));
            $this->line("isKepalaBidang(): " . ($user->isKepalaBidang() ? 'true' : 'false'));
            $this->line("getRoleDisplayName(): " . $user->getRoleDisplayName());

            // Test specific role checks
            $this->line("hasRole('admin'): " . ($user->hasRole('admin') ? 'true' : 'false'));
            $this->line("hasAnyRole(['admin', 'kepala bidang']): " . ($user->hasAnyRole(['admin', 'kepala bidang']) ? 'true' : 'false'));

            $this->info("✅ Role '$role' tests passed");
        } catch (\Exception $e) {
            $this->error("❌ Role '$role' tests failed: " . $e->getMessage());
        }
    }

    private function testUserHelper()
    {
        $this->line("\n--- Testing UserHelper ---");

        try {
            $this->line("getCurrentUser(): " . (UserHelper::getCurrentUser() ? 'Found' : 'Not found'));
            $this->line("isSuperAdmin(): " . (UserHelper::isSuperAdmin() ? 'true' : 'false'));
            $this->line("canApprove(): " . (UserHelper::canApprove() ? 'true' : 'false'));
            $this->line("getDisplayName(): " . UserHelper::getDisplayName());
            $this->line("getRoleDisplayName(): " . UserHelper::getRoleDisplayName());

            // Test validation
            $validation = UserHelper::validateForApproval();
            $this->line("validateForApproval():");
            $this->line("  - success: " . ($validation['success'] ? 'true' : 'false'));
            $this->line("  - message: " . $validation['message']);
            $this->line("  - user: " . ($validation['user'] ? 'Found' : 'Not found'));

            $this->info("✅ UserHelper tests passed");
        } catch (\Exception $e) {
            $this->error("❌ UserHelper tests failed: " . $e->getMessage());
        }
    }
}
