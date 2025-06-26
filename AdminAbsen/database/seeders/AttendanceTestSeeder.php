<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Office;
use App\Models\OfficeSchedule;
use App\Models\Attendance;
use App\Models\Pegawai;

class AttendanceTestSeeder extends Seeder
{
    /**
     * Seeder khusus untuk testing sistem attendance dengan data minimal
     */
    public function run(): void
    {
        $this->command->info('🚀 Memulai seeding data untuk testing attendance system...');

        // 1. Buat office jika belum ada
        if (Office::count() == 0) {
            $this->command->info('📍 Membuat data kantor...');
            $this->call(OfficeSeeder::class);
        }

        // 2. Buat office schedule jika belum ada
        if (OfficeSchedule::count() == 0) {
            $this->command->info('📅 Membuat jadwal kantor...');
            $this->call(OfficeScheduleSeeder::class);
        }

        // 3. Buat attendance data
        $this->command->info('⏰ Membuat data absensi...');
        $this->call(AttendanceSeeder::class);

        $this->command->info('✅ Seeding data attendance system completed!');

        // Summary info
        $this->command->info('📊 Summary:');
        $this->command->info('   - Offices: ' . Office::count());
        $this->command->info('   - Office Schedules: ' . OfficeSchedule::count());
        $this->command->info('   - Attendances: ' . Attendance::count());
        $this->command->info('   - Employees: ' . Pegawai::count());
    }
}
