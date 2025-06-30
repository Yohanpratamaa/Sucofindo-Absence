<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\OvertimeAssignment;
use App\Models\Izin;
use App\Models\Pegawai;
use Carbon\Carbon;

class EmployeeRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil data pegawai employee
        $employees = Pegawai::where('role_user', 'employee')->get();
        
        if ($employees->count() === 0) {
            $this->command->warn('Tidak ada pegawai dengan role employee. Membuat data sample employee...');
            
            // Buat sample employee jika belum ada
            $employee = Pegawai::create([
                'nama' => 'Employee Test',
                'npp' => 'EMP001',
                'email' => 'employee.test@sucofindo.com',
                'password' => bcrypt('password'),
                'role_user' => 'employee',
                'status' => 'active',
                'nik' => '1234567890123456',
                'alamat' => 'Jl. Test Employee No. 1',
                'nomor_handphone' => '081234567890',
                'jabatan_nama' => 'Staff',
                'jabatan_tunjangan' => 1000000,
                'posisi_nama' => 'Junior Staff',
                'posisi_tunjangan' => 500000,
            ]);
            
            $employees = collect([$employee]);
        }

        foreach ($employees as $employee) {
            $this->command->info("Membuat sample data untuk employee: {$employee->nama}");
            
            // Sample Overtime Requests dari employee
            $overtimeData = [
                [
                    'user_id' => $employee->id,
                    'assigned_by' => $employee->id, // Employee mengajukan sendiri
                    'overtime_id' => 'EMP-OT-001',
                    'assigned_at' => Carbon::now()->addDays(1)->setTime(19, 0, 0),
                    'keterangan' => 'Menyelesaikan laporan bulanan yang deadline besok pagi',
                    'status' => 'Assigned',
                    'created_at' => Carbon::now()->subHours(2),
                    'updated_at' => Carbon::now()->subHours(2),
                ],
                [
                    'user_id' => $employee->id,
                    'assigned_by' => $employee->id,
                    'overtime_id' => 'EMP-OT-002',
                    'assigned_at' => Carbon::now()->addDays(2)->setTime(18, 0, 0),
                    'keterangan' => 'Maintenance server dan backup database',
                    'status' => 'Assigned',
                    'created_at' => Carbon::now()->subHours(1),
                    'updated_at' => Carbon::now()->subHours(1),
                ],
                [
                    'user_id' => $employee->id,
                    'assigned_by' => $employee->id,
                    'overtime_id' => 'EMP-OT-003',
                    'assigned_at' => Carbon::now()->subDays(1)->setTime(20, 0, 0),
                    'keterangan' => 'Deployment aplikasi baru ke production server',
                    'status' => 'Accepted',
                    'approved_by' => 1, // Assuming admin ID 1
                    'approved_at' => Carbon::now()->subHours(6),
                    'created_at' => Carbon::now()->subDays(2),
                    'updated_at' => Carbon::now()->subHours(6),
                ],
                [
                    'user_id' => $employee->id,
                    'assigned_by' => $employee->id,
                    'overtime_id' => 'EMP-OT-004',
                    'assigned_at' => Carbon::now()->subDays(3)->setTime(17, 30, 0),
                    'keterangan' => 'Training untuk tim baru tentang sistem yang ada',
                    'status' => 'Rejected',
                    'approved_by' => 1,
                    'approved_at' => Carbon::now()->subDays(2),
                    'created_at' => Carbon::now()->subDays(4),
                    'updated_at' => Carbon::now()->subDays(2),
                ],
            ];

            foreach ($overtimeData as $data) {
                OvertimeAssignment::create($data);
            }

            // Sample Leave Requests dari employee
            $izinData = [
                [
                    'user_id' => $employee->id,
                    'tanggal_mulai' => Carbon::now()->addDays(5),
                    'tanggal_akhir' => Carbon::now()->addDays(5),
                    'jenis_izin' => 'sakit',
                    'keterangan' => 'Demam tinggi dan perlu istirahat untuk recovery',
                    'created_at' => Carbon::now()->subHours(3),
                    'updated_at' => Carbon::now()->subHours(3),
                ],
                [
                    'user_id' => $employee->id,
                    'tanggal_mulai' => Carbon::now()->addDays(10),
                    'tanggal_akhir' => Carbon::now()->addDays(12),
                    'jenis_izin' => 'cuti',
                    'keterangan' => 'Cuti tahunan untuk liburan keluarga',
                    'created_at' => Carbon::now()->subHours(1),
                    'updated_at' => Carbon::now()->subHours(1),
                ],
                [
                    'user_id' => $employee->id,
                    'tanggal_mulai' => Carbon::now()->subDays(2),
                    'tanggal_akhir' => Carbon::now()->subDays(2),
                    'jenis_izin' => 'izin',
                    'keterangan' => 'Mengurus dokumen penting di kantor pemerintahan',
                    'approved_by' => 1,
                    'approved_at' => Carbon::now()->subDays(3),
                    'created_at' => Carbon::now()->subDays(4),
                    'updated_at' => Carbon::now()->subDays(3),
                ],
                [
                    'user_id' => $employee->id,
                    'tanggal_mulai' => Carbon::now()->subDays(7),
                    'tanggal_akhir' => Carbon::now()->subDays(7),
                    'jenis_izin' => 'sakit',
                    'keterangan' => 'Flu dan batuk, tidak ingin menularkan ke rekan kerja',
                    'approved_by' => 1,
                    'approved_at' => null, // Ditolak
                    'created_at' => Carbon::now()->subDays(8),
                    'updated_at' => Carbon::now()->subDays(7),
                ],
            ];

            foreach ($izinData as $data) {
                Izin::create($data);
            }
        }

        $this->command->info("Sample data berhasil dibuat untuk employee requests!");
        $this->command->info("Data includes:");
        $this->command->info("- 4 overtime requests (2 pending, 1 approved, 1 rejected)");
        $this->command->info("- 4 leave requests (2 pending, 1 approved, 1 rejected)");
    }
}
