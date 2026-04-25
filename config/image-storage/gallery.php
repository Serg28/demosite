<?php

return [

    'title' => 'Галереи',

    'per_page' => 20,

    'fields' => [
        'title' => [
            'caption' => 'Название',
            'type' => 'text',
            'field' => 'string',
        ],

        'is_active' => [
            'caption' => 'Галерея активна',
            'type' => 'checkbox',
            'options' => [
                1 => 'Активные',
                0 => 'He aктивные',
            ],
            'field' => 'tinyInteger',
        ],
    ],

];
