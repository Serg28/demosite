<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;

class HeaderPhonesEmailComposer
{
    public function compose(View $view): void
    {
        $phones = explode(',', setting('phones-v-header'));
        $email = setting('email-v-header');

        $view->with(compact('email', 'phones'));
    }
}
