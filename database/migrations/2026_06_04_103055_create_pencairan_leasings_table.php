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
        Schema::create('pencairan_leasings', function (Blueprint $table) {
            $table->id();
            $table->string('no_bukti')->unique(); // Format: PLP2026/06/0139
            $table->date('tanggal');
            $table->foreignId('leasing_id')->constrained('leasings')->restrictOnDelete();
            $table->timestamps();
        });

        // Tabel Rincian Pencairan per Unit (Surat Jalan)
        Schema::create('pencairan_leasing_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pencairan_leasing_id')->constrained('pencairan_leasings')->cascadeOnDelete();
            $table->foreignId('surat_jalan_id')->constrained('surat_jalans')->restrictOnDelete();

            $table->bigInteger('nilai_pencairan')->default(0); // Input Manual Kasir
            $table->bigInteger('nilai_realisasi')->default(0); // OTR - DP PO
            $table->bigInteger('estimasi_dll')->default(0);    // DLL 1 + DLL 2
            $table->bigInteger('selisih_aktual')->default(0);  // Pencairan - Realisasi
            $table->bigInteger('margin_lebih_kurang')->default(0); // Selisih Aktual - Estimasi DLL

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pencairan_leasing_details');
        Schema::dropIfExists('pencairan_leasings');
    }
};
