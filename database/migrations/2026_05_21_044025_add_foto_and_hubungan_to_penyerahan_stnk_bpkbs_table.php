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
        Schema::table('penyerahan_stnk_bpkbs', function (Blueprint $table) {
            $table->string('hubungan_stnk')->nullable()->after('penerima_stnk');
            $table->string('foto_serah_stnk')->nullable()->after('keterangan_stnk');

            $table->string('hubungan_bpkb')->nullable()->after('penerima_bpkb');
            $table->string('foto_serah_bpkb')->nullable()->after('keterangan_bpkb');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penyerahan_stnk_bpkbs', function (Blueprint $table) {
            $table->dropColumn(['hubungan_stnk', 'foto_serah_stnk', 'hubungan_bpkb', 'foto_serah_bpkb']);
        });
    }
};
