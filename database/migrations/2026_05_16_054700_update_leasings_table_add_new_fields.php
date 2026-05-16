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
        Schema::table('leasings', function (Blueprint $table) {
            $table->string('kode_leasing')->after('id')->unique();
            $table->text('alamat')->after('nama_leasing')->nullable();
            $table->string('keterangan_penagihan_1')->after('alamat')->nullable();
            $table->string('keterangan_penagihan_2')->after('keterangan_penagihan_1')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leasings', function (Blueprint $table) {
            $table->dropColumn([
                'kode_leasing',
                'alamat',
                'keterangan_penagihan_1',
                'keterangan_penagihan_2'
            ]);
        });
    }
};
