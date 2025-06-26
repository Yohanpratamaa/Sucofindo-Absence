<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pegawai;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateSuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create-super-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new super admin account';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Create Super Admin Account ===');
        $this->newLine();

        // Get input from user
        $nama = $this->ask('Nama Lengkap');
        $npp = $this->ask('NPP (Nomor Pokok Pegawai)');
        $email = $this->ask('Email', strtolower($npp) . '@sucofindo.com');
        $password = $this->secret('Password (kosongkan untuk default: admin123)');
        $nik = $this->ask('NIK');

        // Set default password if empty
        if (empty($password)) {
            $password = 'admin123';
        }

        // Validate input
        $validator = Validator::make([
            'nama' => $nama,
            'npp' => $npp,
            'email' => $email,
            'password' => $password,
            'nik' => $nik,
        ], [
            'nama' => 'required|string|max:255',
            'npp' => 'required|string|unique:pegawais,npp|max:50',
            'email' => 'required|email|unique:pegawais,email|max:255',
            'password' => 'required|string|min:6',
            'nik' => 'required|string|unique:pegawais,nik|max:20',
        ]);

        if ($validator->fails()) {
            $this->error('Validation failed:');
            foreach ($validator->errors()->all() as $error) {
                $this->error('- ' . $error);
            }
            return 1;
        }

        try {
            // Create super admin
            $superAdmin = Pegawai::create([
                'nama' => $nama,
                'npp' => $npp,
                'email' => $email,
                'password' => $password, // Will be hashed by mutator
                'nik' => $nik,
                'status_pegawai' => 'PTT',
                'nomor_handphone' => '08123456789', // Default
                'status' => 'active',
                'role_user' => 'super admin',
                'alamat' => 'Jakarta', // Default
                'jabatan_nama' => 'System Administrator',
                'jabatan_tunjangan' => 0,
                'posisi_nama' => 'System Administrator',
                'posisi_tunjangan' => 0,
                'pendidikan_list' => [],
                'emergency_contacts' => [],
                'fasilitas_list' => [],
            ]);

            $this->newLine();
            $this->info('✅ Super Admin berhasil dibuat!');
            $this->newLine();
            $this->table(['Field', 'Value'], [
                ['ID', $superAdmin->id],
                ['Nama', $superAdmin->nama],
                ['NPP', $superAdmin->npp],
                ['Email', $superAdmin->email],
                ['Password', $password],
                ['Role', $superAdmin->role_user],
                ['Status', $superAdmin->status],
            ]);
            $this->newLine();
            $this->info('Login URL: http://localhost:8000/admin');
            $this->warn('⚠️  Segera ganti password setelah login pertama kali!');

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Gagal membuat super admin: ' . $e->getMessage());
            return 1;
        }
    }
}
