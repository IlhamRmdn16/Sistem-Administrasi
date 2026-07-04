<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
    {
        Schema::create('penerimaan_units', function (Blueprint $table) {
            $table->id();
            $table->string('no_bukti')->unique();
            $table->date('tanggal');
            $table->string('no_kendaraan');
            $table->string('ekspedisi');
            $table->string('no_sj');
            $table->string('no_nd')->nullable();
            $table->string('no_so')->nullable();
            $table->timestamps();
        });

        Schema::table('motor_units', function (Blueprint $table) {
            $table->dropColumn(['no_do', 'no_sp']);
            $table->foreignId('penerimaan_unit_id')->nullable()->after('id')->constrained('penerimaan_units')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('motor_units', function (Blueprint $table) {
            $table->dropForeign(['penerimaan_unit_id']);
            $table->dropColumn('penerimaan_unit_id');
            $table->string('no_do')->nullable();
            $table->string('no_sp')->nullable();
        });

        Schema::dropIfExists('penerimaan_units');
    }
};
