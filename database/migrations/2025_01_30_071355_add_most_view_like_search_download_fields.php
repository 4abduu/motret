<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMostViewLikeSearchDownloadFields extends Migration
{
    public function up()
    {
        Schema::table('foto', function (Blueprint $table) {
            $table->unsignedBigInteger('views_today')->default(0)->after('description');
            $table->unsignedBigInteger('likes_today')->default(0)->after('views_today');
            $table->unsignedBigInteger('downloads_today')->default(0)->after('likes_today');
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
            $table->dropColumn('views_today');
            $table->dropColumn('likes_today');
            $table->dropColumn('downloads_today');
        });

        Schema::dropIfExists('searches');
    }
}