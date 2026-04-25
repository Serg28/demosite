<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderUtm;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cookie;

class UtmLabel
{
    private array $utms = ['UTM_CAMPAIGN', 'UTM_CONTENT', 'UTM_MEDIUM', 'UTM_SOURCE', 'UTM_TERM'];

    private int $time = 100000;

    public function saveCookies(): void
    {
        $utmsCollection = request()->only($this->utms);

        foreach ($utmsCollection as $utm => $value) {
            Cookie::queue($utm, $value, $this->time, '/');
        }
    }

    public function saveUtmLabels(Order $order): void
    {
        if (array_filter($this->prepareOrderUtms())) {
            $orderUtm = new OrderUtm($this->prepareOrderUtms());
            $order->orderUtm()->save($orderUtm);
        }
    }

    private function prepareOrderUtms(): array
    {
        return Arr::only(request()->cookie(), $this->utms);
    }
}
