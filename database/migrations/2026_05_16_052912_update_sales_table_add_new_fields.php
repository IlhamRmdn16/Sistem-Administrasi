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
        Schema::table('sales', function (Blueprint $table) {
            $table->string('kode_sales')->after('id')->unique();
            $table->enum('jenis_sales', ['Sales', 'POP'])->after('kode_sales');
            $table->text('alamat')->after('nama_sales')->nullable();
            $table->date('tgl_masuk')->after('telepon')->nullable();

            $table->string('nik')->nullable()->change();
            $table->string('telepon')->nullable()->change();

            $table->dropColumn('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['kode_sales', 'jenis_sales', 'alamat', 'tgl_masuk']);
            $table->string('nik')->nullable(false)->change();
            $table->string('telepon')->nullable(false)->change();
            $table->enum('status', ['Aktif', 'Nonaktif'])->default('Aktif');
        });
    }
};
