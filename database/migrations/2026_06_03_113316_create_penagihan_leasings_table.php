<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Tabel Induk Penagihan
        Schema::create('penagihan_leasings', function (Blueprint $table) {
            $table->id();
            $table->string('no_bukti')->unique();
            $table->date('tanggal');
            $table->foreignId('leasing_id')->constrained('leasings')->restrictOnDelete();
            $table->timestamps();
        });

        // Tabel Rincian Surat Jalan yang Ditagih
        Schema::create('penagihan_leasing_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penagihan_leasing_id')->constrained('penagihan_leasings')->cascadeOnDelete();
            $table->foreignId('surat_jalan_id')->constrained('surat_jalans')->restrictOnDelete();
            $table->bigInteger('otr')->default(0);
            $table->bigInteger('dp_po')->default(0);
            $table->bigInteger('sisa')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('penagihan_leasing_details');
        Schema::dropIfExists('penagihan_leasings');
    }
};
