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
        Schema::create('mutasi_details', function (Blueprint $table) {
           $table->id();
            $table->unsignedBigInteger('mutasi_id');
            $table->unsignedBigInteger('motor_unit_id');
            $table->timestamps();

            $table->foreign('mutasi_id')->references('id')->on('mutasis')->onDelete('cascade');
            $table->foreign('motor_unit_id')->references('id')->on('motor_units')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mutasi_details');
    }
};
