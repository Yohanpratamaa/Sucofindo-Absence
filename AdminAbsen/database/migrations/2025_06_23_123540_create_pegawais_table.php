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
            $table->unsignedBigInteger('id_fasilitas')->nullable();
            $table->unsignedBigInteger('id_jabatan')->nullable();
            $table->unsignedBigInteger('id_posisi')->nullable();
            $table->unsignedBigInteger('id_nomor_emergency')->nullable();
            $table->unsignedBigInteger('id_pendidikan')->nullable();
            $table->string('nik')->unique();
            $table->text('alamat')->nullable();
            $table->enum('status_pegawai', ['PTT', 'LS'])->nullable(false);
            $table->enum('status', ['active', 'resign'])->default('active');
            $table->enum('role_user', ['super admin', 'employee', 'Kepala Bidang'])->nullable(false);

            // Data JSON untuk repeater forms
            $table->json('pendidikan_list')->nullable();
            $table->json('emergency_contacts')->nullable();
            $table->json('fasilitas_list')->nullable(); // Tambahan untuk fasilitas list

            // Data fasilitas langsung (untuk backward compatibility)
            $table->string('nama_jaminan')->nullable();
            $table->string('no_jaminan')->nullable();
            $table->integer('transport')->default(0);
            $table->integer('overtime_rate')->default(0);
            $table->integer('payroll')->default(0);
            $table->text('keterangan_fasilitas')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints (optional)
            // $table->foreign('id_fasilitas')->references('id')->on('fasilitas')->onDelete('set null');
            // $table->foreign('id_jabatan')->references('id')->on('jabatans')->onDelete('set null');
            // $table->foreign('id_posisi')->references('id')->on('posisis')->onDelete('set null');
            // $table->foreign('id_nomor_emergency')->references('id')->on('nomor_emergencies')->onDelete('set null');
            // $table->foreign('id_pendidikan')->references('id')->on('pendidikans')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pegawais');
    }
};
