<?php
return array(

    'title' => "Документы",

    'per_page' => 40,

    'size_validation' => array(
        'enabled' => true,
        'max_size' => '1500000',
        'error_message' => "Превышен максимальный размер файла в [size] MB"
    ),

    'extension_validation' => array(
        'enabled' => true,
        'allowed_extensions' => array('xls', 'xlsx', 'doc', 'docx', 'ppt', 'pptx', 'pdf', 'txt'),
        'error_message' => "Допустимы только файлы форматов: [extension_list]"
    ),

    /* use source file name as title when uploading images */
    'source_title' => true,

    /* delete files upon deleting entry from database */
    'delete_files' => false,

    /* rename files upon renaming entry title in database */
    'rename_files' => false,

    /* displays or hides generate new size button in cms */
    'display_generate_new_size_button' => true,

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
            'caption' => 'Документ активен',
            'type' => 'checkbox',
            'options' => array(
                1 => 'Активные',
                0 => 'He aктивные',
            ),
            'field' => 'tinyInteger',
        ),
    ),

    'sizes' => array(
        'source' => array(
            'caption' => 'Основной файл',
            'default_tab' => true,
        ),
        'ua' => array(
            'caption' => 'Файл на укр',
            'default_tab' => false,
        ),
        'en' => array(
            'caption' => 'Файл на англ',
            'default_tab' => false,
        ),

    ),

);
