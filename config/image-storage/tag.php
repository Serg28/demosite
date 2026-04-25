<?php

return [
    'title' => 'Теги',

    'per_page' => 20,

    'fields' => [
        'title' => [
            'caption' => 'Название',
            'type' => 'text',
            'field' => 'string',
            'tabs' => config('translations.config.languages'),
        ],
        'is_active' => [
            'caption' => 'Тег активен',
            'type' => 'checkbox',
            'options' => [
                1 => 'Активные',
                0 => 'He aктивные',
            ],
            'field' => 'tinyInteger',
        ],
    ],

];
