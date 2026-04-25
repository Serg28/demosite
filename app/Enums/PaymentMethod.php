<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case LiqPay = 'liqpay';
    case MonoPay = 'monopay';
    case WayForPay = 'wayfopay';
    case PrivatPay = 'privatpay';
    case EasyPay = 'easypay';
    case CashOnDelivery = 'cash_on_delivery';
    case BankTransfer = 'bank_transfer';
}
