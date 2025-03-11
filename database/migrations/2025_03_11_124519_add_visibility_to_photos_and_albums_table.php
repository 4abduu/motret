<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVisibilityToPhotosAndAlbumsTable extends Migration
{
    public function up()
    {
        Schema::table('photos', function (Blueprint $table) {
            $table->string('visibility', 10)->default('public')->after('premium');
        });

        Schema::table('albums', function (Blueprint $table) {
            $table->string('visibility', 10)->default('public')->after('description');
        });
    }

    public function down()
    {
        Schema::table('photos', function (Blueprint $table) {
            $table->dropColumn('visibility');
        });

        Schema::table('albums', function (Blueprint $table) {
            $table->dropColumn('visibility');
        });
    }
}