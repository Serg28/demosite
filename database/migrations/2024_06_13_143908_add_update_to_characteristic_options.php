<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('characteristic_options', function (Blueprint $table) {
            $table->tinyInteger('updated')->index()->nullable()->default(1);
        });

        // Триггер для события BEFORE UPDATE
        DB::unprepared('
        CREATE TRIGGER update_characteristic_options_updated_trigger
        BEFORE UPDATE ON characteristic_options
        FOR EACH ROW
        BEGIN
            -- Установка поля updated при обновлении категории и разрешение сбросить это поле, если явно записываем в него 0
            IF NEW.updated = 0 AND NEW.updated <> OLD.updated THEN
                SET NEW.updated = 0;
            ELSE
                SET NEW.updated = 1;
            END IF;
        END;
        ');
    }

    public function down(): void
    {
        Schema::table('characteristic_options', function (Blueprint $table) {
            $table->dropColumn(['updated']);
        });

        DB::unprepared('DROP TRIGGER IF EXISTS update_characteristic_options_updated_trigger');
    }
};
