<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kamars', function (Blueprint $table) {
            $table->id();
            $table->string('kode_kamar', 20)->unique();
            $table->string('nama_bangsal');
            $table->enum('kelas_kamar', ['vvip', 'vip', 'kelas_1', 'kelas_2', 'kelas_3', 'icu', 'nicu', 'isolasi']);
            $table->integer('total_tempat_tidur')->default(1);
            $table->integer('tempat_tidur_terisi')->default(0);
            $table->decimal('tarif_per_hari', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kamars');
    }
};
