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
        Schema::create('penyerahan_stnk_bpkbs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_jalan_id')->constrained('surat_jalans')->cascadeOnDelete();
            $table->date('tgl_serah_stnk')->nullable();
            $table->string('penerima_stnk')->nullable();
            $table->string('alamat_penerima_stnk')->nullable();
            $table->string('keterangan_stnk')->nullable();
            $table->date('tgl_serah_bpkb')->nullable();
            $table->string('penerima_bpkb')->nullable();
            $table->string('alamat_penerima_bpkb')->nullable();
            $table->string('keterangan_bpkb')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penyerahan_stnk_bpkbs');
    }
};
