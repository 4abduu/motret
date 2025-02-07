<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReasonToVerificationRequestsTable extends Migration
{
    public function up()
    {
        Schema::table('verification_requests', function (Blueprint $table) {
            $table->text('reason')->nullable()->after('username');
        });
    }

    public function down()
    {
        Schema::table('verification_requests', function (Blueprint $table) {
            $table->dropColumn('reason');
        });
    }
}