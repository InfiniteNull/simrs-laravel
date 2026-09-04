<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pendaftarans', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_antrean', 20); // Contoh: A-001
            $table->string('kode_booking', 30)->unique();
            $table->foreignId('pasien_id')->constrained('pasiens')->cascadeOnDelete();
            $table->foreignId('dokter_id')->constrained('dokters')->cascadeOnDelete();
            $table->date('tanggal_kunjungan');
            $table->enum('jenis_pelayanan', ['rawat_jalan', 'rawat_inap', 'igd'])->default('rawat_jalan');
            $table->enum('metode_pembayaran', ['bpjs', 'asuransi_swasta', 'umum'])->default('bpjs');
            $table->enum('status_antrean', ['menunggu', 'sedang_dilayani', 'selesai', 'batal'])->default('menunggu');
            $table->timestamps();

            $table->index(['tanggal_kunjungan', 'dokter_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendaftarans');
    }
};
