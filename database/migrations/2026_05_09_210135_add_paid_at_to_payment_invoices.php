<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payment_invoices', function (Blueprint $table) {
            $table->timestamp('paid_at')->nullable()->after('gateway_response');
            $table->string('fail_reason')->nullable()->after('paid_at');
        });
    }

    public function down(): void
    {
        Schema::table('payment_invoices', function (Blueprint $table) {
            $table->dropColumn(['paid_at', 'fail_reason']);
        });
    }
};
