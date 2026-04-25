<?php
return array(

    'title' => "Галереи",

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
        'event_date' => array(
            'caption' => 'Дата события',
            'type' => 'datetime',
            'is_sorting' => true,
            'months' => 2,
            'field' => 'timestamp',
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
