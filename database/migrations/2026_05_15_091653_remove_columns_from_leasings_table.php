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
        Schema::table('leasings', function (Blueprint $table) {
            $table->dropColumn(['pic', 'telepon_pic', 'alamat']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leasings', function (Blueprint $table) {
            $table->string('pic');
            $table->string('telepon_pic');
            $table->text('alamat')->nullable();
        });
    }
};
