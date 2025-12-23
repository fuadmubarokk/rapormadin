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
        Schema::create('setting_sekolah', function (Blueprint $table) {
            $table->id();
            $table->string('nama_madrasah');
            $table->string('nama_madrasah_ar');
            $table->text('alamat');
            $table->string('npsn');
            $table->string('logo')->nullable();
            $table->string('kepala_madrasah');
            $table->string('nip_kepala');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setting_sekolah');
    }
};