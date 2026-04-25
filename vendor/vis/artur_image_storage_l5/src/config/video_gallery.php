<?php
return array(

    'title' => "Видеогалереи",

    'per_page' => 20,

    'fields' => array(
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
            'caption' => 'Галерея активна',
            'type' => 'checkbox',
            'options' => array(
                1 => 'Активные',
                0 => 'He aктивные',
            ),
            'field' => 'tinyInteger',
        ),
    ),

);
