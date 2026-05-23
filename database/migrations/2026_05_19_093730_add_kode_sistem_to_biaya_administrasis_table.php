<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('biaya_administrasis', function (Blueprint $table) {
            $table->string('kode_sistem')->nullable()->after('keterangan');
        });
    }

    public function down(): void
    {
        Schema::table('biaya_administrasis', function (Blueprint $table) {
            $table->dropColumn('kode_sistem');
        });
    }
};
