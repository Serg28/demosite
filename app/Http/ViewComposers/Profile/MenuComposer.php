<?php

namespace App\Http\ViewComposers\Profile;

use Illuminate\View\View;

class MenuComposer
{
    public function compose(View $view): void
    {
        $menu = [
            'profile' => __t('Персональні дані'),
            'profile.orders' => __t('Заказы'),
            'profile.favorites' => __t('Обране'),
            'profile.viewed_products' => __t('Просмотренное'),
            //'profile.compare' => __t('Сравнение'),
            //'profile.discount' => __t('Скидки'),
            'profile.logout' => __t('Вихід'),
        ];

        /*$icons = [
            'profile' => 'ti-id-badge',
            'profile.orders' => 'ti-shopping-cart-full',
            'profile.favorites' => 'ti-heart',
            'profile.viewed_products' => 'ti-eye',
            'profile.discount' => 'ti-server',
            'profile.logout' => 'ti-lock',
        ];*/
        $icons = [
            'profile' => '/img/pig1.svg',
            'profile.orders' => '/img/pig2.svg',
            'profile.favorites' => '/img/pig3.svg',
            'profile.viewed_products' => '/img/pig6.svg',
            //'profile.compare' => '/img/ci4.svg',
            //'profile.discount' => '/img/sbi7.svg',
            'profile.logout' => '/img/pig5.svg',
        ];

        $view->with(compact('menu', 'icons'));
    }
}
