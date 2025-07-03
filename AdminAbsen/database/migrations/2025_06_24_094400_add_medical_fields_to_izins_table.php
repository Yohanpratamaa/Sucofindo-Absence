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
        Schema::table('izins', function (Blueprint $table) {
            $table->string('lokasi_berobat')->nullable()->after('keterangan');
            $table->string('nama_dokter')->nullable()->after('lokasi_berobat');
            $table->string('diagnosa_dokter')->nullable()->after('nama_dokter');
            $table->text('keterangan_medis')->nullable()->after('diagnosa_dokter');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('izins', function (Blueprint $table) {
            $table->dropColumn(['lokasi_berobat', 'nama_dokter', 'diagnosa_dokter', 'keterangan_medis']);
        });
    }
};
