<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Izin;
use App\Models\Pegawai;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IzinAttendanceService
{
    /**
     * Create attendance records for approved izin
     */
    public function createAttendanceForApprovedIzin(Izin $izin): void
    {
        try {
            DB::transaction(function () use ($izin) {
                // Hapus attendance yang sudah ada untuk periode izin ini jika ada
                $this->deleteExistingAttendanceForIzin($izin);

                // Buat attendance baru untuk setiap hari dalam periode izin
                $this->createAttendanceRecords($izin);
            });

            Log::info("Attendance records created for izin ID: {$izin->id}");
        } catch (\Exception $e) {
            Log::error("Failed to create attendance for izin ID: {$izin->id}. Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete attendance records for rejected or deleted izin
     */
    public function deleteAttendanceForRejectedIzin(Izin $izin): void
    {
        try {
            $deletedCount = Attendance::where('izin_id', $izin->id)->delete();
            Log::info("Deleted {$deletedCount} attendance records for rejected/deleted izin ID: {$izin->id}");
        } catch (\Exception $e) {
            Log::error("Failed to delete attendance for izin ID: {$izin->id}. Error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update attendance records when izin is modified
     */
    public function updateAttendanceForModifiedIzin(Izin $izin): void
    {
        if ($izin->status === 'approved' && $izin->approved_at && $izin->approved_by) {
            $this->createAttendanceForApprovedIzin($izin);
        } else {
            $this->deleteAttendanceForRejectedIzin($izin);
        }
    }

    /**
     * Delete existing attendance records for the izin period
     */
    private function deleteExistingAttendanceForIzin(Izin $izin): void
    {
        Attendance::where('izin_id', $izin->id)->delete();
    }

    /**
     * Create attendance records for each day in the izin period
     */
    private function createAttendanceRecords(Izin $izin): void
    {
        $startDate = Carbon::parse($izin->tanggal_mulai)->startOfDay();
        $endDate = Carbon::parse($izin->tanggal_akhir)->endOfDay();

        // Create period untuk setiap hari dalam rentang izin
        $period = CarbonPeriod::create($startDate, '1 day', $endDate);

        foreach ($period as $date) {
            // Skip weekends (optional - bisa disesuaikan dengan kebijakan perusahaan)
            if ($this->shouldSkipWeekend($date)) {
                continue;
            }

            $this->createSingleAttendanceRecord($izin, $date);
        }
    }

    /**
     * Create single attendance record for specific date
     */
    private function createSingleAttendanceRecord(Izin $izin, Carbon $date): void
    {
        // Cek apakah sudah ada attendance untuk hari ini (non-izin)
        $existingAttendance = Attendance::where('user_id', $izin->user_id)
            ->whereDate('created_at', $date->toDateString())
            ->whereNull('izin_id')
            ->first();

        // Jika sudah ada attendance normal, skip
        if ($existingAttendance && ($existingAttendance->check_in || $existingAttendance->check_out)) {
            return;
        }

        // Hapus attendance kosong jika ada
        if ($existingAttendance) {
            $existingAttendance->delete();
        }

        // Tentukan status berdasarkan jenis izin
        $statusKehadiran = $this->getStatusFromJenisIzin($izin->jenis_izin);

        // Buat keterangan izin
        $keteranganIzin = $this->buildKeteranganIzin($izin);

        // Buat record attendance
        Attendance::create([
            'user_id' => $izin->user_id,
            'office_working_hours_id' => $this->getDefaultOfficeWorkingHoursId($izin->user_id),
            'check_in' => null,
            'check_out' => null,
            'attendance_type' => 'WFO',
            'izin_id' => $izin->id,
            'status_kehadiran' => $statusKehadiran,
            'keterangan_izin' => $keteranganIzin,
            'created_at' => $date,
            'updated_at' => $date,
        ]);
    }

    /**
     * Get status kehadiran based on jenis izin
     */
    private function getStatusFromJenisIzin(string $jenisIzin): string
    {
        return match (strtolower($jenisIzin)) {
            'sakit' => 'Sakit',
            'cuti' => 'Cuti',
            default => 'Izin'
        };
    }

    /**
     * Build keterangan izin text
     */
    private function buildKeteranganIzin(Izin $izin): string
    {
        $keterangan = "Izin {$izin->jenis_izin}";

        if ($izin->keterangan) {
            $keterangan .= " - " . $izin->keterangan;
        }

        if ($izin->durasi_hari > 1) {
            $keterangan .= " ({$izin->durasi_hari} hari)";
        }

        return $keterangan;
    }

    /**
     * Check if should skip weekend
     */
    private function shouldSkipWeekend(Carbon $date): bool
    {
        // Skip Saturday (6) and Sunday (0)
        // Bisa disesuaikan dengan kebijakan perusahaan
        return in_array($date->dayOfWeek, [0, 6]);
    }

    /**
     * Get default office working hours ID for user
     */
    private function getDefaultOfficeWorkingHoursId(int $userId): ?int
    {
        // Ambil dari user atau gunakan default
        $user = Pegawai::find($userId);

        // Bisa disesuaikan dengan logic bisnis
        // Untuk sementara return 1 sebagai default
        return 1;
    }

    /**
     * Check if date has existing valid attendance
     */
    private function hasValidAttendanceForDate(int $userId, Carbon $date): bool
    {
        return Attendance::where('user_id', $userId)
            ->whereDate('created_at', $date->toDateString())
            ->where(function ($query) {
                $query->whereNotNull('check_in')
                    ->orWhereNotNull('check_out')
                    ->orWhereNotNull('izin_id');
            })
            ->exists();
    }
}
