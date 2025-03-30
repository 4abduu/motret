<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->string('payment_type')->nullable()->change();
        });
    }

    public function down() {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->string('payment_type')->nullable(false)->change();
        });
    }
};
