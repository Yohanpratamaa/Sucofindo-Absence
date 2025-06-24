<?php

namespace Database\Seeders;

use App\Models\Posisi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PosisiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $posisis = [
            [
                'nama' => 'Kepala Divisi IT',
                'tunjangan' => 6000000,
                'deskripsi' => 'Memimpin divisi teknologi informasi dan bertanggung jawab atas infrastruktur IT perusahaan',
                'status' => 'active',
            ],
            [
                'nama' => 'Kepala Divisi HRD',
                'tunjangan' => 5500000,
                'deskripsi' => 'Memimpin divisi sumber daya manusia dan mengelola kebijakan karyawan',
                'status' => 'active',
            ],
            [
                'nama' => 'Kepala Divisi Keuangan',
                'tunjangan' => 6000000,
                'deskripsi' => 'Memimpin divisi keuangan dan bertanggung jawab atas laporan keuangan perusahaan',
                'status' => 'active',
            ],
            [
                'nama' => 'Kepala Divisi Marketing',
                'tunjangan' => 5000000,
                'deskripsi' => 'Memimpin divisi pemasaran dan mengembangkan strategi promosi produk',
                'status' => 'active',
            ],
            [
                'nama' => 'Software Developer',
                'tunjangan' => 4000000,
                'deskripsi' => 'Mengembangkan dan memelihara aplikasi software perusahaan',
                'status' => 'active',
            ],
            [
                'nama' => 'System Administrator',
                'tunjangan' => 3500000,
                'deskripsi' => 'Mengelola dan memelihara infrastruktur IT server dan jaringan',
                'status' => 'active',
            ],
            [
                'nama' => 'Database Administrator',
                'tunjangan' => 3800000,
                'deskripsi' => 'Mengelola dan memelihara database perusahaan',
                'status' => 'active',
            ],
            [
                'nama' => 'HR Specialist',
                'tunjangan' => 3000000,
                'deskripsi' => 'Menangani rekrutmen, pelatihan, dan administrasi karyawan',
                'status' => 'active',
            ],
            [
                'nama' => 'Accounting Staff',
                'tunjangan' => 2500000,
                'deskripsi' => 'Menangani pembukuan dan laporan keuangan',
                'status' => 'active',
            ],
            [
                'nama' => 'Marketing Staff',
                'tunjangan' => 2800000,
                'deskripsi' => 'Melaksanakan strategi pemasaran dan promosi produk',
                'status' => 'active',
            ],
            [
                'nama' => 'Customer Service',
                'tunjangan' => 2000000,
                'deskripsi' => 'Melayani keluhan dan pertanyaan pelanggan',
                'status' => 'active',
            ],
            [
                'nama' => 'Administrative Staff',
                'tunjangan' => 1800000,
                'deskripsi' => 'Menangani administrasi umum dan dokumentasi perusahaan',
                'status' => 'active',
            ],
        ];

        foreach ($posisis as $posisi) {
            Posisi::create($posisi);
        }
    }
}
