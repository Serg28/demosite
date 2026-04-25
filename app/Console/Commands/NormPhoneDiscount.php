<?php

namespace App\Console\Commands;

use App\Helpers\PhoneNumberHelper;
use App\Models\DiscountCard;
use Illuminate\Console\Command;

class NormPhoneDiscount extends Command
{
    protected $signature = 'discount:normalize';

    protected $description = 'Normalizing all discount cards: phone number';

    public function __construct(DiscountCard $discountCard)
    {
        parent::__construct();
    }

    public function handle(): void
    {
        $this->info('Normalizing all discount cards. This might take a while...');

        foreach (DiscountCard::cursor() as $item) {
            if ($phone = $this->norm($item->phone)) {
                $item->update(['phone' => $phone]);
            }
        }

        $this->info('Done!');
    }

    public function norm($phone): ?string
    {
        if ($phone) {
            return PhoneNumberHelper::formatPhoneNumber($phone);
        }
        return null;
    }
}