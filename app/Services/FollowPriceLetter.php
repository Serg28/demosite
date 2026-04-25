<?php

namespace App\Services;

use App\Jobs\SendFollowPriceLetterJob;
use App\Models\FollowPrice;
use App\Models\Product;
use Illuminate\Support\Collection;

class FollowPriceLetter
{
    private Product $product;

    public function send(Product $product): bool
    {
        $this->product = $product;

        return $this->sendContestLetterToJob(
            $this->getEmails()
        );
    }

    private function sendContestLetterToJob(Collection $emails): bool
    {
        if (count($emails)) {
            foreach ($emails as $email) {
                dispatch(new SendFollowPriceLetterJob($email, $this->product));
            }

            return true;
        }

        return false;
    }

    private function getEmails(): Collection
    {
        return FollowPrice::where('product_id', $this->product->id)->pluck('email');
    }
}
