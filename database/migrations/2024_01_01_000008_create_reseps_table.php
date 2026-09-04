<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reseps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pendaftaran_id')->constrained('pendaftarans')->cascadeOnDelete();
            $table->string('nama_obat');
            $table->string('bentuk_sediaan', 50)->default('Tablet');
            $table->integer('jumlah')->default(10);
            $table->string('aturan_pakai_latin', 100); // S 3 dd tab 1 pc
            $table->decimal('harga_satuan', 12, 2)->default(0);
            $table->decimal('total_harga', 12, 2)->default(0);
            $table->enum('status_telaah', ['lolos', 'ada_alergi', 'interaksi_obat'])->default('lolos');
            $table->enum('status_dispensing', ['antre', 'diracik', 'diserahkan'])->default('antre');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reseps');
    }
};
