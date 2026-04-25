<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EasyPayController extends Controller
{
    public function success(Request $request): RedirectResponse
    {
        $data = $request->all();
        Log::error('Order Pay confirmed.');
        Order::where('id', $data['orderId'])->update([
            'is_online_payed' => 1,
        ]);

        return redirect('/');
    }

    public function error(Request $request): RedirectResponse
    {
        Log::error('Order Pay not confirmed.');

        return redirect('/');
    }
}
