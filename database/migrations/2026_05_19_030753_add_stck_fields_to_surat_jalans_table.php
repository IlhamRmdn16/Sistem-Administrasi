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
        Schema::table('surat_jalans', function (Blueprint $table) {
            $table->string('no_stck')->nullable()->after('pdi_man_id');
            $table->string('no_registrasi')->nullable()->after('no_stck');
            $table->date('berlaku_sd')->nullable()->after('no_registrasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_jalans', function (Blueprint $table) {
            $table->dropColumn(['no_stck', 'no_registrasi', 'berlaku_sd']);
        });
    }
};
