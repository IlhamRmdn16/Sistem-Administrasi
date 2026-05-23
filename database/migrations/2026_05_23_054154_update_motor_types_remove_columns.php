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
        Schema::table('motor_types', function (Blueprint $table) {
            $table->dropColumn(['bbn', 'adm_stnk']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('motor_types', function (Blueprint $table) {
            $table->integer('bbn')->after('notice_pajak')->default(0);
            $table->integer('adm_stnk')->after('bbn')->default(0);
        });
    }
};
