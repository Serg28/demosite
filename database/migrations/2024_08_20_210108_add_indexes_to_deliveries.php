<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        \DB::statement('CREATE INDEX is_active_price_id_title ON deliveries(is_active, price, id, title(128))');
    }

    public function down(): void
    {
        DB::statement('DROP INDEX is_active_price_id_title ON deliveries');
    }
};
