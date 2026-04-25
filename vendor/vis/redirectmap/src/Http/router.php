<?php

Route::group(['middleware' => ['web']], function () {
    Route::group(
        ['prefix' => 'admin', 'middleware' => 'auth.admin'],
        function () {
            Route::post('redirect/import', 'Litvin\Redirectmap\Controllers\ImportController');
        }
    );
});