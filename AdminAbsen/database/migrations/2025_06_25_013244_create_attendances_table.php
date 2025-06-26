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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // FK ke pegawai
            $table->unsignedBigInteger('office_working_hours_id'); // FK ke jam kerja kantor
            $table->time('check_in'); // Waktu check in
            $table->decimal('longitude_absen_masuk', 11, 8); // Longitude check in
            $table->decimal('latitude_absen_masuk', 10, 8); // Latitude check in
            $table->string('picture_absen_masuk')->nullable(); // Foto check in
            $table->time('absen_siang')->nullable(); // Waktu absen siang
            $table->decimal('longitude_absen_siang', 11, 8)->nullable(); // Longitude absen siang
            $table->decimal('latitude_absen_siang', 10, 8)->nullable(); // Latitude absen siang
            $table->string('picture_absen_siang')->nullable(); // Foto absen siang
            $table->time('check_out')->nullable(); // Waktu check out
            $table->decimal('longitude_absen_pulang', 11, 8)->nullable(); // Longitude check out
            $table->decimal('latitude_absen_pulang', 10, 8)->nullable(); // Latitude check out
            $table->string('picture_absen_pulang')->nullable(); // Foto check out
            $table->integer('overtime')->default(0); // Jam lembur dalam menit
            $table->enum('attendance_type', ['WFO', 'Dinas Luar'])->default('WFO'); // Tipe absensi
            $table->timestamps();

            // Index untuk performa
            $table->index(['user_id', 'created_at']);
            $table->index(['attendance_type', 'created_at']);
            $table->index(['office_working_hours_id', 'created_at']);
            $table->index('check_in');
            $table->index('check_out');

            // Foreign key constraints akan ditambahkan di migration terpisah setelah semua tabel dibuat
            // $table->foreign('user_id')->references('id')->on('pegawais')->onDelete('cascade');
            // $table->foreign('office_working_hours_id')->references('id')->on('office_schedules')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
