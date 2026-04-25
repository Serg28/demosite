<?php

namespace App\Services;

use App\Models\Order;
use Daaner\TurboSMS\Facades\TurboSMS;

class Sms
{
    public function send(Order $order): void
    {
        if (! setting('api_key_turbo_sms')) {
            return;
        }

        if ($type = $this->getType()) {
            TurboSMS::sendMessages($order->phone, $this->getMessage($order), $type);
        }
    }

    public function sendText($phone, $message): void
    {
        if (! setting('api_key_turbo_sms')) {
            return;
        }

        if ($type = $this->getType()) {
            TurboSMS::sendMessages($phone, $message, $type);
        }
    }

    private function getMessage(Order $order): string
    {
        return str_replace('[number]', $order->id, setting('message-in-sms'));
    }

    private function getType(): ?string
    {
        if (setting('sms_turbo_sms') && setting('viber_turbo_sms')) {
            return  'both';
        }

        if (setting('sms_turbo_sms')) {
            return  'sms';
        }

        if (setting('viber_turbo_sms')) {
            return 'viber';
        }

        return null;
    }
}
