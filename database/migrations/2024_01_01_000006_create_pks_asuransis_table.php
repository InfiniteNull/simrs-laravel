<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pks_asuransis', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pks', 100)->unique();
            $table->string('nama_mitra');
            $table->enum('jenis_mitra', ['bpjs', 'asuransi_swasta', 'perusahaan', 'yayasan'])->default('asuransi_swasta');
            $table->date('tanggal_mulai');
            $table->date('tanggal_berakhir');
            $table->string('penanggung_jawab');
            $table->string('kontak_person', 50);
            $table->json('cakupan_layanan')->nullable(); // Rawat Inap, Rawat Jalan, IGD, MCU, Lab
            $table->enum('status_pks', ['aktif', 'evaluasi', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pks_asuransis');
    }
};
