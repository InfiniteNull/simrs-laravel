<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dokters', function (Blueprint $table) {
            $table->id();
            $table->string('nip_dokter', 30)->unique();
            $table->string('nama_dokter');
            $table->string('spesialisasi'); // Spesialis Penyakit Dalam, Anak, Jantung, Bedah, dll
            $table->string('nomor_sip', 50)->unique();
            $table->string('jadwal_hari')->default('Senin - Jumat');
            $table->time('jam_mulai')->default('08:00:00');
            $table->time('jam_selesai')->default('14:00:00');
            $table->integer('kuota_harian')->default(30);
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dokters');
    }
};
