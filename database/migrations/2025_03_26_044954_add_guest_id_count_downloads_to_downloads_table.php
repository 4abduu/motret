<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGuestIdCountDownloadsToDownloadsTable extends Migration
{
    public function up(): void
    {
        Schema::table('downloads', function (Blueprint $table) {
            $table->string('guest_id')->nullable()->after('user_id'); // Tambah guest_id untuk guest user
            $table->integer('count_downloads')->default(0)->after('guest_id'); // Tambah count_downloads untuk tracking
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('downloads', function (Blueprint $table) {
            $table->dropColumn(['guest_id', 'count_downloads']);
        });
    }
}