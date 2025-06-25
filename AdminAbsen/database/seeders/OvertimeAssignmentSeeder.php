<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\OvertimeAssignment;
use App\Models\Pegawai;
use Carbon\Carbon;

class OvertimeAssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil data pegawai yang sudah ada
        $pegawai = Pegawai::all();
        
        if ($pegawai->count() < 2) {
            $this->command->warn('Tidak cukup data pegawai untuk membuat sample overtime assignments. Minimal 2 pegawai diperlukan.');
            return;
        }

        // Data sample overtime assignments
        $overtimeData = [
            [
                'user_id' => $pegawai[0]->id,
                'assigned_by' => $pegawai[1]->id,
                'overtime_id' => 'OT-2025-001',
                'assigned_at' => Carbon::now()->subDays(10)->format('Y-m-d H:i:s'),
                'approved_by' => null,
                'approved_at' => null,
                'assign_by' => null,
                'status' => 'Assigned',
                'created_at' => Carbon::now()->subDays(10),
                'updated_at' => Carbon::now()->subDays(10),
            ],
            [
                'user_id' => $pegawai[1]->id,
                'assigned_by' => $pegawai[0]->id,
                'overtime_id' => 'OT-2025-002',
                'assigned_at' => Carbon::now()->subDays(8)->format('Y-m-d H:i:s'),
                'approved_by' => $pegawai[0]->id,
                'approved_at' => Carbon::now()->subDays(7)->format('Y-m-d H:i:s'),
                'assign_by' => null,
                'status' => 'Accepted',
                'created_at' => Carbon::now()->subDays(8),
                'updated_at' => Carbon::now()->subDays(7),
            ],
            [
                'user_id' => $pegawai[0]->id,
                'assigned_by' => $pegawai[1]->id,
                'overtime_id' => 'OT-2025-003',
                'assigned_at' => Carbon::now()->subDays(6)->format('Y-m-d H:i:s'),
                'approved_by' => $pegawai[1]->id,
                'approved_at' => Carbon::now()->subDays(5)->format('Y-m-d H:i:s'),
                'assign_by' => null,
                'status' => 'Rejected',
                'created_at' => Carbon::now()->subDays(6),
                'updated_at' => Carbon::now()->subDays(5),
            ],
            [
                'user_id' => $pegawai[1]->id,
                'assigned_by' => $pegawai[0]->id,
                'overtime_id' => 'OT-2025-004',
                'assigned_at' => Carbon::now()->subDays(4)->format('Y-m-d H:i:s'),
                'approved_by' => null,
                'approved_at' => null,
                'assign_by' => null,
                'status' => 'Assigned',
                'created_at' => Carbon::now()->subDays(4),
                'updated_at' => Carbon::now()->subDays(4),
            ],
            [
                'user_id' => $pegawai[0]->id,
                'assigned_by' => $pegawai[1]->id,
                'overtime_id' => 'OT-2025-005',
                'assigned_at' => Carbon::now()->subDays(3)->format('Y-m-d H:i:s'),
                'approved_by' => $pegawai[1]->id,
                'approved_at' => Carbon::now()->subDays(2)->format('Y-m-d H:i:s'),
                'assign_by' => null,
                'status' => 'Accepted',
                'created_at' => Carbon::now()->subDays(3),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'user_id' => $pegawai[1]->id,
                'assigned_by' => $pegawai[0]->id,
                'overtime_id' => 'OT-2025-006',
                'assigned_at' => Carbon::now()->subDays(2)->format('Y-m-d H:i:s'),
                'approved_by' => null,
                'approved_at' => null,
                'assign_by' => null,
                'status' => 'Assigned',
                'created_at' => Carbon::now()->subDays(2),
                'updated_at' => Carbon::now()->subDays(2),
            ],
            [
                'user_id' => $pegawai[0]->id,
                'assigned_by' => $pegawai[1]->id,
                'overtime_id' => 'OT-2025-007',
                'assigned_at' => Carbon::now()->subDay()->format('Y-m-d H:i:s'),
                'approved_by' => null,
                'approved_at' => null,
                'assign_by' => null,
                'status' => 'Assigned',
                'created_at' => Carbon::now()->subDay(),
                'updated_at' => Carbon::now()->subDay(),
            ],
        ];

        // Tambahkan data sample jika terdapat pegawai lebih dari 2
        if ($pegawai->count() >= 3) {
            $overtimeData = array_merge($overtimeData, [
                [
                    'user_id' => $pegawai[2]->id,
                    'assigned_by' => $pegawai[0]->id,
                    'overtime_id' => 'OT-2025-008',
                    'assigned_at' => Carbon::now()->subHours(12)->format('Y-m-d H:i:s'),
                    'approved_by' => null,
                    'approved_at' => null,
                    'assign_by' => null,
                    'status' => 'Assigned',
                    'created_at' => Carbon::now()->subHours(12),
                    'updated_at' => Carbon::now()->subHours(12),
                ],
                [
                    'user_id' => $pegawai[2]->id,
                    'assigned_by' => $pegawai[1]->id,
                    'overtime_id' => 'OT-2025-009',
                    'assigned_at' => Carbon::now()->subDays(15)->format('Y-m-d H:i:s'),
                    'approved_by' => $pegawai[0]->id,
                    'approved_at' => Carbon::now()->subDays(14)->format('Y-m-d H:i:s'),
                    'assign_by' => null,
                    'status' => 'Rejected',
                    'created_at' => Carbon::now()->subDays(15),
                    'updated_at' => Carbon::now()->subDays(14),
                ],
            ]);
        }

        // Insert data ke database
        foreach ($overtimeData as $data) {
            OvertimeAssignment::create($data);
        }

        $this->command->info('Sample overtime assignments berhasil dibuat: ' . count($overtimeData) . ' records');
    }
}
