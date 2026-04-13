<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RuntimeException;

class SqlDumpSeeder extends Seeder
{
    public function run(): void
    {
        $dumpPath = base_path('motret (1).sql');

        if (! File::exists($dumpPath)) {
            throw new RuntimeException("SQL dump file not found at: {$dumpPath}");
        }

        $sql = File::get($dumpPath);
        $insertStatements = $this->extractInsertStatements($sql);
        $autoIncrementStatements = $this->extractAutoIncrementStatements($sql);

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        try {
            $tables = $this->extractTables($insertStatements);

            foreach ($tables as $table) {
                if ($table === 'migrations') {
                    continue;
                }

                DB::table($table)->truncate();
            }

            foreach ($insertStatements as $statement) {
                if (str_contains($statement, 'INSERT INTO `migrations`')) {
                    continue;
                }

                DB::unprepared($statement);
            }

            foreach ($autoIncrementStatements as $statement) {
                if (str_contains($statement, 'ALTER TABLE `migrations`')) {
                    continue;
                }

                DB::unprepared($statement);
            }
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }
    }

    /**
     * @return array<int, string>
     */
    private function extractInsertStatements(string $sql): array
    {
        preg_match_all('/INSERT INTO\s+`[^`]+`\s*\([^;]+?;(?=\s*(?:\n--|\n\/\*|\z))/is', $sql, $matches);

        return $matches[0] ?? [];
    }

    /**
     * @return array<int, string>
     */
    private function extractAutoIncrementStatements(string $sql): array
    {
        preg_match_all('/ALTER TABLE\s+`[^`]+`\s+MODIFY\s+`id`[^;]*AUTO_INCREMENT=\d+;(?=\s*(?:\n--|\n\/\*|\z))/is', $sql, $matches);

        return $matches[0] ?? [];
    }

    /**
     * @param  array<int, string>  $insertStatements
     * @return array<int, string>
     */
    private function extractTables(array $insertStatements): array
    {
        $tables = [];

        foreach ($insertStatements as $statement) {
            if (preg_match('/INSERT INTO\s+`([^`]+)`/i', $statement, $matches) === 1) {
                $tables[] = $matches[1];
            }
        }

        return array_values(array_unique($tables));
    }
}
