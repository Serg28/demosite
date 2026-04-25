<?php

namespace App\Enums;

enum DeliveryMethod: string
{
    case NovaPoshta = 'nova_poshta';
    case Justin = 'justin';
    case Meest = 'meest';
    case Ukrposhta = 'ukrposhta';
    case Rozetka = 'rozetka';
    case SelfPickup = 'self_pickup';
    case Courier = 'courier';
}
