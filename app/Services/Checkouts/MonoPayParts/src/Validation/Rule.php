<?php

namespace App\Services\Checkouts\MonoPayParts\src\Validation;

use App\Services\Checkouts\MonoPayParts\src\Invoice;

class Rule
{
    public function getOrderCreateRules(): array
    {
        return [
            'store_order_id' => 'required|between:1,32',
            'total_sum' => 'required|min:500|numeric',
            'client_phone' => 'required|regex:/\+380\d{9}/',
            'invoice' => 'required|array',
            'invoice.date' => sprintf('required|date_format:%s', Invoice::INVOICE_DATE_FORMAT),
            'invoice.number' => 'required',
            'invoice.source' => sprintf('required|in:%s,%s', Invoice::INVOICE_SOURCE_INTERNET, Invoice::INVOICE_SOURCE_STORE),
            'available_programs' => 'required|array',
            'available_programs.*.available_parts_count' => 'required|array',
            'available_programs.*.available_parts_count.*' => 'required|gte:3|lte:25',
            'available_programs.*.type' => sprintf('required|string|in:%s', Invoice::INVOICE_PROGRAM_TYPE_INSTALLMENTS),
            'products' => 'required|array',
            'result_callback' => 'required|url',
        ];
    }

    public function getOrderCreateMessages(): array
    {
        return [
            'client_phone.regex' => 'The client phone format is invalid. Valid pattern is +380\d{9}',
            'invoice.date.date_format' => sprintf('Invalid date format. Valid format is %s', Invoice::INVOICE_DATE_FORMAT),
        ];
    }

    public function getCustomerValidateRules(): array
    {
        return ['telephone' => 'required|regex:/\+380\d{9}/'];
    }

    public function getCustomerValidateMessages(): array
    {
        return [
            'telephone.regex' => 'The client phone format is invalid. Valid pattern is +380\d{9}',
        ];
    }

    public function getStoreValidateRules(): array
    {
        return [
            'date' => sprintf('required|date_format:%s', Invoice::INVOICE_DATE_FORMAT),
        ];
    }

    public function getStoreValidateMessages(): array
    {
        return [
            'date.date_format' => sprintf('Invalid date format. Valid format is %s', Invoice::INVOICE_DATE_FORMAT),
        ];
    }
}
