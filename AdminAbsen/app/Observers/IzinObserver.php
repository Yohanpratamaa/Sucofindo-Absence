<?php

namespace App\Observers;

use App\Models\Izin;
use App\Services\IzinAttendanceService;
use Illuminate\Support\Facades\Log;

class IzinObserver
{
    protected $izinAttendanceService;

    public function __construct(IzinAttendanceService $izinAttendanceService)
    {
        $this->izinAttendanceService = $izinAttendanceService;
    }

    /**
     * Handle the Izin "created" event.
     */
    public function created(Izin $izin): void
    {
        try {
            // Jika izin langsung dibuat dengan status approved, create attendance
            if ($izin->status === 'approved') {
                $this->izinAttendanceService->createAttendanceForApprovedIzin($izin);
                Log::info("Attendance created for newly approved izin ID: {$izin->id}");
            }
        } catch (\Exception $e) {
            Log::error("Error in IzinObserver::created for izin ID {$izin->id}: " . $e->getMessage());
        }
    }

    /**
     * Handle the Izin "updated" event.
     */
    public function updated(Izin $izin): void
    {
        try {
            // Cek apakah status berubah menjadi approved
            if ($izin->wasChanged(['approved_by', 'approved_at']) && $izin->status === 'approved') {
                $this->izinAttendanceService->createAttendanceForApprovedIzin($izin);
                Log::info("Attendance created for approved izin ID: {$izin->id}");
            }

            // Cek apakah izin ditolak (approved_by ada tapi approved_at null)
            elseif ($izin->wasChanged(['approved_by']) && $izin->approved_by && !$izin->approved_at) {
                $this->izinAttendanceService->deleteAttendanceForRejectedIzin($izin);
                Log::info("Attendance deleted for rejected izin ID: {$izin->id}");
            }

            // Cek apakah tanggal izin berubah untuk izin yang sudah approved
            elseif ($izin->wasChanged(['tanggal_mulai', 'tanggal_akhir']) && $izin->status === 'approved') {
                $this->izinAttendanceService->updateAttendanceForModifiedIzin($izin);
                Log::info("Attendance updated for modified izin ID: {$izin->id}");
            }
        } catch (\Exception $e) {
            Log::error("Error in IzinObserver::updated for izin ID {$izin->id}: " . $e->getMessage());
        }
    }

    /**
     * Handle the Izin "deleted" event.
     */
    public function deleted(Izin $izin): void
    {
        try {
            $this->izinAttendanceService->deleteAttendanceForRejectedIzin($izin);
            Log::info("Attendance deleted for deleted izin ID: {$izin->id}");
        } catch (\Exception $e) {
            Log::error("Error in IzinObserver::deleted for izin ID {$izin->id}: " . $e->getMessage());
        }
    }

    /**
     * Handle the Izin "restored" event.
     */
    public function restored(Izin $izin): void
    {
        try {
            if ($izin->status === 'approved') {
                $this->izinAttendanceService->createAttendanceForApprovedIzin($izin);
                Log::info("Attendance recreated for restored izin ID: {$izin->id}");
            }
        } catch (\Exception $e) {
            Log::error("Error in IzinObserver::restored for izin ID {$izin->id}: " . $e->getMessage());
        }
    }

    /**
     * Handle the Izin "force deleted" event.
     */
    public function forceDeleted(Izin $izin): void
    {
        try {
            $this->izinAttendanceService->deleteAttendanceForRejectedIzin($izin);
            Log::info("Attendance deleted for force deleted izin ID: {$izin->id}");
        } catch (\Exception $e) {
            Log::error("Error in IzinObserver::forceDeleted for izin ID {$izin->id}: " . $e->getMessage());
        }
    }
}
