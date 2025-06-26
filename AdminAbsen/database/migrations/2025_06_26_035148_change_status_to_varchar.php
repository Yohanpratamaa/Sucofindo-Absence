<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Change status column from enum to varchar to allow longer values
        DB::statement("ALTER TABLE pegawais MODIFY COLUMN status VARCHAR(20) DEFAULT 'active'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to enum (this might cause issues if data exists)
        DB::statement("ALTER TABLE pegawais MODIFY COLUMN status ENUM('active', 'non-active') DEFAULT 'active'");
    }
};
