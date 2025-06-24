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
        Schema::create('jabatans', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->decimal('tunjangan', 15, 2)->default(0);
            $table->text('deskripsi')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            // Index untuk performa
            $table->index(['status', 'nama']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jabatans');
    }
};
