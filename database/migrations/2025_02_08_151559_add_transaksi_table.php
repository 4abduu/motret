<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // User yang melakukan pembayaran
            $table->string('order_id')->unique(); // ID pesanan dari Midtrans
            $table->string('transaction_status'); // Status transaksi (pending, success, failed)
            $table->string('payment_type'); // Jenis pembayaran (credit card, bank transfer, dll.)
            $table->decimal('gross_amount', 15, 2); // Total pembayaran
            $table->string('transaction_id')->nullable(); // ID transaksi Midtrans
            $table->string('fraud_status')->nullable(); // Status fraud (jika ada)
            $table->string('type'); // Tipe pembayaran: system, user, combo
            $table->timestamps();

            // Foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down() {
        Schema::dropIfExists('transaksi');
    }
};
