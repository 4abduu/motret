<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tarik_saldo', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->decimal('amount', 15, 2);
            $table->enum('status', ['pending', 'success', 'rejected'])->default('pending');
            $table->string('method'); // bank, e-wallet, dll
            $table->string('destination'); // no rekening/e-wallet
            $table->string('destination_name'); // atas nama rekening / e-wallet
            $table->text('note')->nullable(); // catatan tambahan
            $table->timestamps();
        
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tarik_saldo');
    }
};
