<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Izin;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FixIzinAttendanceDatesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'izin:fix-attendance-dates
                            {--izin-id= : Fix specific izin ID only}
                            {--dry-run : Show what will be fixed without actually fixing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix attendance dates to match izin period dates instead of current date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 FIXING IZIN ATTENDANCE DATES');
        $this->info('=====================================');

        $izinId = $this->option('izin-id');
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->warn('🧪 DRY RUN MODE - No changes will be made');
        }

        // Query izin
        $query = Izin::whereNotNull('approved_by')->whereNotNull('approved_at');

        if ($izinId) {
            $query->where('id', $izinId);
        }

        $approvedIzins = $query->get();

        if ($approvedIzins->count() === 0) {
            $this->error('❌ No approved izin found');
            return 1;
        }

        $this->info("📋 Found {$approvedIzins->count()} approved izin(s) to check");
        $this->line('');

        $fixedCount = 0;

        foreach ($approvedIzins as $izin) {
            $this->info("🔍 Checking Izin ID: {$izin->id}");
            $this->info("   User: {$izin->user->nama} ({$izin->user->nip})");
            $this->info("   Type: {$izin->jenis_izin}");
            $this->info("   Period: {$izin->tanggal_mulai->format('Y-m-d')} to {$izin->tanggal_akhir->format('Y-m-d')}");

            // Cek attendance yang ada
            $existingAttendances = Attendance::where('izin_id', $izin->id)->get();

            if ($existingAttendances->count() === 0) {
                $this->warn("   ⚠️  No attendance found, creating new ones...");

                if (!$dryRun) {
                    $this->createCorrectAttendanceForIzin($izin);
                    $fixedCount++;
                }
                continue;
            }

            $needsFix = false;
            $wrongDates = [];

            // Cek apakah ada attendance dengan tanggal yang salah
            foreach ($existingAttendances as $attendance) {
                $attendanceDate = $attendance->created_at->format('Y-m-d');
                $izinStart = $izin->tanggal_mulai->format('Y-m-d');
                $izinEnd = $izin->tanggal_akhir->format('Y-m-d');

                // Jika tanggal attendance tidak dalam periode izin, perlu diperbaiki
                if ($attendanceDate < $izinStart || $attendanceDate > $izinEnd) {
                    $needsFix = true;
                    $wrongDates[] = $attendanceDate;
                }
            }

            if ($needsFix) {
                $this->error("   ❌ Found wrong dates: " . implode(', ', array_unique($wrongDates)));
                $this->info("   🔧 " . ($dryRun ? 'WOULD FIX' : 'FIXING') . " attendance dates...");

                if (!$dryRun) {
                    // Hapus attendance lama
                    $deletedCount = Attendance::where('izin_id', $izin->id)->delete();
                    $this->info("   🗑️  Deleted {$deletedCount} old records");

                    // Buat attendance baru dengan tanggal yang benar
                    $this->createCorrectAttendanceForIzin($izin);
                    $fixedCount++;

                    $newAttendances = Attendance::where('izin_id', $izin->id)->get();
                    $this->info("   ✅ Created {$newAttendances->count()} correct attendance records");
                }
            } else {
                $this->info("   ✅ Attendance dates are correct");
            }

            $this->line('');
        }

        $this->info('=====================================');
        $this->info('📊 SUMMARY');
        $this->info("Total izin checked: {$approvedIzins->count()}");

        if ($dryRun) {
            $this->info("Would fix: " . ($approvedIzins->filter(function($izin) {
                $existingAttendances = Attendance::where('izin_id', $izin->id)->get();
                foreach ($existingAttendances as $attendance) {
                    $attendanceDate = $attendance->created_at->format('Y-m-d');
                    $izinStart = $izin->tanggal_mulai->format('Y-m-d');
                    $izinEnd = $izin->tanggal_akhir->format('Y-m-d');
                    if ($attendanceDate < $izinStart || $attendanceDate > $izinEnd) {
                        return true;
                    }
                }
                return false;
            })->count()) . " izin");
        } else {
            $this->info("Total izin fixed: {$fixedCount}");

            if ($fixedCount > 0) {
                $this->info('');
                $this->info('🎯 Please check Filament UI - Riwayat Absensi to see correct dates');
            }
        }

        $this->info('✅ Command completed!');
        return 0;
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
            $this->info("     📅 Created ID {$attendanceId} for {$attendanceDateTime->format('Y-m-d')} ({$attendanceDateTime->format('l')})");

            $currentDate->addDay();
        }

        $this->info("   📊 Total records created: {$recordsCreated}");
    }
}
