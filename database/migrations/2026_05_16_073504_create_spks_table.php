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
        Schema::create('spks', function (Blueprint $table) {
            $table->id();
            $table->string('no_spk')->unique();
            $table->date('tanggal');
            $table->foreignId('sales_id')->constrained('sales');
            $table->string('nama_pemohon');
            $table->string('nama_stnk');
            $table->text('alamat');
            $table->string('desa_kelurahan');
            $table->string('kecamatan');
            $table->string('kota_kabupaten');
            $table->string('telepon');
            $table->string('nik');
            $table->string('email')->nullable();
            $table->enum('jenis_pembayaran', ['Cash', 'Kredit']);
            $table->foreignId('motor_type_id')->constrained('motor_types');
            $table->foreignId('motor_color_id')->constrained('motor_colors');
            $table->integer('harga_otr');
            $table->integer('uang_muka')->nullable();
            $table->integer('tanda_jadi')->nullable();
            $table->foreignId('leasing_id')->nullable()->constrained('leasings');
            $table->integer('tenor_bulan')->nullable();
            $table->integer('cicilan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spks');
    }
};
