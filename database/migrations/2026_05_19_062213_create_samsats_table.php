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
        Schema::create('samsats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_jalan_id')->constrained('surat_jalans')->cascadeOnDelete();
            $table->string('no_polisi')->nullable();
            $table->string('no_stnk')->nullable();
            $table->date('tgl_stnk')->nullable();
            $table->date('tgl_terima_stnk')->nullable();
            $table->integer('no_kendaraan')->default(1);
            $table->integer('piutang_notice_pajak')->default(0);
            $table->string('no_bpkb')->nullable();
            $table->date('tgl_bpkb')->nullable();
            $table->date('tgl_terima_bpkb')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('samsats');
    }
};
