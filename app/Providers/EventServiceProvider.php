<?php

namespace App\Providers;

use App\Events\Cart\CartItemAdded;
use App\Events\Cart\CartItemRemoved;
use App\Events\Cart\CartItemUpdated;
use App\Listeners\Cart\PersistUnfinishedBasket;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /** @var array<class-string, list<class-string>> */
    protected $listen = [
        CartItemAdded::class   => [PersistUnfinishedBasket::class],
        CartItemUpdated::class => [PersistUnfinishedBasket::class],
        CartItemRemoved::class => [PersistUnfinishedBasket::class],
    ];
}
