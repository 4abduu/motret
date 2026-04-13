<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMostViewLikeSearchDownloadFields extends Migration
{
    public function up()
    {
        if (! Schema::hasColumn('foto', 'views')) {
            Schema::table('foto', function (Blueprint $table) {
                $table->unsignedBigInteger('views')->default(0)->after('description');
            });
        }

        if (! Schema::hasColumn('foto', 'likes')) {
            Schema::table('foto', function (Blueprint $table) {
                $table->unsignedBigInteger('likes')->default(0)->after('views');
            });
        }

        if (! Schema::hasColumn('foto', 'downloads')) {
            Schema::table('foto', function (Blueprint $table) {
                $table->unsignedBigInteger('downloads')->default(0)->after('likes');
            });
        }

        if (! Schema::hasTable('cari')) {
            Schema::create('cari', function (Blueprint $table) {
                $table->id();
                $table->string('keyword');
                $table->unsignedBigInteger('count')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::table('foto', function (Blueprint $table) {
            if (Schema::hasColumn('foto', 'views')) {
                $table->dropColumn('views');
            }

            if (Schema::hasColumn('foto', 'likes')) {
                $table->dropColumn('likes');
            }

            if (Schema::hasColumn('foto', 'downloads')) {
                $table->dropColumn('downloads');
            }
        });

        Schema::dropIfExists('cari');
    }
}
