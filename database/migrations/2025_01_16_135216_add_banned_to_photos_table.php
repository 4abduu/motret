<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBannedToPhotosTable extends Migration
{
    public function up()
    {
        Schema::table('foto', function (Blueprint $table) {
            $table->boolean('banned')->default(false);
        });
    }

    public function down()
    {
        Schema::table('foto', function (Blueprint $table) {
            $table->dropColumn('banned');
        });
    }
}