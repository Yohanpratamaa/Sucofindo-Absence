<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pegawais', function (Blueprint $table) {
            $table->id();

            // Users
            $table->string('nama')->nullable(false);
            $table->string('npp')->unique();
            $table->string('email')->unique()->nullable(false);
            $table->string('password')->nullable(false);
            $table->unsignedBigInteger('id_jaminan')->nullable();
            $table->unsignedBigInteger('id_jabatan')->nullable();
            $table->unsignedBigInteger('id_posisi')->nullable();
            $table->unsignedBigInteger('id_nomor_emergency')->nullable();
            $table->unsignedBigInteger('id_pendidikan')->nullable();
            $table->string('nik')->unique();
            $table->text('alamat')->nullable();
            $table->enum('status_pegawai', ['PTT', 'LS'])->nullable(false);
            $table->enum('status', ['active', 'resign'])->default('active');
            $table->enum('role_user', ['super admin', 'employee', 'Kepala Bidang'])->nullable(false);

            // // Informasi Umum
            // $table->string('nip', 20)->unique();
            // $table->string('nama');
            // $table->string('email')->unique();
            // $table->string('phone', 15)->nullable();
            // $table->enum('gender', ['L', 'P']);
            // $table->date('tanggal_lahir');
            // $table->string('tempat_lahir', 100)->nullable();
            // $table->enum('agama', ['islam', 'kristen', 'katolik', 'hindu', 'buddha', 'konghucu']);
            // $table->enum('status_perkawinan', ['belum_kawin', 'kawin', 'cerai_hidup', 'cerai_mati']);
            // $table->string('kewarganegaraan', 50)->default('Indonesia');
            // $table->text('alamat')->nullable();
            // $table->enum('jabatan', ['manager', 'supervisor', 'staff', 'intern']);
            // $table->enum('divisi', ['it', 'hr', 'finance', 'operations', 'marketing']);
            // $table->date('tanggal_masuk');
            // $table->enum('status_karyawan', ['tetap', 'kontrak', 'magang', 'freelance']);

            // // Pendidikan
            // $table->enum('pendidikan_terakhir', ['SD', 'SMP', 'SMA', 'D1', 'D2', 'D3', 'S1', 'S2', 'S3']);
            // $table->string('nama_sekolah')->nullable();
            // $table->string('jurusan')->nullable();
            // $table->year('tahun_lulus')->nullable();
            // $table->decimal('ipk_nilai', 3, 2)->nullable();
            // $table->enum('akreditasi', ['A', 'B', 'C', 'tidak_terakreditasi'])->nullable();
            // $table->text('sertifikat_keahlian')->nullable();

            // // Emergency Contact
            // $table->string('emergency_contact_name');
            // $table->enum('emergency_contact_relation', ['orangtua', 'suami', 'istri', 'anak', 'saudara', 'kerabat', 'teman']);
            // $table->string('emergency_contact_phone', 15);
            // $table->string('emergency_contact_phone_2', 15)->nullable();
            // $table->text('emergency_contact_address')->nullable();
            // $table->string('emergency_contact_name_2')->nullable();
            // $table->enum('emergency_contact_relation_2', ['orangtua', 'suami', 'istri', 'anak', 'saudara', 'kerabat', 'teman'])->nullable();
            // $table->string('emergency_contact_phone_alt', 15)->nullable();

            // // Jaminan
            // $table->string('no_bpjs_kesehatan', 20)->nullable();
            // $table->string('no_bpjs_ketenagakerjaan', 20)->nullable();
            // $table->string('no_ktp', 16)->unique();
            // $table->string('no_npwp', 20)->nullable();
            // $table->string('no_rekening', 20)->nullable();
            // $table->string('nama_bank', 100)->nullable();
            // $table->string('nama_pemilik_rekening')->nullable();
            // $table->enum('jenis_rekening', ['tabungan', 'giro', 'deposito'])->nullable();
            // $table->string('asuransi_nama')->nullable();
            // $table->string('asuransi_no_polis', 50)->nullable();
            // $table->date('asuransi_mulai')->nullable();
            // $table->date('asuransi_berakhir')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pegawais');
    }
};
