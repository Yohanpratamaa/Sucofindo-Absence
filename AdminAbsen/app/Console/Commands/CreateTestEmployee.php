<?php

namespace App\Console\Commands;

use App\Models\Pegawai;
use Illuminate\Console\Command;

class CreateTestEmployee extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:create-employee {--email=employee@test.com} {--role=employee}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a test employee for testing authentication';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->option('email');
        $role = $this->option('role');

        // Check if employee already exists
        if (Pegawai::where('email', $email)->exists()) {
            $this->error("Employee with email {$email} already exists!");
            return;
        }

        $pegawai = Pegawai::create([
            'nama' => ucfirst($role) . ' Test User',
            'npp' => strtoupper(substr($role, 0, 3)) . '001',
            'email' => $email,
            'password' => 'password123',
            'role_user' => $role,
            'status' => 'active',
            'nik' => '123456789012345' . rand(0, 9),
            'nomor_handphone' => '081234567890',
            'alamat' => 'Test Address'
        ]);

        $this->info("Test employee created successfully!");
        $this->table(
            ['Field', 'Value'],
            [
                ['ID', $pegawai->id],
                ['Nama', $pegawai->nama],
                ['NPP', $pegawai->npp],
                ['Email', $pegawai->email],
                ['Role', $pegawai->role_user],
                ['Login URL', $this->getLoginUrl($role)],
            ]
        );
    }

    private function getLoginUrl($role)
    {
        switch ($role) {
            case 'employee':
                return url('/pegawai/login');
            case 'Kepala Bidang':
                return url('/kepala-bidang/login');
            case 'admin':
            case 'super admin':
                return url('/admin/login');
            default:
                return url('/admin/login');
        }
    }
}
