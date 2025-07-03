<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;

class TidakAbsensiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil user pertama atau buat user dummy
        $user = User::first();
        if (!$user) {
            // Buat user dummy untuk testing
            $user = User::create([
                'name' => 'Test User Absensi',
                'email' => 'test.absensi@example.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]);
            echo "User dummy berhasil dibuat dengan email: test.absensi@example.com\n";
        }

        $userId = $user->id;
        $today = Carbon::today();

        // 1. Data absensi normal (tepat waktu) - sebagai pembanding
        Attendance::create([
            'user_id' => $userId,
            'check_in' => $today->copy()->setTime(8, 0), // 08:00
            'check_out' => $today->copy()->setTime(17, 0), // 17:00
            'attendance_type' => 'WFO',
            'picture_absen_masuk' => 'attendance/dummy_checkin.jpg',
            'picture_absen_pulang' => 'attendance/dummy_checkout.jpg',
            'latitude_absen_masuk' => -6.2088,
            'longitude_absen_masuk' => 106.8456,
            'latitude_absen_pulang' => -6.2088,
            'longitude_absen_pulang' => 106.8456,
            'created_at' => $today->copy()->subDays(5),
            'updated_at' => $today->copy()->subDays(5),
        ]);

        // 2. Data check-in di atas jam 17:00 - akan ditandai sebagai "Tidak Absensi"
        Attendance::create([
            'user_id' => $userId,
            'check_in' => $today->copy()->subDays(3)->setTime(18, 30), // 18:30 (di atas jam 17)
            'check_out' => $today->copy()->subDays(3)->setTime(20, 0), // 20:00
            'attendance_type' => 'WFO',
            'picture_absen_masuk' => 'attendance/dummy_late_evening_checkin.jpg',
            'picture_absen_pulang' => 'attendance/dummy_late_checkout.jpg',
            'latitude_absen_masuk' => -6.2088,
            'longitude_absen_masuk' => 106.8456,
            'latitude_absen_pulang' => -6.2088,
            'longitude_absen_pulang' => 106.8456,
            'created_at' => $today->copy()->subDays(3),
            'updated_at' => $today->copy()->subDays(3),
        ]);

        // 3. Data tidak check-in sama sekali - akan ditandai sebagai "Tidak Absensi"
        Attendance::create([
            'user_id' => $userId,
            'check_in' => null,
            'check_out' => null,
            'attendance_type' => 'WFO',
            'picture_absen_masuk' => null,
            'picture_absen_pulang' => null,
            'latitude_absen_masuk' => null,
            'longitude_absen_masuk' => null,
            'latitude_absen_pulang' => null,
            'longitude_absen_pulang' => null,
            'created_at' => $today->copy()->subDays(2),
            'updated_at' => $today->copy()->subDays(2),
        ]);

        // 4. Data check-in tepat jam 17:00 (masih dianggap "Tidak Absensi")
        Attendance::create([
            'user_id' => $userId,
            'check_in' => $today->copy()->subDays(1)->setTime(17, 0), // 17:00 tepat
            'check_out' => $today->copy()->subDays(1)->setTime(19, 0), // 19:00
            'attendance_type' => 'WFO',
            'picture_absen_masuk' => 'attendance/dummy_evening_checkin.jpg',
            'picture_absen_pulang' => 'attendance/dummy_checkout.jpg',
            'latitude_absen_masuk' => -6.2088,
            'longitude_absen_masuk' => 106.8456,
            'latitude_absen_pulang' => -6.2088,
            'longitude_absen_pulang' => 106.8456,
            'created_at' => $today->copy()->subDays(1),
            'updated_at' => $today->copy()->subDays(1),
        ]);

        // 5. Data check-in sore tapi masih sebelum jam 17:00 (terlambat normal)
        Attendance::create([
            'user_id' => $userId,
            'check_in' => $today->copy()->subDays(4)->setTime(16, 30), // 16:30 (masih sebelum 17:00)
            'check_out' => $today->copy()->subDays(4)->setTime(18, 0), // 18:00
            'attendance_type' => 'WFO',
            'picture_absen_masuk' => 'attendance/dummy_afternoon_checkin.jpg',
            'picture_absen_pulang' => 'attendance/dummy_checkout.jpg',
            'latitude_absen_masuk' => -6.2088,
            'longitude_absen_masuk' => 106.8456,
            'latitude_absen_pulang' => -6.2088,
            'longitude_absen_pulang' => 106.8456,
            'created_at' => $today->copy()->subDays(4),
            'updated_at' => $today->copy()->subDays(4),
        ]);

        // 6. Data hari ini - check-in normal (belum check-out)
        Attendance::create([
            'user_id' => $userId,
            'check_in' => $today->copy()->setTime(8, 15), // 08:15
            'check_out' => null,
            'attendance_type' => 'WFO',
            'picture_absen_masuk' => 'attendance/dummy_today_checkin.jpg',
            'picture_absen_pulang' => null,
            'latitude_absen_masuk' => -6.2088,
            'longitude_absen_masuk' => 106.8456,
            'latitude_absen_pulang' => null,
            'longitude_absen_pulang' => null,
            'created_at' => $today,
            'updated_at' => $today,
        ]);

        echo "Seeder TidakAbsensiSeeder berhasil dijalankan!\n";
        echo "Data yang ditambahkan:\n";
        echo "1. Absensi normal (5 hari lalu) 08:00 - Tepat Waktu\n";
        echo "2. Check-in jam 18:30 (3 hari lalu) - Tidak Absensi\n";
        echo "3. Tidak check-in sama sekali (2 hari lalu) - Tidak Absensi\n";
        echo "4. Check-in jam 17:00 tepat (1 hari lalu) - Tidak Absensi\n";
        echo "5. Check-in jam 16:30 (4 hari lalu) - Terlambat (masih normal)\n";
        echo "6. Check-in normal hari ini 08:15 - Tepat Waktu (belum checkout)\n";
    }
}
