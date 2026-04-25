<?php

namespace App\Services;

use App\Models\Delivery;
use App\Models\DeliverySchedule;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;

class DeliveryTime
{
    public function getDeliveryTimeForProduct(Product $product): array
    {
        $warehouseIds = $product->getAvailabilityInfo()->pluck('id')->unique()->toArray();
        return $this->getDeliverySchedule($warehouseIds);
    }

    public function getDeliveryTimeForProducts(array $productIds): array
    {
        $warehouseIds = Product::whereIn('id', $productIds)
            ->pluck('availability_info')
            ->map(function ($availabilityInfo) {
                return collect(json_decode($availabilityInfo))
                    ->pluck('id')
                    ->toArray();
            })
            ->flatten()
            ->unique()
            ->toArray();

        return $this->getDeliverySchedule($warehouseIds);
    }

    private function getDeliverySchedule(array $warehouseIds)
    {
        $dayOfWeek = Carbon::now()->dayOfWeekIso;
        $currentTime = Carbon::now()->format('H:i:s');

        $availability = [];
        $deliveryMethods = Delivery::select('id', 'title->'.App::getLocale().' as title', 'price')
            ->active()
            ->groupBy('id', 'title->'.App::getLocale())
            ->get();

        $schedules = DeliverySchedule::whereIn('warehouse_id', $warehouseIds)
            ->where('day_of_week', $dayOfWeek)
            ->whereTime('start_time', '<=', $currentTime)
            ->whereTime('end_time', '>=', $currentTime)
            ->with(['deliveryInfo' => function ($query) use ($deliveryMethods) {
                $query->whereIn('delivery_id', $deliveryMethods->pluck('id'));
            }])
            ->get();

        foreach ($deliveryMethods as $deliveryMethod) {
            $schedule = $schedules->first(function ($schedule) use ($deliveryMethod) {
                return $schedule->deliveryInfo->contains('delivery_id', $deliveryMethod->id);
            });

            if ($schedule) {
                $deliveryInfo = $schedule->deliveryInfo
                    ->where('delivery_id', $deliveryMethod->id)
                    ->sortByDesc('days_to_delivery')
                    ->first();
                $availability[$deliveryMethod->id] = [
                    'title' => $deliveryMethod->title,
                    'price' => $deliveryMethod->price ? $deliveryMethod->price . ' '.setting('currency') : '',
                    'description' => $this->processDate($deliveryInfo->t('description'))
                ];
            }
        }

        return $availability;
    }

    public function processDate(string $inputDate): string
    {
        // Ищем шаблон "n+2"
        if (!preg_match('/n(\+\d+)?\+(\d+)/', $inputDate, $matches)) {
            return $inputDate;
        }

        $modifier = $matches[2] ?? '';
        $daysToAdd = (int)str_replace('+', '', $modifier);
        $today = new \DateTimeImmutable('now');
        $date = $today->modify("+{$daysToAdd} day");

        return str_replace($matches[0], $date->format('d.m.Y'), $inputDate);
    }

}
