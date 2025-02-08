<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLanggananUserTable extends Migration
{
    public function up()
    {
        Schema::create('langganan_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // ID pengguna yang berlangganan
            $table->unsignedBigInteger('verified_user_id'); // ID pengguna terverifikasi yang dilanggan
            $table->timestamps();

            // Tambahkan foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('verified_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('langganan_user');
    }
}