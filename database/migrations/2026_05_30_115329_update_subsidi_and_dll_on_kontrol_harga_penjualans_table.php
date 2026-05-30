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
            $table->renameColumn('subsidi_leasing', 'subsidi_leasing_1');
            $table->renameColumn('dll', 'dll_1');
        });

        // 2. Tambahkan kolom baru setelah kolom yang diubah namanya
        Schema::table('kontrol_harga_penjualans', function (Blueprint $table) {
            $table->bigInteger('subsidi_leasing_2')->default(0)->after('subsidi_leasing_1');
            $table->bigInteger('dll_2')->default(0)->after('dll_1');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kontrol_harga_penjualans', function (Blueprint $table) {
            $table->dropColumn(['subsidi_leasing_2', 'dll_2']);
        });

        Schema::table('kontrol_harga_penjualans', function (Blueprint $table) {
            $table->renameColumn('subsidi_leasing_1', 'subsidi_leasing');
            $table->renameColumn('dll_1', 'dll');
        });
    }
};
