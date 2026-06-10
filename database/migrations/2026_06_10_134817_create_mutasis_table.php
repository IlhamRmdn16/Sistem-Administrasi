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
        Schema::create('mutasis', function (Blueprint $table) {
            $table->id();
            $table->string('no_bukti')->unique();
            $table->date('tanggal');

            $table->string('lokasi_asal');
            $table->unsignedBigInteger('lokasi_asal_pop_id')->nullable();

            $table->string('lokasi_tujuan');
            $table->unsignedBigInteger('lokasi_tujuan_pop_id')->nullable();

            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('lokasi_asal_pop_id')->references('id')->on('sales')->onDelete('set null');
            $table->foreign('lokasi_tujuan_pop_id')->references('id')->on('sales')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mutasis');
    }
};
