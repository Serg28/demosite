<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Синхронізує pay_methods.slug та pay_methods.gateway з ключами реєстру config('payment.gateways').
 * Після міграції slug === gateway скрізь.
 */
return new class extends Migration
{
    /**
     * [old_slug => [new_slug, new_gateway]]
     */
    private const MAP = [
        'online'      => ['liqpay',        'liqpay'],
        'monoparts'   => ['monopayparts',  'monopayparts'],
        'privatparts' => ['privatpayparts','privatpayparts'],
        'cod'         => ['liqpay_cod',    'liqpay_cod'],
    ];

    public function up(): void
    {
        foreach (self::MAP as $oldSlug => [$newSlug, $newGateway]) {
            DB::table('pay_methods')
                ->where('slug', $oldSlug)
                ->update(['slug' => $newSlug, 'gateway' => $newGateway]);
        }
    }

    public function down(): void
    {
        // Інвертований MAP: new_slug → old_slug, old_gateway
        foreach (self::MAP as $oldSlug => [$newSlug, $newGateway]) {
            DB::table('pay_methods')
                ->where('slug', $newSlug)
                ->update(['slug' => $oldSlug, 'gateway' => $oldSlug]);
        }
    }
};
