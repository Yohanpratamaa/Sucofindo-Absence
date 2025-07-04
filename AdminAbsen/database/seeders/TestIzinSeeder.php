<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Izin;
use App\Models\Pegawai;
use Carbon\Carbon;

class TestIzinSeeder extends Seeder
{
    /**
     * Seed test izin data untuk testing perbaikan tanggal
     */
    public function run()
    {
        $this->command->info('🌱 Creating test izin data...');

        // Cari pegawai yang ada
        $user = Pegawai::first();

        if (!$user) {
            $this->command->error('❌ No user found. Please seed users first.');
            return;
        }

        // Gunakan user yang sama sebagai admin untuk approval
        $admin = $user;

        // Buat izin sakit (kemarin - 2 hari lalu)
        $izinSakit = Izin::create([
            'user_id' => $user->id,
            'tanggal_mulai' => Carbon::now()->subDays(2),
            'tanggal_akhir' => Carbon::now()->subDays(1),
            'jenis_izin' => 'sakit',
            'keterangan' => 'Demam dan flu',
            'approved_by' => $admin->id,
            'approved_at' => Carbon::now()->subDay(),
        ]);

        // Buat izin cuti (minggu depan)
        $izinCuti = Izin::create([
            'user_id' => $user->id,
            'tanggal_mulai' => Carbon::now()->addWeek()->startOfWeek()->addDay(), // Selasa minggu depan
            'tanggal_akhir' => Carbon::now()->addWeek()->startOfWeek()->addDays(2), // Rabu minggu depan
            'jenis_izin' => 'cuti',
            'keterangan' => 'Liburan keluarga',
            'approved_by' => $admin->id,
            'approved_at' => Carbon::now(),
        ]);

        // Buat izin pribadi (hari ini)
        $izinPribadi = Izin::create([
            'user_id' => $user->id,
            'tanggal_mulai' => Carbon::now(),
            'tanggal_akhir' => Carbon::now(),
            'jenis_izin' => 'izin',
            'keterangan' => 'Urusan keluarga mendadak',
            'approved_by' => $admin->id,
            'approved_at' => Carbon::now(),
        ]);

        $this->command->info("✅ Created 3 test izin:");
        $this->command->info("   1. Izin Sakit ID: {$izinSakit->id} ({$izinSakit->tanggal_mulai->format('Y-m-d')} - {$izinSakit->tanggal_akhir->format('Y-m-d')})");
        $this->command->info("   2. Izin Cuti ID: {$izinCuti->id} ({$izinCuti->tanggal_mulai->format('Y-m-d')} - {$izinCuti->tanggal_akhir->format('Y-m-d')})");
        $this->command->info("   3. Izin Pribadi ID: {$izinPribadi->id} ({$izinPribadi->tanggal_mulai->format('Y-m-d')} - {$izinPribadi->tanggal_akhir->format('Y-m-d')})");

        $this->command->info('');
        $this->command->info('🎯 Run: php artisan izin:fix-attendance-dates --dry-run');
        $this->command->info('   to see what will be fixed');
    }
}
