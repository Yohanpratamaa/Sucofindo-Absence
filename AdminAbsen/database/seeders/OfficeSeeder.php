<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Office;

class OfficeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $offices = [
            [
                'name' => 'Sucofindo Bandung',
                'latitude' => -6.9431000,
                'longitude' => 107.5851494,
                'radius' => 300.0,
            ],
        ];

        foreach ($offices as $office) {
            Office::create($office);
        }

        $this->command->info('Sample office data berhasil dibuat: ' . count($offices) . ' kantor.');
    }
}
