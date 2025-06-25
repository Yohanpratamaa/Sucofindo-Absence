<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('office_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')->constrained()->onDelete('cascade');
            $table->enum('day_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']);
            $table->time('start_time')->nullable(); // Nullable untuk hari libur
            $table->time('end_time')->nullable();   // Nullable untuk hari libur
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('office_schedules');
    }
};
