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
            $table->string('no_kwitansi_kw1')->nullable()->unique()->after('tgl_kwitansi_kwm');
            $table->date('tgl_kwitansi_kw1')->nullable()->after('no_kwitansi_kw1');
            $table->string('no_kwitansi_kw2')->nullable()->unique()->after('tgl_kwitansi_kw1');
            $table->date('tgl_kwitansi_kw2')->nullable()->after('no_kwitansi_kw2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kontrol_harga_penjualans', function (Blueprint $table) {
            $table->dropColumn(['no_kwitansi_kw1', 'tgl_kwitansi_kw1', 'no_kwitansi_kw2', 'tgl_kwitansi_kw2']);
        });
    }
};
