<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengajuan_stnk_details', function (Blueprint $table) {
            $table->dropForeign(['samsat_id']);
            $table->dropColumn('samsat_id');
            $table->foreignId('surat_jalan_id')->after('pengajuan_stnk_id')->constrained('surat_jalans')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pengajuan_stnk_details', function (Blueprint $table) {
            $table->dropForeign(['surat_jalan_id']);
            $table->dropColumn('surat_jalan_id');
            $table->foreignId('samsat_id')->constrained('samsats')->cascadeOnDelete();
        });
    }
};
