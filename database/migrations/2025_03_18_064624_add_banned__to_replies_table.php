<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBannedToRepliesTable extends Migration
{
    public function up()
    {
        Schema::table('replies', function (Blueprint $table) {
            $table->boolean('banned')->default(false);
            $table->timestamp('ban_expires_at')->nullable();

        });
    }

    public function down()
    {
        Schema::table('replies', function (Blueprint $table) {
            $table->dropColumn('banned', 'ban_expires_at');
        });
    }
}