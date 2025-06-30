<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // OfficeSeeder::class,
            // OfficeScheduleSeeder::class,
            // JabatanSeeder::class,
            // PosisiSeeder::class,
            // PegawaiSeeder::class,
            // IzinSeeder::class,
            // OvertimeAssignmentSeeder::class,
            // AttendanceSeeder::class,
            SuperAdminSeeder::class,
        ]);

        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
