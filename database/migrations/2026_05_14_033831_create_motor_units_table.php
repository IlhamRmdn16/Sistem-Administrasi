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
        Schema::create('motor_units', function (Blueprint $table) {
            $table->id();
            $table->string('no_do');
            $table->string('no_sp');
            $table->foreignId('motor_type_id')->constrained('motor_types')->restrictOnDelete();
            $table->foreignId('motor_color_id')->constrained('motor_colors')->restrictOnDelete();
            $table->string('no_mesin')->unique();
            $table->string('no_rangka')->unique();
            $table->string('no_seri_kunci');
            $table->string('no_kunci');
            $table->year('tahun_pembuatan');
            $table->string('no_accu');
            $table->enum('status', ['Tersedia', 'Terjual'])->default('Tersedia');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('motor_units');
    }
};
