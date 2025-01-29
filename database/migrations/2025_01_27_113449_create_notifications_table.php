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
        Schema::create('notifs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('notify_for'); // ID pengguna yang menerima notifikasi
            $table->unsignedBigInteger('notify_from'); // ID pengguna yang mengirim notifikasi
            $table->unsignedBigInteger('target_id')->nullable(); // ID target terkait (foto, komentar, dll.)
            $table->enum('type', ['follow', 'like', 'comment', 'reply', 'system']); // Tipe notifikasi
            $table->text('message')->nullable(); // Pesan tambahan (opsional)
            $table->boolean('status')->default(false); // Status baca (false: belum dibaca, true: sudah dibaca)
            $table->timestamps();

            // Relasi ke tabel users
            $table->foreign('notify_for')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('notify_from')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifs');
    }
};