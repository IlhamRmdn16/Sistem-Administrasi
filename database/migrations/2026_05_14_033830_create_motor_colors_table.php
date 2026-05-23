<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('motor_colors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('motor_type_id')->constrained('motor_types')->cascadeOnDelete();
            $table->string('warna');
            $table->string('kode_warna');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('motor_colors');
    }
};
