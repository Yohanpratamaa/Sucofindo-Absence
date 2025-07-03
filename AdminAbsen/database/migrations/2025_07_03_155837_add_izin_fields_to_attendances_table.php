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
            $table->unsignedBigInteger('izin_id')->nullable()->after('attendance_type');
            $table->string('status_kehadiran')->default('Hadir')->after('izin_id');
            $table->text('keterangan_izin')->nullable()->after('status_kehadiran');

            // Add foreign key constraint
            $table->foreign('izin_id')->references('id')->on('izins')->onDelete('set null');

            // Add index for performance
            $table->index(['izin_id', 'created_at']);
            $table->index(['status_kehadiran', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['izin_id']);
            $table->dropIndex(['izin_id', 'created_at']);
            $table->dropIndex(['status_kehadiran', 'created_at']);
            $table->dropColumn(['izin_id', 'status_kehadiran', 'keterangan_izin']);
        });
    }
};
