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
        Schema::create('pendidikans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('jenjang');
            $table->string('nama_univ');
            $table->string('jurusan');
            $table->date('thn_masuk');
            $table->date('thn_lulus');
            $table->integer('ipk');
            $table->string('gelar', 255);
            $table->timestamp('created_at');
            $table->timestamp('updated_at');

            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('pegawais')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendidikans');
    }
};
