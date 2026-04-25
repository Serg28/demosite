<?php

return [
    'title' => 'Видео',

    'per_page' => 40,

    'fields' => [
        'api_provider' => [
            'caption' => 'Видео сервис',
            'type' => 'select',
            'options' => config('image-storage.video_api.provider_names'),
        ],
        'api_id' => [
            'caption' => 'Идентификатор видео',
            'type' => 'text',
            'field' => 'string',
            'placeholder' => 'Идентификатор видео',
        ],

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
            'caption' => 'Видео активно',
            'type' => 'checkbox',
            'options' => [
                1 => 'Активные',
                0 => 'He aктивные',
            ],
            'field' => 'tinyInteger',
        ],
    ],

];
