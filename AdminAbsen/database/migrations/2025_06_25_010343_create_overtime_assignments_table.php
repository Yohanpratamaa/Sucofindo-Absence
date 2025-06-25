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
        Schema::create('overtime_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // FK ke pegawai/user yang mengajukan lembur
            $table->unsignedBigInteger('assigned_by'); // FK ke admin yang menugaskan/approve
            $table->string('overtime_id'); // ID lembur project/task
            $table->timestamp('assigned_at')->nullable(); // Waktu penugasan lembur
            $table->unsignedBigInteger('approved_by')->nullable(); // FK ke admin yang approve
            $table->timestamp('approved_at')->nullable(); // Waktu approve
            $table->unsignedBigInteger('assign_by')->nullable(); // FK ke yang assign ulang
            $table->enum('status', ['Assigned', 'Accepted', 'Rejected'])->default('Assigned');
            $table->timestamps();
            
            // Index untuk performa
            $table->index(['user_id', 'status']);
            $table->index(['assigned_by', 'assigned_at']);
            $table->index(['approved_by', 'approved_at']);
            $table->index(['overtime_id', 'status']);
            
            // Foreign key constraints (optional, sesuai kebutuhan)
            // $table->foreign('user_id')->references('id')->on('pegawais')->onDelete('cascade');
            // $table->foreign('assigned_by')->references('id')->on('pegawais')->onDelete('set null');
            // $table->foreign('approved_by')->references('id')->on('pegawais')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('overtime_assignments');
    }
};
