<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\Pegawai;
use Carbon\Carbon;
use Faker\Factory as Faker;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Ambil data pegawai yang sudah ada
        $pegawai = Pegawai::all();

        if ($pegawai->count() < 1) {
            $this->command->warn('Tidak ada data pegawai untuk membuat sample attendance. Jalankan PegawaiSeeder terlebih dahulu.');
            return;
        }

        // Generate data absensi untuk 30 hari terakhir
        for ($i = 29; $i >= 0; $i--) {
            $tanggal = Carbon::now()->subDays($i);

            // Skip weekend (Sabtu & Minggu)
            if ($tanggal->isWeekend()) {
                continue;
            }

            foreach ($pegawai as $karyawan) {
                // 90% peluang karyawan hadir
                if ($faker->boolean(90)) {
                    $this->createAttendanceRecord($karyawan, $tanggal, $faker);
                }
            }
        }

        $this->command->info('Sample attendance records berhasil dibuat untuk ' . $pegawai->count() . ' karyawan selama 30 hari terakhir.');
    }

    private function createAttendanceRecord($karyawan, $tanggal, $faker)
    {
        // Tentukan jam masuk (antara 07:30 - 09:00)
        $jamMasuk = $tanggal->copy()->setTime(
            $faker->numberBetween(7, 8),
            $faker->numberBetween(0, 59),
            0
        );

        // Jika jam masuk > 08:00, maka terlambat
        if ($jamMasuk->hour >= 8 && $jamMasuk->minute > 0) {
            $jamMasuk = $tanggal->copy()->setTime(
                $faker->numberBetween(8, 9),
                $faker->numberBetween(1, 30),
                0
            );
        }

        // Jam istirahat siang (12:00 - 13:00)
        $jamSiang = null;
        if ($faker->boolean(80)) { // 80% peluang absen siang
            $jamSiang = $tanggal->copy()->setTime(
                12,
                $faker->numberBetween(0, 59),
                0
            );
        }

        // Jam pulang (17:00 - 18:30)
        $jamPulang = null;
        if ($faker->boolean(95)) { // 95% peluang check out
            $jamPulang = $tanggal->copy()->setTime(
                $faker->numberBetween(17, 18),
                $faker->numberBetween(0, 59),
                0
            );
        }

        // Hitung lembur (jika pulang > 17:00)
        $overtime = 0;
        if ($jamPulang && $jamPulang->hour >= 18) {
            $overtime = $faker->numberBetween(30, 180); // 30 menit - 3 jam
        }

        // Koordinat kantor Sucofindo (contoh: Jakarta)
        $kantorLat = -6.2088;
        $kantorLng = 106.8456;

        // Variasi lokasi dalam radius 100 meter
        $latVariation = $faker->randomFloat(6, -0.001, 0.001);
        $lngVariation = $faker->randomFloat(6, -0.001, 0.001);

        $attendanceData = [
            'user_id' => $karyawan->id,
            'office_working_hours_id' => 1, // Default office working hours ID
            'check_in' => $jamMasuk->format('H:i:s'),
            'longitude_absen_masuk' => $kantorLng + $lngVariation,
            'latitude_absen_masuk' => $kantorLat + $latVariation,
            'picture_absen_masuk' => null, // Bisa ditambahkan path foto sample
            'absen_siang' => $jamSiang ? $jamSiang->format('H:i:s') : null,
            'longitude_absen_siang' => $jamSiang ? ($kantorLng + $faker->randomFloat(6, -0.001, 0.001)) : null,
            'latitude_absen_siang' => $jamSiang ? ($kantorLat + $faker->randomFloat(6, -0.001, 0.001)) : null,
            'picture_absen_siang' => null,
            'check_out' => $jamPulang ? $jamPulang->format('H:i:s') : null,
            'longitude_absen_pulang' => $jamPulang ? ($kantorLng + $faker->randomFloat(6, -0.001, 0.001)) : null,
            'latitude_absen_pulang' => $jamPulang ? ($kantorLat + $faker->randomFloat(6, -0.001, 0.001)) : null,
            'picture_absen_pulang' => null,
            'overtime' => $overtime,
            'attendance_type' => $faker->randomElement(['WFO', 'Dinas Luar']),
            'created_at' => $tanggal,
            'updated_at' => $tanggal,
        ];

        Attendance::create($attendanceData);
    }
}
