<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToReportsTable extends Migration
{
    public function up()
    {
        Schema::table('laporan', function (Blueprint $table) {
            $table->boolean('status')->default(false);
        });
    }

    public function down()
    {
        Schema::table('laporan', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }   
}