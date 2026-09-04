<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laboratoriums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->constrained('pendaftarans')->cascadeOnDelete();
            $table->string('nama_pemeriksaan');
            $table->string('kode_loinc', 20)->nullable();
            $table->string('nilai_hasil', 50)->nullable();
            $table->string('nilai_rujukan', 50)->nullable();
            $table->string('satuan', 20)->nullable();
            $table->enum('status_hasil', ['normal', 'low', 'high', 'critical'])->default('normal');
            $table->text('catatan_lab')->nullable();
            $table->enum('status_pemeriksaan', ['requested', 'in_progress', 'completed'])->default('requested');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laboratoriums');
    }
};
