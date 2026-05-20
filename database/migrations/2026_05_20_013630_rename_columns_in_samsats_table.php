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
        Schema::table('samsats', function (Blueprint $table) {
            $table->renameColumn('no_kendaraan', 'jumlah_motor');
            $table->renameColumn('piutang_notice_pajak', 'pajak_progresif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('samsats', function (Blueprint $table) {
            $table->renameColumn('jumlah_motor', 'no_kendaraan');
            $table->renameColumn('pajak_progresif', 'piutang_notice_pajak');
        });
    }
};
