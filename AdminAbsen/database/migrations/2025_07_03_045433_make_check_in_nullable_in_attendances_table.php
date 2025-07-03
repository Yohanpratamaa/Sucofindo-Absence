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
        Schema::table('attendances', function (Blueprint $table) {
            // Ubah check_in menjadi nullable untuk mendukung case "tidak absen sama sekali"
            $table->time('check_in')->nullable()->change();
            
            // Ubah longitude dan latitude absen masuk menjadi nullable juga
            $table->decimal('longitude_absen_masuk', 11, 8)->nullable()->change();
            $table->decimal('latitude_absen_masuk', 10, 8)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Kembalikan ke state semula (not nullable)
            $table->time('check_in')->nullable(false)->change();
            $table->decimal('longitude_absen_masuk', 11, 8)->nullable(false)->change();
            $table->decimal('latitude_absen_masuk', 10, 8)->nullable(false)->change();
        });
    }
};
