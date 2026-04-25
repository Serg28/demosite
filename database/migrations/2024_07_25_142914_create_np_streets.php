<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('np_streets', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->unsignedBigInteger('city_id')->nullable();
            $table->string('city_ref'); // Новое поле для хранения city_ref
            $table->string('ref');

            // Внешний ключ с таблицей cities
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
        });

        // Создание триггера для вставки
        DB::unprepared('
            CREATE TRIGGER set_city_id_before_insert_np_streets
            BEFORE INSERT ON np_streets FOR EACH ROW
            BEGIN
                DECLARE city_id_value INT;
                SET city_id_value = (SELECT id FROM cities WHERE ref = NEW.city_ref LIMIT 1);
                SET NEW.city_id = city_id_value;
            END
        ');

        // Создание триггера для обновления
        DB::unprepared('
            CREATE TRIGGER set_city_id_before_update_np_streets
            BEFORE UPDATE ON np_streets FOR EACH ROW
            BEGIN
                DECLARE city_id_value INT;
                SET city_id_value = (SELECT id FROM cities WHERE ref = NEW.city_ref LIMIT 1);
                SET NEW.city_id = city_id_value;
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
        // Удаление триггеров
        DB::unprepared('DROP TRIGGER IF EXISTS set_city_id_before_insert_np_streets');
        DB::unprepared('DROP TRIGGER IF EXISTS set_city_id_before_update_np_streets');

        Schema::dropIfExists('np_streets');
    }
};
