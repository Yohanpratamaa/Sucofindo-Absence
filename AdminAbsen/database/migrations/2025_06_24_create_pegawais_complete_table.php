<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pegawais', function (Blueprint $table) {
            $table->id();

            // Tab Users - Data Dasar Pegawai
            $table->string('nama');
            $table->string('npp')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('nik')->unique();
            $table->enum('status_pegawai', ['PTT', 'LS']);
            $table->string('nomor_handphone', 15);
            $table->enum('status', ['active', 'resign'])->default('active');
            $table->enum('role_user', ['super admin', 'employee', 'Kepala Bidang']);
            $table->text('alamat')->nullable();

            // Tab Jabatan - Data Jabatan (tidak menggunakan foreign key)
            $table->string('jabatan_nama')->nullable();
            $table->decimal('jabatan_tunjangan', 15, 2)->default(0);

            // Tab Posisi - Data Posisi (tidak menggunakan foreign key)
            $table->string('posisi_nama')->nullable();
            $table->decimal('posisi_tunjangan', 15, 2)->default(0);

            // Tab Pendidikan - Data dalam format JSON untuk multiple records
            $table->json('pendidikan_list')->nullable();
            // Struktur JSON akan berisi array seperti:
            // [
            //     {
            //         "jenjang": "S1",
            //         "sekolah_univ": "Universitas Indonesia",
            //         "fakultas_program_studi": "Teknik Informatika",
            //         "jurusan": "Sistem Informasi",
            //         "thn_masuk": "2015-01-01",
            //         "thn_lulus": "2019-01-01",
            //         "ipk_nilai": "3.50",
            //         "ijazah": "path/to/ijazah.pdf"
            //     }
            // ]

            // Tab Nomor Emergency - Data dalam format JSON untuk multiple contacts
            $table->json('emergency_contacts')->nullable();
            // Struktur JSON akan berisi array seperti:
            // [
            //     {
            //         "relationship": "Ayah",
            //         "nama_kontak": "John Doe",
            //         "no_emergency": "081234567890"
            //     }
            // ]

            // Tab Fasilitas - Data dalam format JSON untuk multiple fasilitas
            $table->json('fasilitas_list')->nullable();
            // Struktur JSON akan berisi array seperti:
            // [
            //     {
            //         "nama_jaminan": "BPJS Kesehatan",
            //         "no_jaminan": "123456789",
            //         "jenis_fasilitas": "BPJS Kesehatan",
            //         "provider": "BPJS",
            //         "nilai_fasilitas": 100000
            //     }
            // ]

            $table->timestamps();
            $table->softDeletes();

            // Indexes untuk performa
            $table->index(['status', 'status_pegawai']);
            $table->index('role_user');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawais');
    }
};
