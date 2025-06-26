<?php

namespace App\Observers;

use App\Models\Pegawai;
use App\Services\UserRoleService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class PegawaiObserver
{
    /**
     * Handle the Pegawai "created" event.
     */
    public function created(Pegawai $pegawai): void
    {
        // Setup user account based on role
        UserRoleService::createUserBasedOnRole($pegawai);
    }

    /**
     * Handle the Pegawai "creating" event.
     */
    public function creating(Pegawai $pegawai): void
    {
        // Set default password jika tidak ada
        if (empty($pegawai->password)) {
            $pegawai->password = 'password123'; // Will be hashed by mutator
        }

        // Set default status jika tidak ada
        if (empty($pegawai->status)) {
            $pegawai->status = 'active';
        }

        // Ensure email is unique and formatted correctly
        if (!$pegawai->email) {
            $pegawai->email = strtolower($pegawai->npp) . '@sucofindo.com';
        }
    }

    /**
     * Handle the Pegawai "updated" event.
     */
    public function updated(Pegawai $pegawai): void
    {
        // Log role changes if needed
        if ($pegawai->wasChanged('role_user')) {
            Log::info("Pegawai {$pegawai->nama} (ID: {$pegawai->id}) role changed from {$pegawai->getOriginal('role_user')} to {$pegawai->role_user}");

            // Re-setup account based on new role
            UserRoleService::createUserBasedOnRole($pegawai);
        }
    }

    /**
     * Handle the Pegawai "deleted" event.
     */
    public function deleted(Pegawai $pegawai): void
    {
        Log::info("Pegawai {$pegawai->nama} (ID: {$pegawai->id}) account deleted");
    }

    /**
     * Handle the Pegawai "restored" event.
     */
    public function restored(Pegawai $pegawai): void
    {
        Log::info("Pegawai {$pegawai->nama} (ID: {$pegawai->id}) account restored");
    }

    /**
     * Handle the Pegawai "force deleted" event.
     */
    public function forceDeleted(Pegawai $pegawai): void
    {
        Log::info("Pegawai {$pegawai->nama} (ID: {$pegawai->id}) account permanently deleted");
    }
}
