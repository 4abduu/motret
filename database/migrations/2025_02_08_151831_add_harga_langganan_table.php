<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('harga_langganan_pengguna', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // User yang menentukan harga langganan
            $table->decimal('price_1_month', 15, 2);
            $table->decimal('price_3_months', 15, 2)->nullable();
            $table->decimal('price_6_months', 15, 2)->nullable();
            $table->decimal('price_1_year', 15, 2)->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down() {
        Schema::dropIfExists('harga_langganan_pengguna');
    }
};
