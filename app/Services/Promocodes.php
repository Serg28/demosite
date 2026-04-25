<?php

namespace App\Services;

use App\Models\PromoCode;
use Carbon\Carbon;
use Gloudemans\Shoppingcart\Cart;
use Illuminate\Session\SessionManager;

class Promocodes
{
    public function get()
    {
        return session()->get('promocode');
    }

    public function set($code)
    {
        return (! $code) ?
            $this->remove() :
            session()->put('promocode', $code);
    }

    public function remove()
    {
        return session()->remove('promocode');
    }

    public function getSale(): int
    {
        $promoCode = $this->get();
        return $promoCode ? $promoCode->sale : 0;
    }

    public function check($code)
    {
        $code = PromoCode::whereCode($code)
            ->active()
            ->where('is_used', 0)
            ->where(function ($query) {
                $query->where('date_exp', '>=', Carbon::now()->format('Y-m-d'))
                    ->orWhereNull('date_exp')
                    ->orWhere('date_exp', 0)
                    ->orWhere('date_exp', '0000-00-00');
            })
            ->first();

        if ($code) {
            if ($code->type === 'once') {
                $code->update([
                    'is_used' => 1,
                ]);
            }
            $this->set($code);

            return $code;
        }

        $this->remove();

        return false;
    }

    public function resetPromoCode()
    {
        $code = $this->get();
        if ($code && $code->type === 'once') {
            $code->update(['is_used' => 0]);
        }
        return $this->remove();
    }
}
