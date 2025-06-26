<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pegawai;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if super admin already exists
        $existingAdmin = Pegawai::where('role_user', 'super admin')
            ->where('email', 'admin@sucofindo.com')
            ->first();

        if ($existingAdmin) {
            $this->command->info('Super Admin already exists with email: admin@sucofindo.com');
            $this->command->table(['Field', 'Value'], [
                ['ID', $existingAdmin->id],
                ['Nama', $existingAdmin->nama],
                ['NPP', $existingAdmin->npp],
                ['Email', $existingAdmin->email],
                ['Role', $existingAdmin->role_user],
                ['Login URL', 'http://localhost:8000/admin'],
            ]);
            return;
        }

        // Generate unique NPP and NIK
        do {
            $npp = 'ADM' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        } while (Pegawai::where('npp', $npp)->exists());

        do {
            $nik = '123456789012' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        } while (Pegawai::where('nik', $nik)->exists());

        // Create default super admin
        $superAdmin = Pegawai::create([
            // Tab Users - Data Dasar
            'nama' => 'Super Administrator',
            'npp' => $npp,
            'email' => 'admin@sucofindo.com',
            'password' => 'admin123', // Will be hashed by mutator
            'nik' => $nik,
            'status_pegawai' => 'PTT',
            'nomor_handphone' => '081234567890',
            'status' => 'active',
            'role_user' => 'super admin',
            'alamat' => 'Jakarta Pusat',

            // Tab Jabatan
            'jabatan_nama' => 'System Administrator',
            'jabatan_tunjangan' => 0,

            // Tab Posisi
            'posisi_nama' => 'System Administrator',
            'posisi_tunjangan' => 0,

            // Tab Data JSON
            'pendidikan_list' => [
                [
                    'jenjang' => 'S1',
                    'sekolah_univ' => 'Universitas Indonesia',
                    'fakultas_program_studi' => 'Ilmu Komputer',
                    'jurusan' => 'Sistem Informasi',
                    'thn_masuk' => '2015-08-01',
                    'thn_lulus' => '2019-07-01',
                    'ipk_nilai' => '3.50',
                    'ijazah' => null
                ]
            ],
            'emergency_contacts' => [
                [
                    'relationship' => 'Family',
                    'nama_kontak' => 'Emergency Contact',
                    'no_emergency' => '081234567891'
                ]
            ],
            'fasilitas_list' => [],
        ]);

        $this->command->info('✅ Super Admin created successfully!');
        $this->command->table(['Field', 'Value'], [
            ['ID', $superAdmin->id],
            ['Nama', $superAdmin->nama],
            ['NPP', $superAdmin->npp],
            ['Email', $superAdmin->email],
            ['Password', 'admin123'],
            ['Role', $superAdmin->role_user],
            ['Login URL', 'http://localhost:8000/admin'],
        ]);
    }
}
