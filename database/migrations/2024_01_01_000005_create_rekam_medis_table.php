<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rekam_medis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->unique()->constrained('pendaftarans')->cascadeOnDelete();
            
            // S: Subjektif
            $table->text('keluhan_utama');
            $table->text('riwayat_penyakit_sekarang')->nullable();
            $table->text('riwayat_alergi')->nullable();

            // O: Objektif
            $table->string('tekanan_darah', 20); // 120/80 mmHg
            $table->integer('nadi'); // 80 bpm
            $table->decimal('suhu', 4, 1); // 36.5 C
            $table->integer('pernapasan'); // 20 x/menit
            $table->decimal('berat_badan', 5, 2); // 65.5 kg
            $table->decimal('tinggi_badan', 5, 2); // 170 cm
            $table->integer('spo2')->nullable()->default(98); // 98%

            // A: Asesmen (ICD-10)
            $table->string('kode_icd10', 10); // I10, E11, J06.9, K29.7, dll
            $table->string('nama_diagnosis');
            $table->enum('tipe_diagnosis', ['primer', 'sekunder'])->default('primer');

            // P: Plan (Penatalaksanaan)
            $table->text('tindakan_medis')->nullable();
            $table->text('resep_obat');
            $table->text('edukasi_pasien')->nullable();
            $table->date('rencana_kontrol_lanjutan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekam_medis');
    }
};
