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
        Schema::table('spks', function (Blueprint $table) {
            $table->dropForeign(['motor_type_id']);
            $table->dropForeign(['motor_color_id']);
            
            // 2. Sekarang aman untuk menghapus kolom
            $table->dropColumn(['motor_type_id', 'motor_color_id']);
            
            // 3. Tambahkan kolom relasi baru
            $table->unsignedBigInteger('motor_unit_id')->nullable()->after('jenis_pembayaran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spks', function (Blueprint $table) {
            $table->dropColumn('motor_unit_id');  
            $table->unsignedBigInteger('motor_type_id')->nullable();
            $table->unsignedBigInteger('motor_color_id')->nullable();
        });
    }
};
