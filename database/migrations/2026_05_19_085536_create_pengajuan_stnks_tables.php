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
       Schema::create('pengajuan_stnks', function (Blueprint $table) {
            $table->id();
            $table->string('no_bukti')->unique();
            $table->date('tanggal');
            $table->integer('total_pajak')->default(0);
            $table->integer('total_adm')->default(0);
            $table->integer('total_tambahan')->default(0);
            $table->integer('grand_total')->default(0);
            $table->timestamps();
        });

        Schema::create('pengajuan_stnk_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_stnk_id')->constrained('pengajuan_stnks')->cascadeOnDelete();
            $table->foreignId('samsat_id')->constrained('samsats')->cascadeOnDelete();
            $table->integer('notice_pajak');
            $table->integer('adm');
            $table->integer('sub_total');
            $table->timestamps();
        });

        Schema::create('pengajuan_stnk_tambahans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengajuan_stnk_id')->constrained('pengajuan_stnks')->cascadeOnDelete();
            $table->string('keterangan');
            $table->integer('nominal');
            $table->integer('total');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_stnk_tambahans');
        Schema::dropIfExists('pengajuan_stnk_details');
        Schema::dropIfExists('pengajuan_stnks');
    }
};
