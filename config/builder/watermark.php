<?php

return [

    'active' => env('WATERMARK_ENABLED', false),
    //top-left, top, top-right, left, center, right, bottom-left, bottom, bottom-right
    'position' => env('WATERMARK_POSITION', 'top'),
    'path' => public_path(env('WATERMARK_PATH', 'images/gpay.png')),
    'x' => env('WATERMARK_X', 10), //Optional relative offset of the new image on x-axis of the current image
    'y' => env('WATERMARK_Y', 10),  //Optional relative offset of the new image on y-axis of the current image
    'width' => env('WATERMARK_WIDTH', 800),
];
