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
       Schema::create('kwitansi_pajak_progresifs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('surat_jalan_id')->unique();
            $table->string('no_kwitansi')->unique();
            $table->date('tanggal');
            $table->integer('bayar_kontan')->default(0);
            $table->integer('bayar_transfer')->default(0);
            $table->string('rekening_tujuan')->nullable();
            $table->string('no_po_leasing')->nullable();
            $table->timestamps();
            $table->foreign('surat_jalan_id')->references('id')->on('surat_jalans')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kwitansi_pajak_progresifs');
    }
};
