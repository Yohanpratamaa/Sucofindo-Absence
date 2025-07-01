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
        Schema::create('manajemen_izins', function (Blueprint $table) {
            $table->id();
            $table->string('nama_izin')->comment('Nama jenis izin (contoh: Izin Sakit, Cuti Tahunan)');
            $table->string('kode_izin')->unique()->comment('Kode unik untuk jenis izin (contoh: sakit, cuti, izin)');
            $table->text('deskripsi')->nullable()->comment('Deskripsi detail jenis izin');
            $table->integer('max_hari')->nullable()->comment('Maksimal hari untuk jenis izin ini');
            $table->boolean('perlu_dokumen')->default(false)->comment('Apakah memerlukan dokumen pendukung');
            $table->boolean('auto_approve')->default(false)->comment('Apakah otomatis disetujui tanpa perlu approval');
            $table->enum('kategori', ['cuti', 'izin_khusus', 'sakit', 'dinas'])->default('izin_khusus')->comment('Kategori jenis izin');
            $table->enum('warna_badge', ['primary', 'success', 'warning', 'danger', 'info', 'secondary'])->default('primary')->comment('Warna badge untuk tampilan');
            $table->boolean('is_active')->default(true)->comment('Status aktif jenis izin');
            $table->integer('urutan_tampil')->default(1)->comment('Urutan tampil di dropdown');
            $table->json('syarat_pengajuan')->nullable()->comment('Syarat-syarat untuk pengajuan izin ini');
            $table->unsignedBigInteger('created_by')->nullable()->comment('User yang membuat');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('User yang mengupdate terakhir');
            $table->timestamps();
            $table->softDeletes();

            // Index untuk performa
            $table->index(['is_active', 'urutan_tampil']);
            $table->index('kategori');
            $table->index('kode_izin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manajemen_izins');
    }
};
