<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        DB::unprepared('
            CREATE TRIGGER update_update_products_on_change_trigger
            AFTER UPDATE ON product_characteristic_options
            FOR EACH ROW
            BEGIN
                -- После изменения записи выставить у товара в таблице products значение updated=1
                UPDATE products
                SET updated = 1
                WHERE id = NEW.product_id;
            END;
        ');

        DB::unprepared('
            CREATE TRIGGER delete_update_products_on_delete_trigger
            AFTER DELETE ON product_characteristic_options
            FOR EACH ROW
            BEGIN
                -- После удаления записи выставить у товара в таблице products значение updated=1
                UPDATE products
                SET updated = 1
                WHERE id = OLD.product_id;
            END;
        ');
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS update_update_products_on_change_trigger;');
        DB::unprepared('DROP TRIGGER IF EXISTS delete_update_products_on_delete_trigger;');
    }
};
