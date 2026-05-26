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
        Schema::create('penyerahan_bpkb_leasings', function (Blueprint $table) {
            $table->id();
            $table->string('no_bukti')->unique();
            $table->date('tanggal');
            $table->foreignId('leasing_id')->constrained('leasings')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('penyerahan_bpkb_leasing_details', function (Blueprint $table) {
            $table->id();

            // Kolom didefinisikan secara manual
            $table->unsignedBigInteger('penyerahan_bpkb_leasing_id');

            // Relasi foreign key diberikan nama khusus yang lebih pendek (contoh: fk_pb_leasing_detail)
            $table->foreign('penyerahan_bpkb_leasing_id', 'fk_pb_leasing_detail')
                  ->references('id')
                  ->on('penyerahan_bpkb_leasings')
                  ->cascadeOnDelete();

            $table->foreignId('surat_jalan_id')->constrained('surat_jalans')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penyerahan_bpkb_leasing_details');
        Schema::dropIfExists('penyerahan_bpkb_leasings');
    }
};
