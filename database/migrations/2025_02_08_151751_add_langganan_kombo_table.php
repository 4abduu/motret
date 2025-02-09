<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('langganan_kombo', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // User yang berlangganan
            $table->unsignedBigInteger('target_user_id'); // User yang menerima langganan
            $table->decimal('system_price', 15, 2); // Harga langganan sistem
            $table->decimal('user_price', 15, 2); // Harga langganan user verified
            $table->decimal('total_price', 15, 2); // Harga total (system + user)
            $table->dateTime('start_date'); // Tanggal mulai langganan
            $table->dateTime('end_date'); // Tanggal berakhir langganan
            $table->unsignedBigInteger('transaction_id'); // Transaksi terkait
            $table->timestamps();

            // Foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('target_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('transaction_id')->references('id')->on('transaksi')->onDelete('cascade');
        });
    }

    public function down() {
        Schema::dropIfExists('langganan_kombo');
    }
};
