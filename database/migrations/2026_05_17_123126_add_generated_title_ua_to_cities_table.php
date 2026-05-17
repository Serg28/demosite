<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    // Generated virtual columns for all supported locales + composite index with is_active.
    // Blueprint doesn't support MariaDB VIRTUAL generated columns — raw SQL required.
    private array $locales = ['ua', 'ru', 'en'];

    public function up(): void
    {
        $addColumns = implode(', ', array_map(
            fn (string $locale) => "ADD COLUMN title_{$locale} VARCHAR(255) AS (JSON_UNQUOTE(JSON_EXTRACT(title, '$.{$locale}'))) VIRTUAL",
            $this->locales,
        ));

        $addIndexes = implode(', ', array_map(
            fn (string $locale) => "ADD INDEX idx_cities_active_title_{$locale} (is_active, title_{$locale}(191))",
            $this->locales,
        ));

        DB::statement("ALTER TABLE cities {$addColumns}, {$addIndexes}");
    }

    public function down(): void
    {
        $dropIndexes = implode(', ', array_map(
            fn (string $locale) => "DROP INDEX idx_cities_active_title_{$locale}",
            $this->locales,
        ));

        $dropColumns = implode(', ', array_map(
            fn (string $locale) => "DROP COLUMN title_{$locale}",
            $this->locales,
        ));

        DB::statement("ALTER TABLE cities {$dropIndexes}, {$dropColumns}");
    }
};
