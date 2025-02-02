<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('foto', function (Blueprint $table) {
            $table->timestamp('ban_expires_at')->nullable();
        });

        Schema::table('komentar', function (Blueprint $table) {
            $table->boolean('banned')->default(false);
            $table->timestamp('ban_expires_at')->nullable();
        });
    }

    public function down()
    {
        Schema::table('foto', function (Blueprint $table) {
            $table->dropColumn('ban_expires_at');
        });

        Schema::table('komentar', function (Blueprint $table) {
            $table->dropColumn(['banned', 'ban_expires_at']);
        });
    }
};
