<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('album_foto')) {
            return;
        }

        Schema::table('album_foto', function (Blueprint $table) {
            if (! $this->foreignKeyExists('album_foto', 'album_foto_album_id_foreign')) {
                $table->foreign('album_id', 'album_foto_album_id_foreign')
                    ->references('id')
                    ->on('album')
                    ->onDelete('cascade');
            }

            if (! $this->foreignKeyExists('album_foto', 'album_foto_photo_id_foreign')) {
                $table->foreign('photo_id', 'album_foto_photo_id_foreign')
                    ->references('id')
                    ->on('foto')
                    ->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('album_foto')) {
            return;
        }

        Schema::table('album_foto', function (Blueprint $table) {
            if ($this->foreignKeyExists('album_foto', 'album_foto_album_id_foreign')) {
                $table->dropForeign('album_foto_album_id_foreign');
            }

            if ($this->foreignKeyExists('album_foto', 'album_foto_photo_id_foreign')) {
                $table->dropForeign('album_foto_photo_id_foreign');
            }
        });
    }

    private function foreignKeyExists(string $table, string $constraintName): bool
    {
        $database = DB::getDatabaseName();

        $count = DB::table('information_schema.table_constraints')
            ->where('constraint_schema', $database)
            ->where('table_name', $table)
            ->where('constraint_type', 'FOREIGN KEY')
            ->where('constraint_name', $constraintName)
            ->count();

        return $count > 0;
    }
};
