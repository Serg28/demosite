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
            'callMe' => ['boolean'],
            'receiver' => ['required', 'in:user,other'],
            'receiverFirstName' => ['required_if:receiver,other', 'nullable', 'string', 'min:2', 'max:100'],
            'receiverLastName' => ['nullable', 'string', 'max:100'],
            'receiverPatronymic' => ['nullable', 'string', 'max:100'],
            'receiverPhone' => ['required_if:receiver,other', 'nullable', 'string', 'min:10', 'max:20'],
            'receiverEmail' => ['nullable', 'email', 'max:255'],
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
            'receiver.in' => __t('Невірний тип одержувача'),
            'receiverFirstName.required_if' => __t("Вкажіть ім'я одержувача"),
            'receiverFirstName.min' => __t("Ім'я одержувача занадто коротке"),
            'receiverPhone.required_if' => __t('Вкажіть телефон одержувача'),
            'receiverPhone.min' => __t('Телефон одержувача занадто короткий'),
            'receiverEmail.email' => __t('Невірний формат email одержувача'),
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
