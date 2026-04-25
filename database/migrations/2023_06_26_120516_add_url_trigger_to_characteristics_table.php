<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('
            CREATE TRIGGER before_characteristic_insert
            BEFORE INSERT ON characteristics
            FOR EACH ROW
            BEGIN
                IF NEW.url = \'{"ua":"","ru":"","en":"","pl":""}\' THEN
                    SET NEW.url = NULL;
                END IF;
            END
        ');

        DB::statement('
            CREATE TRIGGER before_characteristic_update
            BEFORE UPDATE ON characteristics
            FOR EACH ROW
            BEGIN
                IF NEW.url = \'{"ua":"","ru":"","en":"","pl":""}\' THEN
                    SET NEW.url = NULL;
                END IF;
            END
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP TRIGGER IF EXISTS before_characteristic_insert');
        DB::statement('DROP TRIGGER IF EXISTS before_characteristic_update');
    }
};
