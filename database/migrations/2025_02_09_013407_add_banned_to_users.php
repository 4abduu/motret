<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBannedToUsers extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('banned')->default(false)->after('status'); // Status banned (true, false)
            $table->string('banned_type')->nullable()->after('banned'); // Tipe banned (temporary, permanent)
            $table->timestamp('banned_until')->nullable()->after('banned_type'); // Tanggal berakhir banned (untuk banned sementara)
            $table->text('banned_reason')->nullable()->after('banned_until'); // Alasan banned (optional)
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('banned');
            $table->dropColumn('banned_type');
            $table->dropColumn('banned_until');
            $table->dropColumn('banned_reason');
        });
    }
}