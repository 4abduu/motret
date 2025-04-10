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
        Schema::create('downloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('photo_id')
                  ->references('id')
                  ->on('foto')
                  ->onDelete('cascade');
            $table->foreignId('user_id')
                  ->nullable()
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
            $table->string('resolution')->default('low');
            $table->enum('status', ['1', '0'])->default('1');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('downloads');
    }
};
