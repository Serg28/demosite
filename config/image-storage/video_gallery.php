<?php

return [

    'title' => 'Видеогалереи',

    'per_page' => 20,

    'fields' => [
        'title' => [
            'caption' => 'Название',
            'type' => 'text',
            'field' => 'string',
            'tabs' => config('translations.config.languages'),

        ],
        'description' => [
            'caption' => 'Описание',
            'type' => 'wysiwyg',
            'field' => 'text',
            'tabs' => config('translations.config.languages'),

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
