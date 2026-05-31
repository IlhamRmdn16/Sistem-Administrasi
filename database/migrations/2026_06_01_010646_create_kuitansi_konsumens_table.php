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
        Schema::create('kuitansi_konsumens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spk_id')->constrained('spks')->cascadeOnDelete();
            $table->string('no_kuitansi')->unique();
            $table->date('tanggal');
            $table->bigInteger('bayar_kontan')->default(0);
            $table->bigInteger('bayar_transfer')->default(0);
            $table->foreignId('rekening_id')->nullable()->constrained('rekenings')->nullOnDelete();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kuitansi_konsumens');
    }
};
