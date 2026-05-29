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
        Schema::table('kontrol_harga_penjualans', function (Blueprint $table) {
            $table->string('no_kwitansi_dp')->nullable()->unique()->after('tgl_kwitansi_otr');
            $table->date('tgl_kwitansi_dp')->nullable()->after('no_kwitansi_dp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kontrol_harga_penjualans', function (Blueprint $table) {
            $table->dropColumn(['no_kwitansi_dp', 'tgl_kwitansi_dp']);
        });
    }
};
