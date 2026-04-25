<?php

namespace App\Services\Checkouts\MonoPayParts\src\Api;

use App\Services\Checkouts\MonoPayParts\src\Api;

class Store extends Api
{
    const API_URL_ORDER_CHECK_PAID = 'api/store/report';

    public function report(string $date)
    {
        $this->validateDate($date);

        return $this->send([
            'date' => $date,
        ], self::API_URL_ORDER_CHECK_PAID);
    }

    protected function validateDate(string $date)
    {
        $this->validator()->validate(
            ['date' => $date],
            $this->rule()->getStoreValidateRules(),
            $this->rule()->getStoreValidateMessages()
        );
    }
}
