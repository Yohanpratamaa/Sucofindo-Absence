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
        Schema::create('izins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // FK ke pegawai/user
            $table->date('tanggal_mulai');
            $table->date('tanggal_akhir');
            $table->enum('jenis_izin', ['sakit', 'cuti', 'izin'])->default('cuti');
            $table->text('keterangan')->nullable();
            $table->string('dokumen_pendukung')->nullable(); // path file
            $table->unsignedBigInteger('approved_by')->nullable(); // FK ke admin yang approve
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            // Index untuk performa
            $table->index(['user_id', 'tanggal_mulai']);
            $table->index(['approved_by', 'approved_at']);
            $table->index('jenis_izin');

            // Foreign key constraints (optional, sesuai kebutuhan)
            // $table->foreign('user_id')->references('id')->on('pegawais')->onDelete('cascade');
            // $table->foreign('approved_by')->references('id')->on('pegawais')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('izins');
    }
};
