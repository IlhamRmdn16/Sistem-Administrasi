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
        Schema::table('motor_units', function (Blueprint $table) {
            $table->string('posisi_stok')->default('Gudang 1')->after('tahun_pembuatan');
            $table->unsignedBigInteger('lokasi_pop_id')->nullable()->after('posisi_stok');
            $table->string('status_unit')->default('Tersedia')->after('lokasi_pop_id');
            $table->foreign('lokasi_pop_id')->references('id')->on('sales')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('motor_units', function (Blueprint $table) {
            $table->dropForeign(['lokasi_pop_id']);
            $table->dropColumn(['posisi_stok', 'lokasi_pop_id', 'status_unit']);
        });
    }
};
