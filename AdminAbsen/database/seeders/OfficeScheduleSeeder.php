<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Office;
use App\Models\OfficeSchedule;

class OfficeScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $offices = Office::all();

        if ($offices->count() < 1) {
            $this->command->warn('Tidak ada data office untuk membuat schedule. Jalankan OfficeSeeder terlebih dahulu.');
            return;
        }

        foreach ($offices as $office) {
            // Jadwal untuk setiap hari kerja
            $schedules = [
                // Senin - Kamis: 08:00 - 17:00
                [
                    'office_id' => $office->id,
                    'day_of_week' => 'monday',
                    'start_time' => '08:00:00',
                    'end_time' => '17:00:00',
                ],
                [
                    'office_id' => $office->id,
                    'day_of_week' => 'tuesday',
                    'start_time' => '08:00:00',
                    'end_time' => '17:00:00',
                ],
                [
                    'office_id' => $office->id,
                    'day_of_week' => 'wednesday',
                    'start_time' => '08:00:00',
                    'end_time' => '17:00:00',
                ],
                [
                    'office_id' => $office->id,
                    'day_of_week' => 'thursday',
                    'start_time' => '08:00:00',
                    'end_time' => '17:00:00',
                ],
                // Jumat: 08:00 - 16:30 (lebih awal)
                [
                    'office_id' => $office->id,
                    'day_of_week' => 'friday',
                    'start_time' => '08:00:00',
                    'end_time' => '16:30:00',
                ],
                // Sabtu & Minggu: Libur (null)
                [
                    'office_id' => $office->id,
                    'day_of_week' => 'saturday',
                    'start_time' => null,
                    'end_time' => null,
                ],
                [
                    'office_id' => $office->id,
                    'day_of_week' => 'sunday',
                    'start_time' => null,
                    'end_time' => null,
                ],
            ];

            // Variasi jadwal untuk kantor yang berbeda
            if ($office->id == 2) { // Surabaya - shift pagi
                foreach ($schedules as &$schedule) {
                    if ($schedule['start_time']) {
                        $schedule['start_time'] = '07:30:00';
                        $schedule['end_time'] = $schedule['day_of_week'] == 'friday' ? '16:00:00' : '16:30:00';
                    }
                }
            }

            if ($office->id == 3) { // Medan - shift normal tapi agak telat
                foreach ($schedules as &$schedule) {
                    if ($schedule['start_time']) {
                        $schedule['start_time'] = '08:30:00';
                        $schedule['end_time'] = $schedule['day_of_week'] == 'friday' ? '17:00:00' : '17:30:00';
                    }
                }
            }

            foreach ($schedules as $schedule) {
                OfficeSchedule::create($schedule);
            }
        }

        $this->command->info('Sample office schedule berhasil dibuat untuk ' . $offices->count() . ' kantor.');
    }
}
