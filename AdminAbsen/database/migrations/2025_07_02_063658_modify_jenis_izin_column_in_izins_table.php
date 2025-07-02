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
            // Change jenis_izin from enum to varchar to accommodate dynamic values
            $table->string('jenis_izin', 50)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('izins', function (Blueprint $table) {
            // Revert back to enum (only if you want to go back)
            $table->enum('jenis_izin', ['sakit', 'cuti', 'izin'])->default('cuti')->change();
        });
    }
};
