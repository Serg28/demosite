<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('orders-channel', function ($user) {

    $authUser = app('user');

    if (!$authUser || !$user) {
        Log::warning('No authenticated for orders-channel');
        return false;
    }

    $hasPermissions = $authUser->hasPermissions(["orders.view", "orders.update", "orders.save"]);

    if ((int) $user->user_id === (int) $authUser->id || (int) $user->manager_id === (int) $authUser->id || $hasPermissions) {
        Log::info('User has access to orders-channel');
        return true;
    }

    Log::warning('User does not have access to orders-channel');
    return false;
});



/*
Broadcast::channel('orders-channel.{id}', function ($user, $id) {
    $permissions = $user->hasPermissions(["orders.view", "orders.update", "orders.save"]);
    $order = Order::find($id)->select(['user_id', 'manager_id'])->first();
    return ((int) $user->id === (int) $order->user_id || (int) $user->id === (int) $order->manager_id || $permissions);
});*/

