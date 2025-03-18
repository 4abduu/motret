<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReplyIdToReportsTable extends Migration
{
    public function up()
    {
        Schema::table('laporan', function (Blueprint $table) {
            $table->unsignedBigInteger('reply_id')->nullable()->after('comment_id');
            $table->foreign('reply_id')->references('id')->on('replies')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('laporan', function (Blueprint $table) {
            $table->dropForeign(['reply_id']);
            $table->dropColumn('reply_id');
        });
    }
}