<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pegawai;
use Illuminate\Support\Facades\Hash;

class PegawaiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample data pegawai dengan semua tab
        $pegawaiData = [
            [
                // Tab Users
                'nama' => 'John Doe',
                'npp' => 'NPP001',
                'email' => 'john.doe@sucofindo.com',
                'password' => Hash::make('password123'),
                'nik' => '1234567890123456',
                'status_pegawai' => 'LS',
                'nomor_handphone' => '081234567890',
                'status' => 'active',
                'role_user' => 'employee',
                'alamat' => 'Jl. Kebon Jeruk No. 123, Jakarta Barat',

                // Tab Jabatan
                'jabatan_nama' => 'Senior Developer',
                'jabatan_tunjangan' => 2000000,

                // Tab Posisi
                'posisi_nama' => 'Lead Programmer',
                'posisi_tunjangan' => 1500000,

                // Tab Pendidikan (JSON)
                'pendidikan_list' => [
                    [
                        'jenjang' => 'S1',
                        'sekolah_univ' => 'Universitas Indonesia',
                        'fakultas_program_studi' => 'Ilmu Komputer',
                        'jurusan' => 'Sistem Informasi',
                        'thn_masuk' => '2015-08-01',
                        'thn_lulus' => '2019-07-01',
                        'ipk_nilai' => '3.75',
                        'ijazah' => null
                    ],
                    [
                        'jenjang' => 'SMA',
                        'sekolah_univ' => 'SMA Negeri 1 Jakarta',
                        'fakultas_program_studi' => null,
                        'jurusan' => 'IPA',
                        'thn_masuk' => '2012-07-01',
                        'thn_lulus' => '2015-06-01',
                        'ipk_nilai' => '85.5',
                        'ijazah' => null
                    ]
                ],

                // Tab Emergency (JSON)
                'emergency_contacts' => [
                    [
                        'relationship' => 'Ayah',
                        'nama_kontak' => 'David Doe',
                        'no_emergency' => '081234567891'
                    ],
                    [
                        'relationship' => 'Ibu',
                        'nama_kontak' => 'Jane Doe',
                        'no_emergency' => '081234567892'
                    ]
                ],

                // Tab Fasilitas (JSON)
                'fasilitas_list' => [
                    [
                        'nama_jaminan' => 'BPJS Kesehatan',
                        'no_jaminan' => '0001234567890',
                        'jenis_fasilitas' => 'BPJS Kesehatan',
                        'provider' => 'BPJS',
                        'nilai_fasilitas' => 150000
                    ],
                    [
                        'nama_jaminan' => 'BPJS Ketenagakerjaan',
                        'no_jaminan' => '0001234567891',
                        'jenis_fasilitas' => 'BPJS Ketenagakerjaan',
                        'provider' => 'BPJS',
                        'nilai_fasilitas' => 200000
                    ],
                    [
                        'nama_jaminan' => 'Tunjangan Transport',
                        'no_jaminan' => null,
                        'jenis_fasilitas' => 'Tunjangan Transport',
                        'provider' => 'Sucofindo',
                        'nilai_fasilitas' => 500000
                    ]
                ]
            ],
            [
                // Tab Users
                'nama' => 'Jane Smith',
                'npp' => 'NPP002',
                'email' => 'jane.smith@sucofindo.com',
                'password' => Hash::make('password123'),
                'nik' => '1234567890123457',
                'status_pegawai' => 'PTT',
                'nomor_handphone' => '081234567893',
                'status' => 'active',
                'role_user' => 'Kepala Bidang',
                'alamat' => 'Jl. Sudirman No. 456, Jakarta Pusat',

                // Tab Jabatan
                'jabatan_nama' => 'Manager IT',
                'jabatan_tunjangan' => 3000000,

                // Tab Posisi
                'posisi_nama' => 'Head of Development',
                'posisi_tunjangan' => 2500000,

                // Tab Pendidikan (JSON)
                'pendidikan_list' => [
                    [
                        'jenjang' => 'S2',
                        'sekolah_univ' => 'Institut Teknologi Bandung',
                        'fakultas_program_studi' => 'Teknik Informatika',
                        'jurusan' => 'Sistem Informasi',
                        'thn_masuk' => '2019-08-01',
                        'thn_lulus' => '2021-07-01',
                        'ipk_nilai' => '3.85',
                        'ijazah' => null
                    ],
                    [
                        'jenjang' => 'S1',
                        'sekolah_univ' => 'Universitas Gadjah Mada',
                        'fakultas_program_studi' => 'Teknik',
                        'jurusan' => 'Teknik Informatika',
                        'thn_masuk' => '2015-08-01',
                        'thn_lulus' => '2019-07-01',
                        'ipk_nilai' => '3.90',
                        'ijazah' => null
                    ]
                ],

                // Tab Emergency (JSON)
                'emergency_contacts' => [
                    [
                        'relationship' => 'Suami',
                        'nama_kontak' => 'Michael Smith',
                        'no_emergency' => '081234567894'
                    ]
                ],

                // Tab Fasilitas (JSON)
                'fasilitas_list' => [
                    [
                        'nama_jaminan' => 'BPJS Kesehatan',
                        'no_jaminan' => '0001234567895',
                        'jenis_fasilitas' => 'BPJS Kesehatan',
                        'provider' => 'BPJS',
                        'nilai_fasilitas' => 150000
                    ],
                    [
                        'nama_jaminan' => 'Asuransi Jiwa',
                        'no_jaminan' => 'AJ001234567',
                        'jenis_fasilitas' => 'Asuransi Jiwa',
                        'provider' => 'Prudential',
                        'nilai_fasilitas' => 500000
                    ],
                    [
                        'nama_jaminan' => 'Tunjangan Makan',
                        'no_jaminan' => null,
                        'jenis_fasilitas' => 'Tunjangan Makan',
                        'provider' => 'Sucofindo',
                        'nilai_fasilitas' => 300000
                    ]
                ]
            ]
        ];

        // Insert data
        foreach ($pegawaiData as $data) {
            Pegawai::create($data);
        }
    }
}
