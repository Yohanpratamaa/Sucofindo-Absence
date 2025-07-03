<?php

namespace Database\Seeders;

use App\Models\Izin;
use App\Models\Pegawai;
use App\Models\Attendance;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class IzinAttendanceTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * This seeder creates sample Izin data with approved status to test
     * the automatic attendance record creation feature.
     */
    public function run(): void
    {
        $this->command->info('🚀 Membuat data test untuk integrasi Izin-Attendance...');

        // Ambil beberapa pegawai untuk contoh data
        $pegawais = Pegawai::limit(3)->get();

        if ($pegawais->isEmpty()) {
            $this->command->warn('❌ Tidak ada data pegawai. Jalankan PegawaiSeeder terlebih dahulu.');
            return;
        }

        // Admin yang akan meng-approve (ambil pegawai pertama sebagai admin)
        $admin = $pegawais->first();

        // Data izin yang akan dibuat dengan status approved untuk testing
        $izinTestData = [
            [
                'user_id' => $pegawais->get(0)?->id,
                'tanggal_mulai' => Carbon::now()->subDays(7)->startOfDay(), // 7 hari yang lalu
                'tanggal_akhir' => Carbon::now()->subDays(5)->startOfDay(),  // 5 hari yang lalu (3 hari)
                'jenis_izin' => 'sakit',
                'keterangan' => 'Sakit demam tinggi dan perlu istirahat total. Sudah diperiksakan ke dokter.',
                'dokumen_pendukung' => 'surat-dokter-sakit.pdf',
                'approved_by' => $admin->id,
                'approved_at' => Carbon::now()->subDays(8), // Approved sebelum tanggal mulai
                'lokasi_berobat' => 'RS Siloam Jakarta',
                'nama_dokter' => 'Dr. Ahmad Sutrisno, Sp.PD',
                'diagnosa_dokter' => 'Demam tinggi dan flu berat',
                'keterangan_medis' => 'Pasien memerlukan istirahat total selama 3 hari',
            ],
            [
                'user_id' => $pegawais->get(1)?->id,
                'tanggal_mulai' => Carbon::now()->subDays(4)->startOfDay(), // 4 hari yang lalu
                'tanggal_akhir' => Carbon::now()->subDays(2)->startOfDay(),  // 2 hari yang lalu (3 hari)
                'jenis_izin' => 'cuti',
                'keterangan' => 'Cuti tahunan untuk liburan keluarga ke Yogyakarta.',
                'dokumen_pendukung' => null,
                'approved_by' => $admin->id,
                'approved_at' => Carbon::now()->subDays(10), // Approved jauh sebelumnya
            ],
            [
                'user_id' => $pegawais->get(2)?->id,
                'tanggal_mulai' => Carbon::now()->subDays(1)->startOfDay(), // Kemarin
                'tanggal_akhir' => Carbon::now()->subDays(1)->startOfDay(),  // Kemarin (1 hari)
                'jenis_izin' => 'izin',
                'keterangan' => 'Menghadiri pernikahan saudara kandung.',
                'dokumen_pendukung' => 'undangan-pernikahan.jpg',
                'approved_by' => $admin->id,
                'approved_at' => Carbon::now()->subDays(3), // Approved 3 hari sebelumnya
            ],
            [
                'user_id' => $pegawais->get(0)?->id,
                'tanggal_mulai' => Carbon::now()->addDays(1)->startOfDay(), // Besok
                'tanggal_akhir' => Carbon::now()->addDays(3)->startOfDay(),  // Lusa (3 hari)
                'jenis_izin' => 'cuti',
                'keterangan' => 'Cuti untuk menghadiri acara keluarga di Surabaya.',
                'dokumen_pendukung' => null,
                'approved_by' => $admin->id,
                'approved_at' => Carbon::now(), // Approved sekarang (akan trigger attendance creation)
            ],
            [
                'user_id' => $pegawais->get(1)?->id,
                'tanggal_mulai' => Carbon::now()->addDays(5)->startOfDay(), // 5 hari ke depan
                'tanggal_akhir' => Carbon::now()->addDays(5)->startOfDay(),  // 5 hari ke depan (1 hari)
                'jenis_izin' => 'sakit',
                'keterangan' => 'Kontrol rutin ke dokter spesialis jantung.',
                'dokumen_pendukung' => 'surat-kontrol-dokter.pdf',
                'approved_by' => $admin->id,
                'approved_at' => Carbon::now(), // Approved sekarang
                'lokasi_berobat' => 'RS Harapan Kita Jakarta',
                'nama_dokter' => 'Dr. Sari Wijaya, Sp.JP',
                'diagnosa_dokter' => 'Kontrol rutin post operasi jantung',
                'keterangan_medis' => 'Pemeriksaan lanjutan pasca operasi',
            ],
        ];

        $createdIzin = [];
        $attendanceCount = 0;

        foreach ($izinTestData as $data) {
            // Buat izin
            $izin = Izin::create($data);
            $createdIzin[] = $izin;

            // Hitung berapa hari untuk izin ini
            $start = Carbon::parse($data['tanggal_mulai']);
            $end = Carbon::parse($data['tanggal_akhir']);
            $days = $start->diffInDays($end) + 1;
            $attendanceCount += $days;

            $this->command->info("   ✅ Izin {$data['jenis_izin']} untuk {$izin->user->nama} ({$days} hari)");
        }

        // Buat juga beberapa izin yang belum di-approve untuk kontras
        $pendingIzinData = [
            [
                'user_id' => $pegawais->get(2)?->id,
                'tanggal_mulai' => Carbon::now()->addDays(10)->startOfDay(),
                'tanggal_akhir' => Carbon::now()->addDays(12)->startOfDay(),
                'jenis_izin' => 'cuti',
                'keterangan' => 'Cuti untuk liburan akhir tahun.',
                'dokumen_pendukung' => null,
                'approved_by' => null,
                'approved_at' => null,
            ],
            [
                'user_id' => $pegawais->get(1)?->id,
                'tanggal_mulai' => Carbon::now()->addDays(15)->startOfDay(),
                'tanggal_akhir' => Carbon::now()->addDays(15)->startOfDay(),
                'jenis_izin' => 'izin',
                'keterangan' => 'Mengurus dokumen di kantor kelurahan.',
                'dokumen_pendukung' => null,
                'approved_by' => null,
                'approved_at' => null,
            ],
        ];

        foreach ($pendingIzinData as $data) {
            $izin = Izin::create($data);
            $this->command->info("   ⏳ Izin pending: {$data['jenis_izin']} untuk {$izin->user->nama}");
        }

        // Hitung berapa attendance record yang seharusnya terbuat
        $actualAttendanceCount = Attendance::whereNotNull('izin_id')->count();

        $this->command->info('');
        $this->command->info('📊 RINGKASAN DATA TEST:');
        $this->command->info('   📝 Total Izin Approved: ' . count($createdIzin));
        $this->command->info('   📝 Total Izin Pending: ' . count($pendingIzinData));
        $this->command->info('   📅 Expected Attendance Records: ' . $attendanceCount);
        $this->command->info('   📅 Actual Attendance Records: ' . $actualAttendanceCount);

        if ($actualAttendanceCount > 0) {
            $this->command->info('   ✅ Fitur automatic attendance creation BERHASIL!');
        } else {
            $this->command->warn('   ⚠️  Belum ada attendance record otomatis. Pastikan IzinObserver sudah terdaftar.');
        }

        $this->command->info('');
        $this->command->info('🎯 CARA TESTING:');
        $this->command->info('   1. Buka halaman "My All Attendance" di Filament');
        $this->command->info('   2. Filter berdasarkan "Izin/Sakit/Cuti"');
        $this->command->info('   3. Lihat kolom "Status" dan "Keterangan"');
        $this->command->info('   4. Coba approve izin yang pending untuk melihat attendance otomatis terbuat');
    }
}
