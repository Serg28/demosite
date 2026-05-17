<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'firstName' => ['required', 'string', 'min:2', 'max:100'],
            'lastName' => ['nullable', 'string', 'max:100'],
            'phone' => ['required', 'string', 'min:10', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'comment' => ['nullable', 'string', 'max:1000'],
            'cityId' => ['required', 'integer', 'exists:cities,id'],
            'deliveryId' => ['required', 'exists:deliveries,id'],
            'payMethodId' => ['required', 'exists:pay_methods,id'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'firstName.required' => __t("Вкажіть ім'я"),
            'firstName.min' => __t("Ім'я занадто коротке"),
            'phone.required' => __t('Вкажіть номер телефону'),
            'phone.min' => __t('Номер телефону занадто короткий'),
            'email.email' => __t('Невірний формат email'),
            'cityId.required' => __t('Оберіть місто доставки'),
            'cityId.exists' => __t('Обране місто недоступне'),
            'deliveryId.required' => __t('Оберіть спосіб доставки'),
            'deliveryId.exists' => __t('Обраний спосіб доставки недоступний'),
            'payMethodId.required' => __t('Оберіть спосіб оплати'),
            'payMethodId.exists' => __t('Обраний спосіб оплати недоступний'),
            'deliveryWarehouseId.required' => __t('Оберіть відділення'),
            'deliveryPickupPointId.required' => __t('Оберіть пункт самовивозу'),
            'address.required' => __t('Вкажіть адресу доставки'),
            'payPartsCount.required' => __t('Оберіть кількість місяців розстрочки'),
            'payPartsCount.min' => __t('Мінімум 2 місяці'),
            'b2bCompany.required' => __t('Вкажіть назву компанії'),
            'b2bEdrpou.required' => __t('Вкажіть ЄДРПОУ'),
            'b2bEdrpou.digits' => __t('ЄДРПОУ має містити рівно 8 цифр'),
        ];
    }
}
