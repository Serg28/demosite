<?php
return array(
    'title' => "Видео",

    'per_page' => 40,
    
    'fields' => array(
        'api_provider' => array(
            'caption' => 'Видео сервис',
            'type' => 'select',
            'options' => config('image-storage.video_api.provider_names')
        ),
        'api_id' => array(
            'caption' => 'Идентификатор видео',
            'type' => 'text',
            'field' => 'string',
            'placeholder' => 'Идентификатор видео',
        ),

        'title' => array(
            'caption' => 'Название',
            'type' => 'text',
            'field' => 'string',
            'tabs' => config('translations.config.languages')
        ),
        'description' => array(
            'caption' => 'Описание',
            'type' => 'wysiwyg',
            'field' => 'text',
            'tabs' => config('translations.config.languages')

        ),
        'is_active' => array(
            'caption' => 'Видео активно',
            'type' => 'checkbox',
            'options' => array(
                1 => 'Активные',
                0 => 'He aктивные',
            ),
            'field' => 'tinyInteger',
        ),
    ),

);
