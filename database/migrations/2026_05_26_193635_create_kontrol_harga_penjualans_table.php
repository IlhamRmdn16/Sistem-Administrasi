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
        Schema::create('kontrol_harga_penjualans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spk_id')->constrained('spks')->cascadeOnDelete();
            $table->bigInteger('discount')->default(0);
            $table->bigInteger('subsidi_ahm')->default(0);
            $table->bigInteger('subsidi_dealer')->default(0);
            $table->bigInteger('subsidi_main_dealer')->default(0);
            $table->bigInteger('subsidi_leasing')->default(0);
            $table->bigInteger('dll')->default(0);
            $table->bigInteger('ekstra')->default(0);
            $table->string('nama_mediator')->nullable();
            $table->bigInteger('mediator_fee')->default(0);
            $table->bigInteger('tambahan')->default(0);
            $table->bigInteger('refund_transfer')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kontrol_harga_penjualans');
    }
};
