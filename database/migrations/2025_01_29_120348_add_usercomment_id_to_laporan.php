<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('laporan', function (Blueprint $table) {
            $table->bigInteger('reported_user_id')->unsigned()->nullable()->after('user_id');
            $table->bigInteger('comment_id')->unsigned()->nullable()->after('reported_user_id');
            $table->bigInteger('photo_id')->unsigned()->nullable()->change();
            
            // Jika ingin menambahkan foreign key untuk reported_user_id dan comment_id
            $table->foreign('reported_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('comment_id')->references('id')->on('komentar')->onDelete('cascade');
        });
    }

    public function down() {
        Schema::table('laporan', function (Blueprint $table) {
            $table->dropForeign(['reported_user_id']);
            $table->dropForeign(['comment_id']);
            $table->dropColumn(['reported_user_id', 'comment_id']);
            $table->bigInteger('photo_id')->unsigned()->nullable(false)->change();
        });
    }
};
