<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Izin;
use App\Models\Attendance;
use App\Services\IzinAttendanceService;
use Carbon\Carbon;

class FixExistingIzinDatesSeeder extends Seeder
{
    /**
     * Perbaiki tanggal attendance untuk izin yang sudah ada
     */
    public function run()
    {
        $this->command->info('=== FIXING EXISTING IZIN ATTENDANCE DATES ===');

        // Cari semua izin yang sudah disetujui
        $approvedIzins = Izin::whereNotNull('approved_by')
            ->whereNotNull('approved_at')
            ->get();

        $this->command->info("Found {$approvedIzins->count()} approved izin(s) to check");

        $service = app(IzinAttendanceService::class);
        $fixedCount = 0;

        foreach ($approvedIzins as $izin) {
            $this->command->info('');
            $this->command->info("Checking Izin ID: {$izin->id}");
            $this->command->info("Period: {$izin->tanggal_mulai->format('Y-m-d')} to {$izin->tanggal_akhir->format('Y-m-d')}");

            // Cek attendance yang ada
            $existingAttendances = Attendance::where('izin_id', $izin->id)->get();

            if ($existingAttendances->count() > 0) {
                $needsFix = false;

                // Cek apakah ada attendance dengan tanggal yang salah
                foreach ($existingAttendances as $attendance) {
                    $attendanceDate = $attendance->created_at->format('Y-m-d');
                    $izinStart = $izin->tanggal_mulai->format('Y-m-d');
                    $izinEnd = $izin->tanggal_akhir->format('Y-m-d');

                    // Jika tanggal attendance tidak dalam periode izin, perlu diperbaiki
                    if ($attendanceDate < $izinStart || $attendanceDate > $izinEnd) {
                        $needsFix = true;
                        $this->command->warn("  ❌ Attendance ID {$attendance->id} has wrong date: {$attendanceDate}");
                        break;
                    } else {
                        $this->command->info("  ✅ Attendance ID {$attendance->id} has correct date: {$attendanceDate}");
                    }
                }

                if ($needsFix) {
                    $this->command->info("  🔧 Fixing attendance dates for izin ID {$izin->id}...");

                    try {
                        // Hapus attendance lama terlebih dahulu
                        $deletedCount = Attendance::where('izin_id', $izin->id)->delete();
                        $this->command->info("  🗑️ Deleted {$deletedCount} old attendance records");

                        // Buat attendance baru dengan tanggal yang benar
                        $this->createCorrectAttendanceForIzin($izin);
                        $fixedCount++;

                        $newAttendances = Attendance::where('izin_id', $izin->id)->get();
                        $this->command->info("  ✅ Fixed! Created {$newAttendances->count()} new attendance records:");

                        foreach ($newAttendances as $attendance) {
                            $this->command->info("    - Attendance ID {$attendance->id}: {$attendance->created_at->format('Y-m-d H:i:s')} - Status: {$attendance->status_kehadiran}");
                        }

                    } catch (\Exception $e) {
                        $this->command->error("  ❌ Error fixing izin ID {$izin->id}: " . $e->getMessage());
                    }
                } else {
                    $this->command->info("  ✅ Attendance dates are already correct");
                }
            } else {
                $this->command->warn("  ❌ No attendance found, creating new ones...");

                try {
                    $this->createCorrectAttendanceForIzin($izin);
                    $newAttendances = Attendance::where('izin_id', $izin->id)->get();
                    $this->command->info("  ✅ Created {$newAttendances->count()} attendance records");
                    $fixedCount++;
                } catch (\Exception $e) {
                    $this->command->error("  ❌ Error creating attendance for izin ID {$izin->id}: " . $e->getMessage());
                }
            }
        }

        $this->command->info('');
        $this->command->info('=== SUMMARY ===');
        $this->command->info("Total izin checked: {$approvedIzins->count()}");
        $this->command->info("Total izin fixed: {$fixedCount}");
        $this->command->info('✅ Fix completed!');

        if ($fixedCount > 0) {
            $this->command->info('');
            $this->command->info('🎯 Please check Filament UI - Riwayat Absensi now shows correct dates');
        }
    }

    /**
     * Create attendance records dengan tanggal yang benar untuk izin
     */
    private function createCorrectAttendanceForIzin(Izin $izin): void
    {
        $startDate = Carbon::parse($izin->tanggal_mulai)->startOfDay();
        $endDate = Carbon::parse($izin->tanggal_akhir)->endOfDay();

        // Tentukan status berdasarkan jenis izin
        $statusKehadiran = match (strtolower($izin->jenis_izin)) {
            'sakit' => 'Sakit',
            'cuti' => 'Cuti',
            default => 'Izin'
        };

        // Buat keterangan izin
        $keteranganIzin = "Izin {$izin->jenis_izin}";
        if ($izin->keterangan) {
            $keteranganIzin .= " - " . $izin->keterangan;
        }

        $currentDate = $startDate->copy();
        $recordsCreated = 0;

        while ($currentDate <= $endDate) {
            // Skip weekend (Sabtu=6, Minggu=0)
            if (in_array($currentDate->dayOfWeek, [0, 6])) {
                $currentDate->addDay();
                continue;
            }

            // Cek apakah sudah ada attendance untuk hari ini (non-izin)
            $existingAttendance = Attendance::where('user_id', $izin->user_id)
                ->whereDate('created_at', $currentDate->toDateString())
                ->whereNull('izin_id')
                ->first();

            // Jika sudah ada attendance normal, skip
            if ($existingAttendance && ($existingAttendance->check_in || $existingAttendance->check_out)) {
                $currentDate->addDay();
                continue;
            }

            // Hapus attendance kosong jika ada
            if ($existingAttendance) {
                $existingAttendance->delete();
            }

            // Set waktu attendance ke jam 8 pagi untuk hari tersebut
            $attendanceDateTime = $currentDate->copy()->setTime(8, 0, 0);

            // PERBAIKAN: Gunakan DB::table untuk bypass Eloquent timestamps
            $attendanceId = \Illuminate\Support\Facades\DB::table('attendances')->insertGetId([
                'user_id' => $izin->user_id,
                'office_working_hours_id' => 1, // Default
                'check_in' => null,
                'check_out' => null,
                'longitude_absen_masuk' => 0,
                'latitude_absen_masuk' => 0,
                'attendance_type' => 'WFO',
                'izin_id' => $izin->id,
                'status_kehadiran' => $statusKehadiran,
                'keterangan_izin' => $keteranganIzin,
                'created_at' => $attendanceDateTime,
                'updated_at' => $attendanceDateTime,
            ]);

            $recordsCreated++;
            $this->command->info("    ✅ Created attendance ID {$attendanceId} for {$attendanceDateTime->format('Y-m-d')} ({$attendanceDateTime->format('l')})");

            $currentDate->addDay();
        }

        $this->command->info("  📊 Total records created: {$recordsCreated}");
    }
}
