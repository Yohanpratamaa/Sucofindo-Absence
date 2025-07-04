<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Attendance;
use App\Models\Pegawai;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CreateMissingAttendanceCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'attendance:create-missing {--date= : Date to check (Y-m-d format, default: today)}';

    /**
     * The console command description.
     */
    protected $description = 'Create missing attendance records for employees who did not check in (status: Tidak Absensi)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = $this->option('date') ? Carbon::parse($this->option('date')) : Carbon::today();

        // Skip weekend
        if ($date->isWeekend()) {
            $this->info("Skipping weekend: {$date->format('Y-m-d')}");
            return;
        }

        $this->info("Checking missing attendance for: {$date->format('Y-m-d')}");

        // Ambil semua pegawai aktif
        $allEmployees = Pegawai::where('status', 'active')->get();

        // Ambil pegawai yang sudah ada attendance record hari ini
        $employeesWithAttendance = Attendance::whereDate('created_at', $date)
            ->pluck('user_id')
            ->unique();

        // Cari pegawai yang belum ada attendance record
        $missingEmployees = $allEmployees->whereNotIn('id', $employeesWithAttendance);

        $createdCount = 0;

        foreach ($missingEmployees as $employee) {
            // Cek apakah pegawai ada izin yang disetujui untuk hari ini
            $hasApprovedIzin = \App\Models\Izin::where('user_id', $employee->id)
                ->where('tanggal_mulai', '<=', $date)
                ->where('tanggal_akhir', '>=', $date)
                ->whereNotNull('approved_by')
                ->whereNotNull('approved_at')
                ->exists();

            // Jika ada izin yang disetujui, skip (attendance sudah dibuat oleh IzinObserver)
            if ($hasApprovedIzin) {
                continue;
            }

            // Buat record attendance dengan status "Tidak Absensi"
            $attendanceDate = $date->copy()->setTime(17, 0, 0);
            $attendance = Attendance::create([
                'user_id' => $employee->id,
                'created_at' => $attendanceDate,
                'updated_at' => $attendanceDate,
                'attendance_type' => 'WFO',
                'status_kehadiran' => 'Tidak Absensi',
                'check_in' => null,
                'check_out' => null,
                'absen_siang' => null,
                'longitude_absen_masuk' => 0,
                'latitude_absen_masuk' => 0,
                'overtime' => 0,
            ]);

            $createdCount++;

            $this->line("Created missing attendance for: {$employee->nama}");

            Log::info("Created missing attendance record", [
                'user_id' => $employee->id,
                'user_name' => $employee->nama,
                'date' => $date->format('Y-m-d'),
                'attendance_id' => $attendance->id
            ]);
        }

        $this->info("Created {$createdCount} missing attendance records");

        return Command::SUCCESS;
    }
}
