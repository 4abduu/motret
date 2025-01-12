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
        Schema::create('album_foto', function (Blueprint $table) {
            $table->id();
            $table->foreignId('album_id')
                  ->references('id')
                  ->on('album')
                  ->onDelete('cascade');
            $table->foreignId('photo_id')
                  ->references('id')
                  ->on('foto')
                  ->onDelete('cascade');
            $table->enum('status', ['1', '0'])->default('1');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('album_foto');
    }
};
