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
        Schema::table('samsats', function (Blueprint $table) {
            $table->integer('jumlah_motor')->default(1)->change();
            $table->integer('pajak_progresif')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('samsats', function (Blueprint $table) {
            $table->integer('jumlah_motor')->default(1)->change();
            $table->integer('pajak_progresif')->default(0)->change();
        });
    }
};
