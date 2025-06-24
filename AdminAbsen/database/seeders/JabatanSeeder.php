<?php

namespace Database\Seeders;

use App\Models\Jabatan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JabatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jabatans = [
            [
                'nama' => 'Direktur Utama',
                'tunjangan' => 15000000,
                'deskripsi' => 'Pimpinan tertinggi perusahaan yang bertanggung jawab atas seluruh operasional dan strategi perusahaan',
                'status' => 'active',
            ],
            [
                'nama' => 'Direktur Operasional',
                'tunjangan' => 12000000,
                'deskripsi' => 'Bertanggung jawab atas operasional harian perusahaan dan koordinasi antar divisi',
                'status' => 'active',
            ],
            [
                'nama' => 'Manager',
                'tunjangan' => 8000000,
                'deskripsi' => 'Memimpin dan mengkoordinasikan tim dalam suatu divisi atau departemen',
                'status' => 'active',
            ],
            [
                'nama' => 'Supervisor',
                'tunjangan' => 5000000,
                'deskripsi' => 'Mengawasi dan membimbing karyawan dalam pelaksanaan tugas sehari-hari',
                'status' => 'active',
            ],
            [
                'nama' => 'Staff Senior',
                'tunjangan' => 3000000,
                'deskripsi' => 'Karyawan berpengalaman yang menangani tugas-tugas kompleks dan membimbing staff junior',
                'status' => 'active',
            ],
            [
                'nama' => 'Staff',
                'tunjangan' => 2000000,
                'deskripsi' => 'Karyawan yang menjalankan tugas operasional dan administratif',
                'status' => 'active',
            ],
            [
                'nama' => 'Staff Junior',
                'tunjangan' => 1500000,
                'deskripsi' => 'Karyawan entry level yang sedang dalam masa pembelajaran dan pengembangan',
                'status' => 'active',
            ],
            [
                'nama' => 'Magang',
                'tunjangan' => 500000,
                'deskripsi' => 'Peserta magang yang sedang menjalani program pelatihan kerja',
                'status' => 'active',
            ],
        ];

        foreach ($jabatans as $jabatan) {
            Jabatan::create($jabatan);
        }
    }
}
