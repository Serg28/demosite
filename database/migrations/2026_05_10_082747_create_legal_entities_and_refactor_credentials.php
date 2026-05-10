<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── legal_entities_recipients ───────────────────────────────────────────
        Schema::create('legal_entities_recipients', function (Blueprint $table) {
            $table->id();
            $table->json('title');
            $table->integer('priority')->default(100);
            $table->text('detail')->nullable();
            $table->json('sms_detail')->nullable();
            $table->tinyInteger('is_default')->nullable()->default(0)->index();
            $table->text('checkbox_domain')->nullable();
            $table->text('checkbox_login')->nullable();
            $table->text('checkbox_password')->nullable();
            $table->text('checkbox_license_key')->nullable();
            $table->text('checkbox_token')->nullable();
        });

        // ── pay_methods: додати commission_rates JSON ───────────────────────────
        Schema::table('pay_methods', function (Blueprint $table) {
            $table->json('commission_rates')->nullable()->after('commission_percent');
        });

        // ── payment_credentials: day_of_week → days_of_week JSON ───────────────
        Schema::table('payment_credentials', function (Blueprint $table) {
            $table->dropColumn('day_of_week');
        });

        Schema::table('payment_credentials', function (Blueprint $table) {
            $table->json('days_of_week')->nullable()->after('pay_method_id');
            $table->foreignId('legal_entity_id')
                ->nullable()
                ->after('days_of_week')
                ->constrained('legal_entities_recipients')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('payment_credentials', function (Blueprint $table) {
            $table->dropForeign(['legal_entity_id']);
            $table->dropColumn(['days_of_week', 'legal_entity_id']);
        });

        Schema::table('payment_credentials', function (Blueprint $table) {
            $table->tinyInteger('day_of_week')->nullable()->after('pay_method_id');
        });

        Schema::table('pay_methods', function (Blueprint $table) {
            $table->dropColumn('commission_rates');
        });

        Schema::dropIfExists('legal_entities_recipients');
    }
};
