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
            $table->string('desa')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kabupaten')->nullable();
            $table->string('sekretariat')->nullable();
            $table->string('tempat_ttd')->nullable();
            $table->date('tanggal_rapor')->nullable();
            $table->string('npsn');
            $table->string('logo')->nullable();
            $table->string('kepala_madrasah');
            $table->string('ttd_kepala_madrasah')->nullable();
            $table->foreignId('tahun_ajaran_id')->nullable()->constrained('tahun_ajaran')->onDelete('set null');
            $table->string('semester')->nullable();
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