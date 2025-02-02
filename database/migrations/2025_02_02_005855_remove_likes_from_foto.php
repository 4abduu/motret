<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('foto', function (Blueprint $table) {
            $table->dropColumn('likes');
        });
    }

    public function down()
    {
        Schema::table('foto', function (Blueprint $table) {
            $table->bigInteger('likes')->unsigned()->default(0);
        });
    }
};
