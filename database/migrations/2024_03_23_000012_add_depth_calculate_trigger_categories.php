<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Создает триггеры для вычисления глубины узлов в структуре категорий.
 */
return new class extends Migration {
    /**
     * Запускает миграцию.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            DB::unprepared('
                CREATE TRIGGER before_insert_calculate_depth_trigger 
                BEFORE INSERT ON categories 
                FOR EACH ROW
                BEGIN
                    DECLARE parent_depth INT;
                    IF NEW.parent_id IS NULL THEN
                        SET NEW.depth = 0; 
                    ELSE
                        SELECT COUNT(parent.id) INTO parent_depth 
                        FROM categories AS node
                        JOIN categories AS parent
                        WHERE node.lft BETWEEN parent.lft AND parent.rgt
                        AND node.id = NEW.parent_id
                        AND (parent.lft IS NOT NULL AND parent.rgt IS NOT NULL AND parent.lft > 0 AND parent.rgt > 0);
                        SET NEW.depth = parent_depth; 
                    END IF;
                END
            ');

            DB::unprepared('
                CREATE TRIGGER before_update_calculate_depth_trigger 
                BEFORE UPDATE ON categories 
                FOR EACH ROW
                BEGIN
                    DECLARE parent_depth INT;
                    IF NEW.parent_id IS NULL THEN
                        SET NEW.depth = 0; 
                    ELSE
                        SELECT COUNT(parent.id) INTO parent_depth 
                        FROM categories AS node
                        JOIN categories AS parent
                        WHERE node.lft BETWEEN parent.lft AND parent.rgt
                        AND node.id = NEW.parent_id
                        AND (parent.lft IS NOT NULL AND parent.rgt IS NOT NULL AND parent.lft > 0 AND parent.rgt > 0);
                        SET NEW.depth = parent_depth; 
                    END IF;
                END
            ');
        });
    }

    /**
     * Откатывает миграцию.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // Удаляет созданные триггеры
            DB::unprepared('DROP TRIGGER IF EXISTS before_insert_calculate_depth_trigger');
            DB::unprepared('DROP TRIGGER IF EXISTS before_update_calculate_depth_trigger');
        });
    }
};
