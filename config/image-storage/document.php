<?php

return [

    'title' => 'Документы',

    'per_page' => 40,

    'size_validation' => [
        'enabled' => true,
        'max_size' => '1500000',
        'error_message' => 'Превышен максимальный размер файла в [size] MB',
    ],

    'extension_validation' => [
        'enabled' => true,
        'allowed_extensions' => ['xls', 'xlsx', 'doc', 'docx', 'ppt', 'pptx', 'pdf', 'txt'],
        'error_message' => 'Допустимы только файлы форматов: [extension_list]',
    ],

    /* use source file name as title when uploading images */
    'source_title' => true,

    /* delete files upon deleting entry from database */
    'delete_files' => false,

    /* rename files upon renaming entry title in database */
    'rename_files' => false,

    /* displays or hides generate new size button in cms */
    'display_generate_new_size_button' => true,

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
            'caption' => 'Документ активен',
            'type' => 'checkbox',
            'options' => [
                1 => 'Активные',
                0 => 'He aктивные',
            ],
            'field' => 'tinyInteger',
        ],
    ],

    'sizes' => [
        'source' => [
            'caption' => 'Основной файл',
            'default_tab' => true,
        ],
        'ua' => [
            'caption' => 'Файл на укр',
            'default_tab' => false,
        ],
        'en' => [
            'caption' => 'Файл на англ',
            'default_tab' => false,
        ],

    ],

];
