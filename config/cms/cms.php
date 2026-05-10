<?php

return [
    'admin_prefix' => env('CMS_ADMIN_PREFIX', 'admin'), // Пока не менять
    'login_path' => env('CMS_LOGIN_PATH', 'login'),

    'menu' => [
        [
            'title' => 'Налаштування',
            'icon' => 'ri-settings-4-line',
            'link' => '/settings',
            'submenu' => [
                ['title' => 'Способи оплати',   'link' => '/pay_methods'],
                ['title' => 'Юридичні особи',   'link' => '/legal_entities_recipients'],
                ['title' => 'Системні налаштування', 'link' => '/settings'],
            ],
        ],
    ],
];
