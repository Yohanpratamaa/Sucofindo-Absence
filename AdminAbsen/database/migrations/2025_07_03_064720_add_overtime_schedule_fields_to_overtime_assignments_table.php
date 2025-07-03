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
        Schema::table('overtime_assignments', function (Blueprint $table) {
            $table->date('tanggal_lembur')->nullable()->after('keterangan'); // Tanggal lembur
            $table->time('jam_mulai')->nullable()->after('tanggal_lembur'); // Jam mulai lembur
            $table->time('jam_selesai')->nullable()->after('jam_mulai'); // Jam selesai lembur
            $table->integer('total_jam')->nullable()->after('jam_selesai'); // Total jam lembur (dalam menit)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('overtime_assignments', function (Blueprint $table) {
            $table->dropColumn(['tanggal_lembur', 'jam_mulai', 'jam_selesai', 'total_jam']);
        });
    }
};
