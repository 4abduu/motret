<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVerificationRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Tabel untuk pengajuan verifikasi
        Schema::create('verification_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Foreign key ke tabel users
            $table->string('full_name'); // Nama lengkap
            $table->string('username'); // Username
            $table->string('status')->default('pending'); // Status: pending, approved, rejected
            $table->text('admin_notes')->nullable(); // Catatan dari admin
            $table->timestamps();

            // Relasi ke tabel users
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // Tabel untuk dokumen pendukung
        Schema::create('verification_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('verification_request_id'); // Foreign key ke tabel verification_requests
            $table->string('file_path'); // Path file dokumen
            $table->string('file_type'); // Jenis dokumen (KTP, SIM, dsb.)
            $table->timestamps();

            // Relasi ke tabel verification_requests
            $table->foreign('verification_request_id')->references('id')->on('verification_requests')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('verification_documents');
        Schema::dropIfExists('verification_requests');
    }
}