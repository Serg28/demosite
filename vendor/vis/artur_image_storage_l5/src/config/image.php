<?php
return array(

    'title' => "Изображения",

    'per_page' => 40,

    'size_validation' => array(
        'enabled' => true,
        'max_size' => '1500000',
        'error_message' => "Превышен максимальный размер изображения в [size] MB"
    ),

    'extension_validation' => array(
        'enabled' => true,
        'allowed_extensions' => array('png', 'jpg', 'jpeg'),
        'error_message' => "Допустимы только изображения форматов: [extension_list]"
    ),

    /* Quality is only applied if you're encoding JPG format since PNG compression. Value range is 0-100.*/
    'quality' => 85,

    /* Optimization with Vis\Builder\OptimizationImg. May greatly increase execution time when used to large sized photos. */
    'optimization' => true,

    /* use source file name as title when uploading images */
    'source_title' => true,

    /* store EXIF metadata in database */
    'store_exif' => true,

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
            'caption' => 'Изображение активно',
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
            'caption' => 'Оригинал',
            'default_tab' => true,
        ),
        'cms_preview' => array(
            'caption' => 'Превью в ЦМС',
            'default_tab' => false,
            'modify' => array(
                'fit' => array(160, 160, function (\Intervention\Image\Constraint $constraint) {
                    $constraint->upsize();
                }),
            ),
        ),

        /*            'extra_small' => array(
                        'caption' => 'Очень маленькая',
                        'default_tab' => false,
                        'modify' => array(
                            'resize' => array(160, null, function (\Intervention\Image\Constraint $constraint) {
                                $constraint->aspectRatio();
                            }),
                            'resizeCanvas' => array(160, 80, 'center', false, 'rgba(0, 0, 0, 0)'),
                        ),
                    ),*/

    ),

);
