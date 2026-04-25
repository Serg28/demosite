<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->tinyInteger('updated')->after('updated_at')->index()->nullable()->default(1);
        });

        // Триггер для события BEFORE UPDATE
        DB::unprepared('
        CREATE TRIGGER update_product_updated_trigger
        BEFORE UPDATE ON products
        FOR EACH ROW
        BEGIN
            -- Установка поля updated при обновлении товара и разрешение сбросить это поле, если явно записываем в него 0
            -- Игнорируем поле count_view
            IF NEW.count_views = OLD.count_views THEN
                IF NEW.updated = 0 AND NEW.updated <> OLD.updated THEN
                    SET NEW.updated = 0;
                ELSE
                    SET NEW.updated = 1;
                END IF;
            END IF;
        END;
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['updated']);
        });

        DB::unprepared('DROP TRIGGER IF EXISTS update_product_updated_trigger');
    }
};
