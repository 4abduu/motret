<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMostViewLikeSearchDownloadFields extends Migration
{
    public function up()
    {
        Schema::table('foto', function (Blueprint $table) {
            $table->unsignedBigInteger('views')->default(0)->after('description');
            $table->unsignedBigInteger('likes')->default(0)->after('views');
            $table->unsignedBigInteger('downloads')->default(0)->after('likes');
        });

        Schema::create('cari', function (Blueprint $table) {
            $table->id();
            $table->string('keyword');
            $table->unsignedBigInteger('count')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::table('photos', function (Blueprint $table) {
            $table->dropColumn('views');
            $table->dropColumn('likes');
            $table->dropColumn('downloads');
        });

        Schema::dropIfExists('searches');
    }
}