<?php

namespace App\Jobs;

use App\Services\Payment\WebhookProcessor;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessPaymentWebhook implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    /** @var array<int> */
    public array $backoff = [60, 300, 900];

    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(
        public readonly string $gatewaySlug,
        public readonly array $payload,
    ) {
        $this->onQueue('payments');
    }

    public function handle(WebhookProcessor $processor): void
    {
        $result = $processor->process($this->gatewaySlug, $this->payload);

        if (! $result) {
            Log::channel('payments')->warning('WebhookJob: not processed', [
                'gateway' => $this->gatewaySlug,
                'payload' => $this->payload,
                'attempt' => $this->attempts(),
            ]);
        }
    }

    public function failed(\Throwable $e): void
    {
        Log::channel('payments')->error('WebhookJob: failed after all retries', [
            'gateway' => $this->gatewaySlug,
            'payload' => $this->payload,
            'error' => $e->getMessage(),
        ]);
    }
}
