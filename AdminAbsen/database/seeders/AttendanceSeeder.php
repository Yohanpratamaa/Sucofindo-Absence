<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\Pegawai;
use App\Models\Office;
use App\Models\OfficeSchedule;
use Carbon\Carbon;
use Faker\Generator;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = fake('id_ID');

        // Ambil data pegawai dan office yang sudah ada
        $pegawai = Pegawai::all();
        $offices = Office::all();

        if ($pegawai->count() < 1) {
            $this->command->warn('Tidak ada data pegawai untuk membuat sample attendance. Jalankan PegawaiSeeder terlebih dahulu.');
            return;
        }

        if ($offices->count() < 1) {
            $this->command->warn('Tidak ada data office untuk membuat sample attendance. Jalankan OfficeSeeder terlebih dahulu.');
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
                    // Assign random office untuk karyawan (bisa dikustomisasi sesuai kebutuhan)
                    $randomOffice = $faker->randomElement($offices);
                    $this->createAttendanceRecord($karyawan, $tanggal, $randomOffice, $faker);
                }
            }
        }

        $this->command->info('Sample attendance records berhasil dibuat untuk ' . $pegawai->count() . ' karyawan selama 30 hari terakhir.');
    }

    private function createAttendanceRecord($karyawan, $tanggal, $office, $faker)
    {
        // Ambil jadwal kantor untuk hari tersebut
        $dayOfWeek = strtolower($tanggal->format('l')); // monday, tuesday, etc.
        $schedule = OfficeSchedule::where('office_id', $office->id)
                                 ->where('day_of_week', $dayOfWeek)
                                 ->first();

        // Jika tidak ada jadwal atau hari libur, skip
        if (!$schedule || !$schedule->start_time) {
            return;
        }

        // Parse jam masuk standar dari schedule
        $jamMasukStandar = Carbon::parse($schedule->start_time);
        $jamKeluarStandar = Carbon::parse($schedule->end_time);

        // Generate waktu check in berdasarkan jadwal kantor
        $jamMasuk = $this->generateCheckInTime($tanggal, $jamMasukStandar, $faker);

        // Tentukan attendance type secara random (70% WFO, 30% Dinas Luar)
        $attendanceType = $faker->boolean(70) ? 'WFO' : 'Dinas Luar';

        // Jam istirahat siang - berbeda berdasarkan attendance type
        $jamSiang = null;
        if ($attendanceType === 'Dinas Luar') {
            // Dinas Luar: WAJIB absen siang
            $jamSiang = $tanggal->copy()->setTime(
                12,
                $faker->numberBetween(0, 59),
                0
            );
        }
        // WFO: Tidak perlu absen siang (null)

        // Generate waktu check out berdasarkan jadwal kantor
        $jamPulang = null;
        if ($faker->boolean(95)) { // 95% peluang check out
            $jamPulang = $this->generateCheckOutTime($tanggal, $jamKeluarStandar, $faker);
        }

        // Hitung lembur (jika pulang lebih dari jam kerja standar)
        $overtime = 0;
        if ($jamPulang && $jamPulang->greaterThan($jamKeluarStandar->copy()->setDate($tanggal->year, $tanggal->month, $tanggal->day))) {
            $overtime = $faker->numberBetween(30, 180); // 30 menit - 3 jam
        }

        // Koordinat dan foto berdasarkan attendance type
        if ($attendanceType === 'WFO') {
            // WFO: Lokasi di kantor (dalam radius kantor)
            $kantorLat = $office->latitude;
            $kantorLng = $office->longitude;
            $radiusInDegrees = $office->radius / 111000;

            $latVariation = $faker->randomFloat(6, -$radiusInDegrees, $radiusInDegrees);
            $lngVariation = $faker->randomFloat(6, -$radiusInDegrees, $radiusInDegrees);

            $checkInLat = $kantorLat + $latVariation;
            $checkInLng = $kantorLng + $lngVariation;
            $checkOutLat = $kantorLat + $faker->randomFloat(6, -$radiusInDegrees, $radiusInDegrees);
            $checkOutLng = $kantorLng + $faker->randomFloat(6, -$radiusInDegrees, $radiusInDegrees);

            // Absen siang coordinates (tidak digunakan untuk WFO)
            $siangLat = null;
            $siangLng = null;

        } else {
            // Dinas Luar: Lokasi fleksibel (simulasi lokasi berbeda)
            $baseLocations = [
                ['lat' => -6.9175, 'lng' => 107.6191], // Bandung area 1
                ['lat' => -6.8951, 'lng' => 107.6081], // Bandung area 2
                ['lat' => -6.9389, 'lng' => 107.7186], // Bandung area 3
                ['lat' => -6.8650, 'lng' => 107.5886], // Bandung area 4
            ];

            // Check in location
            $checkInLocation = $faker->randomElement($baseLocations);
            $checkInLat = $checkInLocation['lat'] + $faker->randomFloat(4, -0.01, 0.01);
            $checkInLng = $checkInLocation['lng'] + $faker->randomFloat(4, -0.01, 0.01);

            // Absen siang location (berbeda dari check in)
            $siangLocation = $faker->randomElement($baseLocations);
            $siangLat = $siangLocation['lat'] + $faker->randomFloat(4, -0.01, 0.01);
            $siangLng = $siangLocation['lng'] + $faker->randomFloat(4, -0.01, 0.01);

            // Check out location (berbeda lagi)
            $checkOutLocation = $faker->randomElement($baseLocations);
            $checkOutLat = $checkOutLocation['lat'] + $faker->randomFloat(4, -0.01, 0.01);
            $checkOutLng = $checkOutLocation['lng'] + $faker->randomFloat(4, -0.01, 0.01);
        }

        // Cari office schedule ID untuk relasi
        $officeScheduleId = $schedule->id;

        $attendanceData = [
            'user_id' => $karyawan->id,
            'office_working_hours_id' => $officeScheduleId, // Relasi ke office schedule
            'check_in' => $jamMasuk->format('H:i:s'),
            'longitude_absen_masuk' => $checkInLng,
            'latitude_absen_masuk' => $checkInLat,
            'picture_absen_masuk' => null, // Bisa ditambahkan path foto sample
            'absen_siang' => $jamSiang ? $jamSiang->format('H:i:s') : null,
            'longitude_absen_siang' => $siangLng,
            'latitude_absen_siang' => $siangLat,
            'picture_absen_siang' => $jamSiang ? null : null, // Foto hanya jika ada absen siang
            'check_out' => $jamPulang ? $jamPulang->format('H:i:s') : null,
            'longitude_absen_pulang' => $jamPulang ? $checkOutLng : null,
            'latitude_absen_pulang' => $jamPulang ? $checkOutLat : null,
            'picture_absen_pulang' => $jamPulang ? null : null, // Foto hanya jika ada check out
            'overtime' => $overtime,
            'attendance_type' => $attendanceType,
            'created_at' => $tanggal,
            'updated_at' => $tanggal,
        ];

        Attendance::create($attendanceData);
    }

    private function generateCheckInTime($tanggal, $jamMasukStandar, $faker)
    {
        // 70% tepat waktu, 30% terlambat (tanpa toleransi)
        $statusChance = $faker->numberBetween(1, 100);

        if ($statusChance <= 70) {
            // Tepat waktu: 30 menit sebelum sampai tepat jam masuk
            $minutes = $faker->numberBetween(-30, 0);
            return $tanggal->copy()->setTime($jamMasukStandar->hour, $jamMasukStandar->minute)->addMinutes($minutes);
        } else {
            // Terlambat: 1-60 menit setelah jam masuk
            $minutes = $faker->numberBetween(1, 60);
            return $tanggal->copy()->setTime($jamMasukStandar->hour, $jamMasukStandar->minute)->addMinutes($minutes);
        }
    }

    private function generateCheckOutTime($tanggal, $jamKeluarStandar, $faker)
    {
        // 80% pulang normal, 20% lembur
        if ($faker->boolean(80)) {
            // Pulang normal: sekitar jam keluar standar ± 30 menit
            $minutes = $faker->numberBetween(-30, 30);
            return $tanggal->copy()->setTime($jamKeluarStandar->hour, $jamKeluarStandar->minute)->addMinutes($minutes);
        } else {
            // Lembur: 30 menit - 3 jam setelah jam keluar
            $minutes = $faker->numberBetween(30, 180);
            return $tanggal->copy()->setTime($jamKeluarStandar->hour, $jamKeluarStandar->minute)->addMinutes($minutes);
        }
    }
}
