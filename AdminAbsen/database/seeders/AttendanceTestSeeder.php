<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\Pegawai;
use App\Models\Office;
use App\Models\OfficeSchedule;
use Carbon\Carbon;

class AttendanceTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seeder khusus untuk testing aturan WFO vs Dinas Luar
     */
    public function run(): void
    {
        // Hapus data attendance yang ada
        Attendance::truncate();

        // Ambil data yang diperlukan
        $pegawai = Pegawai::all();
        $office = Office::first();

        if ($pegawai->count() < 1) {
            $this->command->error('Tidak ada data pegawai. Jalankan PegawaiSeeder terlebih dahulu.');
            return;
        }

        if (!$office) {
            $this->command->error('Tidak ada data office. Jalankan OfficeSeeder terlebih dahulu.');
            return;
        }

        // Ambil jadwal hari kerja (Senin)
        $schedule = OfficeSchedule::where('office_id', $office->id)
                                 ->where('day_of_week', 'monday')
                                 ->first();

        if (!$schedule) {
            $this->command->error('Tidak ada jadwal kantor. Jalankan OfficeScheduleSeeder terlebih dahulu.');
            return;
        }

        $today = Carbon::today();

        // Test Case 1: WFO Lengkap (Check In + Check Out)
        $this->createWFOComplete($pegawai->first(), $today, $office, $schedule);

        // Test Case 2: WFO Tidak Lengkap (Hanya Check In)
        $this->createWFOIncomplete($pegawai->first(), $today->copy()->subDay(), $office, $schedule);

        // Test Case 3: Dinas Luar Lengkap (Check In + Siang + Check Out)
        if ($pegawai->count() > 1) {
            $this->createDinasLuarComplete($pegawai->get(1), $today, $office, $schedule);

            // Test Case 4: Dinas Luar Tidak Lengkap (Hanya Check In + Siang)
            $this->createDinasLuarIncomplete($pegawai->get(1), $today->copy()->subDay(), $office, $schedule);
        }

        $this->command->info('Test cases untuk WFO vs Dinas Luar berhasil dibuat!');
        $this->command->info('');
        $this->command->info('Test Cases:');
        $this->command->info('1. WFO Lengkap: Check In + Check Out (Lokasi: Kantor)');
        $this->command->info('2. WFO Tidak Lengkap: Hanya Check In');
        $this->command->info('3. Dinas Luar Lengkap: Check In + Siang + Check Out (Lokasi: Fleksibel)');
        $this->command->info('4. Dinas Luar Tidak Lengkap: Check In + Siang (tanpa Check Out)');
    }

    private function createWFOComplete($karyawan, $tanggal, $office, $schedule)
    {
        // WFO: Lokasi di kantor (dalam radius)
        $kantorLat = $office->latitude;
        $kantorLng = $office->longitude;
        $radiusInDegrees = $office->radius / 111000;

        $checkInLat = $kantorLat + 0.0001; // Sedikit variasi dalam radius
        $checkInLng = $kantorLng + 0.0001;
        $checkOutLat = $kantorLat - 0.0001;
        $checkOutLng = $kantorLng - 0.0001;

        Attendance::create([
            'user_id' => $karyawan->id,
            'office_working_hours_id' => $schedule->id,
            'check_in' => $tanggal->copy()->setTime(8, 0, 0), // Tepat waktu
            'longitude_absen_masuk' => $checkInLng,
            'latitude_absen_masuk' => $checkInLat,
            'picture_absen_masuk' => 'sample_foto_masuk_wfo.jpg',
            'absen_siang' => null, // WFO tidak perlu absen siang
            'longitude_absen_siang' => null,
            'latitude_absen_siang' => null,
            'picture_absen_siang' => null,
            'check_out' => $tanggal->copy()->setTime(17, 0, 0),
            'longitude_absen_pulang' => $checkOutLng,
            'latitude_absen_pulang' => $checkOutLat,
            'picture_absen_pulang' => 'sample_foto_pulang_wfo.jpg',
            'overtime' => 0,
            'attendance_type' => 'WFO',
            'created_at' => $tanggal,
            'updated_at' => $tanggal,
        ]);
    }

    private function createWFOIncomplete($karyawan, $tanggal, $office, $schedule)
    {
        $kantorLat = $office->latitude;
        $kantorLng = $office->longitude;

        $checkInLat = $kantorLat + 0.0002;
        $checkInLng = $kantorLng + 0.0002;

        Attendance::create([
            'user_id' => $karyawan->id,
            'office_working_hours_id' => $schedule->id,
            'check_in' => $tanggal->copy()->setTime(8, 30, 0), // Terlambat 30 menit
            'longitude_absen_masuk' => $checkInLng,
            'latitude_absen_masuk' => $checkInLat,
            'picture_absen_masuk' => 'sample_foto_masuk_wfo_late.jpg',
            'absen_siang' => null, // WFO tidak perlu absen siang
            'longitude_absen_siang' => null,
            'latitude_absen_siang' => null,
            'picture_absen_siang' => null,
            'check_out' => null, // Belum check out
            'longitude_absen_pulang' => null,
            'latitude_absen_pulang' => null,
            'picture_absen_pulang' => null,
            'overtime' => 0,
            'attendance_type' => 'WFO',
            'created_at' => $tanggal,
            'updated_at' => $tanggal,
        ]);
    }

    private function createDinasLuarComplete($karyawan, $tanggal, $office, $schedule)
    {
        // Dinas Luar: Lokasi fleksibel (simulasi lokasi berbeda di Bandung)
        $baseLocations = [
            ['lat' => -6.9175, 'lng' => 107.6191], // Bandung area 1
            ['lat' => -6.8951, 'lng' => 107.6081], // Bandung area 2
            ['lat' => -6.9389, 'lng' => 107.7186], // Bandung area 3
        ];

        $checkInLocation = $baseLocations[0];
        $siangLocation = $baseLocations[1];
        $checkOutLocation = $baseLocations[2];

        Attendance::create([
            'user_id' => $karyawan->id,
            'office_working_hours_id' => $schedule->id,
            'check_in' => $tanggal->copy()->setTime(7, 45, 0), // Lebih awal
            'longitude_absen_masuk' => $checkInLocation['lng'],
            'latitude_absen_masuk' => $checkInLocation['lat'],
            'picture_absen_masuk' => 'sample_foto_masuk_dinas.jpg',
            'absen_siang' => $tanggal->copy()->setTime(12, 15, 0), // WAJIB untuk Dinas Luar
            'longitude_absen_siang' => $siangLocation['lng'],
            'latitude_absen_siang' => $siangLocation['lat'],
            'picture_absen_siang' => 'sample_foto_siang_dinas.jpg',
            'check_out' => $tanggal->copy()->setTime(17, 30, 0),
            'longitude_absen_pulang' => $checkOutLocation['lng'],
            'latitude_absen_pulang' => $checkOutLocation['lat'],
            'picture_absen_pulang' => 'sample_foto_pulang_dinas.jpg',
            'overtime' => 30, // 30 menit lembur
            'attendance_type' => 'Dinas Luar',
            'created_at' => $tanggal,
            'updated_at' => $tanggal,
        ]);
    }

    private function createDinasLuarIncomplete($karyawan, $tanggal, $office, $schedule)
    {
        $baseLocations = [
            ['lat' => -6.9175, 'lng' => 107.6191],
            ['lat' => -6.8951, 'lng' => 107.6081],
        ];

        $checkInLocation = $baseLocations[0];
        $siangLocation = $baseLocations[1];

        Attendance::create([
            'user_id' => $karyawan->id,
            'office_working_hours_id' => $schedule->id,
            'check_in' => $tanggal->copy()->setTime(8, 15, 0), // Terlambat 15 menit
            'longitude_absen_masuk' => $checkInLocation['lng'],
            'latitude_absen_masuk' => $checkInLocation['lat'],
            'picture_absen_masuk' => 'sample_foto_masuk_dinas_late.jpg',
            'absen_siang' => $tanggal->copy()->setTime(12, 0, 0), // Ada absen siang
            'longitude_absen_siang' => $siangLocation['lng'],
            'latitude_absen_siang' => $siangLocation['lat'],
            'picture_absen_siang' => 'sample_foto_siang_dinas_incomplete.jpg',
            'check_out' => null, // Belum check out
            'longitude_absen_pulang' => null,
            'latitude_absen_pulang' => null,
            'picture_absen_pulang' => null,
            'overtime' => 0,
            'attendance_type' => 'Dinas Luar',
            'created_at' => $tanggal,
            'updated_at' => $tanggal,
        ]);
    }
}
