<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billings', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_invoice', 50)->unique();
            $table->foreignId('pendaftaran_id')->unique()->constrained('pendaftarans')->cascadeOnDelete();
            $table->decimal('biaya_konsultasi_dokter', 12, 2)->default(0);
            $table->decimal('biaya_tindakan_medis', 12, 2)->default(0);
            $table->decimal('biaya_obat_farmasi', 12, 2)->default(0);
            $table->decimal('biaya_laboratorium', 12, 2)->default(0);
            $table->decimal('biaya_kamar_rawat', 12, 2)->default(0);
            $table->decimal('total_tagihan', 12, 2)->default(0);
            $table->decimal('potongan_asuransi_bpjs', 12, 2)->default(0);
            $table->decimal('sisa_bayar_pasien', 12, 2)->default(0);
            $table->enum('metode_pembayaran', ['bpjs', 'asuransi_swasta', 'tunai', 'qris', 'kartu_debit'])->default('bpjs');
            $table->enum('status_pembayaran', ['lunas', 'pending', 'klaim_terkirim'])->default('pending');
            $table->timestamp('waktu_pembayaran')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billings');
    }
};
