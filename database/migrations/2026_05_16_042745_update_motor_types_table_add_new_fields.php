<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('motor_types', function (Blueprint $table) {
            $table->renameColumn('kode_type', 'kode_motor');
            $table->string('kode_tipe')->after('id')->nullable();
            $table->string('jenis')->after('kode_tipe')->nullable();
            $table->integer('notice_pajak')->after('otr')->default(0);
            $table->integer('bbn')->after('notice_pajak')->default(0);
            $table->integer('adm_stnk')->after('bbn')->default(0);
            $table->json('sampul_buku')->after('kode_motor')->nullable();
            $table->string('tahun_pembuatan', 4)->after('sampul_buku')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('motor_types', function (Blueprint $table) {
            $table->renameColumn('kode_motor', 'kode_type');
            $table->dropColumn(['kode_tipe', 'jenis', 'notice_pajak', 'bbn', 'adm_stnk', 'sampul_buku', 'tahun_pembuatan']);
        });
    }
};
