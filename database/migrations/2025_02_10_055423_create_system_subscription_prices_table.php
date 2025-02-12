<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('harga_langganan_sistem', function (Blueprint $table) {
            $table->id();
            $table->enum('duration', ['1_month', '3_months', '6_months', '1_year']);
            $table->decimal('price', 15, 2);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('harga_langganan_sistem');
    }
};
