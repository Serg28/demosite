<?php

namespace App\Services;

use App\Jobs\SendFollowAvailableLetterJob;
use App\Models\AvailabilityOrder;
use App\Models\Product;

class FollowAvailableLetter
{
    private Product $product;

    public function send(Product $product): bool
    {
        $this->product = $product;

        return $this->sendAvalaibleLetterToJob(
            $this->getEmails()
        );
    }

    private function sendAvalaibleLetterToJob(array $emails): bool
    {
        if (count($emails)) {
            foreach ($emails as $email) {
                dispatch(new SendFollowAvailableLetterJob($email, $this->product));
            }

            return true;
        }

        return false;
    }

    private function getEmails(): array
    {
        //return AvailabilityOrder::where('product_id', $this->product->id)->pluck('email');
        return AvailabilityOrder::where('product_id', $this->product->id)->pluck('email')->toArray();
    }
}
